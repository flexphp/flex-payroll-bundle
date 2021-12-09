<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\AgreementPeriod;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\AgreementPeriodGateway;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;

class MySQLAgreementPeriodGateway implements AgreementPeriodGateway
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
            'agreementPeriod.Id as id',
            'agreementPeriod.Name as name',
        ]);
        $query->from('`AgreementPeriods`', '`agreementPeriod`');

        $query->orderBy('agreementPeriod.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('agreementPeriod', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(AgreementPeriod $agreementPeriod): string
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`AgreementPeriods`');

        $query->setValue('Id', ':id');
        $query->setValue('Name', ':name');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':id', $agreementPeriod->id(), DB::STRING);
        $query->setParameter(':name', $agreementPeriod->name(), DB::STRING);
        $query->setParameter(':createdAt', $agreementPeriod->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $agreementPeriod->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $agreementPeriod->createdBy(), DB::INTEGER);

        $query->execute();

        return $agreementPeriod->id();
    }

    public function get(AgreementPeriod $agreementPeriod): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'agreementPeriod.Id as id',
            'agreementPeriod.Name as name',
            'agreementPeriod.CreatedAt as createdAt',
            'agreementPeriod.UpdatedAt as updatedAt',
            'agreementPeriod.CreatedBy as createdBy',
            'agreementPeriod.UpdatedBy as updatedBy',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`AgreementPeriods`', '`agreementPeriod`');
        $query->leftJoin('`agreementPeriod`', '`Users`', '`createdBy`', 'agreementPeriod.CreatedBy = createdBy.id');
        $query->leftJoin('`agreementPeriod`', '`Users`', '`updatedBy`', 'agreementPeriod.UpdatedBy = updatedBy.id');
        $query->where('agreementPeriod.Id = :id');
        $query->setParameter(':id', $agreementPeriod->id(), DB::STRING);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(AgreementPeriod $agreementPeriod): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`AgreementPeriods`');

        $query->set('Id', ':id');
        $query->set('Name', ':name');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':id', $agreementPeriod->id(), DB::STRING);
        $query->setParameter(':name', $agreementPeriod->name(), DB::STRING);
        $query->setParameter(':updatedAt', $agreementPeriod->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $agreementPeriod->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $agreementPeriod->id(), DB::STRING);

        $query->execute();
    }

    public function pop(AgreementPeriod $agreementPeriod): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`AgreementPeriods`');

        $query->where('Id = :id');
        $query->setParameter(':id', $agreementPeriod->id(), DB::STRING);

        $query->execute();
    }
}
