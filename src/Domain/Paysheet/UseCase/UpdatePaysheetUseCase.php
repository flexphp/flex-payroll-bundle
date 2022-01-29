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

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\UpdatePaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\UpdatePaysheetResponse;
use Exception;

final class UpdatePaysheetUseCase extends AbstractPaysheetUseCase
{
    /**
     * @param UpdatePaysheetRequest $request
     *
     * @return UpdatePaysheetResponse
     */
    public function execute($request)
    {
        if (!empty($request->id)) {
            $order = $this->orderRepository->getById(new ReadPaysheetRequest($request->id));
        }

        if (empty($order) || !$order->id()) {
            throw new Exception(\sprintf('Paysheet not exist [%d]', $request->id ?? 0), 404);
        }

        if ($request->isDraft && !$order->isDraft()) {
            throw new Exception('Paysheet with payments cannot change to draft');
        }

        if (!empty($request->vehicle)) {
            $request->vehicleId = $this->getVehicleId($request);
        }

        if (!empty($request->customer)) {
            $request->customerId = $this->getCustomerId($request);
        }

        $orderDetails = $this->getPaysheetDetails($request);
        $request->subtotal = $this->getSubTotal($orderDetails);
        $request->taxes = $this->getTotalTaxes($orderDetails);
        $request->kilometers = $this->getKilometers($request);
        $request->kilometersToChange = $this->getKilometersToChange($request);
        $request->discount = $this->getDiscount($request);
        $request->total = $this->getTotal($request);

        $payments = $this->getPayments($request);
        $request->totalPaid = $this->getTotalPaid($payments);
        $request->paidAt = $this->getPaidAt($request);
        $request->statusId = $this->getStatusId($request);

        $request->vehicle = null;
        $request->customer = null;
        $request->orderDetail = null;
        $request->payment = null;

        $order = $this->orderRepository->change($request);

        $this->savePaysheetDetails($orderDetails, $order->id(), $request->updatedBy);
        $this->savePayments($payments, $order->id(), $request->updatedBy);

        return new UpdatePaysheetResponse($order);
    }
}
