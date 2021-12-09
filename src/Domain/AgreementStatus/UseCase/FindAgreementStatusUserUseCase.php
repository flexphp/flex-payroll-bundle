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
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\FindAgreementStatusUserRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Response\FindAgreementStatusUserResponse;

final class FindAgreementStatusUserUseCase
{
    private AgreementStatusRepository $agreementStatusRepository;

    public function __construct(AgreementStatusRepository $agreementStatusRepository)
    {
        $this->agreementStatusRepository = $agreementStatusRepository;
    }

    public function execute(FindAgreementStatusUserRequest $request): FindAgreementStatusUserResponse
    {
        $users = $this->agreementStatusRepository->findUsersBy($request);

        return new FindAgreementStatusUserResponse($users);
    }
}
