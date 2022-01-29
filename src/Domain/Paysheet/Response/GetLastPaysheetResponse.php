<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class GetLastPaysheetResponse implements ResponseInterface
{
    public $order;

    public function __construct(array $order)
    {
        $this->order = $order;
    }
}
