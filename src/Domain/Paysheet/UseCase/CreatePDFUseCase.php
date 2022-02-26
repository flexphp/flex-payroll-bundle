<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase;

// require_once __DIR__ . '/../../../vendor/setasign/fpdf/fpdf.php';

// use FPDF;

final class CreatePDFUseCase// extends FPDF
{
    public function AddGrid($x1, $y1, $x2, $y2, $Dist = 8): void
    {
        $this->AddGridRow($x1, $y1, $x2);
        $this->AddGridRow($x1, $y2, $x2);
        $this->AddGridColumn($x1, $y1, $y2);
        $this->AddGridColumn($x2, $y1, $y2);

        $NoHorz = ($y2 - $y1) / $Dist;
        $NoVert = ($x2 - $x1) / $Dist;

        for ($i = 1; $i < $NoHorz; $i++) {
            $this->AddGridRow($x1, $y1 + ($i * $Dist), $x2);
        }

        for ($i = 1; $i < $NoVert; $i++) {
            $this->AddGridColumn($x1 + ($i * $Dist), $y1, $y2);
        }
    }

    public function AddGridRow($x1, $y, $x2): void
    {
        $this->SetFont('Courier', '', 5);
        $this->Line($x1, $y, $x2, $y);
        $this->Text($x1 - (\strlen((string)$y) * 3) - 1.5, $y + 1.5, $y);
        $this->Text($x2 + 1.5, $y + 1.5, $y);
    }

    public function AddGridColumn($x, $y1, $y2): void
    {
        $this->SetFont('Courier', '', 5);
        $this->Line($x, $y1, $x, $y2);
        $this->Text($x - (\strlen((string)$x) * 1.5), $y1 - 2, $x);
        $this->Text($x - 3, $y2 + 5.5, $x);
    }

    public function Text($x, $y, $txt): void
    {
        // parent::Text($x, $y, \utf8_decode((string)$txt));
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = ''): void
    {
        // parent::Cell($w, $h, \utf8_decode((string)$txt), $border, $ln, $align, $fill, $link);
    }
}
