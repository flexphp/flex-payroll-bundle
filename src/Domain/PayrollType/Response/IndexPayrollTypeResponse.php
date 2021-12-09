<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Response;

use FlexPHP\Messages\ResponseInterface;

final class IndexPayrollTypeResponse implements ResponseInterface
{
    public $payrollTypes;

    public function __construct(array $payrollTypes)
    {
        $this->payrollTypes = $payrollTypes;
    }
}
