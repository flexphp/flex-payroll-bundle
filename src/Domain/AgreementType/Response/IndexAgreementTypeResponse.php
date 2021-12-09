<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Response;

use FlexPHP\Messages\ResponseInterface;

final class IndexAgreementTypeResponse implements ResponseInterface
{
    public $agreementTypes;

    public function __construct(array $agreementTypes)
    {
        $this->agreementTypes = $agreementTypes;
    }
}
