<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet;

use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\PayrollGateway;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreateEPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreatePaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreatePrepaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\DeletePaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetAlternativeProductRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetHistoryServiceRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetPaysheetStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\GetLastPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\IndexPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\UpdatePaysheetRequest;

final class PaysheetRepository
{
    private PaysheetGateway $gateway;

    private PayrollGateway $payrollGateway;

    public function __construct(PaysheetGateway $gateway, PayrollGateway $payrollGateway)
    {
        $this->gateway = $gateway;
        $this->payrollGateway = $payrollGateway;
    }

    /**
     * @return array<Paysheet>
     */
    public function findBy(IndexPaysheetRequest $request): array
    {
        return \array_map(function (array $paysheet) use ($request) {
            $paysheet = (new PaysheetFactory())->make($paysheet);
            $paysheet->withLastPayroll($this->payrollGateway, $request->_offset);

            return $paysheet;
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreatePaysheetRequest $request): Paysheet
    {
        $paysheet = (new PaysheetFactory())->make($request);

        $paysheet->setId($this->gateway->push($paysheet));

        return $paysheet;
    }

    public function getById(ReadPaysheetRequest $request): Paysheet
    {
        $factory = new PaysheetFactory();
        $data = $this->gateway->get($factory->make($request));

        $paysheet = $factory->make($data);
        $paysheet->withLastPayroll($this->payrollGateway, 0);

        return $paysheet;
    }

    public function change(UpdatePaysheetRequest $request): Paysheet
    {
        $paysheet = (new PaysheetFactory())->make($request);

        $this->gateway->shift($paysheet);

        return $paysheet;
    }

    public function remove(DeletePaysheetRequest $request): Paysheet
    {
        $factory = new PaysheetFactory();
        $data = $this->gateway->get($factory->make($request));

        $paysheet = $factory->make($data);

        $this->gateway->pop($paysheet);

        return $paysheet;
    }

    public function findPayrollTypesBy(FindPaysheetPayrollTypeRequest $request): array
    {
        return $this->gateway->filterPayrollTypes($request, $request->_page, $request->_limit);
    }

    public function findEmployeesBy(FindPaysheetEmployeeRequest $request): array
    {
        return $this->gateway->filterEmployees($request, $request->_page, $request->_limit);
    }

    public function findAgreementsBy(FindPaysheetAgreementRequest $request): array
    {
        return $this->gateway->filterAgreements($request, $request->_page, $request->_limit);
    }

    public function findPaysheetStatusBy(FindPaysheetPaysheetStatusRequest $request): array
    {
        return $this->gateway->filterPaysheetStatus($request, $request->_page, $request->_limit);
    }

//     public function findHistoryServiceBy(FindPaysheetHistoryServiceRequest $request): array
//     {
//         return $this->gateway->filterHistoryServices($request, 1);
//     }

//     public function getLastPaysheetBy(GetLastPaysheetRequest $request): array
//     {
//         return $this->gateway->getLastPaysheet($request);
//     }

//     public function findAlternativeProductBy(FindPaysheetAlternativeProductRequest $request): array
//     {
//         return $this->gateway->getAlternativeProducts($request, 1);
//     }

    public function getPrepaysheetData(CreatePrepaysheetRequest $request): array
    {
        return $this->gateway->getPrepaysheetData($request);
    }

    public function getEPayrollData(CreateEPayrollRequest $request): array
    {
        return $this->gateway->getEPayrollData($request);
    }

    public function payrollGateway(): PayrollGateway
    {
        return $this->payrollGateway;
    }
}
