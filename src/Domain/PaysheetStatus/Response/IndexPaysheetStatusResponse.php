<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Response;

use FlexPHP\Messages\ResponseInterface;

final class IndexPaysheetStatusResponse implements ResponseInterface
{
    public $paysheetStatus;

    public function __construct(array $paysheetStatus)
    {
        $this->paysheetStatus = $paysheetStatus;
    }
}
