<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types as DB;
use FlexPHP\Bundle\HelperBundle\Domain\Helper\DbalCriteriaHelper;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Paysheet;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetGateway;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreateEPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreatePrepaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetAlternativeProductRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetHistoryServiceRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetPaysheetStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetWorkerRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\GetLastPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollType;

class MySQLPaysheetGateway implements PaysheetGateway
{
    private Connection $conn;

    private $operator = [

    ];

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'paysheet.Id as id',
            'paysheet.Type as type',
            'paysheet.EmployeeId as employeeId',
            'paysheet.AgreementId as agreementId',
            'paysheet.Subtotal as subtotal',
            'paysheet.Taxes as taxes',
            'paysheet.Total as total',
            'paysheet.Notes as notes',
            'paysheet.TotalPaid as totalPaid',
            'paysheet.PaidAt as paidAt',
            'paysheet.StatusId as statusId',
            'paysheet.PaysheetNotes as paysheetNotes',
            'paysheet.CreatedAt as createdAt',
            'type.id as `type.id`',
            'type.name as `type.name`',
            'employeeId.Id as `employeeId.id`',
            "CONCAT(employeeId.FirstName, ' ', employeeId.SecondName) as `employeeId.name`",
            'employeeId.DocumentTypeId as `employeeId.documentTypeId`',
            'employeeId.DocumentNumber as `employeeId.documentNumber`',
            'agreementId.id as `agreementId.id`',
            'agreementId.name as `agreementId.name`',
            'statusId.id as `statusId.id`',
            'statusId.name as `statusId.name`',
        ]);
        $query->from('`Paysheets`', '`paysheet`');
        $query->join('`paysheet`', '`PayrollTypes`', '`type`', 'paysheet.Type = type.id');
        $query->leftJoin('`paysheet`', '`Employees`', '`employeeId`', 'paysheet.EmployeeId = employeeId.id');
        $query->leftJoin('`paysheet`', '`Agreements`', '`agreementId`', 'paysheet.AgreementId = agreementId.id');
        $query->leftJoin('`paysheet`', '`PaysheetStatus`', '`statusId`', 'paysheet.StatusId = statusId.id');

        $query->orderBy('paysheet.UpdatedAt', 'DESC');

        $criteria = new DbalCriteriaHelper($query, $offset);

        foreach ($wheres as $column => $value) {
            $criteria->getCriteria('paysheet', $column, $value, $this->operator[$column] ?? DbalCriteriaHelper::OP_EQUALS);
        }

        $query->setFirstResult($page ? ($page - 1) * $limit : 0);
        $query->setMaxResults($limit);

        return $query->execute()->fetchAll();
    }

    public function push(Paysheet $paysheet): int
    {
        $query = $this->conn->createQueryBuilder();

        $query->insert('`Paysheets`');

        $query->setValue('Type', ':type');
        $query->setValue('EmployeeId', ':employeeId');
        $query->setValue('AgreementId', ':agreementId');
        $query->setValue('Subtotal', ':subtotal');
        $query->setValue('Taxes', ':taxes');
        $query->setValue('Total', ':total');
        $query->setValue('Notes', ':notes');
        $query->setValue('TotalPaid', ':totalPaid');
        $query->setValue('PaidAt', ':paidAt');
        $query->setValue('StatusId', ':statusId');
        $query->setValue('PaysheetNotes', ':paysheetNotes');
        $query->setValue('CreatedAt', ':createdAt');
        $query->setValue('UpdatedAt', ':updatedAt');
        $query->setValue('CreatedBy', ':createdBy');

        $query->setParameter(':type', $paysheet->type(), DB::STRING);
        $query->setParameter(':employeeId', $paysheet->employeeId(), DB::INTEGER);
        $query->setParameter(':agreementId', $paysheet->agreementId(), DB::INTEGER);
        $query->setParameter(':subtotal', $paysheet->subtotal(), DB::STRING);
        $query->setParameter(':taxes', $paysheet->taxes(), DB::STRING);
        $query->setParameter(':total', $paysheet->total(), DB::STRING);
        $query->setParameter(':notes', $paysheet->notes(), DB::TEXT);
        $query->setParameter(':totalPaid', $paysheet->totalPaid(), DB::STRING);
        $query->setParameter(':paidAt', $paysheet->paidAt(), DB::DATETIME_MUTABLE);
        $query->setParameter(':statusId', $paysheet->statusId(), DB::STRING);
        $query->setParameter(':paysheetNotes', $paysheet->paysheetNotes(), DB::TEXT);
        $query->setParameter(':createdAt', $paysheet->createdAt() ?? new \DateTime(\date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedAt', $paysheet->updatedAt() ?? new \DateTime(\date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':createdBy', $paysheet->createdBy(), DB::INTEGER);

        $query->execute();

        return (int)$query->getConnection()->lastInsertId();
    }

    public function get(Paysheet $paysheet): array
    {
        $query = $this->conn->createQueryBuilder();

        $query->select([
            'paysheet.Id as id',
            'paysheet.Type as type',
            'paysheet.EmployeeId as employeeId',
            'paysheet.AgreementId as agreementId',
            'paysheet.Subtotal as subtotal',
            'paysheet.Taxes as taxes',
            'paysheet.Total as total',
            'paysheet.Notes as notes',
            'paysheet.TotalPaid as totalPaid',
            'paysheet.PaidAt as paidAt',
            'paysheet.StatusId as statusId',
            'paysheet.PaysheetNotes as paysheetNotes',
            'paysheet.CreatedAt as createdAt',
            'paysheet.UpdatedAt as updatedAt',
            'paysheet.CreatedBy as createdBy',
            'paysheet.UpdatedBy as updatedBy',
            'type.id as `type.id`',
            'type.name as `type.name`',
            'employeeId.id as `employeeId.id`',
            "CONCAT(employeeId.FirstName, ' ', employeeId.SecondName) as `employeeId.name`",
            'employeeId.documentTypeId as `employeeId.documentTypeId`',
            'employeeId.documentNumber as `employeeId.documentNumber`',
            'agreementId.id as `agreementId.id`',
            'agreementId.name as `agreementId.name`',
            'statusId.id as `statusId.id`',
            'statusId.name as `statusId.name`',
            'createdBy.id as `createdBy.id`',
            'createdBy.name as `createdBy.name`',
            'updatedBy.id as `updatedBy.id`',
            'updatedBy.name as `updatedBy.name`',
        ]);
        $query->from('`Paysheets`', '`paysheet`');
        $query->join('`paysheet`', '`PayrollTypes`', '`type`', 'paysheet.Type = type.id');
        $query->leftJoin('`paysheet`', '`Employees`', '`employeeId`', 'paysheet.EmployeeId = employeeId.id');
        $query->leftJoin('`paysheet`', '`Agreements`', '`agreementId`', 'paysheet.AgreementId = agreementId.id');
        $query->leftJoin('`paysheet`', '`PaysheetStatus`', '`statusId`', 'paysheet.StatusId = statusId.id');
        $query->leftJoin('`paysheet`', '`Users`', '`createdBy`', 'paysheet.CreatedBy = createdBy.id');
        $query->leftJoin('`paysheet`', '`Users`', '`updatedBy`', 'paysheet.UpdatedBy = updatedBy.id');
        $query->where('paysheet.Id = :id');
        $query->setParameter(':id', $paysheet->id(), DB::INTEGER);

        return $query->execute()->fetch() ?: [];
    }

    public function shift(Paysheet $paysheet): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->update('`Paysheets`');

        $query->set('Type', ':type');
        $query->set('EmployeeId', ':employeeId');
        $query->set('AgreementId', ':agreementId');
        $query->set('Subtotal', ':subtotal');
        $query->set('Taxes', ':taxes');
        $query->set('Total', ':total');
        $query->set('Notes', ':notes');
        $query->set('TotalPaid', ':totalPaid');
        $query->set('PaidAt', ':paidAt');
        $query->set('StatusId', ':statusId');
        $query->set('PaysheetNotes', ':paysheetNotes');
        $query->set('UpdatedAt', ':updatedAt');
        $query->set('UpdatedBy', ':updatedBy');

        $query->setParameter(':type', $paysheet->type(), DB::STRING);
        $query->setParameter(':employeeId', $paysheet->employeeId(), DB::INTEGER);
        $query->setParameter(':agreementId', $paysheet->agreementId(), DB::INTEGER);
        $query->setParameter(':subtotal', $paysheet->subtotal(), DB::STRING);
        $query->setParameter(':taxes', $paysheet->taxes(), DB::STRING);
        $query->setParameter(':total', $paysheet->total(), DB::STRING);
        $query->setParameter(':notes', $paysheet->notes(), DB::TEXT);
        $query->setParameter(':totalPaid', $paysheet->totalPaid(), DB::STRING);
        $query->setParameter(':paidAt', $paysheet->paidAt(), DB::DATETIME_MUTABLE);
        $query->setParameter(':statusId', $paysheet->statusId(), DB::STRING);
        $query->setParameter(':paysheetNotes', $paysheet->paysheetNotes(), DB::TEXT);
        $query->setParameter(':updatedAt', $paysheet->updatedAt() ?? new \DateTime(\date('Y-m-d H:i:s')), DB::DATETIME_MUTABLE);
        $query->setParameter(':updatedBy', $paysheet->updatedBy(), DB::INTEGER);

        $query->where('Id = :id');
        $query->setParameter(':id', $paysheet->id(), DB::INTEGER);

        $query->execute();
    }

    public function pop(Paysheet $paysheet): void
    {
        $query = $this->conn->createQueryBuilder();

        $query->delete('`Paysheets`');

        $query->where('Id = :id');
        $query->setParameter(':id', $paysheet->id(), DB::INTEGER);

        $query->execute();
    }

    // public function filterPayrollTypes(FindPaysheetPayrollTypeRequest $request, int $page, int $limit): array
    // {
    //     $query = $this->conn->createQueryBuilder();

    //     $query->select([
    //         'paysheetType.id as id',
    //         'paysheetType.name as text',
    //     ]);
    //     $query->from('`PayrollTypes`', '`paysheetType`');

    //     $query->where('paysheetType.name like :paysheetType_name');
    //     $query->setParameter(':paysheetType_name', "%{$request->term}%");

    //     $query->setFirstResult($page ? ($page - 1) * $limit : 0);
    //     $query->setMaxResults($limit);

    //     return $query->execute()->fetchAll();
    // }

    // public function filterEmployees(FindPaysheetEmployeeRequest $request, int $page, int $limit): array
    // {
    //     $query = $this->conn->createQueryBuilder();

    //     $query->select([
    //         'employee.Id as id',
    //         "CONCAT(employee.Name, ' - ', IFNULL(employee.DocumentNumber, '')) as text",
    //         'employee.DocumentTypeId as documentTypeId',
    //         'employee.DocumentNumber as documentNumber',
    //         'employee.Name as name',
    //         'employee.PhoneNumber as phoneNumber',
    //         'employee.Email as email',
    //         'employee.Address as address',
    //         'city.Id as cityId',
    //         'city.Name as cityName',
    //     ]);
    //     $query->from('`Employees`', '`employee`');
    //     $query->leftJoin('`employee`', '`Cities`', '`city`', 'city.Id = employee.CityId');

    //     $data = [];

    //     switch (true) {
    //         case !empty($request->term):
    //             $query->where('employee.Name like :employeeName');
    //             $query->setParameter(':employeeName', "{$request->term}%");

    //             $query->setFirstResult($page ? ($page - 1) * $limit : 0);
    //             $query->setMaxResults($limit);

    //             $data = $query->execute()->fetchAll();

    //             break;
    //         case empty($request->documentNumber) && !empty($request->employeeId) && $request->employeeId > 0:
    //             $query->where('employee.Id = :employeeId');
    //             $query->setParameter(':employeeId', $request->employeeId);

    //             $data = $query->execute()->fetch() ?: $data;

    //             break;
    //         case !empty($request->documentNumber):
    //             $query->where('employee.DocumentTypeId = :documentTypeId');
    //             $query->setParameter(':documentTypeId', $request->documentTypeId);
    //             $query->andWhere('employee.DocumentNumber = :documentNumber');
    //             $query->setParameter(':documentNumber', $request->documentNumber);

    //             $data = $query->execute()->fetch() ?: $data;

    //             break;
    //     }

    //     return $data;
    // }

    // public function filterAgreements(FindPaysheetAgreementRequest $request, int $page, int $limit): array
    // {
    //     $query = $this->conn->createQueryBuilder();

    //     $query->select([
    //         'agreement.id as id',
    //         'agreement.name as text',
    //         'agreementType.Id as typeId',
    //         'agreementType.Name as typeName',
    //         'agreementBrand.Id as brandId',
    //         'agreementBrand.Name as brandName',
    //         'agreementSerie.Id as serieId',
    //         'agreementSerie.Name as serieName',
    //         'agreement.Model as model',
    //         'agreement.OilQuantity as oilQuantity',
    //         'agreement.Liters as liters',
    //         'agreement.Fuel as fuel',
    //     ]);
    //     $query->from('`Agreements`', '`agreement`');
    //     $query->join('`agreement`', '`AgreementTypes`', '`agreementType`', 'agreement.Type = agreementType.Id');
    //     $query->leftjoin('`agreement`', '`AgreementBrands`', '`agreementBrand`', 'agreement.Brand = agreementBrand.Id');
    //     $query->leftJoin('`agreement`', '`AgreementSeries`', '`agreementSerie`', 'agreement.Serie = agreementSerie.Id');

    //     $query->setParameter(':agreement_name', "{$request->term}%");
    //     $query->andWhere('agreement.IsActive = :agreement_isActive');
    //     $query->setParameter(':agreement_isActive', 1);

    //     $query->setFirstResult($page ? ($page - 1) * $limit : 0);
    //     $query->setMaxResults($limit);

    //     return $query->execute()->fetchAll();
    // }

    // public function filterPaysheetStatus(FindPaysheetPaysheetStatusRequest $request, int $page, int $limit): array
    // {
    //     $query = $this->conn->createQueryBuilder();

    //     $query->select([
    //         'paysheetStatus.id as id',
    //         'paysheetStatus.name as text',
    //     ]);
    //     $query->from('`PaysheetStatus`', '`paysheetStatus`');

    //     $query->where('paysheetStatus.name like :paysheetStatus_name');
    //     $query->setParameter(':paysheetStatus_name', "%{$request->term}%");

    //     $query->setFirstResult($page ? ($page - 1) * $limit : 0);
    //     $query->setMaxResults($limit);

    //     return $query->execute()->fetchAll();
    // }

    // // Custom methods

    // public function filterHistoryServices(FindPaysheetHistoryServiceRequest $request, int $limit): array
    // {
    //     $query = $this->conn->createQueryBuilder();

    //     $query->select([
    //         'o.EmployeeId as employeeId',
    //         'c.DocumentTypeId as documentTypeId',
    //         'c.DocumentNumber as documentNumber',
    //         'o.CreatedAt as createdAt',
    //     ]);

    //     $query->from('`Paysheets`', '`o`');
    //     $query->leftJoin('`o`', '`Employees`', '`c`', 'o.EmployeeId = c.Id');

    //     $query->where('o.AgreementId = :agreementId');
    //     $query->setParameter(':agreementId', $request->agreementId);
    //     $query->andWhere('o.Type = :paysheetType');
    //     $query->setParameter(':paysheetType', PayrollType::VEHICLE);

    //     $query->orderBy('o.Id', 'DESC');
    //     $query->setMaxResults($limit);

    //     $paysheet = $query->execute()->fetch() ?: [];

    //     $sql = <<<SQL
    //     (
    //         SELECT
    //             'oil' as type,
    //             p.Id as id,
    //             CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, '')) as name,
    //             od.Quantity as quantity,
    //             pu.Name as quantityName,
    //             p.Price as price,
    //             p.Taxes as taxes
    //         FROM Paysheets o
    //             JOIN PaysheetDetails od ON o.Id = od.PaysheetId AND o.Type = :paysheetAgreement
    //             JOIN Agreements v ON v.Id = o.AgreementId AND v.Id = :agreementId
    //             JOIN Products p ON p.Id = od.ProductId AND p.ProductTypeId = 1
    //             LEFT JOIN ProductUnits pu ON p.ProductUnitId = pu.Id
    //         ORDER BY o.Id DESC
    //         LIMIT 1
    //     )
    //     UNION
    //     (
    //         SELECT
    //             'oilFilter' as type,
    //             p.Id as id,
    //             CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, '')) as name,
    //             od.Quantity as quantity,
    //             pu.Name as quantityName,
    //             p.Price as price,
    //             p.Taxes as taxes
    //         FROM Paysheets o
    //             JOIN PaysheetDetails od ON o.Id = od.PaysheetId AND o.Type = :paysheetAgreement
    //             JOIN Agreements v ON v.Id = o.AgreementId AND v.Id = :agreementId
    //             JOIN Products p ON p.Id = od.ProductId AND p.ProductTypeId = 2
    //             LEFT JOIN ProductUnits pu ON p.ProductUnitId = pu.Id
    //         ORDER BY o.Id DESC
    //         LIMIT 1
    //     )
    //     UNION
    //     (
    //         SELECT
    //             'airFilter' as type,
    //             p.Id as id,
    //             CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, '')) as name,
    //             od.Quantity as quantity,
    //             pu.Name as quantityName,
    //             p.Price as price,
    //             p.Taxes as taxes
    //         FROM Paysheets o
    //             JOIN PaysheetDetails od ON o.Id = od.PaysheetId AND o.Type = :paysheetAgreement
    //             JOIN Agreements v ON v.Id = o.AgreementId AND v.Id = :agreementId
    //             JOIN Products p ON p.Id = od.ProductId AND p.ProductTypeId = 3
    //             LEFT JOIN ProductUnits pu ON p.ProductUnitId = pu.Id
    //         ORDER BY o.Id DESC
    //         LIMIT 1
    //     )
    //     UNION
    //     (
    //         SELECT
    //             'gasFilter' as type,
    //             p.Id as id,
    //             CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, '')) as name,
    //             od.Quantity as quantity,
    //             pu.Name as quantityName,
    //             p.Price as price,
    //             p.Taxes as taxes
    //         FROM Paysheets o
    //             JOIN PaysheetDetails od ON o.Id = od.PaysheetId AND o.Type = :paysheetAgreement
    //             JOIN Agreements v ON v.Id = o.AgreementId AND v.Id = :agreementId
    //             JOIN Products p ON p.Id = od.ProductId AND p.ProductTypeId = 4
    //             LEFT JOIN ProductUnits pu ON p.ProductUnitId = pu.Id
    //         ORDER BY o.Id DESC
    //         LIMIT 1
    //     )
// SQL;

    //     $statement = $this->conn->prepare($sql);
    //     $statement->execute([
    //         'agreementId' => $request->agreementId,
    //         'paysheetAgreement' => PayrollType::VEHICLE,
    //     ]);

    //     $history = $statement->fetchAll() ?: [];

    //     return \compact('paysheet', 'history');
    // }

    // public function getLastPaysheet(GetLastPaysheetRequest $request): array
    // {
    //     $query = $this->conn->createQueryBuilder();

    //     $query->select([
    //         'o.Id id',
    //         'o.SubTotal subTotal',
    //         'o.Taxes taxes',
    //         'o.Total total',
    //         "IFNULL(o.Notes, '-') notes",
    //     ]);

    //     $query->from('`Paysheets`', '`o`');

    //     $query->where('o.Id != :paysheetId');
    //     $query->setParameter(':paysheetId', $request->paysheetId);
    //     $query->andWhere('o.EmployeeId = :employeeId');
    //     $query->setParameter(':employeeId', $request->employeeId);

    //     if ($request->agreementId > 0) {
    //         $query->andWhere('o.AgreementId = :agreementId');
    //         $query->setParameter(':agreementId', $request->agreementId);
    //     }

    //     if ($request->paysheetType) {
    //         $query->andWhere('o.Type = :paysheetType');
    //         $query->setParameter(':paysheetType', $request->paysheetType);
    //     }

    //     $query->orderBy('o.Id', 'DESC');
    //     $query->setMaxResults(1);

    //     $paysheet = $query->execute()->fetch() ?: [];
    //     $details = [];

    //     if ($paysheet && !empty($paysheet['id'])) {
    //         $query->resetQueryParts();
    //         $query->setMaxResults(null);

    //         $query->select([
    //             "CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, '')) name",
    //             'od.Quantity quantity',
    //             'od.Price price',
    //             'od.Tax tax',
    //             'od.Total total',
    //             'p.Id productId',
    //             'p.Price productPrice',
    //             'p.Taxes productTaxes',
    //         ]);

    //         $query->from('`PaysheetDetails`', '`od`');
    //         $query->join('`od`', '`Products`', '`p`', 'p.Id = od.ProductId');

    //         $query->where('od.PaysheetId = :paysheetId');
    //         $query->setParameter(':paysheetId', $paysheet['id']);
    //         $query->orderBy('od.Id', 'ASC');

    //         $details = $query->execute()->fetchAll();
    //     }

    //     return \compact('paysheet', 'details');
    // }

    // public function getAlternativeProducts(FindPaysheetAlternativeProductRequest $request): array
    // {
    //     $sql = <<<SQL
    //     (
    //         SELECT
    //             'oil' as alternative,
    //             p.Id as id,
    //             CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, '')) as name,
    //             od.Quantity as quantity,
    //             p.Price as price,
    //             p.Taxes as taxes
    //         FROM Paysheets o
    //             JOIN PaysheetDetails od ON o.Id = od.PaysheetId AND o.Type = :paysheetAgreement
    //             JOIN Agreements v ON v.Id = o.AgreementId AND v.Brand = :brandId AND v.Serie = :serieId
    //             JOIN Products p ON p.Id = od.ProductId AND p.ProductTypeId = 1 AND p.Id != :oilId
    //         ORDER BY o.Id DESC, od.Id DESC
    //         LIMIT 1
    //     )
    //     UNION
    //     (
    //         SELECT
    //             'oilFilter' as alternative,
    //             p.Id as id,
    //             CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, '')) as name,
    //             od.Quantity as quantity,
    //             p.Price as price,
    //             p.Taxes as taxes
    //         FROM Paysheets o
    //             JOIN PaysheetDetails od ON o.Id = od.PaysheetId AND o.Type = :paysheetAgreement
    //             JOIN Agreements v ON v.Id = o.AgreementId AND v.Brand = :brandId AND v.Serie = :serieId
    //             JOIN Products p ON p.Id = od.ProductId AND p.ProductTypeId = 2 AND p.Id != :oilFilterId
    //         ORDER BY o.Id DESC, od.Id DESC
    //         LIMIT 1
    //     )
    //     UNION
    //     (
    //         SELECT
    //             'airFilter' as alternative,
    //             p.Id as id,
    //             CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, '')) as name,
    //             od.Quantity as quantity,
    //             p.Price as price,
    //             p.Taxes as taxes
    //         FROM Paysheets o
    //             JOIN PaysheetDetails od ON o.Id = od.PaysheetId AND o.Type = :paysheetAgreement
    //             JOIN Agreements v ON v.Id = o.AgreementId AND v.Brand = :brandId AND v.Serie = :serieId
    //             JOIN Products p ON p.Id = od.ProductId AND p.ProductTypeId = 3 AND p.Id != :airFilterId
    //         ORDER BY o.Id DESC, od.Id DESC
    //         LIMIT 1
    //     )
    //     UNION
    //     (
    //         SELECT
    //             'gasFilter' as alternative,
    //             p.Id as id,
    //             CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, '')) as name,
    //             od.Quantity as quantity,
    //             p.Price as price,
    //             p.Taxes as taxes
    //         FROM Paysheets o
    //             JOIN PaysheetDetails od ON o.Id = od.PaysheetId AND o.Type = :paysheetAgreement
    //             JOIN Agreements v ON v.Id = o.AgreementId AND v.Brand = :brandId AND v.Serie = :serieId
    //             JOIN Products p ON p.Id = od.ProductId AND p.ProductTypeId = 4 AND p.Id != :gasFilterId
    //         ORDER BY o.Id DESC, od.Id DESC
    //         LIMIT 1
    //     )
// SQL;

    //     $statement = $this->conn->prepare($sql);
    //     $statement->execute([
    //         'brandId' => $request->brandId,
    //         'serieId' => $request->serieId,
    //         'oilId' => $request->oilId,
    //         'oilFilterId' => $request->oilFilterId,
    //         'airFilterId' => $request->airFilterId,
    //         'gasFilterId' => $request->gasFilterId,
    //         'paysheetAgreement' => PayrollType::VEHICLE,
    //     ]);

    //     return $statement->fetchAll() ?: [];
    // }

    // public function getPrepaysheetData(CreatePrepaysheetRequest $request): array
    // {
    //     $query = $this->conn->createQueryBuilder();

    //     $query->select([
    //         'o.Id as paysheetId',
    //         'c.Name as payerName',
    //         'c.DocumentTypeId as payerDocumentType',
    //         'c.DocumentNumber as payerDocumentNumber',
    //         'c.Email as payerEmail',
    //         'c.Address as payerAddress',
    //         'ct.Name as payerCity',
    //         'c.PhoneNumber as payerPhoneNumber',
    //         'DATE_ADD(o.CreatedAt, INTERVAL :offset SECOND) as paysheetCreatedAt',
    //         'vb.Name as agreementBrand',
    //         'vs.Name as agreementSerie',
    //         'v.Placa as agreementPlaca',
    //         'v.OilQuantity as agreementOilQuantity',
    //         'u.Name as userName',
    //         'o.Subtotal as paysheetSubtotal',
    //         'o.Taxes as paysheetTaxes',
    //         'o.Total as paysheetTotal',
    //         'o.Discount as paysheetDiscount',
    //         'o.TotalPaid as paysheetTotalPaid',
    //         'os.Name as paysheetStatusName',
    //     ]);

    //     $query->from('`Paysheets`', '`o`');
    //     $query->Join('`o`', '`Users`', '`u`', 'o.CreatedBy  = u.Id');
    //     $query->Join('`o`', '`PaysheetStatus`', '`os`', 'o.StatusId  = os.Id');
    //     $query->leftJoin('`o`', '`Agreements`', '`v`', 'o.AgreementId  = v.Id');
    //     $query->leftJoin('`v`', '`AgreementBrands`', '`vb`', 'v.Brand  = vb.Id');
    //     $query->leftJoin('`v`', '`AgreementSeries`', '`vs`', 'v.Serie  = vs.Id');
    //     $query->leftJoin('`o`', '`Employees`', '`c`', 'o.EmployeeId  = c.Id');
    //     $query->leftJoin('`c`', '`Cities`', '`ct`', 'c.CityId  = ct.Id');
    //     $query->leftJoin('`o`', '`Workers`', '`w`', 'o.Worker  = w.Id');

    //     $query->where('o.Id = :paysheetId');
    //     $query->setParameter(':paysheetId', $request->paysheetId);
    //     $query->setParameter(':offset', $request->offset);

    //     $paysheet = $query->execute()->fetch() ?: [];
    //     $details = [];
    //     $payments = [];

    //     if ($paysheet && !empty($paysheet['paysheetId'])) {
    //         $query->resetQueryParts();

    //         $query->select([
    //             "CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, ''), ' - ' , pt.Name) productName",
    //             'pu.Code productUnit',
    //             'od.Quantity quantity',
    //             'od.Price price',
    //             'od.Tax tax',
    //             'od.Total total',
    //         ]);

    //         $query->from('`PaysheetDetails`', '`od`');
    //         $query->join('`od`', '`Products`', '`p`', 'p.Id = od.ProductId');
    //         $query->join('`p`', '`ProductUnits`', '`pu`', 'p.ProductUnitId = pu.Id');
    //         $query->join('`p`', '`ProductTypes`', '`pt`', 'p.ProductTypeId = pt.Id');

    //         $query->where('od.PaysheetId = :paysheetId');
    //         $query->setParameter(':paysheetId', $paysheet['paysheetId']);
    //         $query->orderBy('od.Id', 'ASC');

    //         $details = $query->execute()->fetchAll();

    //         $query->resetQueryParts();

    //         $query->select([
    //             'pm.Id as paymentMethodId',
    //             'p.CurrencyId as currencyName',
    //             'p.Amount as amount',
    //         ]);

    //         $query->from('`Payments`', '`p`');
    //         $query->leftJoin('`p`', '`PaymentStatus`', '`ps`', 'p.PaymentStatusId = ps.Id');
    //         $query->leftJoin('`p`', '`PaymentMethods`', '`pm`', 'p.PaymentMethodId = pm.Id');

    //         $query->where('p.PaysheetId = :paysheetId');
    //         $query->setParameter(':paysheetId', $paysheet['paysheetId']);
    //         $query->orderBy('p.Id', 'ASC');

    //         $payments = $query->execute()->fetchAll();
    //     }

    //     return \compact('paysheet', 'details', 'payments');
    // }

    // public function getEPayrollData(CreateEPayrollRequest $request): array
    // {
    //     $query = $this->conn->createQueryBuilder();

    //     $query->select([
    //         'o.Id as paysheetId',
    //         'o.Subtotal as paysheetSubtotal',
    //         'o.Taxes as paysheetTaxes',
    //         'o.Total as paysheetTotal',
    //         'o.Discount as paysheetDiscount',
    //         'o.TotalPaid as paysheetTotalPaid',
    //         'c.Name as payerName',
    //         'c.DocumentTypeId as payerDocumentType',
    //         'c.DocumentNumber as payerDocumentNumber',
    //         'c.Email as payerEmail',
    //         'c.Address as payerAddress',
    //         'ct.Name as payerCity',
    //         'c.PhoneNumber as payerPhoneNumber',
    //         'DATE_ADD(o.CreatedAt, INTERVAL :offset SECOND) as paysheetCreatedAt',
    //         'u.Name as userName',
    //     ]);

    //     $query->from('`Paysheets`', '`o`');
    //     $query->Join('`o`', '`Users`', '`u`', 'o.CreatedBy  = u.Id');
    //     $query->leftJoin('`o`', '`Employees`', '`c`', 'o.EmployeeId  = c.Id');
    //     $query->leftJoin('`c`', '`Cities`', '`ct`', 'c.CityId  = ct.Id');
    //     $query->leftJoin('`ct`', '`States`', '`s`', 'ct.StateId  = s.Id');

    //     $query->where('o.Id = :paysheetId');
    //     $query->setParameter(':paysheetId', $request->paysheetId);
    //     $query->setParameter(':offset', $request->offset);

    //     $paysheet = $query->execute()->fetch() ?: [];
    //     $details = [];

    //     if ($paysheet && !empty($paysheet['paysheetId'])) {
    //         $query->resetQueryParts();

    //         $query->select([
    //             "CONCAT(IFNULL(p.Name, ''), ' ', IFNULL(p.Reference, '')) productName",
    //             'pu.Name productUnit',
    //             'od.Quantity quantity',
    //             'od.Price price',
    //             'od.Tax tax',
    //             'od.Total total',
    //         ]);

    //         $query->from('`PaysheetDetails`', '`od`');
    //         $query->join('`od`', '`Products`', '`p`', 'p.Id = od.ProductId');
    //         $query->join('`p`', '`ProductUnits`', '`pu`', 'p.ProductUnitId = pu.Id');

    //         $query->where('od.PaysheetId = :paysheetId');
    //         $query->setParameter(':paysheetId', $paysheet['paysheetId']);
    //         $query->orderBy('od.Id', 'ASC');

    //         $details = $query->execute()->fetchAll();

    //         $query->resetQueryParts();
    //     }

    //     return \compact('paysheet', 'details');
    // }
}
