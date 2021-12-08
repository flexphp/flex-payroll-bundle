<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType;

use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\CreateEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\DeleteEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\IndexEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\ReadEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\UpdateEmployeeSubTypeRequest;

final class EmployeeSubTypeRepository
{
    private EmployeeSubTypeGateway $gateway;

    public function __construct(EmployeeSubTypeGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<EmployeeSubType>
     */
    public function findBy(IndexEmployeeSubTypeRequest $request): array
    {
        return \array_map(function (array $employeeSubType) {
            return (new EmployeeSubTypeFactory())->make($employeeSubType);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreateEmployeeSubTypeRequest $request): EmployeeSubType
    {
        $employeeSubType = (new EmployeeSubTypeFactory())->make($request);

        $employeeSubType->setId($this->gateway->push($employeeSubType));

        return $employeeSubType;
    }

    public function getById(ReadEmployeeSubTypeRequest $request): EmployeeSubType
    {
        $factory = new EmployeeSubTypeFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdateEmployeeSubTypeRequest $request): EmployeeSubType
    {
        $employeeSubType = (new EmployeeSubTypeFactory())->make($request);

        $this->gateway->shift($employeeSubType);

        return $employeeSubType;
    }

    public function remove(DeleteEmployeeSubTypeRequest $request): EmployeeSubType
    {
        $factory = new EmployeeSubTypeFactory();
        $data = $this->gateway->get($factory->make($request));

        $employeeSubType = $factory->make($data);

        $this->gateway->pop($employeeSubType);

        return $employeeSubType;
    }
}
