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

use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\AccountTypeFactory;
use FlexPHP\Bundle\LocationBundle\Domain\DocumentType\DocumentTypeFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\EmployeeSubTypeFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\EmployeeTypeFactory;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\LocationBundle\Domain\PaymentMethod\PaymentMethodFactory;
use FlexPHP\Bundle\UserBundle\Domain\User\UserFactory;

final class EmployeeFactory
{
    use FactoryExtendedTrait;

    public function make($data): Employee
    {
        $employee = new Employee();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $employee->setId((int)$data['id']);
        }

        if (isset($data['documentTypeId'])) {
            $employee->setDocumentTypeId((string)$data['documentTypeId']);
        }

        if (isset($data['documentNumber'])) {
            $employee->setDocumentNumber((string)$data['documentNumber']);
        }

        if (isset($data['firstName'])) {
            $employee->setFirstName((string)$data['firstName']);
        }

        if (isset($data['secondName'])) {
            $employee->setSecondName((string)$data['secondName']);
        }

        if (isset($data['firstSurname'])) {
            $employee->setFirstSurname((string)$data['firstSurname']);
        }

        if (isset($data['secondSurname'])) {
            $employee->setSecondSurname((string)$data['secondSurname']);
        }

        if (isset($data['type'])) {
            $employee->setType((int)$data['type']);
        }

        if (isset($data['subType'])) {
            $employee->setSubType((int)$data['subType']);
        }

        if (isset($data['paymentMethod'])) {
            $employee->setPaymentMethod((string)$data['paymentMethod']);
        }

        if (isset($data['accountType'])) {
            $employee->setAccountType((string)$data['accountType']);
        }

        if (isset($data['accountNumber'])) {
            $employee->setAccountNumber((string)$data['accountNumber']);
        }

        if (isset($data['isActive'])) {
            $employee->setIsActive((bool)$data['isActive']);
        }

        if (isset($data['createdAt'])) {
            $employee->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $employee->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $employee->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $employee->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['documentTypeId.id'])) {
            $employee->setDocumentTypeIdInstance((new DocumentTypeFactory())->make($this->getFkEntity('documentTypeId.', $data)));
        }

        if (isset($data['type.id'])) {
            $employee->setTypeInstance((new EmployeeTypeFactory())->make($this->getFkEntity('type.', $data)));
        }

        if (isset($data['subType.id'])) {
            $employee->setSubTypeInstance((new EmployeeSubTypeFactory())->make($this->getFkEntity('subType.', $data)));
        }

        if (isset($data['paymentMethod.id'])) {
            $employee->setPaymentMethodInstance((new PaymentMethodFactory())->make($this->getFkEntity('paymentMethod.', $data)));
        }

        if (isset($data['accountType.id'])) {
            $employee->setAccountTypeInstance((new AccountTypeFactory())->make($this->getFkEntity('accountType.', $data)));
        }

        if (isset($data['createdBy.id'])) {
            $employee->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $employee->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $employee;
    }
}
