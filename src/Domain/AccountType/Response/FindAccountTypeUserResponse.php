<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindAccountTypeUserResponse implements ResponseInterface
{
    public $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }
}
