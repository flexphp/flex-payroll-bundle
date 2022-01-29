<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPaysheetPayrollTypeResponse implements ResponseInterface
{
    public $orderTypes;

    public function __construct(array $orderTypes)
    {
        $this->orderTypes = $orderTypes;
    }
}
