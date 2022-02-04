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

use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Employee;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatus;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Agreement;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\ToArrayTrait;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Bill;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\BillFactory;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\BillGateway;
use FlexPHP\Bundle\InvoiceBundle\Domain\BillType\BillType;
use FlexPHP\Bundle\UserBundle\Domain\User\User;

final class Paysheet
{
    use ToArrayTrait;

    private $id;

    private $type;

    private $employeeId;

    private $agreementId;

    private $subtotal;

    private $taxes;

    private $total;

    private $notes;

    private $totalPaid;

    private $paidAt;

    private $statusId;

    private $paysheetNotes;

    private $expiratedAt;

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

    private ?Bill $paysheetInstance = null;

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

    public function subtotal(): ?string
    {
        return $this->subtotal;
    }

    public function taxes(): ?string
    {
        return $this->taxes;
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

    public function expiratedAt(): ?\DateTime
    {
        return $this->expiratedAt;
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

    public function statusIdInstance(): ?PayrollStatus
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

    public function setSubtotal(string $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    public function setTaxes(string $taxes): void
    {
        $this->taxes = $taxes;
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

    public function setBillNotes(?string $paysheetNotes): void
    {
        $this->paysheetNotes = $paysheetNotes;
    }

    public function setExpiratedAt(?\DateTime $expiratedAt): void
    {
        $this->expiratedAt = $expiratedAt;
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

    public function setTypeInstance(PayrollType $paysheetType): void
    {
        $this->typeInstance = $paysheetType;
    }

    public function setEmployeeIdInstance(?Employee $employee): void
    {
        $this->employeeIdInstance = $employee;
    }

    public function setAgreementIdInstance(?Agreement $agreement): void
    {
        $this->agreementIdInstance = $agreement;
    }

    public function setStatusIdInstance(?PayrollStatus $paysheetStatus): void
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

    public function paysheetInstance(): ?Bill
    {
        return $this->paysheetInstance;
    }

    public function setBillInstance(?Bill $paysheetInstance): void
    {
        $this->paysheetInstance = $paysheetInstance;
    }

    public function withLastBill(BillGateway $paysheetGateway, int $offset): self
    {
        if ($this->id() && !$this->paysheetInstance) {
            $paysheets = $paysheetGateway->search([
                'paysheetId' => $this->id(),
                'type' => BillType::INVOICE,
            ], [], 1, 1, $offset);

            $this->setBillInstance((\count($paysheets) > 0 ? (new BillFactory)->make($paysheets[0]) : null));
        }

        return $this;
    }

    public function isPayed(): bool
    {
        return $this->statusId() === PayrollStatus::PAYED;
    }

    public function isPending(): bool
    {
        return $this->statusId() === PayrollStatus::PENDING;
    }

    public function isDraft(): bool
    {
        return $this->statusId() === PayrollStatus::DRAFT;
    }
}
