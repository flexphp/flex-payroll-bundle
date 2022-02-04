<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Paysheet;
use FlexPHP\Messages\ResponseInterface;

final class ReadPaysheetResponse implements ResponseInterface
{
    public $paysheet;

    public function __construct(Paysheet $paysheet)
    {
        $this->paysheet = $paysheet;
    }
}
