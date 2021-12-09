<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollTypeGateway;

class MySQLPayrollTypeGateway implements PayrollTypeGateway
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
            'payrollType.Id as id',
            'payrollType.Name as name',
            'payrollType.IsActive as isActive',
        ]);
        $query->from('`PayrollTypes`', '`payrollType`');

        $query->orderBy('payrollType.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('payrollType', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(PayrollType $payrollType): string
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`PayrollTypes`');

        $query->setValue('Id', ':id');
        $query->setValue('Name', ':name');
        $query->setValue('IsActive', ':isActive');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':id', $payrollType->id(), DB::STRING);
        $query->setParameter(':name', $payrollType->name(), DB::STRING);
        $query->setParameter(':isActive', $payrollType->isActive(), DB::BOOLEAN);
        $query->setParameter(':createdAt', $payrollType->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $payrollType->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $payrollType->createdBy(), DB::INTEGER);

        $query->execute();

        return $payrollType->id();
    }

    public function get(PayrollType $payrollType): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'payrollType.Id as id',
            'payrollType.Name as name',
            'payrollType.IsActive as isActive',
            'payrollType.CreatedAt as createdAt',
            'payrollType.UpdatedAt as updatedAt',
            'payrollType.CreatedBy as createdBy',
            'payrollType.UpdatedBy as updatedBy',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`PayrollTypes`', '`payrollType`');
        $query->leftJoin('`payrollType`', '`Users`', '`createdBy`', 'payrollType.CreatedBy = createdBy.id');
        $query->leftJoin('`payrollType`', '`Users`', '`updatedBy`', 'payrollType.UpdatedBy = updatedBy.id');
        $query->where('payrollType.Id = :id');
        $query->setParameter(':id', $payrollType->id(), DB::STRING);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(PayrollType $payrollType): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`PayrollTypes`');

        $query->set('Id', ':id');
        $query->set('Name', ':name');
        $query->set('IsActive', ':isActive');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':id', $payrollType->id(), DB::STRING);
        $query->setParameter(':name', $payrollType->name(), DB::STRING);
        $query->setParameter(':isActive', $payrollType->isActive(), DB::BOOLEAN);
        $query->setParameter(':updatedAt', $payrollType->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $payrollType->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $payrollType->id(), DB::STRING);

        $query->execute();
    }

    public function pop(PayrollType $payrollType): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`PayrollTypes`');

        $query->where('Id = :id');
        $query->setParameter(':id', $payrollType->id(), DB::STRING);

        $query->execute();
    }
}
