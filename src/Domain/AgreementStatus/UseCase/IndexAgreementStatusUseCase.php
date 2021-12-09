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
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\IndexAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Response\IndexAgreementStatusResponse;

final class IndexAgreementStatusUseCase
{
    private AgreementStatusRepository $agreementStatusRepository;

    public function __construct(AgreementStatusRepository $agreementStatusRepository)
    {
        $this->agreementStatusRepository = $agreementStatusRepository;
    }

    public function execute(IndexAgreementStatusRequest $request): IndexAgreementStatusResponse
    {
        $agreementStatus = $this->agreementStatusRepository->findBy($request);

        return new IndexAgreementStatusResponse($agreementStatus);
    }
}
