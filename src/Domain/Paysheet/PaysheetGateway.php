<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet;

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreateEPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreatePrepaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetAlternativeProductRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetCustomerRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetHistoryServiceRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetVehicleRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetWorkerRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\GetLastPaysheetRequest;

interface PaysheetGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(Paysheet $order): int;

    public function get(Paysheet $order): array;

    public function shift(Paysheet $order): void;

    public function pop(Paysheet $order): void;

    // public function filterPayrollTypes(FindPaysheetPayrollTypeRequest $request, int $page, int $limit): array;

    // public function filterCustomers(FindPaysheetCustomerRequest $request, int $page, int $limit): array;

    // public function filterVehicles(FindPaysheetVehicleRequest $request, int $page, int $limit): array;

    // public function filterPayrollStatus(FindPaysheetPayrollStatusRequest $request, int $page, int $limit): array;

    // public function filterWorkers(FindPaysheetWorkerRequest $request, int $page, int $limit): array;

    // public function filterHistoryServices(FindPaysheetHistoryServiceRequest $request, int $limit): array;

    // public function getLastPaysheet(GetLastPaysheetRequest $request): array;

    // public function getAlternativeProducts(FindPaysheetAlternativeProductRequest $request): array;

    // public function getPrepaysheetData(CreatePrepaysheetRequest $request): array;

    // public function getEPayrollData(CreateEPayrollRequest $request): array;
}
