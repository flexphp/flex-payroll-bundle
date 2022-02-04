<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll;

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetFactory;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatusFactory;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollTypeFactory;
use FlexPHP\Bundle\NumerationBundle\Domain\Provider\ProviderFactory;
use FlexPHP\Bundle\UserBundle\Domain\User\UserFactory;

final class PayrollFactory
{
    use FactoryExtendedTrait;

    public function make($data): Payroll
    {
        $payroll = new Payroll();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $payroll->setId((int)$data['id']);
        }

        if (isset($data['prefix'])) {
            $payroll->setPrefix((string)$data['prefix']);
        }

        if (isset($data['number'])) {
            $payroll->setNumber((int)$data['number']);
        }

        if (isset($data['paysheet'])) {
            $payroll->setPaysheet((int)$data['paysheet']);
        }

        if (isset($data['provider'])) {
            $payroll->setProvider((string)$data['provider']);
        }

        if (isset($data['status'])) {
            $payroll->setStatus((string)$data['status']);
        }

        if (isset($data['type'])) {
            $payroll->setType((string)$data['type']);
        }

        if (isset($data['traceId'])) {
            $payroll->setTraceId((string)$data['traceId']);
        }

        if (isset($data['hash'])) {
            $payroll->setHash((string)$data['hash']);
        }

        if (isset($data['hashType'])) {
            $payroll->setHashType((string)$data['hashType']);
        }

        if (isset($data['message'])) {
            $payroll->setMessage((string)$data['message']);
        }

        if (isset($data['pdfPath'])) {
            $payroll->setPdfPath((string)$data['pdfPath']);
        }

        if (isset($data['xmlPath'])) {
            $payroll->setXmlPath((string)$data['xmlPath']);
        }

        if (isset($data['parentId'])) {
            $payroll->setParentId((int)$data['parentId']);
        }

        if (isset($data['downloadedAt'])) {
            $payroll->setDownloadedAt(\is_string($data['downloadedAt']) ? new \DateTime($data['downloadedAt']) : $data['downloadedAt']);
        }

        if (isset($data['createdAt'])) {
            $payroll->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $payroll->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $payroll->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $payroll->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['paysheet.id'])) {
            $payroll->setPaysheetInstance((new PaysheetFactory())->make($this->getFkEntity('paysheet.', $data)));
        }

        if (isset($data['provider.id'])) {
            $payroll->setProviderInstance((new ProviderFactory())->make($this->getFkEntity('provider.', $data)));
        }

        if (isset($data['status.id'])) {
            $payroll->setStatusInstance((new PayrollStatusFactory())->make($this->getFkEntity('status.', $data)));
        }

        if (isset($data['type.id'])) {
            $payroll->setTypeInstance((new PayrollTypeFactory())->make($this->getFkEntity('type.', $data)));
        }

        if (isset($data['parentId.id'])) {
            $payroll->setParentIdInstance((new PayrollFactory())->make($this->getFkEntity('parentId.', $data)));
        }

        if (isset($data['createdBy.id'])) {
            $payroll->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $payroll->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $payroll;
    }
}
