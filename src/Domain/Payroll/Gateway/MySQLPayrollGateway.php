<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Payroll;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\PayrollGateway;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollProviderRequest;

class MySQLPayrollGateway implements PayrollGateway
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
            'payroll.Id as id',
            'payroll.Prefix as prefix',
            'payroll.Number as number',
            'payroll.Employee as employee',
            'payroll.Provider as provider',
            'payroll.Status as status',
            'payroll.Type as type',
            'payroll.TraceId as traceId',
            'payroll.Hash as hash',
            'payroll.HashType as hashType',
            'payroll.Message as message',
            'payroll.PdfPath as pdfPath',
            'payroll.XmlPath as xmlPath',
            'payroll.ParentId as parentId',
            'payroll.DownloadedAt as downloadedAt',
            'employee.id as `employee.id`',
            'employee.documentNumber as `employee.documentNumber`',
            'provider.id as `provider.id`',
            'provider.name as `provider.name`',
            'status.id as `status.id`',
            'status.name as `status.name`',
            'type.id as `type.id`',
            'type.name as `type.name`',
            'parentId.id as `parentId.id`',
            'parentId.number as `parentId.number`',
        ]);
        $query->from('`Payrolls`', '`payroll`');
        $query->join('`payroll`', '`Employees`', '`employee`', 'payroll.Employee = employee.id');
        $query->join('`payroll`', '`Providers`', '`provider`', 'payroll.Provider = provider.id');
        $query->join('`payroll`', '`PayrollStatus`', '`status`', 'payroll.Status = status.id');
        $query->join('`payroll`', '`PayrollTypes`', '`type`', 'payroll.Type = type.id');
        $query->leftJoin('`payroll`', '`Payrolls`', '`parentId`', 'payroll.ParentId = parentId.id');

        $query->orderBy('payroll.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('payroll', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(Payroll $payroll): int
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`Payrolls`');

        $query->setValue('Prefix', ':prefix');
        $query->setValue('Number', ':number');
        $query->setValue('Employee', ':employee');
        $query->setValue('Provider', ':provider');
        $query->setValue('Status', ':status');
        $query->setValue('Type', ':type');
        $query->setValue('TraceId', ':traceId');
        $query->setValue('Hash', ':hash');
        $query->setValue('HashType', ':hashType');
        $query->setValue('Message', ':message');
        $query->setValue('PdfPath', ':pdfPath');
        $query->setValue('XmlPath', ':xmlPath');
        $query->setValue('ParentId', ':parentId');
        $query->setValue('DownloadedAt', ':downloadedAt');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':prefix', $payroll->prefix(), DB::STRING);
        $query->setParameter(':number', $payroll->number(), DB::INTEGER);
        $query->setParameter(':employee', $payroll->employee(), DB::INTEGER);
        $query->setParameter(':provider', $payroll->provider(), DB::STRING);
        $query->setParameter(':status', $payroll->status(), DB::STRING);
        $query->setParameter(':type', $payroll->type(), DB::STRING);
        $query->setParameter(':traceId', $payroll->traceId(), DB::STRING);
        $query->setParameter(':hash', $payroll->hash(), DB::STRING);
        $query->setParameter(':hashType', $payroll->hashType(), DB::STRING);
        $query->setParameter(':message', $payroll->message(), DB::STRING);
        $query->setParameter(':pdfPath', $payroll->pdfPath(), DB::STRING);
        $query->setParameter(':xmlPath', $payroll->xmlPath(), DB::STRING);
        $query->setParameter(':parentId', $payroll->parentId(), DB::INTEGER);
        $query->setParameter(':downloadedAt', $payroll->downloadedAt(), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdAt', $payroll->createdAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $payroll->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $payroll->createdBy(), DB::INTEGER);

        $query->execute();

        return (int)$query->getConnection()->lastInsertId();
    }

    public function get(Payroll $payroll): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'payroll.Id as id',
            'payroll.Prefix as prefix',
            'payroll.Number as number',
            'payroll.Employee as employee',
            'payroll.Provider as provider',
            'payroll.Status as status',
            'payroll.Type as type',
            'payroll.TraceId as traceId',
            'payroll.Hash as hash',
            'payroll.HashType as hashType',
            'payroll.Message as message',
            'payroll.PdfPath as pdfPath',
            'payroll.XmlPath as xmlPath',
            'payroll.ParentId as parentId',
            'payroll.DownloadedAt as downloadedAt',
            'payroll.CreatedAt as createdAt',
            'payroll.UpdatedAt as updatedAt',
            'payroll.CreatedBy as createdBy',
            'payroll.UpdatedBy as updatedBy',
            'employee.id as `employee.id`',
            'employee.documentNumber as `employee.documentNumber`',
            'provider.id as `provider.id`',
            'provider.name as `provider.name`',
            'status.id as `status.id`',
            'status.name as `status.name`',
            'type.id as `type.id`',
            'type.name as `type.name`',
            'parentId.id as `parentId.id`',
            'parentId.number as `parentId.number`',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`Payrolls`', '`payroll`');
        $query->join('`payroll`', '`Employees`', '`employee`', 'payroll.Employee = employee.id');
        $query->join('`payroll`', '`Providers`', '`provider`', 'payroll.Provider = provider.id');
        $query->join('`payroll`', '`PayrollStatus`', '`status`', 'payroll.Status = status.id');
        $query->join('`payroll`', '`PayrollTypes`', '`type`', 'payroll.Type = type.id');
        $query->leftJoin('`payroll`', '`Payrolls`', '`parentId`', 'payroll.ParentId = parentId.id');
        $query->leftJoin('`payroll`', '`Users`', '`createdBy`', 'payroll.CreatedBy = createdBy.id');
        $query->leftJoin('`payroll`', '`Users`', '`updatedBy`', 'payroll.UpdatedBy = updatedBy.id');
        $query->where('payroll.Id = :id');
        $query->setParameter(':id', $payroll->id(), DB::INTEGER);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(Payroll $payroll): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`Payrolls`');

        $query->set('Prefix', ':prefix');
        $query->set('Number', ':number');
        $query->set('Employee', ':employee');
        $query->set('Provider', ':provider');
        $query->set('Status', ':status');
        $query->set('Type', ':type');
        $query->set('TraceId', ':traceId');
        $query->set('Hash', ':hash');
        $query->set('HashType', ':hashType');
        $query->set('Message', ':message');
        $query->set('PdfPath', ':pdfPath');
        $query->set('XmlPath', ':xmlPath');
        $query->set('ParentId', ':parentId');
        $query->set('DownloadedAt', ':downloadedAt');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':prefix', $payroll->prefix(), DB::STRING);
        $query->setParameter(':number', $payroll->number(), DB::INTEGER);
        $query->setParameter(':employee', $payroll->employee(), DB::INTEGER);
        $query->setParameter(':provider', $payroll->provider(), DB::STRING);
        $query->setParameter(':status', $payroll->status(), DB::STRING);
        $query->setParameter(':type', $payroll->type(), DB::STRING);
        $query->setParameter(':traceId', $payroll->traceId(), DB::STRING);
        $query->setParameter(':hash', $payroll->hash(), DB::STRING);
        $query->setParameter(':hashType', $payroll->hashType(), DB::STRING);
        $query->setParameter(':message', $payroll->message(), DB::STRING);
        $query->setParameter(':pdfPath', $payroll->pdfPath(), DB::STRING);
        $query->setParameter(':xmlPath', $payroll->xmlPath(), DB::STRING);
        $query->setParameter(':parentId', $payroll->parentId(), DB::INTEGER);
        $query->setParameter(':downloadedAt', $payroll->downloadedAt(), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $payroll->updatedAt() ?? new \DateTime(date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $payroll->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $payroll->id(), DB::INTEGER);

        $query->execute();
    }

    public function pop(Payroll $payroll): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`Payrolls`');

        $query->where('Id = :id');
        $query->setParameter(':id', $payroll->id(), DB::INTEGER);

        $query->execute();
    }

    public function filterEmployees(FindPayrollEmployeeRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'employee.id as id',
            'employee.documentNumber as text',
        ]);
        $query->from('`Employees`', '`employee`');

        $query->where('employee.documentNumber like :employee_documentNumber');
        $query->setParameter(':employee_documentNumber', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterProviders(FindPayrollProviderRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'provider.id as id',
            'provider.name as text',
        ]);
        $query->from('`Providers`', '`provider`');

        $query->where('provider.name like :provider_name');
        $query->setParameter(':provider_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterPayrollStatus(FindPayrollPayrollStatusRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'payrollStatus.id as id',
            'payrollStatus.name as text',
        ]);
        $query->from('`PayrollStatus`', '`payrollStatus`');

        $query->where('payrollStatus.name like :payrollStatus_name');
        $query->setParameter(':payrollStatus_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterPayrollTypes(FindPayrollPayrollTypeRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'payrollType.id as id',
            'payrollType.name as text',
        ]);
        $query->from('`PayrollTypes`', '`payrollType`');

        $query->where('payrollType.name like :payrollType_name');
        $query->setParameter(':payrollType_name', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function filterPayrolls(FindPayrollPayrollRequest $request, int $page, int $limit): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'payroll.id as id',
            'payroll.number as text',
        ]);
        $query->from('`Payrolls`', '`payroll`');

        $query->where('payroll.number like :payroll_number');
        $query->setParameter(':payroll_number', "%{$request->term}%");

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }
}
