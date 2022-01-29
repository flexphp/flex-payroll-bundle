<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPaysheetPayrollStatusResponse implements ResponseInterface
{
    public $orderStatus;

    public function __construct(array $orderStatus)
    {
        $this->orderStatus = $orderStatus;
    }
}
