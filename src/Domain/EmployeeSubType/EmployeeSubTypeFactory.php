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

use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\UserBundle\Domain\User\UserFactory;

final class EmployeeSubTypeFactory
{
    use FactoryExtendedTrait;

    public function make($data): EmployeeSubType
    {
        $employeeSubType = new EmployeeSubType();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $employeeSubType->setId((int)$data['id']);
        }

        if (isset($data['name'])) {
            $employeeSubType->setName((string)$data['name']);
        }

        if (isset($data['code'])) {
            $employeeSubType->setCode((string)$data['code']);
        }

        if (isset($data['isActive'])) {
            $employeeSubType->setIsActive((bool)$data['isActive']);
        }

        if (isset($data['createdAt'])) {
            $employeeSubType->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $employeeSubType->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $employeeSubType->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $employeeSubType->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['createdBy.id'])) {
            $employeeSubType->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $employeeSubType->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $employeeSubType;
    }
}
