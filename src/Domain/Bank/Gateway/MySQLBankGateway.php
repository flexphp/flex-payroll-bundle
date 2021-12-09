<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Bank\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Bank;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\BankGateway;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;

class MySQLBankGateway implements BankGateway
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
            'bank.Id as id',
            'bank.Name as name',
            'bank.IsActive as isActive',
        ]);
        $query->from('`Banks`', '`bank`');

        $query->orderBy('bank.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('bank', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(Bank $bank): int
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`Banks`');

        $query->setValue('Name', ':name');
        $query->setValue('IsActive', ':isActive');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':name', $bank->name(), DB::STRING);
        $query->setParameter(':isActive', $bank->isActive(), DB::BOOLEAN);
        $query->setParameter(':createdAt', $bank->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $bank->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $bank->createdBy(), DB::INTEGER);

        $query->execute();

        return (int)$query->getConnection()->lastInsertId();
    }

    public function get(Bank $bank): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'bank.Id as id',
            'bank.Name as name',
            'bank.IsActive as isActive',
            'bank.CreatedAt as createdAt',
            'bank.UpdatedAt as updatedAt',
            'bank.CreatedBy as createdBy',
            'bank.UpdatedBy as updatedBy',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`Banks`', '`bank`');
        $query->leftJoin('`bank`', '`Users`', '`createdBy`', 'bank.CreatedBy = createdBy.id');
        $query->leftJoin('`bank`', '`Users`', '`updatedBy`', 'bank.UpdatedBy = updatedBy.id');
        $query->where('bank.Id = :id');
        $query->setParameter(':id', $bank->id(), DB::INTEGER);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(Bank $bank): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`Banks`');

        $query->set('Name', ':name');
        $query->set('IsActive', ':isActive');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':name', $bank->name(), DB::STRING);
        $query->setParameter(':isActive', $bank->isActive(), DB::BOOLEAN);
        $query->setParameter(':updatedAt', $bank->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $bank->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $bank->id(), DB::INTEGER);

        $query->execute();
    }

    public function pop(Bank $bank): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`Banks`');

        $query->where('Id = :id');
        $query->setParameter(':id', $bank->id(), DB::INTEGER);

        $query->execute();
    }
}
