<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase;

use DateInterval;
use DateTime;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreateEPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\CreateEPayrollResponse;
use Exception;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Request\CreateBillRequest;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\UseCase\CreateBillUseCase;
use FlexPHP\Bundle\InvoiceBundle\Domain\BillStatus\BillStatus;
use FlexPHP\Bundle\InvoiceBundle\Domain\BillType\BillType;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\Request\UpdateNumerationRequest;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\UseCase\UpdateNumerationUseCase;

final class CreateEPayrollUseCase extends AbstractEPayrollUseCase
{
    public function execute(CreateEPayrollRequest $request): CreateEPayrollResponse
    {
        if (!empty($request->orderId)) {
            $order = $this->orderRepository->getById(new ReadPaysheetRequest($request->orderId));
        }

        if (empty($order) || !$order->id()) {
            throw new Exception(\sprintf('Paysheet not exist [%d]', $request->orderId ?? 0), 404);
        }

        if (!$order->customerId()) {
            throw new Exception('Paysheet customer not found', 404);
        }

        if ($order->isDraft()) {
            throw new Exception('Paysheet is draft', 400);
        }

        $order->withLastBill($this->orderRepository->billGateway(), 0);
        $bill = $order->billInstance();

        $provider = $this->getProvider();

        $this->testingMode = $provider->url() === 'http://localhost';

        if ($bill && $bill->downloadedAt()) {
            return $this->getResponseOk($bill);
        }

        $orderTimeout = clone $order->createdAt();
        $timeAllowed = new DateInterval(\sprintf('P%dD', $_ENV['EINVOICE_TIMEOUT'] ?? 10));

        if ($orderTimeout->add($timeAllowed) < new DateTime()) {
            throw new Exception('Paysheet is older', 400);
        }

        $sender = $this->getSender();
        $setting = $this->getSetting(BillType::INVOICE);
        $numeration = $this->getNumeration(
            $setting->resolution(),
            $setting->fromNumber(),
            $setting->currentNumber(),
            $setting->toNumber(),
            $setting->startAt(),
            $setting->finishAt(),
            $setting->prefix()
        );

        $receiver = $this->getReceiver($order->customerId());
        $invoice = $this->getInvoice(
            $order->createdAt(),
            $numeration,
            $this->getItems($order),
            $this->getDeposits($order),
            $order->expiratedAt(),
            $order->billNotes()
        );

        if (!$bill) {
            $data = $this->orderRepository->getEPayrollData($request);

            if (empty($data)) {
                throw new Exception(\sprintf('Paysheet data not found [%d]', $request->id), 404);
            }

            $useCase = new CreateBillUseCase($this->billRepository);

            $bill = $useCase->execute(new CreateBillRequest([
                'prefix' => $setting->prefix(),
                'number' => $setting->currentNumber(),
                'orderId' => $order->id(),
                'provider' => $provider->id(),
                'status' => BillStatus::PENDING,
                'type' => BillType::INVOICE,
                'message' => 'Pendiente de procesar',
            ], -1))->bill;

            if (!$bill->id()) {
                return new CreateEPayrollResponse($bill->status(), 'Bill error');
            }

            (new UpdateNumerationUseCase($this->numerationRepository))->execute(
                new UpdateNumerationRequest($setting->id(), [
                    'currentNumber' => $setting->currentNumber() + 1,
                ], -1, true)
            );
        }

        return $this->processEPayroll($invoice, $bill, $provider, $sender, $receiver);
    }
}
