<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPaysheetHistoryServiceResponse implements ResponseInterface
{
    public $historyServices;

    public function __construct(array $historyServices)
    {
        $this->historyServices = $historyServices;
    }
}
