<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod;

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\CreateAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\DeleteAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\IndexAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\ReadAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\UpdateAgreementPeriodRequest;

final class AgreementPeriodRepository
{
    private AgreementPeriodGateway $gateway;

    public function __construct(AgreementPeriodGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<AgreementPeriod>
     */
    public function findBy(IndexAgreementPeriodRequest $request): array
    {
        return \array_map(function (array $agreementPeriod) {
            return (new AgreementPeriodFactory())->make($agreementPeriod);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreateAgreementPeriodRequest $request): AgreementPeriod
    {
        $agreementPeriod = (new AgreementPeriodFactory())->make($request);

        $agreementPeriod->setId($this->gateway->push($agreementPeriod));

        return $agreementPeriod;
    }

    public function getById(ReadAgreementPeriodRequest $request): AgreementPeriod
    {
        $factory = new AgreementPeriodFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdateAgreementPeriodRequest $request): AgreementPeriod
    {
        $agreementPeriod = (new AgreementPeriodFactory())->make($request);

        $this->gateway->shift($agreementPeriod);

        return $agreementPeriod;
    }

    public function remove(DeleteAgreementPeriodRequest $request): AgreementPeriod
    {
        $factory = new AgreementPeriodFactory();
        $data = $this->gateway->get($factory->make($request));

        $agreementPeriod = $factory->make($data);

        $this->gateway->pop($agreementPeriod);

        return $agreementPeriod;
    }
}
