<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request;

use FlexPHP\Messages\RequestInterface;

final class UpdateAgreementTypeRequest implements RequestInterface
{
    public $id;

    public $name;

    public $code;

    public $isActive;

    public $updatedBy;

    public function __construct(int $id, array $data, int $updatedBy)
    {
        $this->id = $id;
        $this->name = $data['name'] ?? null;
        $this->code = $data['code'] ?? null;
        $this->isActive = $data['isActive'] ?? null;
        $this->updatedBy = $updatedBy;
    }
}
