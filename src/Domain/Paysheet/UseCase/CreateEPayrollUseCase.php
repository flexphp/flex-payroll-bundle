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

        $paysheet->withLastPayroll($this->paysheetRepository->payrollGateway(), 0);
        $payroll = $paysheet->payrollInstance();

        $provider = $this->getProvider();

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

        $clerk = $this->getClerk($paysheet, $employee, $agreement);

        $general = $this->getGeneral(
            $paysheet->createdAt()->format('Y-m-d H:i:s'),
            $this->getRecurrenceCode($agreement->period()),
            $agreement->currency(),
            1.0
        );

        $period = $this->getPeriod(
            $paysheet->paidAt() ?? (new DateTime)->format('Y-m-d H:i:s'),
            $agreement->initAt()->format('Y-m-d H:i:s'),
            // TODO: Add period columns in Paysheet table
            // $paysheet->initAt(),
            // $paysheet->finishAt(),
            (new DateTime())->format('Y-m-d H:i:s'),
            (new DateTime())->format('Y-m-d H:i:s'),
            $agreement->finishAt()->format('Y-m-d H:i:s')
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

        $roll = [
            'general' => $general,
            'employer' => $employer,
            'employee' => $clerk,
            'period' => $period,
            'numeration' => $numeration,
            'accrued' => $this->getAccrued(
                $this->getBasic($days, $amount),
                $this->getTransport($subsidy, $viaticSalary, $viaticNoSalary),
                $this->getVacation($initAt, $finishAt, $days, $amount),
                $this->getBonus($days, $amount, $amountNoSalary),
                $this->getCessation($percentage, $amount, $amountInteres),
                $this->getSupport($amount, $amountNoSalary),
                $this->getEdowment($amount, $amountNoSalary),
            ),
            'deduction' => $this->getDeduction(
                $this->getHealth($percentage, $amount),
                $this->getPension($percentage, $amount)
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

        dump($request, $payroll, __FILE__ . ':' . __LINE__);
        return $this->processEPayroll($roll, $payroll, $provider);
    }
}
