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

class TransportPresenter
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function amount(): float
    {
        return (float)$this->data['amount'];
    }

    public function viaticSalary(): float
    {
        return (float)$this->data['viaticSalary'];
    }

    public function viaticNoSalary(): float
    {
        return (float)$this->data['viaticNoSalary'];
    }
}
