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

use DateTime;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreateEPayrollNDRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreateEPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\CreateEPayrollResponse;
use Exception;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Request\CreateBillRequest;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Request\ReadBillRequest;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\UseCase\CreateBillUseCase;
use FlexPHP\Bundle\InvoiceBundle\Domain\BillStatus\BillStatus;
use FlexPHP\Bundle\InvoiceBundle\Domain\BillType\BillType;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\Request\UpdateNumerationRequest;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\UseCase\UpdateNumerationUseCase;

final class CreateEPayrollNDUseCase extends AbstractEPayrollUseCase
{
    public function execute(CreateEPayrollNDRequest $request): CreateEPayrollResponse
    {
        if (!empty($request->billId)) {
            $bill = $this->billRepository->getById(new ReadBillRequest($request->billId));
        }

        if (empty($bill) || !$bill->id()) {
            throw new Exception(\sprintf('Bill not exist [%d]', $request->billId ?? 0), 404);
        }

        if ($bill->status() !== BillStatus::AVAILABLE) {
            throw new Exception('Bill hasn\'t valid status: ' . $bill->status(), 400);
        }

        $bill->withLastDebit($this->billRepository->gateway(), 0);
        $debit = $bill->debitInstance();

        $provider = $this->getProvider();

        $this->testingMode = $provider->url() === 'http://localhost';

        if ($debit && $debit->downloadedAt()) {
            return $this->getResponseOk($debit);
        }

        $order = $this->orderRepository->getById(new ReadPaysheetRequest($bill->orderId()));

        $maxDayMonth = new DateTime(\date('Y-m-t 23:59:59', \strtotime($order->createdAt()->format('c'))));

        if (new DateTime() > $maxDayMonth) {
            throw new Exception('Paysheet is from another month: ' . $order->createdAt()->format('Y-m-d'), 400);
        }

        $sender = $this->getSender();
        $setting = $this->getSetting(BillType::DEBIT);
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
        $invoice = $this->getInvoiceND(
            $bill,
            $order->createdAt(),
            $numeration,
            $this->getItems($order),
            $this->getDeposits($order),
            $order->expiratedAt(),
            $order->billNotes()
        );

        if (!$debit) {
            $data = $this->orderRepository->getEPayrollData(new CreateEPayrollRequest($bill->orderId(), null));

            if (empty($data)) {
                throw new Exception(\sprintf('Paysheet data not found [%d]', $bill->orderId()), 404);
            }

            $useCase = new CreateBillUseCase($this->billRepository);

            $debit = $useCase->execute(new CreateBillRequest([
                'prefix' => $setting->prefix(),
                'number' => $setting->currentNumber(),
                'orderId' => $order->id(),
                'parentId' => $bill->id(),
                'provider' => $provider->id(),
                'status' => BillStatus::PENDING,
                'type' => BillType::DEBIT,
                'message' => 'Pendiente de procesar',
            ], -1))->bill;

            if (!$debit->id()) {
                return new CreateEPayrollResponse($debit->status(), 'Debit error');
            }

            (new UpdateNumerationUseCase($this->numerationRepository))->execute(
                new UpdateNumerationRequest($setting->id(), [
                    'currentNumber' => $setting->currentNumber() + 1,
                ], -1, true)
            );
        }

        return $this->processEPayroll($invoice, $debit, $provider, $sender, $receiver);
    }
}
