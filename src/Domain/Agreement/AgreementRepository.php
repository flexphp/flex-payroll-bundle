<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement;

use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\CreateAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\DeleteAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementCurrencyRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\IndexAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\ReadAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\UpdateAgreementRequest;

final class AgreementRepository
{
    private AgreementGateway $gateway;

    public function __construct(AgreementGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<Agreement>
     */
    public function findBy(IndexAgreementRequest $request): array
    {
        return \array_map(function (array $agreement) {
            return (new AgreementFactory())->make($agreement);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreateAgreementRequest $request): Agreement
    {
        $agreement = (new AgreementFactory())->make($request);

        $agreement->setId($this->gateway->push($agreement));

        return $agreement;
    }

    public function getById(ReadAgreementRequest $request): Agreement
    {
        $factory = new AgreementFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdateAgreementRequest $request): Agreement
    {
        $agreement = (new AgreementFactory())->make($request);

        $this->gateway->shift($agreement);

        return $agreement;
    }

    public function remove(DeleteAgreementRequest $request): Agreement
    {
        $factory = new AgreementFactory();
        $data = $this->gateway->get($factory->make($request));

        $agreement = $factory->make($data);

        $this->gateway->pop($agreement);

        return $agreement;
    }

    public function findEmployeesBy(FindAgreementEmployeeRequest $request): array
    {
        return $this->gateway->filterEmployees($request, $request->_page, $request->_limit);
    }

    public function findAgreementTypesBy(FindAgreementAgreementTypeRequest $request): array
    {
        return $this->gateway->filterAgreementTypes($request, $request->_page, $request->_limit);
    }

    public function findAgreementPeriodsBy(FindAgreementAgreementPeriodRequest $request): array
    {
        return $this->gateway->filterAgreementPeriods($request, $request->_page, $request->_limit);
    }

    public function findCurrenciesBy(FindAgreementCurrencyRequest $request): array
    {
        return $this->gateway->filterCurrencies($request, $request->_page, $request->_limit);
    }

    public function findAgreementStatusBy(FindAgreementAgreementStatusRequest $request): array
    {
        return $this->gateway->filterAgreementStatus($request, $request->_page, $request->_limit);
    }
}
