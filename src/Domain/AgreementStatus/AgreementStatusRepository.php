<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus;

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\CreateAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\DeleteAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\IndexAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\ReadAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\UpdateAgreementStatusRequest;

final class AgreementStatusRepository
{
    private AgreementStatusGateway $gateway;

    public function __construct(AgreementStatusGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return array<AgreementStatus>
     */
    public function findBy(IndexAgreementStatusRequest $request): array
    {
        return \array_map(function (array $agreementStatus) {
            return (new AgreementStatusFactory())->make($agreementStatus);
        }, $this->gateway->search((array)$request, [], $request->_page, $request->_limit, $request->_offset));
    }

    public function add(CreateAgreementStatusRequest $request): AgreementStatus
    {
        $agreementStatus = (new AgreementStatusFactory())->make($request);

        $agreementStatus->setId($this->gateway->push($agreementStatus));

        return $agreementStatus;
    }

    public function getById(ReadAgreementStatusRequest $request): AgreementStatus
    {
        $factory = new AgreementStatusFactory();
        $data = $this->gateway->get($factory->make($request));

        return $factory->make($data);
    }

    public function change(UpdateAgreementStatusRequest $request): AgreementStatus
    {
        $agreementStatus = (new AgreementStatusFactory())->make($request);

        $this->gateway->shift($agreementStatus);

        return $agreementStatus;
    }

    public function remove(DeleteAgreementStatusRequest $request): AgreementStatus
    {
        $factory = new AgreementStatusFactory();
        $data = $this->gateway->get($factory->make($request));

        $agreementStatus = $factory->make($data);

        $this->gateway->pop($agreementStatus);

        return $agreementStatus;
    }
}
