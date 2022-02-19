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

use DateTime;
use DateTimeInterface;

class BasicPresenter
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function days(): int
    {
        return (int)$this->data['days'];
    }

    public function amount(): float
    {
        return (float)$this->data['amount'];
    }

    public function paidAt(): DateTimeInterface
    {
        return new DateTime($this->data['paidAt']);
    }
}
