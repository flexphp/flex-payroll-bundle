<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollType;

use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\CreatePayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\DeletePayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\IndexPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\ReadPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\UpdatePayrollTypeRequest;

final class PayrollTypeRepository
{
    private PayrollTypeGateway $gateway;

    public function __construct(PayrollTypeGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<PayrollType>
     */
    public function findBy(IndexPayrollTypeRequest $request): array
    {
        return \array_map(function (array $payrollType) {
            return (new PayrollTypeFactory())->make($payrollType);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreatePayrollTypeRequest $request): PayrollType
    {
        $payrollType = (new PayrollTypeFactory())->make($request);

        $payrollType->setId($this->gateway->push($payrollType));

        return $payrollType;
    }

    public function getById(ReadPayrollTypeRequest $request): PayrollType
    {
        $factory = new PayrollTypeFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdatePayrollTypeRequest $request): PayrollType
    {
        $payrollType = (new PayrollTypeFactory())->make($request);

        $this->gateway->shift($payrollType);

        return $payrollType;
    }

    public function remove(DeletePayrollTypeRequest $request): PayrollType
    {
        $factory = new PayrollTypeFactory();
        $data = $this->gateway->get($factory->make($request));

        $payrollType = $factory->make($data);

        $this->gateway->pop($payrollType);

        return $payrollType;
    }
}
