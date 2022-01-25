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

use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Employee;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\ToArrayTrait;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatus;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;
use FlexPHP\Bundle\NumerationBundle\Domain\Provider\Provider;
use FlexPHP\Bundle\UserBundle\Domain\User\User;

final class Payroll
{
    use ToArrayTrait;

    private $id;

    private $prefix;

    private $number;

    private $employee;

    private $provider;

    private $status;

    private $type;

    private $traceId;

    private $hash;

    private $hashType;

    private $message;

    private $pdfPath;

    private $xmlPath;

    private $parentId;

    private $downloadedAt;

    private $createdAt;

    private $updatedAt;

    private $createdBy;

    private $updatedBy;

    private $employeeInstance;

    private $providerInstance;

    private $statusInstance;

    private $typeInstance;

    private $parentIdInstance;

    private $createdByInstance;

    private $updatedByInstance;

    public function id(): ?int
    {
        return $this->id;
    }

    public function prefix(): ?string
    {
        return $this->prefix;
    }

    public function number(): ?int
    {
        return $this->number;
    }

    public function employee(): ?int
    {
        return $this->employee;
    }

    public function provider(): ?string
    {
        return $this->provider;
    }

    public function status(): ?string
    {
        return $this->status;
    }

    public function type(): ?string
    {
        return $this->type;
    }

    public function traceId(): ?string
    {
        return $this->traceId;
    }

    public function hash(): ?string
    {
        return $this->hash;
    }

    public function hashType(): ?string
    {
        return $this->hashType;
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function pdfPath(): ?string
    {
        return $this->pdfPath;
    }

    public function xmlPath(): ?string
    {
        return $this->xmlPath;
    }

    public function parentId(): ?int
    {
        return $this->parentId;
    }

    public function downloadedAt(): ?\DateTime
    {
        return $this->downloadedAt;
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

    public function providerInstance(): Provider
    {
        return $this->providerInstance ?: new Provider;
    }

    public function statusInstance(): PayrollStatus
    {
        return $this->statusInstance ?: new PayrollStatus;
    }

    public function typeInstance(): PayrollType
    {
        return $this->typeInstance ?: new PayrollType;
    }

    public function parentIdInstance(): ?self
    {
        return $this->parentIdInstance;
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

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    public function setEmployee(int $employee): void
    {
        $this->employee = $employee;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setTraceId(?string $traceId): void
    {
        $this->traceId = $traceId;
    }

    public function setHash(?string $hash): void
    {
        $this->hash = $hash;
    }

    public function setHashType(?string $hashType): void
    {
        $this->hashType = $hashType;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function setPdfPath(?string $pdfPath): void
    {
        $this->pdfPath = $pdfPath;
    }

    public function setXmlPath(?string $xmlPath): void
    {
        $this->xmlPath = $xmlPath;
    }

    public function setParentId(?int $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function setDownloadedAt(?\DateTime $downloadedAt): void
    {
        $this->downloadedAt = $downloadedAt;
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

    public function setProviderInstance(Provider $provider): void
    {
        $this->providerInstance = $provider;
    }

    public function setStatusInstance(PayrollStatus $payrollStatus): void
    {
        $this->statusInstance = $payrollStatus;
    }

    public function setTypeInstance(PayrollType $payrollType): void
    {
        $this->typeInstance = $payrollType;
    }

    public function setParentIdInstance(?self $payroll): void
    {
        $this->parentIdInstance = $payroll;
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
