<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindEmployeePaymentMethodResponse implements ResponseInterface
{
    public $paymentMethods;

    public function __construct(array $paymentMethods)
    {
        $this->paymentMethods = $paymentMethods;
    }
}
