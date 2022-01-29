<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request;

use FlexPHP\Bundle\HelperBundle\Domain\Helper\DateTimeTrait;
use FlexPHP\Messages\RequestInterface;

final class CreateEPayrollRequest implements RequestInterface
{
    use DateTimeTrait;

    public $orderId;
    public $offset;

    public function __construct(int $orderId, ?string $timezone)
    {
        $this->orderId = $orderId;
        $this->offset = $this->getOffset($this->getTimezone($timezone));
    }
}
