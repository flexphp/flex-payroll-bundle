<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\AccountType;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\AccountTypeGateway;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;

class MySQLAccountTypeGateway implements AccountTypeGateway
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
            'accountType.Id as id',
            'accountType.Name as name',
            'accountType.IsActive as isActive',
        ]);
        $query->from('`AccountTypes`', '`accountType`');

        $query->orderBy('accountType.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('accountType', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(AccountType $accountType): string
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`AccountTypes`');

        $query->setValue('Id', ':id');
        $query->setValue('Name', ':name');
        $query->setValue('IsActive', ':isActive');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':id', $accountType->id(), DB::STRING);
        $query->setParameter(':name', $accountType->name(), DB::STRING);
        $query->setParameter(':isActive', $accountType->isActive(), DB::BOOLEAN);
        $query->setParameter(':createdAt', $accountType->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $accountType->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $accountType->createdBy(), DB::INTEGER);

        $query->execute();

        return $accountType->id();
    }

    public function get(AccountType $accountType): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'accountType.Id as id',
            'accountType.Name as name',
            'accountType.IsActive as isActive',
            'accountType.CreatedAt as createdAt',
            'accountType.UpdatedAt as updatedAt',
            'accountType.CreatedBy as createdBy',
            'accountType.UpdatedBy as updatedBy',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`AccountTypes`', '`accountType`');
        $query->leftJoin('`accountType`', '`Users`', '`createdBy`', 'accountType.CreatedBy = createdBy.id');
        $query->leftJoin('`accountType`', '`Users`', '`updatedBy`', 'accountType.UpdatedBy = updatedBy.id');
        $query->where('accountType.Id = :id');
        $query->setParameter(':id', $accountType->id(), DB::STRING);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(AccountType $accountType): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`AccountTypes`');

        $query->set('Id', ':id');
        $query->set('Name', ':name');
        $query->set('IsActive', ':isActive');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':id', $accountType->id(), DB::STRING);
        $query->setParameter(':name', $accountType->name(), DB::STRING);
        $query->setParameter(':isActive', $accountType->isActive(), DB::BOOLEAN);
        $query->setParameter(':updatedAt', $accountType->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $accountType->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $accountType->id(), DB::STRING);

        $query->execute();
    }

    public function pop(AccountType $accountType): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`AccountTypes`');

        $query->where('Id = :id');
        $query->setParameter(':id', $accountType->id(), DB::STRING);

        $query->execute();
    }
}
