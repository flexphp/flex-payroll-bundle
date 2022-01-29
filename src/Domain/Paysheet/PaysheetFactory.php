<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet;

use FlexPHP\Bundle\PayrollBundle\Domain\Customer\CustomerFactory;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatusFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollTypeFactory;
use FlexPHP\Bundle\UserBundle\Domain\User\UserFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Vehicle\VehicleFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Worker\WorkerFactory;

final class PaysheetFactory
{
    use FactoryExtendedTrait;

    public function make($data): Paysheet
    {
        $order = new Paysheet();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $order->setId((int)$data['id']);
        }

        if (isset($data['type'])) {
            $order->setType((string)$data['type']);
        }

        if (isset($data['customerId'])) {
            $order->setCustomerId((int)$data['customerId']);
        }

        if (isset($data['vehicleId'])) {
            $order->setVehicleId((int)$data['vehicleId']);
        }

        if (isset($data['kilometers'])) {
            $order->setKilometers((int)$data['kilometers']);
        }

        if (isset($data['kilometersToChange'])) {
            $order->setKilometersToChange((int)$data['kilometersToChange']);
        }

        if (isset($data['discount'])) {
            $order->setDiscount((string)$data['discount']);
        }

        if (isset($data['subtotal'])) {
            $order->setSubtotal((string)$data['subtotal']);
        }

        if (isset($data['taxes'])) {
            $order->setTaxes((string)$data['taxes']);
        }

        if (isset($data['total'])) {
            $order->setTotal((string)$data['total']);
        }

        if (isset($data['notes'])) {
            $order->setNotes((string)$data['notes']);
        }

        if (isset($data['totalPaid'])) {
            $order->setTotalPaid((string)$data['totalPaid']);
        }

        if (isset($data['paidAt'])) {
            $order->setPaidAt(\is_string($data['paidAt']) ? new \DateTime($data['paidAt']) : $data['paidAt']);
        }

        if (isset($data['statusId'])) {
            $order->setStatusId((string)$data['statusId']);
        }

        if (isset($data['billNotes'])) {
            $order->setBillNotes((string)$data['billNotes']);
        }

        if (isset($data['expiratedAt'])) {
            $order->setExpiratedAt(\is_string($data['expiratedAt']) ? new \DateTime($data['expiratedAt']) : $data['expiratedAt']);
        }

        if (isset($data['worker'])) {
            $order->setWorker((int)$data['worker']);
        }

        if (isset($data['createdAt'])) {
            $order->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $order->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $order->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $order->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['type.id'])) {
            $order->setTypeInstance((new PayrollTypeFactory())->make($this->getFkEntity('type.', $data)));
        }

        if (isset($data['customerId.id'])) {
            $order->setCustomerIdInstance((new CustomerFactory())->make($this->getFkEntity('customerId.', $data)));
        }

        if (isset($data['vehicleId.id'])) {
            $order->setVehicleIdInstance((new VehicleFactory())->make($this->getFkEntity('vehicleId.', $data)));
        }

        if (isset($data['statusId.id'])) {
            $order->setStatusIdInstance((new PayrollStatusFactory())->make($this->getFkEntity('statusId.', $data)));
        }

        if (isset($data['worker.id'])) {
            $order->setWorkerInstance((new WorkerFactory())->make($this->getFkEntity('worker.', $data)));
        }

        if (isset($data['createdBy.id'])) {
            $order->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $order->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $order;
    }
}
