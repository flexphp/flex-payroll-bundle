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
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Request\UpdatePaysheetStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetStatus\Response\UpdatePaysheetStatusResponse;

final class UpdatePaysheetStatusUseCase
{
    private PaysheetStatusRepository $paysheetStatusRepository;

    public function __construct(PaysheetStatusRepository $paysheetStatusRepository)
    {
        $this->paysheetStatusRepository = $paysheetStatusRepository;
    }

    public function execute(UpdatePaysheetStatusRequest $request): UpdatePaysheetStatusResponse
    {
        return new UpdatePaysheetStatusResponse($this->paysheetStatusRepository->change($request));
    }
}
