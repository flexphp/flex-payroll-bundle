<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\AgreementStatus;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\AgreementStatusGateway;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;

class MySQLAgreementStatusGateway implements AgreementStatusGateway
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
            'agreementStatus.Id as id',
            'agreementStatus.Name as name',
        ]);
        $query->from('`AgreementStatus`', '`agreementStatus`');

        $query->orderBy('agreementStatus.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('agreementStatus', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(AgreementStatus $agreementStatus): string
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`AgreementStatus`');

        $query->setValue('Id', ':id');
        $query->setValue('Name', ':name');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':id', $agreementStatus->id(), DB::STRING);
        $query->setParameter(':name', $agreementStatus->name(), DB::STRING);
        $query->setParameter(':createdAt', $agreementStatus->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $agreementStatus->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $agreementStatus->createdBy(), DB::INTEGER);

        $query->execute();

        return $agreementStatus->id();
    }

    public function get(AgreementStatus $agreementStatus): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'agreementStatus.Id as id',
            'agreementStatus.Name as name',
            'agreementStatus.CreatedAt as createdAt',
            'agreementStatus.UpdatedAt as updatedAt',
            'agreementStatus.CreatedBy as createdBy',
            'agreementStatus.UpdatedBy as updatedBy',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`AgreementStatus`', '`agreementStatus`');
        $query->leftJoin('`agreementStatus`', '`Users`', '`createdBy`', 'agreementStatus.CreatedBy = createdBy.id');
        $query->leftJoin('`agreementStatus`', '`Users`', '`updatedBy`', 'agreementStatus.UpdatedBy = updatedBy.id');
        $query->where('agreementStatus.Id = :id');
        $query->setParameter(':id', $agreementStatus->id(), DB::STRING);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(AgreementStatus $agreementStatus): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`AgreementStatus`');

        $query->set('Id', ':id');
        $query->set('Name', ':name');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':id', $agreementStatus->id(), DB::STRING);
        $query->setParameter(':name', $agreementStatus->name(), DB::STRING);
        $query->setParameter(':updatedAt', $agreementStatus->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $agreementStatus->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $agreementStatus->id(), DB::STRING);

        $query->execute();
    }

    public function pop(AgreementStatus $agreementStatus): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`AgreementStatus`');

        $query->where('Id = :id');
        $query->setParameter(':id', $agreementStatus->id(), DB::STRING);

        $query->execute();
    }
}
