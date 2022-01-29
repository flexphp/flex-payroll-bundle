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

use FlexPHP\Bundle\PayrollBundle\Domain\Customer\Customer;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatus;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\Vehicle;
use FlexPHP\Bundle\PayrollBundle\Domain\Worker\Worker;
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

    private $customerId;

    private $vehicleId;

    private $kilometers;

    private $kilometersToChange;

    private $discount;

    private $subtotal;

    private $taxes;

    private $total;

    private $notes;

    private $totalPaid;

    private $paidAt;

    private $statusId;

    private $billNotes;

    private $expiratedAt;

    private $worker;

    private $createdAt;

    private $updatedAt;

    private $createdBy;

    private $updatedBy;

    private $typeInstance;

    private $customerIdInstance;

    private $vehicleIdInstance;

    private $statusIdInstance;

    private $workerInstance;

    private $createdByInstance;

    private $updatedByInstance;

    private ?Bill $billInstance = null;

    public function id(): ?int
    {
        return $this->id;
    }

    public function type(): ?string
    {
        return $this->type;
    }

    public function customerId(): ?int
    {
        return $this->customerId;
    }

    public function vehicleId(): ?int
    {
        return $this->vehicleId;
    }

    public function kilometers(): ?int
    {
        return $this->kilometers;
    }

    public function kilometersToChange(): ?int
    {
        return $this->kilometersToChange;
    }

    public function discount(): ?string
    {
        return $this->discount;
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

    public function billNotes(): ?string
    {
        return $this->billNotes;
    }

    public function expiratedAt(): ?\DateTime
    {
        return $this->expiratedAt;
    }

    public function worker(): ?int
    {
        return $this->worker;
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

    public function customerIdInstance(): ?Customer
    {
        return $this->customerIdInstance;
    }

    public function vehicleIdInstance(): ?Vehicle
    {
        return $this->vehicleIdInstance;
    }

    public function statusIdInstance(): ?PayrollStatus
    {
        return $this->statusIdInstance;
    }

    public function workerInstance(): ?Worker
    {
        return $this->workerInstance;
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

    public function setCustomerId(?int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function setVehicleId(?int $vehicleId): void
    {
        $this->vehicleId = $vehicleId;
    }

    public function setKilometers(?int $kilometers): void
    {
        $this->kilometers = $kilometers;
    }

    public function setKilometersToChange(?int $kilometersToChange): void
    {
        $this->kilometersToChange = $kilometersToChange;
    }

    public function setDiscount(string $discount): void
    {
        $this->discount = $discount;
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

    public function setBillNotes(?string $billNotes): void
    {
        $this->billNotes = $billNotes;
    }

    public function setExpiratedAt(?\DateTime $expiratedAt): void
    {
        $this->expiratedAt = $expiratedAt;
    }

    public function setWorker(?int $worker): void
    {
        $this->worker = $worker;
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

    public function setTypeInstance(PayrollType $orderType): void
    {
        $this->typeInstance = $orderType;
    }

    public function setCustomerIdInstance(?Customer $customer): void
    {
        $this->customerIdInstance = $customer;
    }

    public function setVehicleIdInstance(?Vehicle $vehicle): void
    {
        $this->vehicleIdInstance = $vehicle;
    }

    public function setStatusIdInstance(?PayrollStatus $orderStatus): void
    {
        $this->statusIdInstance = $orderStatus;
    }

    public function setWorkerInstance(?Worker $worker): void
    {
        $this->workerInstance = $worker;
    }

    public function setCreatedByInstance(?User $user): void
    {
        $this->createdByInstance = $user;
    }

    public function setUpdatedByInstance(?User $user): void
    {
        $this->updatedByInstance = $user;
    }

    public function billInstance(): ?Bill
    {
        return $this->billInstance;
    }

    public function setBillInstance(?Bill $billInstance): void
    {
        $this->billInstance = $billInstance;
    }

    public function withLastBill(BillGateway $billGateway, int $offset): self
    {
        if ($this->id() && !$this->billInstance) {
            $bills = $billGateway->search([
                'orderId' => $this->id(),
                'type' => BillType::INVOICE,
            ], [], 1, 1, $offset);

            $this->setBillInstance((\count($bills) > 0 ? (new BillFactory)->make($bills[0]) : null));
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
