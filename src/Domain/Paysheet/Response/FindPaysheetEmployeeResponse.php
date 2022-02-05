<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPaysheetEmployeeResponse implements ResponseInterface
{
    public $employees;

    public function __construct(array $employees)
    {
        $this->employees = $employees;
    }
}
