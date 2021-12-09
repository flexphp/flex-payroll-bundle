<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll;

use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\CreatePayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\DeletePayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollProviderRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\IndexPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\ReadPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\UpdatePayrollRequest;

final class PayrollRepository
{
    private PayrollGateway $gateway;

    public function __construct(PayrollGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<Payroll>
     */
    public function findBy(IndexPayrollRequest $request): array
    {
        return \array_map(function (array $payroll) {
            return (new PayrollFactory())->make($payroll);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreatePayrollRequest $request): Payroll
    {
        $payroll = (new PayrollFactory())->make($request);

        $payroll->setId($this->gateway->push($payroll));

        return $payroll;
    }

    public function getById(ReadPayrollRequest $request): Payroll
    {
        $factory = new PayrollFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdatePayrollRequest $request): Payroll
    {
        $payroll = (new PayrollFactory())->make($request);

        $this->gateway->shift($payroll);

        return $payroll;
    }

    public function remove(DeletePayrollRequest $request): Payroll
    {
        $factory = new PayrollFactory();
        $data = $this->gateway->get($factory->make($request));

        $payroll = $factory->make($data);

        $this->gateway->pop($payroll);

        return $payroll;
    }

    public function findEmployeesBy(FindPayrollEmployeeRequest $request): array
    {
        return $this->gateway->filterEmployees($request, $request->_page, $request->_limit);
    }

    public function findProvidersBy(FindPayrollProviderRequest $request): array
    {
        return $this->gateway->filterProviders($request, $request->_page, $request->_limit);
    }

    public function findPayrollStatusBy(FindPayrollPayrollStatusRequest $request): array
    {
        return $this->gateway->filterPayrollStatus($request, $request->_page, $request->_limit);
    }

    public function findPayrollTypesBy(FindPayrollPayrollTypeRequest $request): array
    {
        return $this->gateway->filterPayrollTypes($request, $request->_page, $request->_limit);
    }

    public function findPayrollsBy(FindPayrollPayrollRequest $request): array
    {
        return $this->gateway->filterPayrolls($request, $request->_page, $request->_limit);
    }
}
