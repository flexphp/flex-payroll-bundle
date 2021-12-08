<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindEmployeeTypeUserResponse implements ResponseInterface
{
    public $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }
}
