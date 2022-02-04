<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus;

use FlexPHP\Bundle\HelperBundle\Domain\Helper\FactoryExtendedTrait;
use FlexPHP\Bundle\UserBundle\Domain\User\UserFactory;

final class PaysheetStatusFactory
{
    use FactoryExtendedTrait;

    public function make($data): PaysheetStatus
    {
        $paysheetStatus = new PaysheetStatus();

        if (\is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['id'])) {
            $paysheetStatus->setId((string)$data['id']);
        }

        if (isset($data['name'])) {
            $paysheetStatus->setName((string)$data['name']);
        }

        if (isset($data['createdAt'])) {
            $paysheetStatus->setCreatedAt(\is_string($data['createdAt']) ? new \DateTime($data['createdAt']) : $data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            $paysheetStatus->setUpdatedAt(\is_string($data['updatedAt']) ? new \DateTime($data['updatedAt']) : $data['updatedAt']);
        }

        if (isset($data['createdBy'])) {
            $paysheetStatus->setCreatedBy((int)$data['createdBy']);
        }

        if (isset($data['updatedBy'])) {
            $paysheetStatus->setUpdatedBy((int)$data['updatedBy']);
        }

        if (isset($data['createdBy.id'])) {
            $paysheetStatus->setCreatedByInstance((new UserFactory())->make($this->getFkEntity('createdBy.', $data)));
        }

        if (isset($data['updatedBy.id'])) {
            $paysheetStatus->setUpdatedByInstance((new UserFactory())->make($this->getFkEntity('updatedBy.', $data)));
        }

        return $paysheetStatus;
    }
}
