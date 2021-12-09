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

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\AgreementPeriod;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\AgreementStatus;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\AgreementType;
use FlexPHP\Bundle\LocationBundle\Domain\Currency\Currency;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Employee;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\ToArrayTrait;
use FlexPHP\Bundle\UserBundle\Domain\User\User;

final class Agreement
{
    use ToArrayTrait;

    private $id;

    private $name;

    private $employee;

    private $type;

    private $period;

    private $currency;

    private $salary;

    private $healthPercentage = 4;

    private $pensionPercentage = 4;

    private $integralSalary;

    private $highRisk;

    private $initAt;

    private $finishAt;

    private $status;

    private $createdAt;

    private $updatedAt;

    private $createdBy;

    private $updatedBy;

    private $employeeInstance;

    private $typeInstance;

    private $periodInstance;

    private $currencyInstance;

    private $statusInstance;

    private $createdByInstance;

    private $updatedByInstance;

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function employee(): ?int
    {
        return $this->employee;
    }

    public function type(): ?int
    {
        return $this->type;
    }

    public function period(): ?string
    {
        return $this->period;
    }

    public function currency(): ?string
    {
        return $this->currency;
    }

    public function salary(): ?string
    {
        return $this->salary;
    }

    public function healthPercentage(): ?int
    {
        return $this->healthPercentage;
    }

    public function pensionPercentage(): ?int
    {
        return $this->pensionPercentage;
    }

    public function integralSalary(): ?bool
    {
        return $this->integralSalary;
    }

    public function highRisk(): ?bool
    {
        return $this->highRisk;
    }

    public function initAt(): ?\DateTime
    {
        return $this->initAt;
    }

    public function finishAt(): ?\DateTime
    {
        return $this->finishAt;
    }

    public function status(): ?string
    {
        return $this->status;
    }

    public function createdAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function createdBy(): ?int
    {
        return $this->createdBy;
    }

    public function updatedBy(): ?int
    {
        return $this->updatedBy;
    }

    public function employeeInstance(): Employee
    {
        return $this->employeeInstance ?: new Employee;
    }

    public function typeInstance(): AgreementType
    {
        return $this->typeInstance ?: new AgreementType;
    }

    public function periodInstance(): AgreementPeriod
    {
        return $this->periodInstance ?: new AgreementPeriod;
    }

    public function currencyInstance(): Currency
    {
        return $this->currencyInstance ?: new Currency;
    }

    public function statusInstance(): AgreementStatus
    {
        return $this->statusInstance ?: new AgreementStatus;
    }

    public function createdByInstance(): ?User
    {
        return $this->createdByInstance;
    }

    public function updatedByInstance(): ?User
    {
        return $this->updatedByInstance;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmployee(int $employee): void
    {
        $this->employee = $employee;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function setSalary(string $salary): void
    {
        $this->salary = $salary;
    }

    public function setHealthPercentage(int $healthPercentage): void
    {
        $this->healthPercentage = $healthPercentage;
    }

    public function setPensionPercentage(int $pensionPercentage): void
    {
        $this->pensionPercentage = $pensionPercentage;
    }

    public function setIntegralSalary(?bool $integralSalary): void
    {
        $this->integralSalary = $integralSalary;
    }

    public function setHighRisk(?bool $highRisk): void
    {
        $this->highRisk = $highRisk;
    }

    public function setInitAt(\DateTime $initAt): void
    {
        $this->initAt = $initAt;
    }

    public function setFinishAt(?\DateTime $finishAt): void
    {
        $this->finishAt = $finishAt;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setCreatedBy(?int $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function setUpdatedBy(?int $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

    public function setEmployeeInstance(Employee $employee): void
    {
        $this->employeeInstance = $employee;
    }

    public function setTypeInstance(AgreementType $agreementType): void
    {
        $this->typeInstance = $agreementType;
    }

    public function setPeriodInstance(AgreementPeriod $agreementPeriod): void
    {
        $this->periodInstance = $agreementPeriod;
    }

    public function setCurrencyInstance(Currency $currency): void
    {
        $this->currencyInstance = $currency;
    }

    public function setStatusInstance(AgreementStatus $agreementStatus): void
    {
        $this->statusInstance = $agreementStatus;
    }

    public function setCreatedByInstance(?User $user): void
    {
        $this->createdByInstance = $user;
    }

    public function setUpdatedByInstance(?User $user): void
    {
        $this->updatedByInstance = $user;
    }
}
