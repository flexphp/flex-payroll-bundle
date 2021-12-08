<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\EmployeeSubTypeRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\FindEmployeeSubTypeUserRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Response\FindEmployeeSubTypeUserResponse;

final class FindEmployeeSubTypeUserUseCase
{
    private EmployeeSubTypeRepository $employeeSubTypeRepository;

    public function __construct(EmployeeSubTypeRepository $employeeSubTypeRepository)
    {
        $this->employeeSubTypeRepository = $employeeSubTypeRepository;
    }

    public function execute(FindEmployeeSubTypeUserRequest $request): FindEmployeeSubTypeUserResponse
    {
        $users = $this->employeeSubTypeRepository->findUsersBy($request);

        return new FindEmployeeSubTypeUserResponse($users);
    }
}
