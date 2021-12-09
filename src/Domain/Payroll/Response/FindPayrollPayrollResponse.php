<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPayrollPayrollResponse implements ResponseInterface
{
    public $payrolls;

    public function __construct(array $payrolls)
    {
        $this->payrolls = $payrolls;
    }
}
