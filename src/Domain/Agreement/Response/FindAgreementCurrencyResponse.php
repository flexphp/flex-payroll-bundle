<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindAgreementCurrencyResponse implements ResponseInterface
{
    public $currencies;

    public function __construct(array $currencies)
    {
        $this->currencies = $currencies;
    }
}
