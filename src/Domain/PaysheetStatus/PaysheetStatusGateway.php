<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus;

interface PaysheetStatusGateway
{
    public function search(array $wheres, array $orders, int $page, int $limit, int $offset): array;

    public function push(PaysheetStatus $paysheetStatus): string;

    public function get(PaysheetStatus $paysheetStatus): array;

    public function shift(PaysheetStatus $paysheetStatus): void;

    public function pop(PaysheetStatus $paysheetStatus): void;
}
