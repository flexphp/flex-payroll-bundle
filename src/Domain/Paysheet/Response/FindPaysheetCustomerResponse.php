<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPaysheetCustomerResponse implements ResponseInterface
{
    public $customers;

    public function __construct(array $customers)
    {
        $this->customers = $customers;
    }
}
