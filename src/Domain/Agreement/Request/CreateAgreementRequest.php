<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request;

use DateTime;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DateTimeTrait;
use FlexPHP\Messages\RequestInterface;

final class CreateAgreementRequest implements RequestInterface
{
    use DateTimeTrait;

    public $name;

    public $employee;

    public $type;

    public $period;

    public $currency;

    public $salary;

    public $healthPercentage;

    public $pensionPercentage;

    public $integralSalary;

    public $highRisk;

    public $initAt;

    public $finishAt;

    public $status;

    public $createdBy;

    public function __construct(array $data, int $createdBy, ?string $timezone = null)
    {
        $this->name = $data['name'] ?? null;
        $this->employee = $data['employee'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->period = $data['period'] ?? null;
        $this->currency = $data['currency'] ?? null;
        $this->salary = $data['salary'] ?? null;
        $this->healthPercentage = $data['healthPercentage'] ?? null;
        $this->pensionPercentage = $data['pensionPercentage'] ?? null;
        $this->integralSalary = $data['integralSalary'] ?? null;
        $this->highRisk = $data['highRisk'] ?? null;
        $this->initAt = !empty($data['initAt'])
            ? $this->dateTimeToUTC($data['initAt']->format(DateTime::ISO8601), $this->getOffset($this->getTimezone($timezone)))
            : null;
        $this->finishAt = !empty($data['finishAt'])
            ? $this->dateTimeToUTC($data['finishAt']->format(DateTime::ISO8601), $this->getOffset($this->getTimezone($timezone)))
            : null;
        $this->status = $data['status'] ?? null;
        $this->createdBy = $createdBy;
    }
}
