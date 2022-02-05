<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPaysheetPaysheetStatusResponse implements ResponseInterface
{
    public $paysheetStatus;

    public function __construct(array $paysheetStatus)
    {
        $this->paysheetStatus = $paysheetStatus;
    }
}
