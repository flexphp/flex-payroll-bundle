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

class CessationPresenter
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function percentage(): float
    {
        return (float)$this->data['percentage'];
    }

    public function amount(): float
    {
        return (float)$this->data['amount'];
    }

    public function noSalary(): float
    {
        return (float)$this->data['noSalary'];
    }
}
