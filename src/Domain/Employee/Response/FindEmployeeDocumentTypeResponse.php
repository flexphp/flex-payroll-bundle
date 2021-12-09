<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee\Response;

use FlexPHP\Messages\ResponseInterface;

final class FindEmployeeDocumentTypeResponse implements ResponseInterface
{
    public $documentTypes;

    public function __construct(array $documentTypes)
    {
        $this->documentTypes = $documentTypes;
    }
}
