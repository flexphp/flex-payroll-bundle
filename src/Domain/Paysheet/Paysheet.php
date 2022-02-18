<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet;

use FlexPHP\Bundle\HelperBundle\Domain\Helper\ToArrayTrait;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Agreement;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Employee;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Payroll;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\PayrollFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\PayrollGateway;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\PaysheetStatus;
use FlexPHP\Bundle\UserBundle\Domain\User\User;

final class Paysheet
{
    use ToArrayTrait;

    private $id;

    private $type;

    private $employeeId;

    private $agreementId;

    private $totalAccrued;

    private $totalDeduction;

    private $total;

    private $notes;

    private $totalPaid;

    private $paidAt;

    private $statusId;

    private $paysheetNotes;

    private $issuedAt;

    private $initAt;

    private $finishAt;

    private $createdAt;

    private $updatedAt;

    private $createdBy;

    private $updatedBy;

    private $typeInstance;

    private $employeeIdInstance;

    private $agreementIdInstance;

    private $statusIdInstance;

    private $createdByInstance;

    private $updatedByInstance;

    private ?Payroll $payrollInstance = null;

    private array $details = [];

    public function id(): ?int
    {
        return $this->id;
    }

    public function type(): ?string
    {
        return $this->type;
    }

    public function employeeId(): ?int
    {
        return $this->employeeId;
    }

    public function agreementId(): ?int
    {
        return $this->agreementId;
    }

    public function totalAccrued(): ?string
    {
        return $this->totalAccrued;
    }

    public function totalDeduction(): ?string
    {
        return $this->totalDeduction;
    }

    public function total(): ?string
    {
        return $this->total;
    }

    public function notes(): ?string
    {
        return $this->notes;
    }

    public function totalPaid(): ?string
    {
        return $this->totalPaid;
    }

    public function paidAt(): ?\DateTime
    {
        return $this->paidAt;
    }

    public function statusId(): ?string
    {
        return $this->statusId;
    }

    public function paysheetNotes(): ?string
    {
        return $this->paysheetNotes;
    }

    public function issuedAt(): ?\DateTime
    {
        return $this->issuedAt;
    }

    public function initAt(): ?\DateTime
    {
        return $this->initAt;
    }

    public function finishAt(): ?\DateTime
    {
        return $this->finishAt;
    }

    public function details(): array
    {
        return $this->details;
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

    public function typeInstance(): PayrollType
    {
        return $this->typeInstance ?: new PayrollType;
    }

    public function employeeIdInstance(): ?Employee
    {
        return $this->employeeIdInstance;
    }

    public function agreementIdInstance(): ?Agreement
    {
        return $this->agreementIdInstance;
    }

    public function statusIdInstance(): ?PaysheetStatus
    {
        return $this->statusIdInstance;
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

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setEmployeeId(?int $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    public function setAgreementId(?int $agreementId): void
    {
        $this->agreementId = $agreementId;
    }

    public function setTotalAccrued(string $totalAccrued): void
    {
        $this->totalAccrued = $totalAccrued;
    }

    public function setTotalDeduction(string $totalDeduction): void
    {
        $this->totalDeduction = $totalDeduction;
    }

    public function setTotal(string $total): void
    {
        $this->total = $total;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function setTotalPaid(string $totalPaid): void
    {
        $this->totalPaid = $totalPaid;
    }

    public function setPaidAt(?\DateTime $paidAt): void
    {
        $this->paidAt = $paidAt;
    }

    public function setStatusId(?string $statusId): void
    {
        $this->statusId = $statusId;
    }

    public function setPaysheetNotes(?string $paysheetNotes): void
    {
        $this->paysheetNotes = $paysheetNotes;
    }

    public function setIssuedAt(?\DateTime $issuedAt): void
    {
        $this->issuedAt = $issuedAt;
    }

    public function setInitAt(?\DateTime $initAt): void
    {
        $this->initAt = $initAt;
    }

    public function setFinishAt(?\DateTime $finishAt): void
    {
        $this->finishAt = $finishAt;
    }

    public function setDetails(array $details): void
    {
        $this->details = $details;
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

    public function setTypeInstance(PayrollType $payrollType): void
    {
        $this->typeInstance = $payrollType;
    }

    public function setEmployeeIdInstance(?Employee $employee): void
    {
        $this->employeeIdInstance = $employee;
    }

    public function setAgreementIdInstance(?Agreement $agreement): void
    {
        $this->agreementIdInstance = $agreement;
    }

    public function setStatusIdInstance(?PaysheetStatus $paysheetStatus): void
    {
        $this->statusIdInstance = $paysheetStatus;
    }

    public function setCreatedByInstance(?User $user): void
    {
        $this->createdByInstance = $user;
    }

    public function setUpdatedByInstance(?User $user): void
    {
        $this->updatedByInstance = $user;
    }

    public function payrollInstance(): ?Payroll
    {
        return $this->payrollInstance;
    }

    public function setPayrollInstance(?Payroll $payrollInstance): void
    {
        $this->payrollInstance = $payrollInstance;
    }

    public function withLastPayroll(PayrollGateway $payrollGateway, int $offset): self
    {
        if ($this->id() && !$this->payrollInstance) {
            $payrolls = $payrollGateway->search([
                'paysheet' => $this->id(),
                'type' => PayrollType::NOVEL,
            ], [], 1, 1, $offset);

            $this->setPayrollInstance((\count($payrolls) > 0 ? (new PayrollFactory)->make($payrolls[0]) : null));
        }

        return $this;
    }

    public function isPayed(): bool
    {
        return $this->statusId() === PaysheetStatus::PAYED;
    }

    public function isPending(): bool
    {
        return $this->statusId() === PaysheetStatus::PENDING;
    }

    public function isDraft(): bool
    {
        return $this->statusId() === PaysheetStatus::DRAFT;
    }
}
