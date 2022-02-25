<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee;

use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeDocumentTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeePaymentMethodRequest;

interface EmployeeGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(Employee $employee): int;

    public function get(Employee $employee): array;

    public function shift(Employee $employee): void;

    public function pop(Employee $employee): void;

    public function filterDocumentTypes(FindEmployeeDocumentTypeRequest $request, int $page, int $limit): array;

    public function filterEmployeeTypes(FindEmployeeEmployeeTypeRequest $request, int $page, int $limit): array;

    public function filterEmployeeSubTypes(FindEmployeeEmployeeSubTypeRequest $request, int $page, int $limit): array;

    public function filterPaymentMethods(FindEmployeePaymentMethodRequest $request, int $page, int $limit): array;

    public function filterAccountTypes(FindEmployeeAccountTypeRequest $request, int $page, int $limit): array;

    public function filterBanks(FindEmployeeBankRequest $request, int $page, int $limit): array;
}
