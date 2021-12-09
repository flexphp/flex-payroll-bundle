<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\AgreementPeriodRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\ReadAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Response\ReadAgreementPeriodResponse;

final class ReadAgreementPeriodUseCase
{
    private AgreementPeriodRepository $agreementPeriodRepository;

    public function __construct(AgreementPeriodRepository $agreementPeriodRepository)
    {
        $this->agreementPeriodRepository = $agreementPeriodRepository;
    }

    public function execute(ReadAgreementPeriodRequest $request): ReadAgreementPeriodResponse
    {
        $agreementPeriod = $this->agreementPeriodRepository->getById($request);

        return new ReadAgreementPeriodResponse($agreementPeriod);
    }
}
