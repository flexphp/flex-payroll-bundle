<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AccountType\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\AccountTypeRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\DeleteAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Response\DeleteAccountTypeResponse;

final class DeleteAccountTypeUseCase
{
    private AccountTypeRepository $accountTypeRepository;

    public function __construct(AccountTypeRepository $accountTypeRepository)
    {
        $this->accountTypeRepository = $accountTypeRepository;
    }

    public function execute(DeleteAccountTypeRequest $request): DeleteAccountTypeResponse
    {
        return new DeleteAccountTypeResponse($this->accountTypeRepository->remove($request));
    }
}
