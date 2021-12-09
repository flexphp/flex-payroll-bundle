<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPayrollEmployeeResponse implements ResponseInterface
{
    public $employees;

    public function __construct(array $employees)
    {
        $this->employees = $employees;
    }
}
