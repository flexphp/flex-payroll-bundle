<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Response;

use FlexPHP\Messages\ResponseInterface;

final class IndexEmployeeTypeResponse implements ResponseInterface
{
    public $employeeTypes;

    public function __construct(array $employeeTypes)
    {
        $this->employeeTypes = $employeeTypes;
    }
}
