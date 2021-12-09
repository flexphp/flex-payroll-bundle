<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindAgreementPeriodUserResponse implements ResponseInterface
{
    public $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }
}
