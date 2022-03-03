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
use DateTimeInterface;
use DateTimeZone;
use Exception;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\Request\UpdateNumerationRequest;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\UseCase\UpdateNumerationUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\ReadAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\ReadAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\ReadEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\ReadEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\CreatePayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\CreatePayrollUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatus;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreateEPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\CreateEPayrollResponse;
use FlexPHP\ePayroll\Struct\PaymentDate;

final class CreateEPayrollUseCase extends AbstractEPayrollUseCase
{
    public function execute(CreateEPayrollRequest $request): CreateEPayrollResponse
    {
        if (!empty($request->paysheetId)) {
            $paysheet = $this->paysheetRepository->getById(new ReadPaysheetRequest($request->paysheetId));
        }

        if (empty($paysheet) || !$paysheet->id()) {
            throw new Exception(\sprintf('Paysheet not exist [%d]', $request->paysheetId ?? 0), 404);
        }

        if (!$paysheet->employeeId()) {
            throw new Exception('Paysheet employee not found', 404);
        }

        if (!$paysheet->agreementId()) {
            throw new Exception('Paysheet agreement not found', 404);
        }

        if ($paysheet->isDraft()) {
            throw new Exception('Paysheet is draft', 400);
        }

        $timezone = new DateTimeZone('America/Bogota');

        $paysheet->withLastPayroll($this->paysheetRepository->payrollGateway(), 0);
        $payroll = $paysheet->payrollInstance();

        $provider = $this->getProvider('NOM');

        $this->testingMode = $provider->url() === 'http://localhost';

        if ($payroll && $payroll->downloadedAt()) {
            return $this->getResponseOk($payroll);
        }

        //         $paysheetTimeout = clone $paysheet->createdAt();
        //         $timeAllowed = new DateInterval(\sprintf('P%dD', $_ENV['EINVOICE_TIMEOUT'] ?? 10));

        //         if ($paysheetTimeout->add($timeAllowed) < new DateTime()) {
        //             throw new Exception('Paysheet is older', 400);
        //         }

        $this->validateEnvs();

        $employee = (new ReadEmployeeUseCase($this->employeeRepository))->execute(
            new ReadEmployeeRequest($paysheet->employeeId())
        )->employee;

        $agreement = (new ReadAgreementUseCase($this->agreementRepository))->execute(
            new ReadAgreementRequest($paysheet->agreementId())
        )->agreement;

        $clerk = $this->getClerk($employee, $agreement);

        $general = $this->getGeneral(
            $paysheet->createdAt()->setTimezone($timezone)->format('Y-m-d H:i:s'),
            $this->getRecurrenceCode($agreement->period()),
            $agreement->currency(),
            1.0
        );

        $payment = $this->getPayment(
            '1',
            $employee->paymentMethod(),
            $employee->bankInstance()->name() ?: '',
            $employee->accountType() ?: '',
            $employee->accountNumber() ?: ''
        );

        $basic = $paysheet->detailsPresenter()->basic();

        $period = $this->getPeriod(
            $paysheet->issuedAt()->setTimezone($timezone)->format('Y-m-d H:i:s'),
            $agreement->initAt()->setTimezone($timezone)->format('Y-m-d H:i:s'),
            $paysheet->initAt()->setTimezone($timezone)->format('Y-m-d H:i:s'),
            $paysheet->finishAt()->setTimezone($timezone)->format('Y-m-d H:i:s'),
            $agreement->finishAt() ? $agreement->finishAt()->setTimezone($timezone)->format('Y-m-d H:i:s') : null
        );

        $entity = $this->getEntity(
            $_ENV['ORGANIZATION_DOCUMENT'],
            $_ENV['ORGANIZATION_DOCUMENT_TYPE'],
            $_ENV['ORGANIZATION_BRAND_NAME'],
            $_ENV['ORGANIZATION_LEGAL_NAME']
        );

        $location = $this->getLocation(
            $_ENV['ORGANIZATION_COUNTRY'],
            $_ENV['ORGANIZATION_STATE'],
            $_ENV['ORGANIZATION_CITY'],
            'es',
            $_ENV['ORGANIZATION_ADDRESS']
        );

        $employer = $this->getEmployer($entity, $location);

        // TODO: Add PaysheetType in SDK
        $setting = $this->getSetting('NI');

        $numeration = $this->getNumeration(
            $setting->prefix(),
            $setting->currentNumber(),
            (string)$employee->id()
        );

        $transport = $paysheet->detailsPresenter()->transport();
        $vacation = $paysheet->detailsPresenter()->vacation();
        $bonus = $paysheet->detailsPresenter()->bonus();
        $cessation = $paysheet->detailsPresenter()->cessation();
        $support = $paysheet->detailsPresenter()->supports();
        $endowment = $paysheet->detailsPresenter()->endowment();

        $health = $paysheet->detailsPresenter()->health();
        $pension = $paysheet->detailsPresenter()->pension();

        $accrued = $this->getAccrued(
            $this->getBasic($basic->days(), $basic->amount()),
            $this->getTransport($transport->amount(), $transport->viaticSalary(), $transport->viaticNoSalary()),
        );

        $accrued->setPayment($payment);
        $accrued->addPaymentDates(array_map(function (DateTimeInterface $paidAt) {
            return new PaymentDate($paidAt);
        }, $basic->paidAts()));

        if ($vacation->days()) {
            $accrued->setVacation($this->getVacation($vacation->initAt(), $vacation->finishAt(), $vacation->days(), $vacation->amount(), $vacation->compensateDays(), $vacation->compensateAmount()));
        }

        if ($bonus->days()) {
            $accrued->setBonus($this->getBonus($bonus->days(), $bonus->amount(), $bonus->noSalary()));
        }

        if ($cessation->amount() || $cessation->noSalary()) {
            $accrued->setCessation($this->getCessation($cessation->percentage(), $cessation->amount(), $cessation->noSalary()));
        }

        if ($support->count()) {
            $accrued->addSupports($this->getSupports($support));
        }

        if ($endowment->amount()) {
            $accrued->setEndowment($this->getEndowment($endowment->amount()));
        }

        $roll = [
            'general' => $general,
            'employer' => $employer,
            'employee' => $clerk,
            'period' => $period,
            'numeration' => $numeration,
            'accrued' => $accrued,
            'deduction' => $this->getDeduction(
                $this->getHealth($health->percentage(), $health->amount()),
                $this->getPension($pension->percentage(), $pension->amount()),
            ),
        ];

        if (!$payroll) {
            // $data = $this->paysheetRepository->getEPayrollData($request);

            // if (empty($data)) {
            //     throw new Exception(\sprintf('Paysheet data not found [%d]', $request->id), 404);
            // }

            $useCase = new CreatePayrollUseCase($this->payrollRepository);

            $payroll = $useCase->execute(new CreatePayrollRequest([
                'prefix' => $setting->prefix(),
                'number' => $setting->currentNumber(),
                'paysheet' => $paysheet->id(),
                'provider' => $provider->id(),
                'status' => PayrollStatus::PENDING,
                'type' => PayrollType::NOVEL,
                'message' => 'Pendiente de procesar',
            ], -1))->payroll;

            if (!$payroll->id()) {
                return new CreateEPayrollResponse($payroll->status(), 'Payroll error');
            }

            (new UpdateNumerationUseCase($this->numerationRepository))->execute(
                new UpdateNumerationRequest($setting->id(), [
                    'currentNumber' => $setting->currentNumber() + 1,
                ], -1, true)
            );
        }

        return $this->processEPayroll($roll, $payroll, $provider);
    }
}
