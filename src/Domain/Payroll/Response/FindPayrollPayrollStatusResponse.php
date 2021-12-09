<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPayrollPayrollStatusResponse implements ResponseInterface
{
    public $payrollStatus;

    public function __construct(array $payrollStatus)
    {
        $this->payrollStatus = $payrollStatus;
    }
}
