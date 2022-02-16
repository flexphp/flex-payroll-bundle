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

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Bill;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\BillRepository;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Request\UpdateBillRequest;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\UseCase\UpdateBillUseCase;
use FlexPHP\Bundle\InvoiceBundle\Domain\BillStatus\BillStatus;
use FlexPHP\Bundle\LocationBundle\Domain\City\City;
use FlexPHP\Bundle\LocationBundle\Domain\City\CityRepository;
use FlexPHP\Bundle\LocationBundle\Domain\City\Request\ReadCityRequest;
use FlexPHP\Bundle\LocationBundle\Domain\City\UseCase\ReadCityUseCase;
use FlexPHP\Bundle\LocationBundle\Domain\Country\Country;
use FlexPHP\Bundle\LocationBundle\Domain\Country\CountryRepository;
use FlexPHP\Bundle\LocationBundle\Domain\Country\Request\ReadCountryRequest;
use FlexPHP\Bundle\LocationBundle\Domain\Country\UseCase\ReadCountryUseCase;
use FlexPHP\Bundle\LocationBundle\Domain\State\Request\ReadStateRequest;
use FlexPHP\Bundle\LocationBundle\Domain\State\State;
use FlexPHP\Bundle\LocationBundle\Domain\State\StateRepository;
use FlexPHP\Bundle\LocationBundle\Domain\State\UseCase\ReadStateUseCase;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\Numeration as Setting;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\NumerationRepository;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\Request\IndexNumerationRequest;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\UseCase\IndexNumerationUseCase;
use FlexPHP\Bundle\NumerationBundle\Domain\Provider\Provider;
use FlexPHP\Bundle\NumerationBundle\Domain\Provider\ProviderRepository;
use FlexPHP\Bundle\NumerationBundle\Domain\Provider\Request\IndexProviderRequest;
use FlexPHP\Bundle\NumerationBundle\Domain\Provider\UseCase\IndexProviderUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Agreement;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\AgreementRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Employee;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\EmployeeRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Payment;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\PaymentRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Request\IndexPaymentRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\UseCase\IndexPaymentUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Paysheet;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\CreateEPayrollResponse;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\PaysheetDetailRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\Request\IndexPaysheetDetailRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\UseCase\IndexPaysheetDetailUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\ProductRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\Request\ReadProductRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\UseCase\ReadProductUseCase;
use FlexPHP\eInvoice\Contract\MerchantContract;
use FlexPHP\ePayroll\Constant\PaymentMeanCode;
use FlexPHP\ePayroll\Constant\RecurrenceCode;
use FlexPHP\ePayroll\Constant\Status;
use FlexPHP\ePayroll\Constant\TaxType;
use FlexPHP\ePayroll\Contract\AccruedContract;
use FlexPHP\ePayroll\Contract\AgreementContract;
use FlexPHP\ePayroll\Contract\AnexContract;
use FlexPHP\ePayroll\Contract\BasicContract;
use FlexPHP\ePayroll\Contract\BillContract as InvoiceContract;
use FlexPHP\ePayroll\Contract\DeductionContract;
use FlexPHP\ePayroll\Contract\EmployeeContract;
use FlexPHP\ePayroll\Contract\EmployerContract;
use FlexPHP\ePayroll\Contract\EntityContract;
use FlexPHP\ePayroll\Contract\GeneralContract;
use FlexPHP\ePayroll\Contract\HealthContract;
use FlexPHP\ePayroll\Contract\HourContract;
use FlexPHP\ePayroll\Contract\LocationContract;
use FlexPHP\ePayroll\Contract\NumerationContract;
use FlexPHP\ePayroll\Contract\PaymentContract;
use FlexPHP\ePayroll\Contract\PensionContract;
use FlexPHP\ePayroll\Contract\PeriodContract;
use FlexPHP\ePayroll\Contract\TransportContract;
use FlexPHP\ePayroll\Payroll as EPayroll;
use FlexPHP\ePayroll\Provider\Response\DownloadResponse;
use FlexPHP\ePayroll\Provider\Response\StatusResponse;
use FlexPHP\ePayroll\Provider\Response\UploadResponse;
use FlexPHP\ePayroll\Struct\Accrued;
use FlexPHP\ePayroll\Struct\Agreement as Contract;
use FlexPHP\ePayroll\Struct\Anex;
use FlexPHP\ePayroll\Struct\Basic;
use FlexPHP\ePayroll\Struct\Deduction;
use FlexPHP\ePayroll\Struct\Employee as Clerk;
use FlexPHP\ePayroll\Struct\Employer;
use FlexPHP\ePayroll\Struct\Entity;
use FlexPHP\ePayroll\Struct\Entity as Company;
use FlexPHP\ePayroll\Struct\General;
use FlexPHP\ePayroll\Struct\Health;
use FlexPHP\ePayroll\Struct\Hour;
use FlexPHP\ePayroll\Struct\Location;
use FlexPHP\ePayroll\Struct\Numeration;
use FlexPHP\ePayroll\Struct\Payment as Payout;
use FlexPHP\ePayroll\Struct\Pension;
use FlexPHP\ePayroll\Struct\Period;
use FlexPHP\ePayroll\Struct\Transport;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractEPayrollUseCase
{
    protected const RECURRENCE_CODE = [
        1 => RecurrenceCode::SEVEN,
        2 => RecurrenceCode::TEN,
        3 => RecurrenceCode::FOURTEEN,
        4 => RecurrenceCode::FIFTEEN,
        5 => RecurrenceCode::THIRTY,
    ];

    protected PaysheetRepository $paysheetRepository;

    protected ProviderRepository $providerRepository;

    protected NumerationRepository $numerationRepository;

    protected EmployeeRepository $employeeRepository;

    protected AgreementRepository $agreementRepository;

    //     protected CityRepository $cityRepository;

    //     protected StateRepository $stateRepository;

    //     protected CountryRepository $countryRepository;

    //     protected BillRepository $billRepository;

    //     protected PaysheetDetailRepository $paysheetDetailRepository;

    //     protected ProductRepository $productRepository;

    //     protected PaymentRepository $paymentRepository;

    protected LoggerInterface $logger;

    protected bool $testingMode = false;

    public function __construct(
        PaysheetRepository $paysheetRepository,
        ProviderRepository $providerRepository,
        NumerationRepository $numerationRepository,
        EmployeeRepository $employeeRepository,
        AgreementRepository $agreementRepository,
        // CityRepository $cityRepository,
        // StateRepository $stateRepository,
        // CountryRepository $countryRepository,
        // BillRepository $billRepository,
        // PaysheetDetailRepository $paysheetDetailRepository,
        // ProductRepository $productRepository,
        // PaymentRepository $paymentRepository,
        LoggerInterface $logger
    ) {
        $this->paysheetRepository = $paysheetRepository;
        $this->providerRepository = $providerRepository;
        $this->numerationRepository = $numerationRepository;
        $this->employeeRepository = $employeeRepository;
        $this->agreementRepository = $agreementRepository;
        // $this->cityRepository = $cityRepository;
        // $this->stateRepository = $stateRepository;
        // $this->countryRepository = $countryRepository;
        // $this->billRepository = $billRepository;
        // $this->paysheetDetailRepository = $paysheetDetailRepository;
        // $this->productRepository = $productRepository;
        // $this->paymentRepository = $paymentRepository;
        $this->logger = $logger;
    }

    public function getRecurrenceCode(string $period): string
    {
        return self::RECURRENCE_CODE[$period];
    }

    protected function processEPayroll(
        InvoiceContract $invoice,
        Bill $bill,
        Provider $provider,
        MerchantContract $sender,
        MerchantContract $receiver
    ): CreateEPayrollResponse {
        $invoice->numeration()->setCurrentNumber($bill->number());

        $ePayroll = $this->getEPayroll($bill, $provider);

        if (!$ePayroll) {
            return new CreateEPayrollResponse($bill->status(), $bill->message());
        }

        $sleep = false;

        if (\in_array($bill->status(), [BillStatus::PENDING, BillStatus::PROCESSING, BillStatus::REJECTED])) {
            $sleep = true;
            $bill = $this->upload($ePayroll, $bill, $sender, $receiver, $invoice);
        }

        if (!$bill->traceId()) {
            return new CreateEPayrollResponse($bill->status(), $bill->message());
        }

        if ($bill->status() !== BillStatus::AVAILABLE) {
            $bill = $this->status($ePayroll, $bill);
        }

        if ($bill->status() !== BillStatus::AVAILABLE) {
            return new CreateEPayrollResponse($bill->status(), $bill->message());
        }

        if ($sleep) {
            \sleep(5);
        }

        if (!$bill->pdfPath()) {
            $bill = $this->pdf($ePayroll, $bill, $bill->prefix(), (string)$bill->number());
        }

        if (!$bill->pdfPath()) {
            return new CreateEPayrollResponse($bill->status(), $bill->message());
        }

        if (!$bill->xmlPath()) {
            $bill = $this->xml($ePayroll, $bill, $bill->prefix(), (string)$bill->number());
        }

        if (!$bill->xmlPath()) {
            return new CreateEPayrollResponse($bill->status(), $bill->message());
        }

        if ($bill->pdfPath() && $bill->xmlPath() && !$bill->downloadedAt()) {
            $bill->setDownloadedAt(new DateTime());

            $this->updateBill($bill);
        }

        return $this->getResponseOk($bill);
    }

    protected function getResponseOk(Bill $bill): CreateEPayrollResponse
    {
        $filename = \hash('sha256', $bill->getNumeration() . '.pdf');

        if ($this->testingMode) {
            $filename = 'fake.pdf';
        }

        $content = \file_get_contents($bill->pdfPath() . \DIRECTORY_SEPARATOR . $filename);

        return new CreateEPayrollResponse($bill->status(), $bill->message(), $bill->getNumeration(), $content);
    }

    protected function getPeriod(
        string $datePay,
        string $dateIn,
        string $dateStart,
        string $dateEnd,
        ?string $dateOut = null
    ): PeriodContract {
        return new Period(
            new DateTime($datePay),
            new DateTime($dateIn),
            new DateTime($dateStart),
            new DateTime($dateEnd),
            ($dateOut ? new DateTime($dateOut) : null),
        );
    }

    protected function getNumeration(
        string $prefix,
        int $consecutive,
        ?string $identifier = null
    ): NumerationContract {
        return new Numeration($prefix, $consecutive, $identifier);
    }

    protected function getLocation(
        string $countryCode,
        string $stateCode,
        string $cityCode,
        string $language,
        ?string $address = null
    ): LocationContract {
        $location = new Location($countryCode, $stateCode, $cityCode, $language);

        if ($address) {
            $location->setAddress($address);
        }

        return $location;
    }

    protected function getGeneral(
        string $datetime,
        string $recurrenceCode,
        string $currencyCode,
        float $trm = 1.0
    ): GeneralContract {
        return new General(new DateTime($datetime, new DateTimeZone('America/Bogota')), $recurrenceCode, $currencyCode, $trm);
    }

    protected function getEntity(
        string $document,
        string $documentType,
        string $brandName,
        string $legalName
    ): EntityContract {
        return new Company($document, $documentType, $brandName, $legalName);
    }

    protected function getEmployer(
        EntityContract $entity,
        LocationContract $location
    ): EmployerContract {
        return new Employer($entity, $location);
    }

    protected function getAgreement(
        string $typeCode,
        string $subTypeCode,
        string $contractCode,
        float $salary,
        bool $integralSalary,
        bool $highRisk
    ): AgreementContract {
        return new Contract($typeCode, $subTypeCode, $contractCode, $salary, $integralSalary, $highRisk);
    }

    protected function getEmployee(
        string $identifier,
        string $document,
        string $documentType,
        string $firstName,
        string $secondName,
        string $firstSurname,
        string $secondSurname,
        PaymentContract $payment,
        AgreementContract $agreement,
        LocationContract $location
    ): EmployeeContract {
        return new Clerk(
            $identifier,
            $document,
            $documentType,
            $firstName,
            $secondName,
            $firstSurname,
            $secondSurname,
            $payment,
            $agreement,
            $location
        );
    }

    protected function getPayment(
        string $meanCode,
        string $methodCode,
        string $bankName,
        string $accountType,
        string $accountNumber,
        string $date
    ): PaymentContract {
        return new Payout(
            $meanCode,
            $methodCode,
            $bankName,
            $accountType,
            $accountNumber,
            new DateTime($date, new DateTimeZone('America/Bogota'))
        );
    }

    protected function getHour(
        string $type,
        float $quantity,
        float $percentage,
        float $amount,
        ?string $start,
        ?string $end
    ): HourContract {
        $hour = new Hour($type, $quantity, $percentage, $amount);

        if ($start) {
            $hour->setStart(new DateTime($start));
        }

        if ($end) {
            $hour->setEnd(new DateTime($end));
        }

        return $hour;
    }

    protected function getAccrued(
        BasicContract $basic,
        ?TransportContract $transport = null
    ): AccruedContract {
        $accrued = new Accrued($basic);

        if ($transport) {
            $accrued->setTransport($transport);
        }

        return $accrued;
    }

    protected function getBasic(
        int $days,
        float $amount
    ): BasicContract {
        return new Basic($days, $amount);
    }

    protected function getTransport(
        float $subsidy,
        float $viaticSalary,
        float $viaticNoSalary
    ): TransportContract {
        return new Transport($subsidy, $viaticSalary, $viaticNoSalary);
    }

    protected function getDeduction(
        HealthContract $health,
        PensionContract $pension
    ): DeductionContract {
        return new Deduction($health, $pension);
    }

    protected function getHealth(
        float $percentage,
        float $amount
    ): HealthContract {
        return new Health($percentage, $amount);
    }

    protected function getPension(
        float $percentage,
        float $amount
    ): PensionContract {
        return new Pension($percentage, $amount);
    }

    protected function getAnex(
        string $hash,
        ?string $number = null,
        ?DateTimeInterface $date = null
    ): AnexContract {
        return new Anex($hash, $number, $date);
    }

    protected function validateEnvs(): void
    {
        $required = [
            'ORGANIZATION_TYPE',
            'ORGANIZATION_DOCUMENT',
            'ORGANIZATION_DOCUMENT_TYPE',
            'ORGANIZATION_BRAND_NAME',
            'ORGANIZATION_LEGAL_NAME',
            'ORGANIZATION_REGISTRATION',
            'ORGANIZATION_RESPONSABLE',
            'ORGANIZATION_ADDRESS',
            'ORGANIZATION_CITY',
            'ORGANIZATION_STATE',
            'ORGANIZATION_COUNTRY',
            'ORGANIZATION_ZIPCODE',
            'ORGANIZATION_TAXPAYER',
        ];

        $missing = $this->getMissingKeys($required, $_ENV);

        if (\count($missing) > 0) {
            throw new Exception(
                \sprintf(
                    'Envs miss-configuration: %s is required',
                    \implode(',', $missing)
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    protected function getProvider(): Provider
    {
        $useCase = new IndexProviderUseCase($this->providerRepository);

        $response = $useCase->execute(new IndexProviderRequest([
            'isActive' => true,
        ], 1));

        if (\count($response->providers) === 0) {
            throw new Exception('ePayroll provider miss-configuration: None defined', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (\count($response->providers) > 1) {
            throw new Exception('ePayroll provider miss-configuration: Many actives', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \end($response->providers);
    }

    protected function getSetting(string $type): Setting
    {
        $useCase = new IndexNumerationUseCase($this->numerationRepository);

        $response = $useCase->execute(new IndexNumerationRequest([
            'isActive' => true,
            'type' => $type,
        ], 1));

        if (\count($response->numerations) === 0) {
            throw new Exception('ePayroll numeration miss-configuration: None defined', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (\count($response->numerations) > 1) {
            throw new Exception('ePayroll numeration miss-configuration: Many actives', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $numeration = \end($response->numerations);

        if ($numeration->currentNumber() >= $numeration->toNumber()) {
            throw new Exception('ePayroll numeration miss-configuration: Numeration exceeds', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $numeration;
    }

    protected function getContract(Employee $employee, Agreement $agreement): Contract
    {
        try {
            return $this->getAgreement(
                (string)$employee->type(),
                (string)$employee->subType(),
                (string)$agreement->type(),
                (float)$agreement->salary(),
                (bool)$agreement->integralSalary(),
                (bool)$agreement->highRisk()
            );
        } catch (Exception $e) {
            throw new Exception('ePayroll agreement miss-configuration: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    protected function getClerk(Paysheet $paysheet, Employee $employee, Agreement $agreement): EmployeeContract
    {
        $this->validateEmployee($employee);

//         $city = (new ReadCityUseCase($this->cityRepository))->execute(
//             new ReadCityRequest($employee->cityId())
//         )->city;

//         $this->validateCity($city);

//         $state = (new ReadStateUseCase($this->stateRepository))->execute(
//             new ReadStateRequest($city->stateId())
//         )->state;

//         $this->validateState($state);

//         $country = (new ReadCountryUseCase($this->countryRepository))->execute(
//             new ReadCountryRequest($state->countryId())
//         )->country;

//         $this->validateCountry($country);

        try {
            return $this->getEmployee(
                \str_pad((string)$employee->id(), 10, '0', \STR_PAD_RIGHT),
                $employee->documentNumber(),
                $employee->documentTypeId(),
                $employee->firstName(),
                $employee->secondName() ?? '',
                $employee->firstSurname(),
                $employee->secondSurname() ?? '',
                new Payout(
                    '1',
                    $employee->paymentMethod(),
                    '',
                    $employee->accountType() ?? '',
                    $employee->accountNumber() ?? '',
                    $paysheet->paidAt() ?? new DateTime
                ),
                $this->getContract($employee, $agreement),
                // TODO: Add location data to Employee table
                $this->getLocation(
                    // $country->code(),
                    // $state->code(),
                    // $city->code(),
                    // 'es',
                    // $employee->address(),
                    'CO',
                    '11',
                    '11001',
                    'es',
                    ''
                ),
            );
        } catch (Exception $e) {
            throw new Exception('ePayroll employee miss-configuration: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    protected function getMissingKeys(array $requiredKeys, array $keys): array
    {
        $missingKeys = [];

        foreach ($requiredKeys as $name) {
            if (!\array_key_exists($name, $keys)) {
                $missingKeys[] = $name;
            }
        }

        return $missingKeys;
    }

    protected function validateEmployee(Employee $employee): void
    {
        $errors = [];

        if (!$employee->type()) {
            $errors[] = 'type';
        }

        if (!$employee->subType()) {
            $errors[] = 'subType';
        }

        if (!$employee->documentNumber()) {
            $errors[] = 'document';
        }

        if (!$employee->documentTypeId()) {
            $errors[] = 'document type';
        }

        if (!$employee->firstName()) {
            $errors[] = 'name';
        }

        if (!$employee->firstSurname()) {
            $errors[] = 'lastname';
        }

//         if (!$employee->address()) {
//             $errors[] = 'address';
//         }

//         if (!$employee->cityId()) {
//             $errors[] = 'city';
//         }

        if (\count($errors)) {
            throw new Exception(
                \sprintf('ePayroll employee miss-configuration: %s are invalid', \implode(', ', $errors)),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    protected function validateCity(City $city): void
    {
        if (!$city->code()) {
            throw new Exception('ePayroll employee city miss-configuration: code is required', Response::HTTP_BAD_REQUEST);
        }
    }

    protected function validateState(State $state): void
    {
        if (!$state->code()) {
            throw new Exception('ePayroll employee state miss-configuration: code is required', Response::HTTP_BAD_REQUEST);
        }
    }

    protected function validateCountry(Country $country): void
    {
        if (!$country->code()) {
            throw new Exception('ePayroll employee country miss-configuration: code is required', Response::HTTP_BAD_REQUEST);
        }
    }

    protected function getItems(Paysheet $paysheet): array
    {
        $details = (new IndexPaysheetDetailUseCase($this->paysheetDetailRepository))->execute(
            new IndexPaysheetDetailRequest([
                'paysheetId' => $paysheet->id(),
            ], 1)
        )->paysheetDetails;

        $items = [];

        /* @var PaysheetDetail $detail */
        foreach ($details as $detail) {
            /* @var Product $product */
            $product = (new ReadProductUseCase($this->productRepository))->execute(
                new ReadProductRequest($detail->productId())
            )->product;

            $taxes = [];

            if ($detail->taxes() > 0) {
                $taxes = [
                    TaxType::IVA => $detail->taxes() ?? 0,
                ];
            }

            $items[] = $this->getItem(
                $product->sku(),
                $detail->quantity(),
                $product->productUnitIdInstance()->code(),
                (float)$detail->price(),
                'COP',
                $product->name(),
                $product->productTypeIdInstance()->name(),
                $taxes
            );
        }

        if (\count($items) === 0) {
            throw new Exception('ePayroll without items is not allowed', Response::HTTP_BAD_REQUEST);
        }

        return $items;
    }

    protected function getDeposits(Paysheet $paysheet): array
    {
        $payments = (new IndexPaymentUseCase($this->paymentRepository))->execute(
            new IndexPaymentRequest([
                'paysheetId' => $paysheet->id(),
                'paymentStatusId' => '00',
            ], 1)
        )->payments;

        $deposits = [];

        /* @var Payment $payment */
        foreach ($payments as $payment) {
            $deposits[] = $this->getDeposit(
                $payment->paymentMethodId(),
                PaymentMeanCode::CASH
            );
        }

        return $deposits;
    }

    protected function getHash(string $content)
    {
        \libxml_use_internal_errors(true);

        $xml = new \SimpleXMLElement($content);

        $xml->registerXPathNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');

        $hash = (string)$xml->xpath('//cbc:UUID')[0] ?? '';

        if (empty($hash)) {
            throw new Exception('Hash not found');
        }

        \libxml_use_internal_errors(false);

        return $hash;
    }

    protected function getEPayroll(Bill $bill, Provider $provider): ?\FlexPHP\ePayroll\InvoiceInterface
    {
        try {
            if ($this->testingMode) {
                throw new Exception('Running in test mode');
            }

            $wsdl = \realpath(__DIR__ . '/../../../vendor/flexphp/einvoice/resources/FacturaTech.v21.wsdl');

            return new EPayroll($provider->id(), [
                'username' => $provider->username(),
                'password' => $provider->password(),
                'carrier' => new \FlexPHP\ePayroll\Carrier\SoapCarrier($wsdl, [
                    'location' => $provider->url(),
                ]),
            ]);
        } catch (Exception $e) {
            $bill->setMessage($e->getMessage());
            $bill->setStatus(BillStatus::PROCESSING);

            $this->updateBill($bill);
        }

        return null;
    }

    protected function upload(EPayroll $ePayroll, Bill $bill, Merchant $sender, Merchant $receiver, InvoiceContract $invoice): Bill
    {
        try {
            $response = $ePayroll->upload([
                'sender' => $sender,
                'receiver' => $receiver,
                'bill' => $invoice,
                'isTest' => $this->testingMode,
            ]);
        } catch (Exception $e) {
            $response = new UploadResponse(Status::FAILED, $e->getMessage(), null);

            if ($this->testingMode) {
                $response = new UploadResponse(Status::SUCCESS, $e->getMessage(), '123');
            }
        }

        if ($response->status() !== Status::SUCCESS) {
            $bill->setStatus(BillStatus::REJECTED);
            $bill->setMessage($this->convertEncoding($response->message()));
        } else {
            $bill->setStatus(BillStatus::APPROVED);
            $bill->setMessage($response->message());
            $bill->setTraceId($response->traceId());
        }

        return $this->updateBill($bill);
    }

    protected function status(EPayroll $ePayroll, Bill $bill): Bill
    {
        try {
            $response = $ePayroll->getStatus($bill->traceId());
        } catch (Exception $e) {
            $response = new StatusResponse(Status::FAILED, $e->getMessage(), null);

            if ($this->testingMode) {
                $response = new StatusResponse(Status::SUCCESS, $e->getMessage(), '123');
            }
        }

        $bill->setMessage($response->message());

        if ($response->status() === Status::SUCCESS) {
            $bill->setStatus(BillStatus::AVAILABLE);
        }

        return $this->updateBill($bill);
    }

    protected function pdf(EPayroll $ePayroll, Bill $bill, string $prefix, string $number): Bill
    {
        try {
            $path = \realpath(__DIR__ . '/../../../payrolls/pdf');

            if (!\is_dir($path)) {
                throw new Exception('Carpeta PDF no encontrada');
            }

            $response = $ePayroll->getPDF($prefix, $number);
        } catch (Exception $e) {
            $response = new DownloadResponse(Status::FAILED, $e->getMessage(), null);

            if ($this->testingMode) {
                $response = new DownloadResponse(Status::SUCCESS, $e->getMessage(), \file_get_contents($path . 'fake.pdf'));
            }
        }

        $bill->setMessage($response->message());

        if ($response->status() === Status::SUCCESS) {
            $filename = \hash('sha256', $bill->getNumeration() . '.pdf');

            \file_put_contents($path . \DIRECTORY_SEPARATOR . $filename, \base64_decode($response->resource()));

            $bill->setPdfPath($path);
        }

        return $this->updateBill($bill);
    }

    protected function xml(EPayroll $ePayroll, Bill $bill, string $prefix, string $number): Bill
    {
        try {
            $path = \realpath(__DIR__ . '/../../../invoices/xml');

            if (!\is_dir($path)) {
                throw new Exception('Carpeta XML no encontrada');
            }

            $response = $ePayroll->getXML($prefix, $number);
        } catch (Exception $e) {
            $response = new DownloadResponse(Status::FAILED, $e->getMessage(), null);

            if ($this->testingMode) {
                $response = new DownloadResponse(Status::SUCCESS, $e->getMessage(), \file_get_contents($path . 'fake.xml'));
            }
        }

        $bill->setMessage($response->message());

        if ($response->status() === Status::SUCCESS) {
            $base64 = \base64_decode($response->resource());
            $filename = \hash('sha256', $bill->getNumeration() . '.xml');

            \file_put_contents($path . \DIRECTORY_SEPARATOR . $filename, $base64);

            $bill->setXmlPath($path);
            $bill->setHashType('CUFE-SHA384');
            $bill->setHash($this->getHash($base64));
        }

        return $this->updateBill($bill);
    }

    protected function updateBill(Bill $bill): Bill
    {
        $useCase = new UpdateBillUseCase($this->billRepository);

        return $useCase->execute(new UpdateBillRequest($bill->id(), $bill->toArray(), -1))->bill;
    }

    protected function convertEncoding(string $message): string
    {
        $encoding = \mb_detect_encoding($message);
        $this->logger->info($message . ' => encoding: ' . $encoding);

        if ($encoding !== 'UTF-8') {
            $message = \iconv($encoding, 'UTF-8', $message);
        }

        return $message;
    }
}
