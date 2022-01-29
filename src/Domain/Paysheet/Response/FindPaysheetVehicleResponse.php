<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPaysheetVehicleResponse implements ResponseInterface
{
    public $vehicles;

    public function __construct(array $vehicles)
    {
        $this->vehicles = $vehicles;
    }
}
