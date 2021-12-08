<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\EmployeeSubType;
use FlexPHP\Messages\ResponseInterface;

final class DeleteEmployeeSubTypeResponse implements ResponseInterface
{
    public $employeeSubType;

    public function __construct(EmployeeSubType $employeeSubType)
    {
        $this->employeeSubType = $employeeSubType;
    }
}
