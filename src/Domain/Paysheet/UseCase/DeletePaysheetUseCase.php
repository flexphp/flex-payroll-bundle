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
    private PaysheetRepository $paysheetRepository;

    private PaysheetDetailRepository $paysheetDetailRepository;

    private PaymentRepository $paymentRepository;

    public function __construct(
        PaysheetRepository $paysheetRepository
        // PaysheetDetailRepository $paysheetDetailRepository,
        // PaymentRepository $paymentRepository
    ) {
        $this->paysheetRepository = $paysheetRepository;
        // $this->paysheetDetailRepository = $paysheetDetailRepository;
        // $this->paymentRepository = $paymentRepository;
    }

    public function execute(DeletePaysheetRequest $request)
    {
        if (!empty($request->id)) {
            $paysheet = $this->paysheetRepository->getById(new ReadPaysheetRequest($request->id));
        }

        if (empty($paysheet) || !$paysheet->id()) {
            throw new Exception(\sprintf('Paysheet not exist [%d]', $request->id ?? 0), 404);
        }

        if ($paysheet->isPayed()) {
            throw new Exception('Paysheet is payed', 404);
        }

//         $paysheetDetails = $this->getPaysheetDetails($paysheet);

//         if (\count($paysheetDetails) > 0 && !$paysheet->isDraft()) {
//             throw new Exception('Paysheet has details, remove it first', 404);
//         }

//         $payments = $this->getPayments($paysheet);

//         if (\count($payments) > 0) {
//             throw new Exception('Paysheet has payments, remove it first', 404);
//         }

//         if (\count($paysheetDetails) > 0 && $paysheet->isDraft()) {
//             $this->deleteDrafPaysheetDetails($paysheet, $paysheetDetails);
//         }

        return new DeletePaysheetResponse($this->paysheetRepository->remove($request));
    }

    private function getPaysheetDetails(Paysheet $paysheet): array
    {
        $indexPaysheetDetail = new IndexPaysheetDetailUseCase($this->paysheetDetailRepository);

        return $indexPaysheetDetail->execute(
            new IndexPaysheetDetailRequest(['paysheetId' => $paysheet->id()], 1)
        )->paysheetDetails;
    }

    private function getPayments(Paysheet $paysheet): array
    {
        $indexPayment = new IndexPaymentUseCase($this->paymentRepository);

        return $indexPayment->execute(
            new IndexPaymentRequest(['paysheetId' => $paysheet->id()], 1)
        )->payments;
    }

    private function deleteDrafPaysheetDetails(Paysheet $paysheet, array $paysheetDetails): void
    {
        $useCase = new DeletePaysheetDetailUseCase($this->paysheetDetailRepository);

        foreach ($paysheetDetails as $paysheetDetail) {
            $useCase->execute(new DeletePaysheetDetailRequest($paysheetDetail->id()));
        }
    }
}
