<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindPaysheetAlternativeProductResponse implements ResponseInterface
{
    public $alternativeProducts;

    public function __construct(array $alternativeProducts)
    {
        $this->alternativeProducts = $alternativeProducts;
    }
}
