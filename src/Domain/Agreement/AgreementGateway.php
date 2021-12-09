<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement;

use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementCurrencyRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementEmployeeRequest;

interface AgreementGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(Agreement $agreement): int;

    public function get(Agreement $agreement): array;

    public function shift(Agreement $agreement): void;

    public function pop(Agreement $agreement): void;

    public function filterEmployees(FindAgreementEmployeeRequest $request, int $page, int $limit): array;

    public function filterAgreementTypes(FindAgreementAgreementTypeRequest $request, int $page, int $limit): array;

    public function filterAgreementPeriods(FindAgreementAgreementPeriodRequest $request, int $page, int $limit): array;

    public function filterCurrencies(FindAgreementCurrencyRequest $request, int $page, int $limit): array;

    public function filterAgreementStatus(FindAgreementAgreementStatusRequest $request, int $page, int $limit): array;
}
