<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatus;
use FlexPHP\Messages\ResponseInterface;

final class UpdatePayrollStatusResponse implements ResponseInterface
{
    public $payrollStatus;

    public function __construct(PayrollStatus $payrollStatus)
    {
        $this->payrollStatus = $payrollStatus;
    }
}
