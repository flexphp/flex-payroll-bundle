<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementType;

use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\PayrollBundle\Domain\User\UserFactory;

final class AgreementTypeFactory
{
    use FactoryExtendedTrait;

    public function make($data): AgreementType
    {
        $agreementType = new AgreementType();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $agreementType->setId((int)$data['id']);
        }

        if (isset($data['name'])) {
            $agreementType->setName((string)$data['name']);
        }

        if (isset($data['code'])) {
            $agreementType->setCode((string)$data['code']);
        }

        if (isset($data['isActive'])) {
            $agreementType->setIsActive((bool)$data['isActive']);
        }

        if (isset($data['createdAt'])) {
            $agreementType->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $agreementType->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $agreementType->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $agreementType->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['createdBy.id'])) {
            $agreementType->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $agreementType->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $agreementType;
    }
}
