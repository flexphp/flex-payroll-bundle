<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus;

use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\CreatePayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\DeletePayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\IndexPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\ReadPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\UpdatePayrollStatusRequest;

final class PayrollStatusRepository
{
    private PayrollStatusGateway $gateway;

    public function __construct(PayrollStatusGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<PayrollStatus>
     */
    public function findBy(IndexPayrollStatusRequest $request): array
    {
        return \array_map(function (array $payrollStatus) {
            return (new PayrollStatusFactory())->make($payrollStatus);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreatePayrollStatusRequest $request): PayrollStatus
    {
        $payrollStatus = (new PayrollStatusFactory())->make($request);

        $payrollStatus->setId($this->gateway->push($payrollStatus));

        return $payrollStatus;
    }

    public function getById(ReadPayrollStatusRequest $request): PayrollStatus
    {
        $factory = new PayrollStatusFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdatePayrollStatusRequest $request): PayrollStatus
    {
        $payrollStatus = (new PayrollStatusFactory())->make($request);

        $this->gateway->shift($payrollStatus);

        return $payrollStatus;
    }

    public function remove(DeletePayrollStatusRequest $request): PayrollStatus
    {
        $factory = new PayrollStatusFactory();
        $data = $this->gateway->get($factory->make($request));

        $payrollStatus = $factory->make($data);

        $this->gateway->pop($payrollStatus);

        return $payrollStatus;
    }
}
