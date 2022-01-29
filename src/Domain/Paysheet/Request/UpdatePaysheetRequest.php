<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request;

final class UpdatePaysheetRequest extends CreatePaysheetRequest
{
    public $id;

    public $updatedBy;

    public function __construct(int $id, array $data, int $updatedBy, ?string $timezone = null)
    {
        $this->id = $id;
        $this->updatedBy = $updatedBy;

        parent::__construct($data, $updatedBy, $timezone);
    }
}
