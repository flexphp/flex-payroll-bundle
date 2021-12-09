<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Response;

use FlexPHP\Messages\ResponseInterface;

final class IndexAgreementResponse implements ResponseInterface
{
    public $agreements;

    public function __construct(array $agreements)
    {
        $this->agreements = $agreements;
    }
}
