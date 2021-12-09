<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Bank\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\Bank\BankRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\UpdateBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Response\UpdateBankResponse;

final class UpdateBankUseCase
{
    private BankRepository $bankRepository;

    public function __construct(BankRepository $bankRepository)
    {
        $this->bankRepository = $bankRepository;
    }

    public function execute(UpdateBankRequest $request): UpdateBankResponse
    {
        return new UpdateBankResponse($this->bankRepository->change($request));
    }
}
