<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement;

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\AgreementPeriodFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\AgreementStatusFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\AgreementTypeFactory;
use FlexPHP\Bundle\LocationBundle\Domain\Currency\CurrencyFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\EmployeeFactory;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\UserBundle\Domain\User\UserFactory;

final class AgreementFactory
{
    use FactoryExtendedTrait;

    public function make($data): Agreement
    {
        $agreement = new Agreement();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $agreement->setId((int)$data['id']);
        }

        if (isset($data['name'])) {
            $agreement->setName((string)$data['name']);
        }

        if (isset($data['employee'])) {
            $agreement->setEmployee((int)$data['employee']);
        }

        if (isset($data['type'])) {
            $agreement->setType((int)$data['type']);
        }

        if (isset($data['period'])) {
            $agreement->setPeriod((string)$data['period']);
        }

        if (isset($data['currency'])) {
            $agreement->setCurrency((string)$data['currency']);
        }

        if (isset($data['salary'])) {
            $agreement->setSalary((string)$data['salary']);
        }

        if (isset($data['healthPercentage'])) {
            $agreement->setHealthPercentage((int)$data['healthPercentage']);
        }

        if (isset($data['pensionPercentage'])) {
            $agreement->setPensionPercentage((int)$data['pensionPercentage']);
        }

        if (isset($data['integralSalary'])) {
            $agreement->setIntegralSalary((bool)$data['integralSalary']);
        }

        if (isset($data['highRisk'])) {
            $agreement->setHighRisk((bool)$data['highRisk']);
        }

        if (isset($data['initAt'])) {
            $agreement->setInitAt(\is_string($data['initAt']) ? new \DateTime($data['initAt']) : $data['initAt']);
        }

        if (isset($data['finishAt'])) {
            $agreement->setFinishAt(\is_string($data['finishAt']) ? new \DateTime($data['finishAt']) : $data['finishAt']);
        }

        if (isset($data['status'])) {
            $agreement->setStatus((string)$data['status']);
        }

        if (isset($data['createdAt'])) {
            $agreement->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $agreement->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $agreement->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $agreement->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['employee.id'])) {
            $agreement->setEmployeeInstance((new EmployeeFactory())->make($this->getFkEntity('employee.', $data)));
        }

        if (isset($data['type.id'])) {
            $agreement->setTypeInstance((new AgreementTypeFactory())->make($this->getFkEntity('type.', $data)));
        }

        if (isset($data['period.id'])) {
            $agreement->setPeriodInstance((new AgreementPeriodFactory())->make($this->getFkEntity('period.', $data)));
        }

        if (isset($data['currency.id'])) {
            $agreement->setCurrencyInstance((new CurrencyFactory())->make($this->getFkEntity('currency.', $data)));
        }

        if (isset($data['status.id'])) {
            $agreement->setStatusInstance((new AgreementStatusFactory())->make($this->getFkEntity('status.', $data)));
        }

        if (isset($data['createdBy.id'])) {
            $agreement->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $agreement->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $agreement;
    }
}
