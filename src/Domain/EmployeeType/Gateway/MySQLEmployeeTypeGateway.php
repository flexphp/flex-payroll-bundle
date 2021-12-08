<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\EmployeeType;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\EmployeeTypeGateway;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;

class MySQLEmployeeTypeGateway implements EmployeeTypeGateway
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
            'employeeType.Id as id',
            'employeeType.Name as name',
            'employeeType.Code as code',
            'employeeType.IsActive as isActive',
        ]);
        $query->from('`EmployeeTypes`', '`employeeType`');

        $query->orderBy('employeeType.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('employeeType', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(EmployeeType $employeeType): int
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`EmployeeTypes`');

        $query->setValue('Name', ':name');
        $query->setValue('Code', ':code');
        $query->setValue('IsActive', ':isActive');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':name', $employeeType->name(), DB::STRING);
        $query->setParameter(':code', $employeeType->code(), DB::STRING);
        $query->setParameter(':isActive', $employeeType->isActive(), DB::BOOLEAN);
        $query->setParameter(':createdAt', $employeeType->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $employeeType->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $employeeType->createdBy(), DB::INTEGER);

        $query->execute();

        return (int)$query->getConnection()->lastInsertId();
    }

    public function get(EmployeeType $employeeType): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'employeeType.Id as id',
            'employeeType.Name as name',
            'employeeType.Code as code',
            'employeeType.IsActive as isActive',
            'employeeType.CreatedAt as createdAt',
            'employeeType.UpdatedAt as updatedAt',
            'employeeType.CreatedBy as createdBy',
            'employeeType.UpdatedBy as updatedBy',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`EmployeeTypes`', '`employeeType`');
        $query->leftJoin('`employeeType`', '`Users`', '`createdBy`', 'employeeType.CreatedBy = createdBy.id');
        $query->leftJoin('`employeeType`', '`Users`', '`updatedBy`', 'employeeType.UpdatedBy = updatedBy.id');
        $query->where('employeeType.Id = :id');
        $query->setParameter(':id', $employeeType->id(), DB::INTEGER);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(EmployeeType $employeeType): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`EmployeeTypes`');

        $query->set('Name', ':name');
        $query->set('Code', ':code');
        $query->set('IsActive', ':isActive');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':name', $employeeType->name(), DB::STRING);
        $query->setParameter(':code', $employeeType->code(), DB::STRING);
        $query->setParameter(':isActive', $employeeType->isActive(), DB::BOOLEAN);
        $query->setParameter(':updatedAt', $employeeType->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $employeeType->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $employeeType->id(), DB::INTEGER);

        $query->execute();
    }

    public function pop(EmployeeType $employeeType): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`EmployeeTypes`');

        $query->where('Id = :id');
        $query->setParameter(':id', $employeeType->id(), DB::INTEGER);

        $query->execute();
    }
}
