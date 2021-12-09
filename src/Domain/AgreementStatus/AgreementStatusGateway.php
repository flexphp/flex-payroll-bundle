<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus;

interface AgreementStatusGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(AgreementStatus $agreementStatus): string;

    public function get(AgreementStatus $agreementStatus): array;

    public function shift(AgreementStatus $agreementStatus): void;

    public function pop(AgreementStatus $agreementStatus): void;
}
