<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindEmployeeEmployeeSubTypeResponse implements ResponseInterface
{
    public $employeeSubTypes;

    public function __construct(array $employeeSubTypes)
    {
        $this->employeeSubTypes = $employeeSubTypes;
    }
}
