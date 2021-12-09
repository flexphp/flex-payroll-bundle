<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollType;

interface PayrollTypeGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(PayrollType $payrollType): string;

    public function get(PayrollType $payrollType): array;

    public function shift(PayrollType $payrollType): void;

    public function pop(PayrollType $payrollType): void;
}
