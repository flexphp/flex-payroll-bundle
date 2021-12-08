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
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\ReadEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Response\ReadEmployeeSubTypeResponse;

final class ReadEmployeeSubTypeUseCase
{
    private EmployeeSubTypeRepository $employeeSubTypeRepository;

    public function __construct(EmployeeSubTypeRepository $employeeSubTypeRepository)
    {
        $this->employeeSubTypeRepository = $employeeSubTypeRepository;
    }

    public function execute(ReadEmployeeSubTypeRequest $request): ReadEmployeeSubTypeResponse
    {
        $employeeSubType = $this->employeeSubTypeRepository->getById($request);

        return new ReadEmployeeSubTypeResponse($employeeSubType);
    }
}
