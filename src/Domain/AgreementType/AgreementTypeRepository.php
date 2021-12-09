<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementType;

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\CreateAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\DeleteAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\IndexAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\ReadAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\UpdateAgreementTypeRequest;

final class AgreementTypeRepository
{
    private AgreementTypeGateway $gateway;

    public function __construct(AgreementTypeGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<AgreementType>
     */
    public function findBy(IndexAgreementTypeRequest $request): array
    {
        return \array_map(function (array $agreementType) {
            return (new AgreementTypeFactory())->make($agreementType);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreateAgreementTypeRequest $request): AgreementType
    {
        $agreementType = (new AgreementTypeFactory())->make($request);

        $agreementType->setId($this->gateway->push($agreementType));

        return $agreementType;
    }

    public function getById(ReadAgreementTypeRequest $request): AgreementType
    {
        $factory = new AgreementTypeFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdateAgreementTypeRequest $request): AgreementType
    {
        $agreementType = (new AgreementTypeFactory())->make($request);

        $this->gateway->shift($agreementType);

        return $agreementType;
    }

    public function remove(DeleteAgreementTypeRequest $request): AgreementType
    {
        $factory = new AgreementTypeFactory();
        $data = $this->gateway->get($factory->make($request));

        $agreementType = $factory->make($data);

        $this->gateway->pop($agreementType);

        return $agreementType;
    }
}
