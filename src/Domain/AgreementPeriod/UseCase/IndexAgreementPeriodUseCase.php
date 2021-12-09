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
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\IndexAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Response\IndexAgreementPeriodResponse;

final class IndexAgreementPeriodUseCase
{
    private AgreementPeriodRepository $agreementPeriodRepository;

    public function __construct(AgreementPeriodRepository $agreementPeriodRepository)
    {
        $this->agreementPeriodRepository = $agreementPeriodRepository;
    }

    public function execute(IndexAgreementPeriodRequest $request): IndexAgreementPeriodResponse
    {
        $agreementPeriods = $this->agreementPeriodRepository->findBy($request);

        return new IndexAgreementPeriodResponse($agreementPeriods);
    }
}
