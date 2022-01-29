<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request;

use FlexPHP\Bundle\HelperBundle\Domain\Helper\DateTimeTrait;
use FlexPHP\Messages\RequestInterface;

final class CreateEPayrollNDRequest implements RequestInterface
{
    use DateTimeTrait;

    public $orderId;

    public $billId;

    public $offset;

    public function __construct(int $orderId, int $billId, ?string $timezone)
    {
        $this->orderId = $orderId;
        $this->billId = $billId;
        $this->offset = $this->getOffset($this->getTimezone($timezone));
    }
}
