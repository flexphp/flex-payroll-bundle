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

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreatePaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\CreatePaysheetResponse;

final class CreatePaysheetUseCase extends AbstractPaysheetUseCase
{
    /**
     * @param CreatePaysheetRequest $request
     *
     * @return CreatePaysheetResponse
     */
    public function execute($request)
    {
        if (!empty($request->employee)) {
            $request->employeeId = $this->getEmployeeId($request);
            $request->agreement['employee'] = $request->employeeId;
        }

        if (!empty($request->agreement)) {
            $request->agreementId = $this->getAgreementId($request);
        }

        $paysheetDetails = $this->getPaysheetDetails($request);
        $request->subtotal = $this->getSubTotal($paysheetDetails);
        $request->taxes = $this->getTotalTaxes($paysheetDetails);
        $request->total = $this->getTotal($request);

        $payments = $this->getPayments($request);
        $request->totalPaid = $this->getTotalPaid($payments);
        $request->paidAt = $this->getPaidAt($request);
        $request->statusId = $this->getStatusId($request);

        $request->employee = null;
        $request->agreement = null;
        $request->paysheetDetail = null;
        $request->payment = null;

        $paysheet = $this->paysheetRepository->add($request);

        $this->createPaysheetDetails($paysheetDetails, $paysheet->id(), $request->createdBy);
        $this->createPayments($payments, $paysheet->id(), $request->createdBy);

        return new CreatePaysheetResponse($paysheet);
    }
}
