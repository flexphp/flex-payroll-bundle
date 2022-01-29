<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request;

use FlexPHP\Messages\RequestInterface;

final class FindPaysheetHistoryServiceRequest implements RequestInterface
{
    public $vehicleId;

    public function __construct(array $data)
    {
        $this->vehicleId = $data['vehicleId'] ?? '';
    }
}
