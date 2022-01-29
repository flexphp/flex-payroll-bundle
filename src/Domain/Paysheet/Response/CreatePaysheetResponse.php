<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Paysheet;
use FlexPHP\Messages\ResponseInterface;

final class CreatePaysheetResponse implements ResponseInterface
{
    public $order;

    public function __construct(Paysheet $order)
    {
        $this->order = $order;
    }
}
