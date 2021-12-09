<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementType;

interface AgreementTypeGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(AgreementType $agreementType): int;

    public function get(AgreementType $agreementType): array;

    public function shift(AgreementType $agreementType): void;

    public function pop(AgreementType $agreementType): void;
}
