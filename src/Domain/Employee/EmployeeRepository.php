<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee;

use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\CreateEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\DeleteEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeDocumentTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeePaymentMethodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\IndexEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\ReadEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\UpdateEmployeeRequest;

final class EmployeeRepository
{
    private EmployeeGateway $gateway;

    public function __construct(EmployeeGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<Employee>
     */
    public function findBy(IndexEmployeeRequest $request): array
    {
        return \array_map(function (array $employee) {
            return (new EmployeeFactory())->make($employee);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreateEmployeeRequest $request): Employee
    {
        $employee = (new EmployeeFactory())->make($request);

        $employee->setId($this->gateway->push($employee));

        return $employee;
    }

    public function getById(ReadEmployeeRequest $request): Employee
    {
        $factory = new EmployeeFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdateEmployeeRequest $request): Employee
    {
        $employee = (new EmployeeFactory())->make($request);

        $this->gateway->shift($employee);

        return $employee;
    }

    public function remove(DeleteEmployeeRequest $request): Employee
    {
        $factory = new EmployeeFactory();
        $data = $this->gateway->get($factory->make($request));

        $employee = $factory->make($data);

        $this->gateway->pop($employee);

        return $employee;
    }

    public function findDocumentTypesBy(FindEmployeeDocumentTypeRequest $request): array
    {
        return $this->gateway->filterDocumentTypes($request, $request->_page, $request->_limit);
    }

    public function findEmployeeTypesBy(FindEmployeeEmployeeTypeRequest $request): array
    {
        return $this->gateway->filterEmployeeTypes($request, $request->_page, $request->_limit);
    }

    public function findEmployeeSubTypesBy(FindEmployeeEmployeeSubTypeRequest $request): array
    {
        return $this->gateway->filterEmployeeSubTypes($request, $request->_page, $request->_limit);
    }

    public function findPaymentMethodsBy(FindEmployeePaymentMethodRequest $request): array
    {
        return $this->gateway->filterPaymentMethods($request, $request->_page, $request->_limit);
    }

    public function findAccountTypesBy(FindEmployeeAccountTypeRequest $request): array
    {
        return $this->gateway->filterAccountTypes($request, $request->_page, $request->_limit);
    }
}
