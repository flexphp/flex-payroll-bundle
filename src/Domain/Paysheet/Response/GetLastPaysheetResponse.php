<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class GetLastPaysheetResponse implements ResponseInterface
{
    public $paysheet;

    public function __construct(array $paysheet)
    {
        $this->paysheet = $paysheet;
    }
}
