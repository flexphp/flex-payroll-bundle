<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus;

interface PayrollStatusGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(PayrollStatus $payrollStatus): string;

    public function get(PayrollStatus $payrollStatus): array;

    public function shift(PayrollStatus $payrollStatus): void;

    public function pop(PayrollStatus $payrollStatus): void;
}
