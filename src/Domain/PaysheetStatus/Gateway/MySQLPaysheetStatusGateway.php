<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\PaysheetStatus;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\PaysheetStatusGateway;

class MySQLPaysheetStatusGateway implements PaysheetStatusGateway
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
            'paysheetStatus.Id as id',
            'paysheetStatus.Name as name',
        ]);
        $query->from('`PaysheetStatus`', '`paysheetStatus`');

        $query->orderBy('paysheetStatus.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('paysheetStatus', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(PaysheetStatus $paysheetStatus): string
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`PaysheetStatus`');

        $query->setValue('Id', ':id');
        $query->setValue('Name', ':name');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':id', $paysheetStatus->id(), DB::STRING);
        $query->setParameter(':name', $paysheetStatus->name(), DB::STRING);
        $query->setParameter(':createdAt', $paysheetStatus->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $paysheetStatus->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $paysheetStatus->createdBy(), DB::INTEGER);

        $query->execute();

        return $paysheetStatus->id();
    }

    public function get(PaysheetStatus $paysheetStatus): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'paysheetStatus.Id as id',
            'paysheetStatus.Name as name',
            'paysheetStatus.CreatedAt as createdAt',
            'paysheetStatus.UpdatedAt as updatedAt',
            'paysheetStatus.CreatedBy as createdBy',
            'paysheetStatus.UpdatedBy as updatedBy',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`PaysheetStatus`', '`paysheetStatus`');
        $query->leftJoin('`paysheetStatus`', '`Users`', '`createdBy`', 'paysheetStatus.CreatedBy = createdBy.id');
        $query->leftJoin('`paysheetStatus`', '`Users`', '`updatedBy`', 'paysheetStatus.UpdatedBy = updatedBy.id');
        $query->where('paysheetStatus.Id = :id');
        $query->setParameter(':id', $paysheetStatus->id(), DB::STRING);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(PaysheetStatus $paysheetStatus): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`PaysheetStatus`');

        $query->set('Id', ':id');
        $query->set('Name', ':name');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':id', $paysheetStatus->id(), DB::STRING);
        $query->setParameter(':name', $paysheetStatus->name(), DB::STRING);
        $query->setParameter(':updatedAt', $paysheetStatus->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $paysheetStatus->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $paysheetStatus->id(), DB::STRING);

        $query->execute();
    }

    public function pop(PaysheetStatus $paysheetStatus): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`PaysheetStatus`');

        $query->where('Id = :id');
        $query->setParameter(':id', $paysheetStatus->id(), DB::STRING);

        $query->execute();
    }
}
