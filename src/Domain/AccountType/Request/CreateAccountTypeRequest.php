<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request;

use FlexPHP\Messages\RequestInterface;

final class CreateAccountTypeRequest implements RequestInterface
{
    public $id;

    public $name;

    public $isActive;

    public $createdBy;

    public function __construct(array $data, int $createdBy)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->isActive = $data['isActive'] ?? null;
        $this->createdBy = $createdBy;
    }
}
