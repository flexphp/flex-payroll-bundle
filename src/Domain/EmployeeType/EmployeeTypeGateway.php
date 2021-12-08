<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType;

interface EmployeeTypeGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(EmployeeType $employeeType): int;

    public function get(EmployeeType $employeeType): array;

    public function shift(EmployeeType $employeeType): void;

    public function pop(EmployeeType $employeeType): void;
}
