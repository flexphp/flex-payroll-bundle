<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\AgreementStatusRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\ReadAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Response\ReadAgreementStatusResponse;

final class ReadAgreementStatusUseCase
{
    private AgreementStatusRepository $agreementStatusRepository;

    public function __construct(AgreementStatusRepository $agreementStatusRepository)
    {
        $this->agreementStatusRepository = $agreementStatusRepository;
    }

    public function execute(ReadAgreementStatusRequest $request): ReadAgreementStatusResponse
    {
        $agreementStatus = $this->agreementStatusRepository->getById($request);

        return new ReadAgreementStatusResponse($agreementStatus);
    }
}
