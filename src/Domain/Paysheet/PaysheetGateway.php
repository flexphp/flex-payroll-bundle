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
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetHistoryServiceRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetWorkerRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\GetLastPaysheetRequest;

interface PaysheetGateway
{
    public function search(array $wheres, array $paysheets, int $page, int $limit, int $offset): array;

    public function push(Paysheet $paysheet): int;

    public function get(Paysheet $paysheet): array;

    public function shift(Paysheet $paysheet): void;

    public function pop(Paysheet $paysheet): void;

    // public function filterPayrollTypes(FindPaysheetPayrollTypeRequest $request, int $page, int $limit): array;

    // public function filterEmployees(FindPaysheetEmployeeRequest $request, int $page, int $limit): array;

    // public function filterAgreements(FindPaysheetAgreementRequest $request, int $page, int $limit): array;

    // public function filterPayrollStatus(FindPaysheetPayrollStatusRequest $request, int $page, int $limit): array;

    // public function filterHistoryServices(FindPaysheetHistoryServiceRequest $request, int $limit): array;

    // public function getLastPaysheet(GetLastPaysheetRequest $request): array;

    // public function getAlternativeProducts(FindPaysheetAlternativeProductRequest $request): array;

    // public function getPrepaysheetData(CreatePrepaysheetRequest $request): array;

    // public function getEPayrollData(CreateEPayrollRequest $request): array;
}
