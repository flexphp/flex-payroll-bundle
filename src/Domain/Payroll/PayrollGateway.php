<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll;

use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollProviderRequest;

interface PayrollGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(Payroll $payroll): int;

    public function get(Payroll $payroll): array;

    public function shift(Payroll $payroll): void;

    public function pop(Payroll $payroll): void;

    public function filterPaysheets(FindPayrollPaysheetRequest $request, int $page, int $limit): array;

    public function filterProviders(FindPayrollProviderRequest $request, int $page, int $limit): array;

    public function filterPayrollStatus(FindPayrollPayrollStatusRequest $request, int $page, int $limit): array;

    public function filterPayrollTypes(FindPayrollPayrollTypeRequest $request, int $page, int $limit): array;

    public function filterPayrolls(FindPayrollPayrollRequest $request, int $page, int $limit): array;
}
