<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\Request\ReadVehicleRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\UseCase\ReadVehicleUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\Vehicle;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\VehicleRepository;

final class GetPaysheetVehicleUseCase
{
    private PaysheetRepository $orderRepository;

    private VehicleRepository $vehicleRepository;

    public function __construct(PaysheetRepository $orderRepository, VehicleRepository $vehicleRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute(ReadPaysheetRequest $request): Vehicle
    {
        $useCasePaysheet = new ReadPaysheetUseCase($this->orderRepository);

        $responsePaysheet = $useCasePaysheet->execute(new ReadPaysheetRequest($request->id));

        $order = $responsePaysheet->order;

        $vehicle = new Vehicle();

        if ($order->vehicleId()) {
            $requestVehicle = new ReadVehicleRequest($order->vehicleId());

            $useCaseVehicle = new ReadVehicleUseCase($this->vehicleRepository);

            $responseVehicle = $useCaseVehicle->execute($requestVehicle);

            $vehicle = $responseVehicle->vehicle;
        }

        return $vehicle;
    }
}
