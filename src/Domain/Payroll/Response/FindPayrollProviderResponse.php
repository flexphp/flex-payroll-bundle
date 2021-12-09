<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPayrollProviderResponse implements ResponseInterface
{
    public $providers;

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }
}
