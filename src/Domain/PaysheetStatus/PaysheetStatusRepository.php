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

use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Request\CreatePaysheetStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Request\DeletePaysheetStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Request\IndexPaysheetStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Request\ReadPaysheetStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Request\UpdatePaysheetStatusRequest;

final class PaysheetStatusRepository
{
    private PaysheetStatusGateway $gateway;

    public function __construct(PaysheetStatusGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<PaysheetStatus>
     */
    public function findBy(IndexPaysheetStatusRequest $request): array
    {
        return \array_map(function (array $paysheetStatus) {
            return (new PaysheetStatusFactory())->make($paysheetStatus);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreatePaysheetStatusRequest $request): PaysheetStatus
    {
        $paysheetStatus = (new PaysheetStatusFactory())->make($request);

        $paysheetStatus->setId($this->gateway->push($paysheetStatus));

        return $paysheetStatus;
    }

    public function getById(ReadPaysheetStatusRequest $request): PaysheetStatus
    {
        $factory = new PaysheetStatusFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdatePaysheetStatusRequest $request): PaysheetStatus
    {
        $paysheetStatus = (new PaysheetStatusFactory())->make($request);

        $this->gateway->shift($paysheetStatus);

        return $paysheetStatus;
    }

    public function remove(DeletePaysheetStatusRequest $request): PaysheetStatus
    {
        $factory = new PaysheetStatusFactory();
        $data = $this->gateway->get($factory->make($request));

        $paysheetStatus = $factory->make($data);

        $this->gateway->pop($paysheetStatus);

        return $paysheetStatus;
    }
}
