<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\AccountType;
use FlexPHP\Messages\ResponseInterface;

final class DeleteAccountTypeResponse implements ResponseInterface
{
    public $accountType;

    public function __construct(AccountType $accountType)
    {
        $this->accountType = $accountType;
    }
}
