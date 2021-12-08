<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType;

interface EmployeeSubTypeGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(EmployeeSubType $employeeSubType): int;

    public function get(EmployeeSubType $employeeSubType): array;

    public function shift(EmployeeSubType $employeeSubType): void;

    public function pop(EmployeeSubType $employeeSubType): void;
}
