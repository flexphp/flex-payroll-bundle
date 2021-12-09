<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindEmployeeUserResponse implements ResponseInterface
{
    public $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }
}
