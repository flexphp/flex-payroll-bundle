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
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\ReadEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\ReadEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Employee;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\EmployeeRepository;

final class GetPaysheetEmployeeUseCase
{
    private PaysheetRepository $paysheetRepository;

    private EmployeeRepository $employeeRepository;

    public function __construct(PaysheetRepository $paysheetRepository, EmployeeRepository $employeeRepository)
    {
        $this->paysheetRepository = $paysheetRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function execute(ReadPaysheetRequest $request): Employee
    {
        $useCasePaysheet = new ReadPaysheetUseCase($this->paysheetRepository);

        $responsePaysheet = $useCasePaysheet->execute(new ReadPaysheetRequest($request->id));

        $paysheet = $responsePaysheet->paysheet;

        $employee = new Employee();

        if ($paysheet->employeeId()) {
            $requestEmployee = new ReadEmployeeRequest($paysheet->employeeId());

            $useCaseEmployee = new ReadEmployeeUseCase($this->employeeRepository);

            $responseEmployee = $useCaseEmployee->execute($requestEmployee);

            $employee = $responseEmployee->employee;
        }

        return $employee;
    }
}
