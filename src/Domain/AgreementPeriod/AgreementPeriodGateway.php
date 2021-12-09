<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod;

interface AgreementPeriodGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(AgreementPeriod $agreementPeriod): string;

    public function get(AgreementPeriod $agreementPeriod): array;

    public function shift(AgreementPeriod $agreementPeriod): void;

    public function pop(AgreementPeriod $agreementPeriod): void;
}
