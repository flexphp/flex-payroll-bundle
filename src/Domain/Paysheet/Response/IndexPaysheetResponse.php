<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class IndexPaysheetResponse implements ResponseInterface
{
    public $paysheets;

    public function __construct(array $paysheets)
    {
        $this->paysheets = $paysheets;
    }
}
