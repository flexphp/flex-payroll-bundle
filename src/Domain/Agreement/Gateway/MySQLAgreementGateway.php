<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Agreement;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\AgreementGateway;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementCurrencyRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementEmployeeRequest;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;

class MySQLAgreementGateway implements AgreementGateway
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
            'agreement.Id as id',
            'agreement.Name as name',
            'agreement.Employee as employee',
            'agreement.Type as type',
            'agreement.Period as period',
            'agreement.Currency as currency',
            'agreement.Salary as salary',
            'agreement.HealthPercentage as healthPercentage',
            'agreement.PensionPercentage as pensionPercentage',
            'agreement.IntegralSalary as integralSalary',
            'agreement.HighRisk as highRisk',
            'agreement.InitAt as initAt',
            'agreement.FinishAt as finishAt',
            'agreement.Status as status',
            'employee.id as `employee.id`',
            'employee.documentNumber as `employee.documentNumber`',
            'employee.firstName as `employee.firstName`',
            'employee.firstSurname as `employee.firstSurname`',
            'type.id as `type.id`',
            'type.name as `type.name`',
            'period.id as `period.id`',
            'period.name as `period.name`',
            'currency.id as `currency.id`',
            'currency.name as `currency.name`',
            'status.id as `status.id`',
            'status.name as `status.name`',
        ]);
        $query->from('`Agreements`', '`agreement`');
        $query->join('`agreement`', '`Employees`', '`employee`', 'agreement.Employee = employee.id');
        $query->join('`agreement`', '`AgreementTypes`', '`type`', 'agreement.Type = type.id');
        $query->join('`agreement`', '`AgreementPeriods`', '`period`', 'agreement.Period = period.id');
        $query->join('`agreement`', '`Currencies`', '`currency`', 'agreement.Currency = currency.id');
        $query->join('`agreement`', '`AgreementStatus`', '`status`', 'agreement.Status = status.id');

        $query->orderBy('agreement.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('agreement', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(Agreement $agreement): int
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`Agreements`');

        $query->setValue('Name', ':name');
        $query->setValue('Employee', ':employee');
        $query->setValue('Type', ':type');
        $query->setValue('Period', ':period');
        $query->setValue('Currency', ':currency');
        $query->setValue('Salary', ':salary');
        $query->setValue('HealthPercentage', ':healthPercentage');
        $query->setValue('PensionPercentage', ':pensionPercentage');
        $query->setValue('IntegralSalary', ':integralSalary');
        $query->setValue('HighRisk', ':highRisk');
        $query->setValue('InitAt', ':initAt');
        $query->setValue('FinishAt', ':finishAt');
        $query->setValue('Status', ':status');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':name', $agreement->name(), DB::STRING);
        $query->setParameter(':employee', $agreement->employee(), DB::INTEGER);
        $query->setParameter(':type', $agreement->type(), DB::INTEGER);
        $query->setParameter(':period', $agreement->period(), DB::STRING);
        $query->setParameter(':currency', $agreement->currency(), DB::STRING);
        $query->setParameter(':salary', $agreement->salary(), DB::STRING);
        $query->setParameter(':healthPercentage', $agreement->healthPercentage(), DB::INTEGER);
        $query->setParameter(':pensionPercentage', $agreement->pensionPercentage(), DB::INTEGER);
        $query->setParameter(':integralSalary', $agreement->integralSalary(), DB::BOOLEAN);
        $query->setParameter(':highRisk', $agreement->highRisk(), DB::BOOLEAN);
        $query->setParameter(':initAt', $agreement->initAt(), DB::DATETIME_MUTABLE);
        $query->setParameter(':finishAt', $agreement->finishAt(), DB::DATETIME_MUTABLE);
        $query->setParameter(':status', $agreement->status(), DB::STRING);
        $query->setParameter(':createdAt', $agreement->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $agreement->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $agreement->createdBy(), DB::INTEGER);

        $query->execute();

        return (int)$query->getConnection()->lastInsertId();
    }

    public function get(Agreement $agreement): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'agreement.Id as id',
            'agreement.Name as name',
            'agreement.Employee as employee',
            'agreement.Type as type',
            'agreement.Period as period',
            'agreement.Currency as currency',
            'agreement.Salary as salary',
            'agreement.HealthPercentage as healthPercentage',
            'agreement.PensionPercentage as pensionPercentage',
            'agreement.IntegralSalary as integralSalary',
            'agreement.HighRisk as highRisk',
            'agreement.InitAt as initAt',
            'agreement.FinishAt as finishAt',
            'agreement.Status as status',
            'agreement.CreatedAt as createdAt',
            'agreement.UpdatedAt as updatedAt',
            'agreement.CreatedBy as createdBy',
            'agreement.UpdatedBy as updatedBy',
            'employee.id as `employee.id`',
            'employee.documentNumber as `employee.documentNumber`',
            'type.id as `type.id`',
            'type.name as `type.name`',
            'type.code as `type.code`',
            'period.id as `period.id`',
            'period.name as `period.name`',
            'currency.id as `currency.id`',
            'currency.name as `currency.name`',
            'status.id as `status.id`',
            'status.name as `status.name`',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`Agreements`', '`agreement`');
        $query->join('`agreement`', '`Employees`', '`employee`', 'agreement.Employee = employee.id');
        $query->join('`agreement`', '`AgreementTypes`', '`type`', 'agreement.Type = type.id');
        $query->join('`agreement`', '`AgreementPeriods`', '`period`', 'agreement.Period = period.id');
        $query->join('`agreement`', '`Currencies`', '`currency`', 'agreement.Currency = currency.id');
        $query->join('`agreement`', '`AgreementStatus`', '`status`', 'agreement.Status = status.id');
        $query->leftJoin('`agreement`', '`Users`', '`createdBy`', 'agreement.CreatedBy = createdBy.id');
        $query->leftJoin('`agreement`', '`Users`', '`updatedBy`', 'agreement.UpdatedBy = updatedBy.id');
        $query->where('agreement.Id = :id');
        $query->setParameter(':id', $agreement->id(), DB::INTEGER);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(Agreement $agreement): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`Agreements`');

        $query->set('Name', ':name');
        $query->set('Employee', ':employee');
        $query->set('Type', ':type');
        $query->set('Period', ':period');
        $query->set('Currency', ':currency');
        $query->set('Salary', ':salary');
        $query->set('HealthPercentage', ':healthPercentage');
        $query->set('PensionPercentage', ':pensionPercentage');
        $query->set('IntegralSalary', ':integralSalary');
        $query->set('HighRisk', ':highRisk');
        $query->set('InitAt', ':initAt');
        $query->set('FinishAt', ':finishAt');
        $query->set('Status', ':status');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':name', $agreement->name(), DB::STRING);
        $query->setParameter(':employee', $agreement->employee(), DB::INTEGER);
        $query->setParameter(':type', $agreement->type(), DB::INTEGER);
        $query->setParameter(':period', $agreement->period(), DB::STRING);
        $query->setParameter(':currency', $agreement->currency(), DB::STRING);
        $query->setParameter(':salary', $agreement->salary(), DB::STRING);
        $query->setParameter(':healthPercentage', $agreement->healthPercentage(), DB::INTEGER);
        $query->setParameter(':pensionPercentage', $agreement->pensionPercentage(), DB::INTEGER);
        $query->setParameter(':integralSalary', $agreement->integralSalary(), DB::BOOLEAN);
        $query->setParameter(':highRisk', $agreement->highRisk(), DB::BOOLEAN);
        $query->setParameter(':initAt', $agreement->initAt(), DB::DATETIME_MUTABLE);
        $query->setParameter(':finishAt', $agreement->finishAt(), DB::DATETIME_MUTABLE);
        $query->setParameter(':status', $agreement->status(), DB::STRING);
        $query->setParameter(':updatedAt', $agreement->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $agreement->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $agreement->id(), DB::INTEGER);

        $query->execute();
    }

    public function pop(Agreement $agreement): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`Agreements`');

        $query->where('Id = :id');
        $query->setParameter(':id', $agreement->id(), DB::INTEGER);

        $query->execute();
    }

    public function filterEmployees(FindAgreementEmployeeRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $term = \preg_replace('/(\s)+/', '%', \trim($request->term));

        $query->select([
            'employee.id as id',
            "CONCAT(employee.DocumentNumber, ' - ', employee.FirstName, ' ', employee.FirstSurname) as text",
        ]);
        $query->from('`Employees`', '`employee`');

        $query->where("CONCAT(employee.DocumentNumber, ' ', employee.FirstName, ' ', employee.FirstSurname) like :employee_name");
        $query->setParameter(':employee_name', "%{$term}%");
        $query->andWhere('employee.IsActive = :employee_isActive');
        $query->setParameter(':employee_isActive', 1);

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterAgreementTypes(FindAgreementAgreementTypeRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'agreementType.id as id',
            'agreementType.name as text',
        ]);
        $query->from('`AgreementTypes`', '`agreementType`');

        $query->where('agreementType.name like :agreementType_name');
        $query->setParameter(':agreementType_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterAgreementPeriods(FindAgreementAgreementPeriodRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'agreementPeriod.id as id',
            'agreementPeriod.name as text',
        ]);
        $query->from('`AgreementPeriods`', '`agreementPeriod`');

        $query->where('agreementPeriod.name like :agreementPeriod_name');
        $query->setParameter(':agreementPeriod_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterCurrencies(FindAgreementCurrencyRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'currency.id as id',
            'currency.name as text',
        ]);
        $query->from('`Currencies`', '`currency`');

        $query->where('currency.name like :currency_name');
        $query->setParameter(':currency_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterAgreementStatus(FindAgreementAgreementStatusRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'agreementStatus.id as id',
            'agreementStatus.name as text',
        ]);
        $query->from('`AgreementStatus`', '`agreementStatus`');

        $query->where('agreementStatus.name like :agreementStatus_name');
        $query->setParameter(':agreementStatus_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }
}
