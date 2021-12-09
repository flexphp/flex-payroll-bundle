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

use FlexPHP\Messages\RequestInterface;

final class CreateAgreementRequest implements RequestInterface
{
    public $status;

    public $type;

    public $period;

    public $currency;

    public $salary;

    public $healthPercentage;

    public $pensionPercentage;

    public $integralSalary;

    public $highRisk;

    public $isActive;

    public $initAt;

    public $finishAt;

    public $createdBy;

    public function __construct(array $data, int $createdBy)
    {
        $this->status = $data['status'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->period = $data['period'] ?? null;
        $this->currency = $data['currency'] ?? null;
        $this->salary = $data['salary'] ?? null;
        $this->healthPercentage = $data['healthPercentage'] ?? null;
        $this->pensionPercentage = $data['pensionPercentage'] ?? null;
        $this->integralSalary = $data['integralSalary'] ?? null;
        $this->highRisk = $data['highRisk'] ?? null;
        $this->isActive = $data['isActive'] ?? null;
        $this->initAt = $data['initAt'] ?? null;
        $this->finishAt = $data['finishAt'] ?? null;
        $this->createdBy = $createdBy;
    }
}
