<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Employee;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\EmployeeGateway;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeDocumentTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeePaymentMethodRequest;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;

class MySQLEmployeeGateway implements EmployeeGateway
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
            'employee.Id as id',
            'employee.DocumentTypeId as documentTypeId',
            'employee.DocumentNumber as documentNumber',
            'employee.FirstName as firstName',
            'employee.SecondName as secondName',
            'employee.FirstSurname as firstSurname',
            'employee.SecondSurname as secondSurname',
            'employee.Type as type',
            'employee.SubType as subType',
            'employee.PaymentMethod as paymentMethod',
            'employee.AccountType as accountType',
            'employee.AccountNumber as accountNumber',
            'employee.IsActive as isActive',
            'documentTypeId.id as `documentTypeId.id`',
            'documentTypeId.name as `documentTypeId.name`',
            'type.id as `type.id`',
            'type.name as `type.name`',
            'subType.id as `subType.id`',
            'subType.name as `subType.name`',
            'paymentMethod.id as `paymentMethod.id`',
            'paymentMethod.name as `paymentMethod.name`',
            'accountType.id as `accountType.id`',
            'accountType.name as `accountType.name`',
        ]);
        $query->from('`Employees`', '`employee`');
        $query->join('`employee`', '`DocumentTypes`', '`documentTypeId`', 'employee.DocumentTypeId = documentTypeId.id');
        $query->join('`employee`', '`EmployeeTypes`', '`type`', 'employee.Type = type.id');
        $query->join('`employee`', '`EmployeeSubTypes`', '`subType`', 'employee.SubType = subType.id');
        $query->join('`employee`', '`PaymentMethods`', '`paymentMethod`', 'employee.PaymentMethod = paymentMethod.id');
        $query->join('`employee`', '`AccountTypes`', '`accountType`', 'employee.AccountType = accountType.id');

        $query->orderBy('employee.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('employee', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(Employee $employee): int
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`Employees`');

        $query->setValue('DocumentTypeId', ':documentTypeId');
        $query->setValue('DocumentNumber', ':documentNumber');
        $query->setValue('FirstName', ':firstName');
        $query->setValue('SecondName', ':secondName');
        $query->setValue('FirstSurname', ':firstSurname');
        $query->setValue('SecondSurname', ':secondSurname');
        $query->setValue('Type', ':type');
        $query->setValue('SubType', ':subType');
        $query->setValue('PaymentMethod', ':paymentMethod');
        $query->setValue('AccountType', ':accountType');
        $query->setValue('AccountNumber', ':accountNumber');
        $query->setValue('IsActive', ':isActive');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':documentTypeId', $employee->documentTypeId(), DB::STRING);
        $query->setParameter(':documentNumber', $employee->documentNumber(), DB::STRING);
        $query->setParameter(':firstName', $employee->firstName(), DB::STRING);
        $query->setParameter(':secondName', $employee->secondName(), DB::STRING);
        $query->setParameter(':firstSurname', $employee->firstSurname(), DB::STRING);
        $query->setParameter(':secondSurname', $employee->secondSurname(), DB::STRING);
        $query->setParameter(':type', $employee->type(), DB::INTEGER);
        $query->setParameter(':subType', $employee->subType(), DB::INTEGER);
        $query->setParameter(':paymentMethod', $employee->paymentMethod(), DB::STRING);
        $query->setParameter(':accountType', $employee->accountType(), DB::STRING);
        $query->setParameter(':accountNumber', $employee->accountNumber(), DB::STRING);
        $query->setParameter(':isActive', $employee->isActive(), DB::BOOLEAN);
        $query->setParameter(':createdAt', $employee->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $employee->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $employee->createdBy(), DB::INTEGER);

        $query->execute();

        return (int)$query->getConnection()->lastInsertId();
    }

    public function get(Employee $employee): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'employee.Id as id',
            'employee.DocumentTypeId as documentTypeId',
            'employee.DocumentNumber as documentNumber',
            'employee.FirstName as firstName',
            'employee.SecondName as secondName',
            'employee.FirstSurname as firstSurname',
            'employee.SecondSurname as secondSurname',
            'employee.Type as type',
            'employee.SubType as subType',
            'employee.PaymentMethod as paymentMethod',
            'employee.AccountType as accountType',
            'employee.AccountNumber as accountNumber',
            'employee.IsActive as isActive',
            'employee.CreatedAt as createdAt',
            'employee.UpdatedAt as updatedAt',
            'employee.CreatedBy as createdBy',
            'employee.UpdatedBy as updatedBy',
            'documentTypeId.id as `documentTypeId.id`',
            'documentTypeId.name as `documentTypeId.name`',
            'type.id as `type.id`',
            'type.name as `type.name`',
            'subType.id as `subType.id`',
            'subType.name as `subType.name`',
            'paymentMethod.id as `paymentMethod.id`',
            'paymentMethod.name as `paymentMethod.name`',
            'accountType.id as `accountType.id`',
            'accountType.name as `accountType.name`',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`Employees`', '`employee`');
        $query->join('`employee`', '`DocumentTypes`', '`documentTypeId`', 'employee.DocumentTypeId = documentTypeId.id');
        $query->join('`employee`', '`EmployeeTypes`', '`type`', 'employee.Type = type.id');
        $query->join('`employee`', '`EmployeeSubTypes`', '`subType`', 'employee.SubType = subType.id');
        $query->join('`employee`', '`PaymentMethods`', '`paymentMethod`', 'employee.PaymentMethod = paymentMethod.id');
        $query->join('`employee`', '`AccountTypes`', '`accountType`', 'employee.AccountType = accountType.id');
        $query->leftJoin('`employee`', '`Users`', '`createdBy`', 'employee.CreatedBy = createdBy.id');
        $query->leftJoin('`employee`', '`Users`', '`updatedBy`', 'employee.UpdatedBy = updatedBy.id');
        $query->where('employee.Id = :id');
        $query->setParameter(':id', $employee->id(), DB::INTEGER);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(Employee $employee): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`Employees`');

        $query->set('DocumentTypeId', ':documentTypeId');
        $query->set('DocumentNumber', ':documentNumber');
        $query->set('FirstName', ':firstName');
        $query->set('SecondName', ':secondName');
        $query->set('FirstSurname', ':firstSurname');
        $query->set('SecondSurname', ':secondSurname');
        $query->set('Type', ':type');
        $query->set('SubType', ':subType');
        $query->set('PaymentMethod', ':paymentMethod');
        $query->set('AccountType', ':accountType');
        $query->set('AccountNumber', ':accountNumber');
        $query->set('IsActive', ':isActive');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':documentTypeId', $employee->documentTypeId(), DB::STRING);
        $query->setParameter(':documentNumber', $employee->documentNumber(), DB::STRING);
        $query->setParameter(':firstName', $employee->firstName(), DB::STRING);
        $query->setParameter(':secondName', $employee->secondName(), DB::STRING);
        $query->setParameter(':firstSurname', $employee->firstSurname(), DB::STRING);
        $query->setParameter(':secondSurname', $employee->secondSurname(), DB::STRING);
        $query->setParameter(':type', $employee->type(), DB::INTEGER);
        $query->setParameter(':subType', $employee->subType(), DB::INTEGER);
        $query->setParameter(':paymentMethod', $employee->paymentMethod(), DB::STRING);
        $query->setParameter(':accountType', $employee->accountType(), DB::STRING);
        $query->setParameter(':accountNumber', $employee->accountNumber(), DB::STRING);
        $query->setParameter(':isActive', $employee->isActive(), DB::BOOLEAN);
        $query->setParameter(':updatedAt', $employee->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $employee->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $employee->id(), DB::INTEGER);

        $query->execute();
    }

    public function pop(Employee $employee): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`Employees`');

        $query->where('Id = :id');
        $query->setParameter(':id', $employee->id(), DB::INTEGER);

        $query->execute();
    }

    public function filterDocumentTypes(FindEmployeeDocumentTypeRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'documentType.id as id',
            'documentType.name as text',
        ]);
        $query->from('`DocumentTypes`', '`documentType`');

        $query->where('documentType.name like :documentType_name');
        $query->setParameter(':documentType_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterEmployeeTypes(FindEmployeeEmployeeTypeRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'employeeType.id as id',
            'employeeType.name as text',
        ]);
        $query->from('`EmployeeTypes`', '`employeeType`');

        $query->where('employeeType.name like :employeeType_name');
        $query->setParameter(':employeeType_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterEmployeeSubTypes(FindEmployeeEmployeeSubTypeRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'employeeSubType.id as id',
            'employeeSubType.name as text',
        ]);
        $query->from('`EmployeeSubTypes`', '`employeeSubType`');

        $query->where('employeeSubType.name like :employeeSubType_name');
        $query->setParameter(':employeeSubType_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterPaymentMethods(FindEmployeePaymentMethodRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'paymentMethod.id as id',
            'paymentMethod.name as text',
        ]);
        $query->from('`PaymentMethods`', '`paymentMethod`');

        $query->where('paymentMethod.name like :paymentMethod_name');
        $query->setParameter(':paymentMethod_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterAccountTypes(FindEmployeeAccountTypeRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'accountType.id as id',
            'accountType.name as text',
        ]);
        $query->from('`AccountTypes`', '`accountType`');

        $query->where('accountType.name like :accountType_name');
        $query->setParameter(':accountType_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }
}
