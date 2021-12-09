<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindEmployeeEmployeeTypeResponse implements ResponseInterface
{
    public $employeeTypes;

    public function __construct(array $employeeTypes)
    {
        $this->employeeTypes = $employeeTypes;
    }
}
