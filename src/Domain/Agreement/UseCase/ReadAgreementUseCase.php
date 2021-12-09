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
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\ReadAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Response\ReadAgreementResponse;

final class ReadAgreementUseCase
{
    private AgreementRepository $agreementRepository;

    public function __construct(AgreementRepository $agreementRepository)
    {
        $this->agreementRepository = $agreementRepository;
    }

    public function execute(ReadAgreementRequest $request): ReadAgreementResponse
    {
        $agreement = $this->agreementRepository->getById($request);

        return new ReadAgreementResponse($agreement);
    }
}
