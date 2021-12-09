<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\AgreementTypeRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\CreateAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Response\CreateAgreementTypeResponse;

final class CreateAgreementTypeUseCase
{
    private AgreementTypeRepository $agreementTypeRepository;

    public function __construct(AgreementTypeRepository $agreementTypeRepository)
    {
        $this->agreementTypeRepository = $agreementTypeRepository;
    }

    public function execute(CreateAgreementTypeRequest $request): CreateAgreementTypeResponse
    {
        return new CreateAgreementTypeResponse($this->agreementTypeRepository->add($request));
    }
}
