<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AccountType;

interface AccountTypeGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(AccountType $accountType): string;

    public function get(AccountType $accountType): array;

    public function shift(AccountType $accountType): void;

    public function pop(AccountType $accountType): void;
}
