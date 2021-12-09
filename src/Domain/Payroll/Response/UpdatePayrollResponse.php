<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Payroll;
use FlexPHP\Messages\ResponseInterface;

final class UpdatePayrollResponse implements ResponseInterface
{
    public $payroll;

    public function __construct(Payroll $payroll)
    {
        $this->payroll = $payroll;
    }
}
