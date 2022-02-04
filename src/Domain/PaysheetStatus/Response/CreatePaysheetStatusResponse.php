<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\PaysheetStatus;
use FlexPHP\Messages\ResponseInterface;

final class CreatePaysheetStatusResponse implements ResponseInterface
{
    public $paysheetStatus;

    public function __construct(PaysheetStatus $paysheetStatus)
    {
        $this->paysheetStatus = $paysheetStatus;
    }
}
