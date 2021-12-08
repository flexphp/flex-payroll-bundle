<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\EmployeeSubType;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\EmployeeSubTypeGateway;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;

class MySQLEmployeeSubTypeGateway implements EmployeeSubTypeGateway
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
            'employeeSubType.Id as id',
            'employeeSubType.Name as name',
            'employeeSubType.Code as code',
            'employeeSubType.IsActive as isActive',
        ]);
        $query->from('`EmployeeSubTypes`', '`employeeSubType`');

        $query->orderBy('employeeSubType.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('employeeSubType', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(EmployeeSubType $employeeSubType): int
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`EmployeeSubTypes`');

        $query->setValue('Name', ':name');
        $query->setValue('Code', ':code');
        $query->setValue('IsActive', ':isActive');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':name', $employeeSubType->name(), DB::STRING);
        $query->setParameter(':code', $employeeSubType->code(), DB::STRING);
        $query->setParameter(':isActive', $employeeSubType->isActive(), DB::BOOLEAN);
        $query->setParameter(':createdAt', $employeeSubType->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $employeeSubType->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $employeeSubType->createdBy(), DB::INTEGER);

        $query->execute();

        return (int)$query->getConnection()->lastInsertId();
    }

    public function get(EmployeeSubType $employeeSubType): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'employeeSubType.Id as id',
            'employeeSubType.Name as name',
            'employeeSubType.Code as code',
            'employeeSubType.IsActive as isActive',
            'employeeSubType.CreatedAt as createdAt',
            'employeeSubType.UpdatedAt as updatedAt',
            'employeeSubType.CreatedBy as createdBy',
            'employeeSubType.UpdatedBy as updatedBy',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`EmployeeSubTypes`', '`employeeSubType`');
        $query->leftJoin('`employeeSubType`', '`Users`', '`createdBy`', 'employeeSubType.CreatedBy = createdBy.id');
        $query->leftJoin('`employeeSubType`', '`Users`', '`updatedBy`', 'employeeSubType.UpdatedBy = updatedBy.id');
        $query->where('employeeSubType.Id = :id');
        $query->setParameter(':id', $employeeSubType->id(), DB::INTEGER);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(EmployeeSubType $employeeSubType): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`EmployeeSubTypes`');

        $query->set('Name', ':name');
        $query->set('Code', ':code');
        $query->set('IsActive', ':isActive');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':name', $employeeSubType->name(), DB::STRING);
        $query->setParameter(':code', $employeeSubType->code(), DB::STRING);
        $query->setParameter(':isActive', $employeeSubType->isActive(), DB::BOOLEAN);
        $query->setParameter(':updatedAt', $employeeSubType->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $employeeSubType->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $employeeSubType->id(), DB::INTEGER);

        $query->execute();
    }

    public function pop(EmployeeSubType $employeeSubType): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`EmployeeSubTypes`');

        $query->where('Id = :id');
        $query->setParameter(':id', $employeeSubType->id(), DB::INTEGER);

        $query->execute();
    }
}
