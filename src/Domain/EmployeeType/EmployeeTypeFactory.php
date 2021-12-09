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

use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\UserBundle\Domain\User\UserFactory;

final class EmployeeTypeFactory
{
    use FactoryExtendedTrait;

    public function make($data): EmployeeType
    {
        $employeeType = new EmployeeType();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $employeeType->setId((int)$data['id']);
        }

        if (isset($data['name'])) {
            $employeeType->setName((string)$data['name']);
        }

        if (isset($data['code'])) {
            $employeeType->setCode((string)$data['code']);
        }

        if (isset($data['isActive'])) {
            $employeeType->setIsActive((bool)$data['isActive']);
        }

        if (isset($data['createdAt'])) {
            $employeeType->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $employeeType->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $employeeType->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $employeeType->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['createdBy.id'])) {
            $employeeType->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $employeeType->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $employeeType;
    }
}
