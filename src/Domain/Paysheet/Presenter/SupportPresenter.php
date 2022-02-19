<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Presenter;

class SupportPresenter
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function count(): int
    {
        return \count($this->data);
    }

    public function amount(int $index): float
    {
        return (float)$this->data[$index]['amount'];
    }

    public function noSalary(int $index): float
    {
        return (float)$this->data[$index]['noSalary'];
    }
}
