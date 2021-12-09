<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Response;

use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Agreement;
use FlexPHP\Messages\ResponseInterface;

final class DeleteAgreementResponse implements ResponseInterface
{
    public $agreement;

    public function __construct(Agreement $agreement)
    {
        $this->agreement = $agreement;
    }
}
