<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\IndexPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\IndexPaysheetResponse;

final class IndexPaysheetUseCase
{
    private PaysheetRepository $paysheetRepository;

    public function __construct(PaysheetRepository $paysheetRepository)
    {
        $this->paysheetRepository = $paysheetRepository;
    }

    public function execute(IndexPaysheetRequest $request): IndexPaysheetResponse
    {
        $paysheets = $this->paysheetRepository->findBy($request);

        return new IndexPaysheetResponse($paysheets);
    }
}
