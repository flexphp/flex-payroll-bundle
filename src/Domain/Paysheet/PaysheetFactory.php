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

use FlexPHP\Bundle\PayrollBundle\Domain\Employee\EmployeeFactory;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\PaysheetStatusFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollTypeFactory;
use FlexPHP\Bundle\UserBundle\Domain\User\UserFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\AgreementFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\Worker\WorkerFactory;

final class PaysheetFactory
{
    use FactoryExtendedTrait;

    public function make($data): Paysheet
    {
        $paysheet = new Paysheet();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $paysheet->setId((int)$data['id']);
        }

        if (isset($data['type'])) {
            $paysheet->setType((string)$data['type']);
        }

        if (isset($data['employeeId'])) {
            $paysheet->setEmployeeId((int)$data['employeeId']);
        }

        if (isset($data['agreementId'])) {
            $paysheet->setAgreementId((int)$data['agreementId']);
        }

        if (isset($data['subtotal'])) {
            $paysheet->setSubtotal((string)$data['subtotal']);
        }

        if (isset($data['taxes'])) {
            $paysheet->setTaxes((string)$data['taxes']);
        }

        if (isset($data['total'])) {
            $paysheet->setTotal((string)$data['total']);
        }

        if (isset($data['notes'])) {
            $paysheet->setNotes((string)$data['notes']);
        }

        if (isset($data['totalPaid'])) {
            $paysheet->setTotalPaid((string)$data['totalPaid']);
        }

        if (isset($data['paidAt'])) {
            $paysheet->setPaidAt(\is_string($data['paidAt']) ? new \DateTime($data['paidAt']) : $data['paidAt']);
        }

        if (isset($data['statusId'])) {
            $paysheet->setStatusId((string)$data['statusId']);
        }

        if (isset($data['paysheetNotes'])) {
            $paysheet->setPaysheetNotes((string)$data['paysheetNotes']);
        }

        if (isset($data['issuedAt'])) {
            $paysheet->setIssuedAt(\is_string($data['issuedAt']) ? new \DateTime($data['issuedAt']) : $data['issuedAt']);
        }

        if (isset($data['initAt'])) {
            $paysheet->setInitAt(\is_string($data['initAt']) ? new \DateTime($data['initAt']) : $data['initAt']);
        }

        if (isset($data['finishAt'])) {
            $paysheet->setFinishAt(\is_string($data['finishAt']) ? new \DateTime($data['finishAt']) : $data['finishAt']);
        }

        if (isset($data['details'])) {
            $paysheet->setDetails($data['details']);
        }

        if (isset($data['createdAt'])) {
            $paysheet->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $paysheet->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $paysheet->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $paysheet->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['type.id'])) {
            $paysheet->setTypeInstance((new PayrollTypeFactory())->make($this->getFkEntity('type.', $data)));
        }

        if (isset($data['employeeId.id'])) {
            $paysheet->setEmployeeIdInstance((new EmployeeFactory())->make($this->getFkEntity('employeeId.', $data)));
        }

        if (isset($data['agreementId.id'])) {
            $paysheet->setAgreementIdInstance((new AgreementFactory())->make($this->getFkEntity('agreementId.', $data)));
        }

        if (isset($data['statusId.id'])) {
            $paysheet->setStatusIdInstance((new PaysheetStatusFactory())->make($this->getFkEntity('statusId.', $data)));
        }

        if (isset($data['createdBy.id'])) {
            $paysheet->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $paysheet->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $paysheet;
    }
}
