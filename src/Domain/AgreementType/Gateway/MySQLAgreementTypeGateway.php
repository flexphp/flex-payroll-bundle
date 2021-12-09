<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\AgreementType;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\AgreementTypeGateway;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;

class MySQLAgreementTypeGateway implements AgreementTypeGateway
{
    private $conn;

    private $operator = [
        //
    ];

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'agreementType.Id as id',
            'agreementType.Name as name',
            'agreementType.Code as code',
            'agreementType.IsActive as isActive',
        ]);
        $query->from('`AgreementTypes`', '`agreementType`');

        $query->orderBy('agreementType.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('agreementType', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(AgreementType $agreementType): int
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`AgreementTypes`');

        $query->setValue('Name', ':name');
        $query->setValue('Code', ':code');
        $query->setValue('IsActive', ':isActive');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':name', $agreementType->name(), DB::STRING);
        $query->setParameter(':code', $agreementType->code(), DB::STRING);
        $query->setParameter(':isActive', $agreementType->isActive(), DB::BOOLEAN);
        $query->setParameter(':createdAt', $agreementType->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $agreementType->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $agreementType->createdBy(), DB::INTEGER);

        $query->execute();

        return (int)$query->getConnection()->lastInsertId();
    }

    public function get(AgreementType $agreementType): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'agreementType.Id as id',
            'agreementType.Name as name',
            'agreementType.Code as code',
            'agreementType.IsActive as isActive',
            'agreementType.CreatedAt as createdAt',
            'agreementType.UpdatedAt as updatedAt',
            'agreementType.CreatedBy as createdBy',
            'agreementType.UpdatedBy as updatedBy',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`AgreementTypes`', '`agreementType`');
        $query->leftJoin('`agreementType`', '`Users`', '`createdBy`', 'agreementType.CreatedBy = createdBy.id');
        $query->leftJoin('`agreementType`', '`Users`', '`updatedBy`', 'agreementType.UpdatedBy = updatedBy.id');
        $query->where('agreementType.Id = :id');
        $query->setParameter(':id', $agreementType->id(), DB::INTEGER);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(AgreementType $agreementType): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`AgreementTypes`');

        $query->set('Name', ':name');
        $query->set('Code', ':code');
        $query->set('IsActive', ':isActive');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':name', $agreementType->name(), DB::STRING);
        $query->setParameter(':code', $agreementType->code(), DB::STRING);
        $query->setParameter(':isActive', $agreementType->isActive(), DB::BOOLEAN);
        $query->setParameter(':updatedAt', $agreementType->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $agreementType->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $agreementType->id(), DB::INTEGER);

        $query->execute();
    }

    public function pop(AgreementType $agreementType): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`AgreementTypes`');

        $query->where('Id = :id');
        $query->setParameter(':id', $agreementType->id(), DB::INTEGER);

        $query->execute();
    }
}
