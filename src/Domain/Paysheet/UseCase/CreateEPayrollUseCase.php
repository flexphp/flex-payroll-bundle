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
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\CreatePayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\CreatePayrollUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatus;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\Request\UpdateNumerationRequest;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\UseCase\UpdateNumerationUseCase;

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

        if (!$paysheet->customerId()) {
            throw new Exception('Paysheet customer not found', 404);
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

        $paysheetTimeout = clone $paysheet->createdAt();
        $timeAllowed = new DateInterval(\sprintf('P%dD', $_ENV['EINVOICE_TIMEOUT'] ?? 10));

        if ($paysheetTimeout->add($timeAllowed) < new DateTime()) {
            throw new Exception('Paysheet is older', 400);
        }

        $sender = $this->getSender();
        $setting = $this->getSetting(PayrollType::INVOICE);
        $numeration = $this->getNumeration(
            $setting->resolution(),
            $setting->fromNumber(),
            $setting->currentNumber(),
            $setting->toNumber(),
            $setting->startAt(),
            $setting->finishAt(),
            $setting->prefix()
        );

        $receiver = $this->getReceiver($paysheet->customerId());
        $payroll = $this->getPayroll(
            $paysheet->createdAt(),
            $numeration,
            $this->getItems($paysheet),
            $this->getDeposits($paysheet),
            $paysheet->expiratedAt(),
            $paysheet->payrollNotes()
        );

        if (!$payroll) {
            $data = $this->paysheetRepository->getEPayrollData($request);

            if (empty($data)) {
                throw new Exception(\sprintf('Paysheet data not found [%d]', $request->id), 404);
            }

            $useCase = new CreatePayrollUseCase($this->payrollRepository);

            $payroll = $useCase->execute(new CreatePayrollRequest([
                'prefix' => $setting->prefix(),
                'number' => $setting->currentNumber(),
                'paysheet' => $paysheet->id(),
                'provider' => $provider->id(),
                'status' => PayrollStatus::PENDING,
                'type' => PayrollType::INVOICE,
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

        return $this->processEPayroll($payroll, $payroll, $provider, $sender, $receiver);
    }
}
