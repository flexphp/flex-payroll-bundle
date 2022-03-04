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

use FlexPHP\Bundle\HelperBundle\Domain\Helper\ToArrayTrait;
use FlexPHP\Bundle\LocationBundle\Domain\DocumentType\DocumentType;
use FlexPHP\Bundle\LocationBundle\Domain\PaymentMethod\PaymentMethod;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\AccountType;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Bank;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\EmployeeSubType;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\EmployeeType;
use FlexPHP\Bundle\UserBundle\Domain\User\User;

final class Employee
{
    use ToArrayTrait;

    private $id;

    private $documentTypeId;

    private $documentNumber;

    private $firstName;

    private $secondName;

    private $firstSurname;

    private $secondSurname;

    private $type;

    private $subType;

    private $paymentMethod;

    private $accountType;

    private $accountNumber;

    private $isActive;

    private $bank;

    private $createdAt;

    private $updatedAt;

    private $createdBy;

    private $updatedBy;

    private $documentTypeIdInstance;

    private $typeInstance;

    private $subTypeInstance;

    private $paymentMethodInstance;

    private $accountTypeInstance;

    private $bankInstance;

    private $createdByInstance;

    private $updatedByInstance;

    private $name;

    public function id(): ?int
    {
        return $this->id;
    }

    public function documentTypeId(): ?string
    {
        return $this->documentTypeId;
    }

    public function documentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function firstName(): ?string
    {
        return $this->firstName;
    }

    public function secondName(): ?string
    {
        return $this->secondName;
    }

    public function firstSurname(): ?string
    {
        return $this->firstSurname;
    }

    public function secondSurname(): ?string
    {
        return $this->secondSurname;
    }

    public function type(): ?int
    {
        return $this->type;
    }

    public function subType(): ?int
    {
        return $this->subType;
    }

    public function paymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function accountType(): ?string
    {
        return $this->accountType;
    }

    public function accountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function bank(): ?int
    {
        return $this->bank;
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

    public function documentTypeIdInstance(): DocumentType
    {
        return $this->documentTypeIdInstance ?: new DocumentType;
    }

    public function typeInstance(): EmployeeType
    {
        return $this->typeInstance ?: new EmployeeType;
    }

    public function subTypeInstance(): EmployeeSubType
    {
        return $this->subTypeInstance ?: new EmployeeSubType;
    }

    public function paymentMethodInstance(): PaymentMethod
    {
        return $this->paymentMethodInstance ?: new PaymentMethod;
    }

    public function accountTypeInstance(): AccountType
    {
        return $this->accountTypeInstance ?: new AccountType;
    }

    public function bankInstance(): Bank
    {
        return $this->bankInstance ?: new Bank;
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

    public function setDocumentTypeId(string $documentTypeId): void
    {
        $this->documentTypeId = $documentTypeId;
    }

    public function setDocumentNumber(string $documentNumber): void
    {
        $this->documentNumber = $documentNumber;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setSecondName(?string $secondName): void
    {
        $this->secondName = $secondName;
    }

    public function setFirstSurname(string $firstSurname): void
    {
        $this->firstSurname = $firstSurname;
    }

    public function setSecondSurname(?string $secondSurname): void
    {
        $this->secondSurname = $secondSurname;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function setSubType(int $subType): void
    {
        $this->subType = $subType;
    }

    public function setPaymentMethod(string $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function setAccountType(string $accountType): void
    {
        $this->accountType = $accountType;
    }

    public function setAccountNumber(?string $accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }

    public function setIsActive(?bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setBank(int $bank): void
    {
        $this->bank = $bank;
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

    public function setDocumentTypeIdInstance(DocumentType $documentType): void
    {
        $this->documentTypeIdInstance = $documentType;
    }

    public function setTypeInstance(EmployeeType $employeeType): void
    {
        $this->typeInstance = $employeeType;
    }

    public function setSubTypeInstance(EmployeeSubType $employeeSubType): void
    {
        $this->subTypeInstance = $employeeSubType;
    }

    public function setPaymentMethodInstance(PaymentMethod $paymentMethod): void
    {
        $this->paymentMethodInstance = $paymentMethod;
    }

    public function setAccountTypeInstance(AccountType $accountType): void
    {
        $this->accountTypeInstance = $accountType;
    }

    public function setBankInstance(Bank $bank): void
    {
        $this->bankInstance = $bank;
    }

    public function setCreatedByInstance(?User $user): void
    {
        $this->createdByInstance = $user;
    }

    public function setUpdatedByInstance(?User $user): void
    {
        $this->updatedByInstance = $user;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFullname(): string
    {
        return sprintf('%s - %s %s', $this->documentNumber(), $this->firstName(), $this->firstSurname());
    }
}
