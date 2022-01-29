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
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetUserRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\FindPaysheetUserResponse;

final class FindPaysheetUserUseCase
{
    private PaysheetRepository $orderRepository;

    public function __construct(PaysheetRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function execute(FindPaysheetUserRequest $request): FindPaysheetUserResponse
    {
        $users = $this->orderRepository->findUsersBy($request);

        return new FindPaysheetUserResponse($users);
    }
}
