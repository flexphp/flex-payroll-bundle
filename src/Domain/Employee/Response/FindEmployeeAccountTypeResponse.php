<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindEmployeeAccountTypeResponse implements ResponseInterface
{
    public $accountTypes;

    public function __construct(array $accountTypes)
    {
        $this->accountTypes = $accountTypes;
    }
}
