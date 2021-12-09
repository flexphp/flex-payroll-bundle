<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;
use FlexPHP\Messages\ResponseInterface;

final class UpdatePayrollTypeResponse implements ResponseInterface
{
    public $payrollType;

    public function __construct(PayrollType $payrollType)
    {
        $this->payrollType = $payrollType;
    }
}
