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
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\ReadEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Response\ReadEmployeeTypeResponse;

final class ReadEmployeeTypeUseCase
{
    private EmployeeTypeRepository $employeeTypeRepository;

    public function __construct(EmployeeTypeRepository $employeeTypeRepository)
    {
        $this->employeeTypeRepository = $employeeTypeRepository;
    }

    public function execute(ReadEmployeeTypeRequest $request): ReadEmployeeTypeResponse
    {
        $employeeType = $this->employeeTypeRepository->getById($request);

        return new ReadEmployeeTypeResponse($employeeType);
    }
}
