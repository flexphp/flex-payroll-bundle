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

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\UpdatePaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\UpdatePaysheetResponse;
use Exception;

final class UpdatePaysheetUseCase extends AbstractPaysheetUseCase
{
    /**
     * @param UpdatePaysheetRequest $request
     *
     * @return UpdatePaysheetResponse
     */
    public function execute($request)
    {
        if (!empty($request->id)) {
            $paysheet = $this->paysheetRepository->getById(new ReadPaysheetRequest($request->id));
        }

        if (empty($paysheet) || !$paysheet->id()) {
            throw new Exception(\sprintf('Paysheet not exist [%d]', $request->id ?? 0), 404);
        }

        if ($request->isDraft && !$paysheet->isDraft()) {
            throw new Exception('Paysheet with payments cannot change to draft');
        }

        if (!empty($request->agreement)) {
            $request->agreementId = $this->getAgreementId($request);
        }

        if (!empty($request->employee)) {
            $request->employeeId = $this->getEmployeeId($request);
        }

        $paysheetDetails = $this->getPaysheetDetails($request);
        $request->subtotal = $this->getSubTotal($paysheetDetails);
        $request->taxes = $this->getTotalTaxes($paysheetDetails);
        $request->total = $this->getTotal($request);

        $payments = $this->getPayments($request);
        $request->totalPaid = $this->getTotalPaid($payments);
        $request->paidAt = $this->getPaidAt($request);
        $request->statusId = $this->getStatusId($request);

        $request->agreement = null;
        $request->employee = null;
        $request->paysheetDetail = null;
        $request->payment = null;

        $paysheet = $this->paysheetRepository->change($request);

        $this->savePaysheetDetails($paysheetDetails, $paysheet->id(), $request->updatedBy);
        $this->savePayments($payments, $paysheet->id(), $request->updatedBy);

        return new UpdatePaysheetResponse($paysheet);
    }
}
