<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\PaysheetStatusRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Request\ReadPaysheetStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Response\ReadPaysheetStatusResponse;

final class ReadPaysheetStatusUseCase
{
    private PaysheetStatusRepository $paysheetStatusRepository;

    public function __construct(PaysheetStatusRepository $paysheetStatusRepository)
    {
        $this->paysheetStatusRepository = $paysheetStatusRepository;
    }

    public function execute(ReadPaysheetStatusRequest $request): ReadPaysheetStatusResponse
    {
        $paysheetStatus = $this->paysheetStatusRepository->getById($request);

        return new ReadPaysheetStatusResponse($paysheetStatus);
    }
}
