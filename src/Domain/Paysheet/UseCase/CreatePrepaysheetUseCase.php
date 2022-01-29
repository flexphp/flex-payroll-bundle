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

use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreatePrepaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\CreatePrepaysheetResponse;
use Exception;
use JsonException;
use NumberFormatter;

final class CreatePrepaysheetUseCase
{
    private PaysheetRepository $orderRepository;

    public function __construct(PaysheetRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function execute(CreatePrepaysheetRequest $request): CreatePrepaysheetResponse
    {
        if (!empty($request->orderId)) {
            $order = $this->orderRepository->getById(new ReadPaysheetRequest($request->orderId));
        }

        if (empty($order) || !$order->id()) {
            throw new Exception(\sprintf('Paysheet not exist [%d]', $request->id ?? 0), 404);
        }

        $data = $this->orderRepository->getPrepaysheetData($request);

        if (empty($data)) {
            throw new Exception(\sprintf('Paysheet data not found [%d]', $request->id ?? 0), 404);
        }

        $pdftkDriver = $_ENV['PDFTK_DRIVER'] ?? 'local';
        $pdftkStyle = $_ENV['PDFTK_STYLE'] ?? 'normal';
        $pdftkTemplate = $_ENV['PDFTK_TEMPLATE'] ?? '';

        $order = $data['order'];
        $details = $data['details'];
        $payments = $data['payments'];

        $file = \rtrim(\sys_get_temp_dir(), \DIRECTORY_SEPARATOR) . '/' . \date('U') . '.fpdf';
        $output = \rtrim(\sys_get_temp_dir(), \DIRECTORY_SEPARATOR) . '/' . \date('U') . '.pdf';
        $filename = ($order['orderId'] ?? \date('U')) . '.pdf';
        $template = \realpath(__DIR__ . '/../Asset/Template.pdf');

        if (!empty($pdftkTemplate)) {
            $template = \realpath($pdftkTemplate);
        }

        if (!\is_string($template) || !\file_exists($template)) {
            throw new Exception('File [Template] not found: ' . $pdftkTemplate, 400);
        }

        if ($pdftkStyle === 'normal') {
            $this->createFPDFNormal($file, $order, $details, $payments);
        } elseif ($pdftkStyle === 'short') {
            $this->createFPDFShort($file, $order, $details);
        } else {
            throw new Exception('Pdf [Style] not supported', 500);
        }

        if (!\file_exists($file)) {
            throw new Exception('File [FPDF] not found: ' . $file, 400);
        }

        if ($pdftkDriver === 'local') {
            $this->addPDFBackgroundLocal($file, $template, $output);
        } elseif ($pdftkDriver === 'remote') {
            $this->addPDFBackgroundRemote($file, $template, $output);
        } else {
            throw new Exception('Pdf [Driver] not supported', 500);
        }

        $content = \file_get_contents($output);

        \unlink($file);
        \unlink($output);

        return new CreatePrepaysheetResponse($filename, $content);
    }

    private function currencyFormat(string $currency, float $number): string
    {
        $fmt = \numfmt_create('es_CO', NumberFormatter::CURRENCY);

        return \numfmt_format_currency($fmt, $number, $currency);
    }

    private function currencyFormatShort(string $currency, float $number): string
    {
        $fmt = \numfmt_create('es_CO', NumberFormatter::CURRENCY);
        $fmt->setTextAttribute(NumberFormatter::CURRENCY_CODE, $currency);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);

        return \numfmt_format_currency($fmt, $number, $currency);
    }

    private function createFPDFNormal(string $file, array $order, array $details, array $payments): void
    {
        $pdf = new CreatePDFUseCase('P', 'pt', 'letter');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        // $pdf->AddGrid(10, 10, 594, 770, 10);
        $pdf->SetFont('Arial', '', 8);

        $pdf->Text(90, 74, $order['payerName'] ?? '');
        $pdf->Text(345, 73, $order['orderId'] ?? '');
        $pdf->Text(475, 73, \strtoupper($order['orderStatusName'] ?? ''));

        if (!empty($order['payerDocumentNumber'])) {
            $pdf->Text(90, 88, $order['payerDocumentType'] ?? '');
            $pdf->Text(103, 88, $order['payerDocumentNumber'] ?? '');
        }
        $pdf->Text(345, 85, \substr($order['billCreatedAt'] ?? '', 0, 10));
        $pdf->Text(475, 85, \substr($order['billDueAt'] ?? '', 0, 10));

        $pdf->Text(90, 101, $order['payerEmail'] ?? '');
        $pdf->Text(330, 102, ($order['vehicleBrand'] ?? '') . ' ' . ($order['vehicleSerie'] ?? ''));

        $pdf->SetY(95);
        $pdf->SetX(526);
        $pdf->Cell(38, 10, $order['kilometers'] ?? 0, 0, 0, 'R');

        $pdf->Text(330, 116, $order['vehiclePlaca'] ?? '');

        $pdf->SetY(107);
        $pdf->SetX(455);
        $pdf->Cell(15, 10, ($order['vehicleOilQuantity'] ?? ''), 0, 0, 'R');

        $pdf->SetY(108);
        $pdf->SetX(526);
        $pdf->Cell(38, 10, ($order['kilometers'] ?? 0) + ($order['kilometersToChange'] ?? 0), 0, 0, 'R');

        $pdf->Text(42, 130, $order['payerAddress'] ?? '');
        $pdf->Text(187, 130, $order['payerCity'] ?? '');
        $pdf->Text(238, 130, $order['payerPhoneNumber'] ?? '');
        $pdf->Text(330, 130, $order['workerName'] ?? '');

        $paymentMethods = [];

        foreach ($payments as $payment) {
            $type = $payment['paymentMethodId'] === '10' ? 'C' : 'T';
            $paymentMethods[$type] = ($paymentMethods[$type] ?? 0) + $payment['amount'];
        }

        $pdf->SetFont('Arial', '', 9);

        $currency = 'COP';
        $totalPending = ($order['orderTotal'] ?? 0) - ($order['orderTotalPaid'] ?? 0);

        $pdf->SetY(278);
        $pdf->SetX(325);
        $pdf->Cell(95, 10, $this->currencyFormat($currency, (float)($paymentMethods['C'] ?? 0)), 0, 0, 'R');
        $pdf->SetY(292);
        $pdf->SetX(325);
        $pdf->Cell(95, 10, $this->currencyFormat($currency, (float)($paymentMethods['T'] ?? 0)), 0, 0, 'R');
        $pdf->SetY(306);
        $pdf->SetX(325);
        $pdf->Cell(95, 10, $this->currencyFormat($currency, (float)$totalPending), 0, 0, 'R');
        $pdf->SetY(320);
        $pdf->SetX(325);
        $pdf->Cell(95, 10, $this->currencyFormat($currency, (float)$order['orderTotalPaid'] ?? 0), 0, 0, 'R');

        $pdf->SetY(278);
        $pdf->SetX(473);
        $pdf->Cell(92, 10, $this->currencyFormat($currency, (float)($order['orderSubtotal'] ?? 0)), 0, 0, 'R');
        $pdf->SetY(292);
        $pdf->SetX(473);
        $pdf->Cell(92, 10, $this->currencyFormat($currency, (float)($order['orderTaxes'] ?? 0)), 0, 0, 'R');
        $pdf->SetY(306);
        $pdf->SetX(473);
        $pdf->Cell(92, 10, $this->currencyFormat($currency, (float)(-$order['orderDiscount'] ?? 0)), 0, 0, 'R');
        $pdf->SetY(320);
        $pdf->SetX(473);
        $pdf->Cell(92, 10, $this->currencyFormat($currency, (float)($order['orderTotal'] ?? 0)), 0, 0, 'R');

        $pdf->SetFont('Arial', '', 8);

        $counter = 1;
        $initRow = 156;
        $incrementRow = 10;
        $maxLengthProductName = 60;

        foreach ($details as $detail) {
            $quantity = $detail['quantity'] ?? 0;

            if (!$quantity) {
                continue;
            }

            $productName = $detail['productName'] ?? '';
            $rowsUsed = strlen($productName) / $maxLengthProductName;

            $pdf->Text(45, $initRow, $counter++);
            $pdf->SetY($initRow - $incrementRow + 2.5);
            $pdf->SetX(55);
            $pdf->MultiCell(230, $incrementRow, $productName);
            $pdf->Text(289, $initRow, $detail['productUnit'] ?? '');
            $pdf->SetY($initRow - $incrementRow + 2.5);
            $pdf->SetX(326);
            $pdf->Cell(42, $incrementRow, $quantity, 0, 0, 'C');
            $pdf->SetY($initRow - $incrementRow + 2.5);
            $pdf->SetX(368);
            $pdf->Cell(55, $incrementRow, $this->currencyFormat($currency, (float)($detail['price'] ?? 0)), 0, 0, 'R');
            $pdf->SetY($initRow - $incrementRow + 2.5);
            $pdf->SetX(423);
            $pdf->Cell(52, $incrementRow, $this->currencyFormat($currency, (float)($detail['tax'] ?? 0)), 0, 0, 'R');
            $pdf->SetY($initRow - $incrementRow + 2.5);
            $pdf->SetX(475);
            $pdf->Cell(90, $incrementRow, $this->currencyFormat($currency, (float)($detail['total'] ?? 0)), 0, 0, 'R');

            $initRow += ($incrementRow * $rowsUsed) + 2.5;

            if ($counter === 13) {
                break;
            }
        }

        $pdf->Output($file, 'F');

        if (!\file_exists($file)) {
            throw new Exception('File [FPDF] normal not found: ' . $file, 400);
        }
    }

    private function createFPDFShort(string $file, array $order, array $details): void
    {
        $pdf = new CreatePDFUseCase('P', 'mm', [85, 135]);

        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        // $pdf->AddGrid(5, 5, 75, 135, 5);
        $pdf->SetFont('Arial', '', 8);

        $payerName = $order['payerName'] ?? '';
        $payerName = \substr($payerName, 0, 24);

        $pdf->Text(25, 33, \substr($order['billCreatedAt'] ?? '', 0, 10));
        $pdf->Text(25, 38, $order['orderId'] ?? '');
        $pdf->Text(25, 43, $payerName);
        $pdf->Text(25, 48, $order['vehiclePlaca'] ?? '');

        $pdf->SetY(50);
        $pdf->SetX(15);
        $pdf->Cell(15, 5, $order['kilometers'] ?? 0, 0, 0, 'R');

        $pdf->SetY(50);
        $pdf->SetX(55);
        $pdf->Cell(15, 5, ($order['kilometers'] ?? 0) + ($order['kilometersToChange'] ?? 0), 0, 0, 'R');

        $pdf->SetFont('Arial', '', 11);
        $currency = 'COP';

        $pdf->SetY(117);
        $pdf->SetX(25);
        $pdf->Cell(40, 5, $this->currencyFormatShort($currency, (float)($order['orderTotal'] ?? 0)), 0, 0, 'R');

        $counter = 1;
        $initRow = 72;
        $incrementRow = 5;
        $maxLength = 18;

        $pdf->SetFont('Arial', '', 8);

        foreach ($details as $detail) {
            $quantity = $detail['quantity'] ?? 0;

            if (!$quantity) {
                continue;
            }

            $productName = $detail['productName'] ?? '';
            $productName = \strlen($productName) > $maxLength ? \substr($productName, 0, $maxLength) . '..' : $productName;

            $pdf->SetY($initRow - $incrementRow + 2);
            $pdf->SetX(3);
            $pdf->Cell(5, $incrementRow, $quantity, 0, 0, 'C');

            $pdf->SetY($initRow - $incrementRow + 2);
            $pdf->SetX(6);
            $pdf->Cell(5, $incrementRow, $productName);

            $pdf->SetY($initRow - $incrementRow + 2);
            $pdf->SetX(49);
            $pdf->Cell(5, $incrementRow, $this->currencyFormatShort($currency, (float)($detail['price'] ?? 0)), 0, 0, 'R');

            $pdf->SetY($initRow - $incrementRow + 2);
            $pdf->SetX(67);
            $pdf->Cell(5, $incrementRow, $this->currencyFormatShort($currency, (float)($detail['total'] ?? 0)), 0, 0, 'R');

            $initRow += $incrementRow;

            if ($counter === 10) {
                break;
            }

            ++$counter;
        }

        $pdf->Output($file, 'F');

        if (!\file_exists($file)) {
            throw new Exception('File [FPDF] short not found: ' . $file, 400);
        }
    }

    private function addPDFBackgroundLocal(string $file, string $template, string $output): void
    {
        if (empty($_ENV['PDFTK_PATH'])) {
            throw new Exception('PdfTK not defined', 500);
        }

        $response = [];
        $command = \sprintf(
            '%s %s background %s output %s 2>&1',
            \escapeshellarg($_ENV['PDFTK_PATH']),
            \escapeshellarg($file),
            \escapeshellarg($template),
            \escapeshellarg($output)
        );

        \exec($command, $response, $codeStatus);

        if ($codeStatus !== 0) {
            throw new Exception(\sprintf('%s: %s', $codeStatus, \implode(\PHP_EOL, $response)), 500);
        }

        if (!\file_exists($output)) {
            throw new Exception('File [Output] not found: ' . $output, 500);
        }
    }

    private function addPDFBackgroundRemote(string $file, string $template, string $output): void
    {
        if (empty($_ENV['PDFTK_URL']) || !\filter_var($_ENV['PDFTK_URL'], \FILTER_VALIDATE_URL)) {
            throw new Exception('PdfTK [Url] not defined or malformed', 500);
        }

        if (empty($_ENV['PDFTK_USERNAME'])) {
            throw new Exception('PdfTK [Username] not defined', 500);
        }

        if (empty($_ENV['PDFTK_PASSWORD'])) {
            throw new Exception('PdfTK [Password] not defined', 500);
        }

        $curl = \curl_init();
        $url = \rtrim($_ENV['PDFTK_URL'], '/') . '/background';

        \curl_setopt_array($curl, [
            \CURLOPT_URL => $url,
            \CURLOPT_RETURNTRANSFER => true,
            // \CURLOPT_FAILONERROR => true, // Not use this, allow codes 400+ in http
            \CURLOPT_ENCODING => '',
            \CURLOPT_MAXREDIRS => 10,
            \CURLOPT_TIMEOUT => 0,
            \CURLOPT_FOLLOWLOCATION => true,
            \CURLOPT_HTTP_VERSION => \CURL_HTTP_VERSION_1_1,
            \CURLOPT_CUSTOMREQUEST => 'POST',
            \CURLOPT_POSTFIELDS => '{
    "auth": {
        "username": "' . $_ENV['PDFTK_USERNAME'] . '",
        "password": "' . \hash('sha256', $_ENV['PDFTK_PASSWORD']) . '"
    },
    "data": {
        "type": "pdf",
        "id": ' . \time() . ',
        "attributes": {
            "background": "' . \base64_encode(\file_get_contents($template)) . '",
            "content": "' . \base64_encode(\file_get_contents($file)) . '",
            "encode": "base64"
        }
    }
}',
            \CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = \curl_exec($curl);
        $httpStatus = \curl_getinfo($curl, \CURLINFO_HTTP_CODE);

        if ($response === false || \curl_errno($curl)) {
            throw new Exception(\sprintf('Pdftk [Error] %d => %s', \curl_errno($curl), \curl_error($curl)), 500);
        }

        try {
            $responseJson = \json_decode($response, true, 512, \JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception(\sprintf(
                'Pdftk [Json] %d => %s: %s',
                $e->getCode(),
                $e->getMessage(),
                \htmlentities($response)
            ), 500);
        }

        if ($httpStatus >= 400) {
            throw new Exception(\sprintf(
                'Pdftk [HTTP] %s => %s',
                $responseJson['errors'][0]['status'] ?? 'No code error',
                $responseJson['errors'][0]['detail'] ?? 'No message error'
            ), 500);
        }

        \curl_close($curl);

        $content = $responseJson['data'][0]['attributes']['content'] ?? '';

        if (empty($content)) {
            throw new Exception('File [Empty] error', 500);
        }

        if (!\file_put_contents($output, \base64_decode($content))) {
            throw new Exception('File [Output] error: ' . $output, 500);
        }
    }
}
