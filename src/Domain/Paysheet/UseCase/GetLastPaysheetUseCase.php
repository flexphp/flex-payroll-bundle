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
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\GetLastPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\GetLastPaysheetResponse;

final class GetLastPaysheetUseCase
{
    private PaysheetRepository $orderRepository;

    public function __construct(PaysheetRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function execute(GetLastPaysheetRequest $request)
    {
        $customers = $this->orderRepository->getLastPaysheetBy($request);

        return new GetLastPaysheetResponse($customers);
    }
}
