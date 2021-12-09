<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod;

use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\UserBundle\Domain\User\UserFactory;

final class AgreementPeriodFactory
{
    use FactoryExtendedTrait;

    public function make($data): AgreementPeriod
    {
        $agreementPeriod = new AgreementPeriod();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $agreementPeriod->setId((string)$data['id']);
        }

        if (isset($data['name'])) {
            $agreementPeriod->setName((string)$data['name']);
        }

        if (isset($data['createdAt'])) {
            $agreementPeriod->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $agreementPeriod->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $agreementPeriod->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $agreementPeriod->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['createdBy.id'])) {
            $agreementPeriod->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $agreementPeriod->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $agreementPeriod;
    }
}
