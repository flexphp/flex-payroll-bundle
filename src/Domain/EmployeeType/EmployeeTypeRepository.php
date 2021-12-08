<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType;

use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\CreateEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\DeleteEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\IndexEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\ReadEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\UpdateEmployeeTypeRequest;

final class EmployeeTypeRepository
{
    private EmployeeTypeGateway $gateway;

    public function __construct(EmployeeTypeGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<EmployeeType>
     */
    public function findBy(IndexEmployeeTypeRequest $request): array
    {
        return \array_map(function (array $employeeType) {
            return (new EmployeeTypeFactory())->make($employeeType);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreateEmployeeTypeRequest $request): EmployeeType
    {
        $employeeType = (new EmployeeTypeFactory())->make($request);

        $employeeType->setId($this->gateway->push($employeeType));

        return $employeeType;
    }

    public function getById(ReadEmployeeTypeRequest $request): EmployeeType
    {
        $factory = new EmployeeTypeFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdateEmployeeTypeRequest $request): EmployeeType
    {
        $employeeType = (new EmployeeTypeFactory())->make($request);

        $this->gateway->shift($employeeType);

        return $employeeType;
    }

    public function remove(DeleteEmployeeTypeRequest $request): EmployeeType
    {
        $factory = new EmployeeTypeFactory();
        $data = $this->gateway->get($factory->make($request));

        $employeeType = $factory->make($data);

        $this->gateway->pop($employeeType);

        return $employeeType;
    }
}
