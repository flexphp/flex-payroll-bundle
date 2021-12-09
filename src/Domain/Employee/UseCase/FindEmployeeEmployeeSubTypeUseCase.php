<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase;

use FlexPHP\Bundle\PayrollBundle\Domain\Employee\EmployeeRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Response\FindEmployeeEmployeeSubTypeResponse;

final class FindEmployeeEmployeeSubTypeUseCase
{
    private EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function execute(FindEmployeeEmployeeSubTypeRequest $request): FindEmployeeEmployeeSubTypeResponse
    {
        $employeeSubTypes = $this->employeeRepository->findEmployeeSubTypesBy($request);

        return new FindEmployeeEmployeeSubTypeResponse($employeeSubTypes);
    }
}
