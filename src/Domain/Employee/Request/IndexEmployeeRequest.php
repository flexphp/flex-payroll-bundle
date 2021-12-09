<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request;

use FlexPHP\Bundle\HelperBundle\Domain\Helper\DateTimeTrait;
use FlexPHP\Messages\RequestInterface;

final class IndexEmployeeRequest implements RequestInterface
{
    use DateTimeTrait;

    public $id;

    public $documentTypeId;

    public $documentNumber;

    public $firstName;

    public $secondName;

    public $firstSurname;

    public $secondSurname;

    public $type;

    public $subType;

    public $paymentMethod;

    public $accountType;

    public $accountNumber;

    public $isActive;

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
        $this->documentTypeId = $data['documentTypeId'] ?? null;
        $this->documentNumber = $data['documentNumber'] ?? null;
        $this->firstName = $data['firstName'] ?? null;
        $this->secondName = $data['secondName'] ?? null;
        $this->firstSurname = $data['firstSurname'] ?? null;
        $this->secondSurname = $data['secondSurname'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->subType = $data['subType'] ?? null;
        $this->paymentMethod = $data['paymentMethod'] ?? null;
        $this->accountType = $data['accountType'] ?? null;
        $this->accountNumber = $data['accountNumber'] ?? null;
        $this->isActive = $data['isActive'] ?? null;
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
