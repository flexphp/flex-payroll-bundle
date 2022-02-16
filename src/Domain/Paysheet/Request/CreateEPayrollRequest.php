<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request;

use FlexPHP\Bundle\HelperBundle\Domain\Helper\DateTimeTrait;
use FlexPHP\Messages\RequestInterface;

final class CreateEPayrollRequest implements RequestInterface
{
    use DateTimeTrait;

    public $paysheetId;
    public $offset;

    public function __construct(int $paysheetId, ?string $timezone)
    {
        $this->paysheetId = $paysheetId;
        $this->offset = $this->getOffset($this->getTimezone($timezone));
    }
}
