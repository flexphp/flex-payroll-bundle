<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request;

use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DateTimeTrait;
use FlexPHP\Messages\RequestInterface;

class CreatePaysheetRequest implements RequestInterface
{
    use DateTimeTrait;

    public $customer;

    public $vehicle;

    public $orderDetail;

    public $payment;

    public $isDraft;

    public $type;

    public $customerId;

    public $vehicleId;

    public $kilometers;

    public $kilometersToChange;

    public $discount;

    public $subtotal;

    public $taxes;

    public $total;

    public $notes;

    public $totalPaid;

    public $paidAt;

    public $statusId;

    public $billNotes;

    public $expiratedAt;

    public $worker;

    public $createdBy;

    public function __construct(array $data, int $createdBy, ?string $timezone = null)
    {
        $this->customer = $data['customer'] ?? [];
        $this->vehicle = $data['vehicle'] ?? [];
        $this->orderDetail = $data['order_detail'] ?? [];
        $this->payment = $data['payment'] ?? [];

        $this->type = $data['order']['type'] ?? PayrollType::VEHICLE;
        $this->isDraft = $data['order']['isDraft'] ?? false;
        $this->kilometers = $data['order']['kilometers'] ?? 0;
        $this->kilometersToChange = $data['order']['kilometersToChange'] ?? 0;
        $this->discount = $data['order']['discount'] ?? 0;
        $this->notes = $data['order']['notes'] ?? null;
        $this->billNotes = $data['order']['billNotes'] ?? null;
        $this->expiratedAt = !empty($data['order']['expiratedAt'])
            ? $this->dateTimeToUTC($data['order']['expiratedAt'], $this->getOffset($this->getTimezone($timezone)))
            : null;
        $this->worker = $data['order']['worker'] ?? null;

        $this->createdAt = !empty($data['order']['createdAt'])
            ? $this->dateTimeToUTC($data['order']['createdAt'], $this->getOffset($this->getTimezone($timezone)))
            : null;
        $this->createdBy = $createdBy;
    }
}
