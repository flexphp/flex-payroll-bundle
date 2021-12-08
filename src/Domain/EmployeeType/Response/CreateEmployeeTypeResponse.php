<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\EmployeeType;
use FlexPHP\Messages\ResponseInterface;

final class CreateEmployeeTypeResponse implements ResponseInterface
{
    public $employeeType;

    public function __construct(EmployeeType $employeeType)
    {
        $this->employeeType = $employeeType;
    }
}
