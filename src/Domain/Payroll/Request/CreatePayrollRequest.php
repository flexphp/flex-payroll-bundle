<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request;

use FlexPHP\Messages\RequestInterface;

final class CreatePayrollRequest implements RequestInterface
{
    public $prefix;

    public $number;

    public $paysheet;

    public $provider;

    public $status;

    public $type;

    public $traceId;

    public $hash;

    public $hashType;

    public $message;

    public $pdfPath;

    public $xmlPath;

    public $parentId;

    public $downloadedAt;

    public $createdBy;

    public function __construct(array $data, int $createdBy)
    {
        $this->prefix = $data['prefix'] ?? null;
        $this->number = $data['number'] ?? null;
        $this->paysheet = $data['paysheet'] ?? null;
        $this->provider = $data['provider'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->traceId = $data['traceId'] ?? null;
        $this->hash = $data['hash'] ?? null;
        $this->hashType = $data['hashType'] ?? null;
        $this->message = $data['message'] ?? null;
        $this->pdfPath = $data['pdfPath'] ?? null;
        $this->xmlPath = $data['xmlPath'] ?? null;
        $this->parentId = $data['parentId'] ?? null;
        $this->downloadedAt = $data['downloadedAt'] ?? null;
        $this->createdBy = $createdBy;
    }
}
