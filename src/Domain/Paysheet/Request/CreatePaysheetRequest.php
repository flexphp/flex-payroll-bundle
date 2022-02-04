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

use FlexPHP\Bundle\HelperBundle\Domain\Helper\DateTimeTrait;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;
use FlexPHP\Messages\RequestInterface;

class CreatePaysheetRequest implements RequestInterface
{
    use DateTimeTrait;

    public $employee;

    public $agreement;

    public $paysheetDetail;

    public $payment;

    public $isDraft;

    public $type;

    public $employeeId;

    public $agreementId;

    public $subtotal;

    public $taxes;

    public $total;

    public $notes;

    public $totalPaid;

    public $paidAt;

    public $statusId;

    public $billNotes;

    public $expiratedAt;

    public $createdBy;

    public function __construct(array $data, int $createdBy, ?string $timezone = null)
    {
        $this->employee = $data['employee'] ?? [];
        $this->agreement = $data['agreement'] ?? [];
        $this->paysheetDetail = $data['paysheet_detail'] ?? [];
        $this->payment = $data['payment'] ?? [];

        $this->type = $data['paysheet']['type'] ?? PayrollType::NOVEL;
        $this->isDraft = $data['paysheet']['isDraft'] ?? false;
        $this->notes = $data['paysheet']['notes'] ?? null;
        $this->billNotes = $data['paysheet']['billNotes'] ?? null;
        $this->expiratedAt = !empty($data['paysheet']['expiratedAt'])
            ? $this->dateTimeToUTC($data['paysheet']['expiratedAt'], $this->getOffset($this->getTimezone($timezone)))
            : null;

        $this->createdAt = !empty($data['paysheet']['createdAt'])
            ? $this->dateTimeToUTC($data['paysheet']['createdAt'], $this->getOffset($this->getTimezone($timezone)))
            : null;
        $this->createdBy = $createdBy;
    }
}
