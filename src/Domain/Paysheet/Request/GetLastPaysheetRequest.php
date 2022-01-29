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

use FlexPHP\Messages\RequestInterface;

final class GetLastPaysheetRequest implements RequestInterface
{
    public $vehicleId;

    public $customerId;

    public $orderType;

    public $orderId;

    public function __construct(array $data)
    {
        $this->vehicleId = $data['vehicleId'] ?? 0;
        $this->customerId = $data['customerId'] ?? 0;
        $this->orderType = $data['orderType'] ?? null;
        $this->orderId = $data['orderId'] ?? 0;
    }
}
