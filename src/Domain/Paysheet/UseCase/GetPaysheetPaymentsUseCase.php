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
    private PaysheetRepository $paysheetRepository;

    private PaymentRepository $paymentRepository;

    public function __construct(PaysheetRepository $paysheetRepository, PaymentRepository $paymentRepository)
    {
        $this->paysheetRepository = $paysheetRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @return array<Payment>
     */
    public function execute(ReadPaysheetRequest $request): array
    {
        $useCasePaysheet = new ReadPaysheetUseCase($this->paysheetRepository);

        $responsePaysheet = $useCasePaysheet->execute(new ReadPaysheetRequest($request->id));

        $paysheet = $responsePaysheet->paysheet;

        $payments = [];

        if ($paysheet->id()) {
            $requestPayment = new IndexPaymentRequest([
                'paysheetId' => $paysheet->id(),
            ], 1);

            $useCasePayment = new IndexPaymentUseCase($this->paymentRepository);

            $responsePayment = $useCasePayment->execute($requestPayment);

            $payments = $responsePayment->payments;
        }

        return $payments;
    }
}
