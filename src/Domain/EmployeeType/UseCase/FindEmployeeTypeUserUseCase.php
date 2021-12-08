<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\EmployeeTypeRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\FindEmployeeTypeUserRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Response\FindEmployeeTypeUserResponse;

final class FindEmployeeTypeUserUseCase
{
    private EmployeeTypeRepository $employeeTypeRepository;

    public function __construct(EmployeeTypeRepository $employeeTypeRepository)
    {
        $this->employeeTypeRepository = $employeeTypeRepository;
    }

    public function execute(FindEmployeeTypeUserRequest $request): FindEmployeeTypeUserResponse
    {
        $users = $this->employeeTypeRepository->findUsersBy($request);

        return new FindEmployeeTypeUserResponse($users);
    }
}
