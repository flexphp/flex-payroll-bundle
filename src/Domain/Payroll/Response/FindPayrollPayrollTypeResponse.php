<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPayrollPayrollTypeResponse implements ResponseInterface
{
    public $payrollTypes;

    public function __construct(array $payrollTypes)
    {
        $this->payrollTypes = $payrollTypes;
    }
}
