<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\AgreementRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Response\FindAgreementAgreementPeriodResponse;

final class FindAgreementAgreementPeriodUseCase
{
    private AgreementRepository $agreementRepository;

    public function __construct(AgreementRepository $agreementRepository)
    {
        $this->agreementRepository = $agreementRepository;
    }

    public function execute(FindAgreementAgreementPeriodRequest $request): FindAgreementAgreementPeriodResponse
    {
        $agreementPeriods = $this->agreementRepository->findAgreementPeriodsBy($request);

        return new FindAgreementAgreementPeriodResponse($agreementPeriods);
    }
}
