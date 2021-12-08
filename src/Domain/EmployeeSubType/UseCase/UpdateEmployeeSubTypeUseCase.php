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
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\UpdateEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Response\UpdateEmployeeSubTypeResponse;

final class UpdateEmployeeSubTypeUseCase
{
    private EmployeeSubTypeRepository $employeeSubTypeRepository;

    public function __construct(EmployeeSubTypeRepository $employeeSubTypeRepository)
    {
        $this->employeeSubTypeRepository = $employeeSubTypeRepository;
    }

    public function execute(UpdateEmployeeSubTypeRequest $request): UpdateEmployeeSubTypeResponse
    {
        return new UpdateEmployeeSubTypeResponse($this->employeeSubTypeRepository->change($request));
    }
}
