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

use FlexPHP\Messages\RequestInterface;

final class CreateEmployeeRequest implements RequestInterface
{
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

    public $bank;

    public $createdBy;

    public function __construct(array $data, int $createdBy)
    {
        $this->documentTypeId = $data['documentTypeId'] ?? null;
        $this->documentNumber = isset($data['documentNumber']) ? \trim($data['documentNumber']) : null;
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
        $this->bank = $data['bank'] ?? null;
        $this->createdBy = $createdBy;
    }
}
