<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response;

use FlexPHP\Messages\ResponseInterface;

final class CreatePrepaysheetResponse implements ResponseInterface
{
    public $filename;
    public $content;

    public function __construct(string $filename, string $content)
    {
        $this->filename = $filename;
        $this->content = $content;
    }
}
