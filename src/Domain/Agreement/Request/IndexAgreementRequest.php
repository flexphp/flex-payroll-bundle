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

use FlexPHP\Bundle\HelperBundle\Domain\Helper\DateTimeTrait;
use FlexPHP\Messages\RequestInterface;

final class IndexAgreementRequest implements RequestInterface
{
    use DateTimeTrait;

    public $id;

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

    public $createdAt = [];

    public $updatedAt;

    public $createdBy;

    public $updatedBy;

    public $_page;

    public $_limit;

    public $_offset;

    public function __construct(array $data, int $_page, int $_limit = 50, ?string $timezone = null)
    {
        $this->id = $data['id'] ?? null;
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
        $this->createdAt[] = $data['createdAt_START'] ?? null;
        $this->createdAt[] = $data['createdAt_END'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
        $this->createdBy = $data['createdBy'] ?? null;
        $this->updatedBy = $data['updatedBy'] ?? null;
        $this->_page = $_page;
        $this->_limit = $_limit;
        $this->_offset = $this->getOffset($this->getTimezone($timezone));
    }
}
