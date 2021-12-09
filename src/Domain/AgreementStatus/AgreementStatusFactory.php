<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus;

use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\UserBundle\Domain\User\UserFactory;

final class AgreementStatusFactory
{
    use FactoryExtendedTrait;

    public function make($data): AgreementStatus
    {
        $agreementStatus = new AgreementStatus();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $agreementStatus->setId((string)$data['id']);
        }

        if (isset($data['name'])) {
            $agreementStatus->setName((string)$data['name']);
        }

        if (isset($data['createdAt'])) {
            $agreementStatus->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $agreementStatus->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $agreementStatus->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $agreementStatus->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['createdBy.id'])) {
            $agreementStatus->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $agreementStatus->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $agreementStatus;
    }
}
