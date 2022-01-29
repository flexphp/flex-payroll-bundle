<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request;

use FlexPHP\Messages\RequestInterface;

final class FindPaysheetAlternativeProductRequest implements RequestInterface
{
    public $oilId;
    public $oilFilterId;
    public $airFilterId;
    public $gasFilterId;
    public $brandId;
    public $serieId;

    public function __construct(array $data)
    {
        $this->oilId = $data['oilId'] ?? 0;
        $this->oilFilterId = $data['oilFilterId'] ?? 0;
        $this->airFilterId = $data['airFilterId'] ?? 0;
        $this->gasFilterId = $data['gasFilterId'] ?? 0;
        $this->brandId = $data['brandId'] ?? 0;
        $this->serieId = $data['serieId'] ?? 0;
    }
}
