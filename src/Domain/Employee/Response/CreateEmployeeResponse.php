<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Employee;
use FlexPHP\Messages\ResponseInterface;

final class CreateEmployeeResponse implements ResponseInterface
{
    public $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }
}
