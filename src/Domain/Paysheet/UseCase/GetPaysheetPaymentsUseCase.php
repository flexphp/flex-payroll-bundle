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

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Payment;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\PaymentRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Request\IndexPaymentRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\UseCase\IndexPaymentUseCase;

final class GetPaysheetPaymentsUseCase
{
    private PaysheetRepository $orderRepository;

    private PaymentRepository $paymentRepository;

    public function __construct(PaysheetRepository $orderRepository, PaymentRepository $paymentRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @return array<Payment>
     */
    public function execute(ReadPaysheetRequest $request): array
    {
        $useCasePaysheet = new ReadPaysheetUseCase($this->orderRepository);

        $responsePaysheet = $useCasePaysheet->execute(new ReadPaysheetRequest($request->id));

        $order = $responsePaysheet->order;

        $payments = [];

        if ($order->id()) {
            $requestPayment = new IndexPaymentRequest([
                'orderId' => $order->id(),
            ], 1);

            $useCasePayment = new IndexPaymentUseCase($this->paymentRepository);

            $responsePayment = $useCasePayment->execute($requestPayment);

            $payments = $responsePayment->payments;
        }

        return $payments;
    }
}
