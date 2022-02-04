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

use FlexPHP\Bundle\HelperBundle\Domain\Helper\DateTimeTrait;
use FlexPHP\Messages\RequestInterface;

final class IndexPayrollRequest implements RequestInterface
{
    use DateTimeTrait;

    public $id;

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
