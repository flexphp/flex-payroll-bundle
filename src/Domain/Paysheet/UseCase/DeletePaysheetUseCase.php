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

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Paysheet;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\DeletePaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\DeletePaysheetResponse;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\PaysheetDetailRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\Request\DeletePaysheetDetailRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\Request\IndexPaysheetDetailRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\UseCase\DeletePaysheetDetailUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\UseCase\IndexPaysheetDetailUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\PaymentRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Request\IndexPaymentRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\UseCase\IndexPaymentUseCase;
use Exception;

final class DeletePaysheetUseCase
{
    private PaysheetRepository $orderRepository;

    private PaysheetDetailRepository $orderDetailRepository;

    private PaymentRepository $paymentRepository;

    public function __construct(
        PaysheetRepository $orderRepository,
        PaysheetDetailRepository $orderDetailRepository,
        PaymentRepository $paymentRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function execute(DeletePaysheetRequest $request)
    {
        if (!empty($request->id)) {
            $order = $this->orderRepository->getById(new ReadPaysheetRequest($request->id));
        }

        if (empty($order) || !$order->id()) {
            throw new Exception(\sprintf('Paysheet not exist [%d]', $request->id ?? 0), 404);
        }

        if ($order->isPayed()) {
            throw new Exception('Paysheet is payed', 404);
        }

        $orderDetails = $this->getPaysheetDetails($order);

        if (\count($orderDetails) > 0 && !$order->isDraft()) {
            throw new Exception('Paysheet has details, remove it first', 404);
        }

        $payments = $this->getPayments($order);

        if (\count($payments) > 0) {
            throw new Exception('Paysheet has payments, remove it first', 404);
        }

        if (\count($orderDetails) > 0 && $order->isDraft()) {
            $this->deleteDrafPaysheetDetails($order, $orderDetails);
        }

        return new DeletePaysheetResponse($this->orderRepository->remove($request));
    }

    private function getPaysheetDetails(Paysheet $order): array
    {
        $indexPaysheetDetail = new IndexPaysheetDetailUseCase($this->orderDetailRepository);

        return $indexPaysheetDetail->execute(
            new IndexPaysheetDetailRequest(['orderId' => $order->id()], 1)
        )->orderDetails;
    }

    private function getPayments(Paysheet $order): array
    {
        $indexPayment = new IndexPaymentUseCase($this->paymentRepository);

        return $indexPayment->execute(
            new IndexPaymentRequest(['orderId' => $order->id()], 1)
        )->payments;
    }

    private function deleteDrafPaysheetDetails(Paysheet $order, array $orderDetails): void
    {
        $useCase = new DeletePaysheetDetailUseCase($this->orderDetailRepository);

        foreach ($orderDetails as $orderDetail) {
            $useCase->execute(new DeletePaysheetDetailRequest($orderDetail->id()));
        }
    }
}
