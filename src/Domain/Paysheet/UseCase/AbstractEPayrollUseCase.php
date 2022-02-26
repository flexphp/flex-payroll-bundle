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
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Request\UpdateBillRequest;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\UseCase\UpdateBillUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatus;
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
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Payroll;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\PayrollRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\UpdatePayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\UpdatePayrollUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Paysheet;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Presenter\SupportPresenter;
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
use FlexPHP\ePayroll\Contract\BonusContract;
use FlexPHP\ePayroll\Contract\CessationContract;
use FlexPHP\ePayroll\Contract\DeductionContract;
use FlexPHP\ePayroll\Contract\EmployeeContract;
use FlexPHP\ePayroll\Contract\EmployerContract;
use FlexPHP\ePayroll\Contract\EndowmentContract;
use FlexPHP\ePayroll\Contract\EntityContract;
use FlexPHP\ePayroll\Contract\GeneralContract;
use FlexPHP\ePayroll\Contract\HealthContract;
use FlexPHP\ePayroll\Contract\HourContract;
use FlexPHP\ePayroll\Contract\LocationContract;
use FlexPHP\ePayroll\Contract\NumerationContract;
use FlexPHP\ePayroll\Contract\PaymentContract;
use FlexPHP\ePayroll\Contract\PensionContract;
use FlexPHP\ePayroll\Contract\PeriodContract;
use FlexPHP\ePayroll\Contract\SupportContract;
use FlexPHP\ePayroll\Contract\TransportContract;
use FlexPHP\ePayroll\Contract\VacationContract;
use FlexPHP\ePayroll\Payroll as EPayroll;
use FlexPHP\ePayroll\Provider\Response\DownloadResponse;
use FlexPHP\ePayroll\Provider\Response\StatusResponse;
use FlexPHP\ePayroll\Provider\Response\UploadResponse;
use FlexPHP\ePayroll\Struct\Accrued;
use FlexPHP\ePayroll\Struct\Agreement as Contract;
use FlexPHP\ePayroll\Struct\Anex;
use FlexPHP\ePayroll\Struct\Basic;
use FlexPHP\ePayroll\Struct\Bonus;
use FlexPHP\ePayroll\Struct\Cessation;
use FlexPHP\ePayroll\Struct\Deduction;
use FlexPHP\ePayroll\Struct\Employee as Clerk;
use FlexPHP\ePayroll\Struct\Employer;
use FlexPHP\ePayroll\Struct\Endowment;
use FlexPHP\ePayroll\Struct\Entity as Company;
use FlexPHP\ePayroll\Struct\General;
use FlexPHP\ePayroll\Struct\Health;
use FlexPHP\ePayroll\Struct\Hour;
use FlexPHP\ePayroll\Struct\Location;
use FlexPHP\ePayroll\Struct\Numeration;
use FlexPHP\ePayroll\Struct\Payment as Payout;
use FlexPHP\ePayroll\Struct\Pension;
use FlexPHP\ePayroll\Struct\Period;
use FlexPHP\ePayroll\Struct\Support;
use FlexPHP\ePayroll\Struct\Transport;
use FlexPHP\ePayroll\Struct\Vacation;
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

    protected PayrollRepository $payrollRepository;

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
        PayrollRepository $payrollRepository,
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
        $this->payrollRepository = $payrollRepository;
        $this->logger = $logger;
    }

    public function getRecurrenceCode(string $period): string
    {
        return self::RECURRENCE_CODE[$period];
    }

    protected function processEPayroll(
        array $roll,
        Payroll $payroll,
        Provider $provider
    ): CreateEPayrollResponse {
        // $roll['numeration']->setCurrentNumber($payroll->number());

        $ePayroll = $this->getEPayroll($payroll, $provider);

        if (!$ePayroll) {
            return new CreateEPayrollResponse($payroll->status(), $payroll->message());
        }

        $sleep = false;

        if (\in_array($payroll->status(), [PayrollStatus::PENDING, PayrollStatus::PROCESSING, PayrollStatus::REJECTED])) {
            $sleep = true;
            $payroll = $this->upload($ePayroll, $payroll, $roll);
        }

        if (!$payroll->traceId()) {
            return new CreateEPayrollResponse($payroll->status(), $payroll->message());
        }

        if ($payroll->status() !== PayrollStatus::AVAILABLE) {
            $payroll = $this->status($ePayroll, $payroll);
        }

        if ($payroll->status() !== PayrollStatus::AVAILABLE) {
            return new CreateEPayrollResponse($payroll->status(), $payroll->message());
        }

        if ($sleep) {
            \sleep(5);
        }

        if (!$payroll->pdfPath()) {
            $payroll = $this->pdf($ePayroll, $payroll, $payroll->prefix(), (string)$payroll->number());
        }

        if (!$payroll->pdfPath()) {
            return new CreateEPayrollResponse($payroll->status(), $payroll->message());
        }

        if (!$payroll->xmlPath()) {
            $payroll = $this->xml($ePayroll, $payroll, $payroll->prefix(), (string)$payroll->number());
        }

        if (!$payroll->xmlPath()) {
            return new CreateEPayrollResponse($payroll->status(), $payroll->message());
        }

        if ($payroll->pdfPath() && $payroll->xmlPath() && !$payroll->downloadedAt()) {
            $payroll->setDownloadedAt(new DateTime());

            $this->updatePayroll($payroll);
        }

        return $this->getResponseOk($payroll);
    }

    protected function getResponseOk(Payroll $payroll): CreateEPayrollResponse
    {
        $filename = \hash('sha256', $payroll->getNumeration() . '.pdf');

        if ($this->testingMode) {
            $filename = 'fake.pdf';
        }

        $content = \file_get_contents($payroll->pdfPath() . \DIRECTORY_SEPARATOR . $filename);

        return new CreateEPayrollResponse($payroll->status(), $payroll->message(), $payroll->getNumeration(), $content);
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
            $agreement,
            $location
        );
    }

    protected function getPayment(
        string $meanCode,
        string $methodCode,
        string $bankName,
        string $accountType,
        string $accountNumber
    ): PaymentContract {
        return new Payout(
            $meanCode,
            $methodCode,
            $bankName,
            $accountType,
            $accountNumber
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

    protected function getVacation(
        DateTimeInterface $initAt,
        DateTimeInterface $finishAt,
        int $days,
        float $amount,
        int $compensateDays,
        float $compensateAmount
    ): VacationContract {
        return new Vacation($initAt, $finishAt, $days, $amount, $compensateDays, $compensateAmount);
    }

    protected function getBonus(
        int $days,
        float $amount,
        float $noSalary
    ): BonusContract {
        $bonus = new Bonus($days, $amount);

        if ($noSalary) {
            $bonus->setNoSalary($noSalary);
        }

        return $bonus;
    }

    protected function getCessation(
        float $percentage,
        float $amount,
        float $amountInteres
    ): CessationContract {
        return new Cessation($percentage, $amount, $amountInteres);
    }

    protected function getSupports(SupportPresenter $support): array
    {
        $response = [];

        for ($i = 0; $i < $support->count(); ++$i) {
            $response[] = $this->getSupport($support->amount($i), $support->noSalary($i));
        }

        return $response;
    }

    protected function getSupport(
        float $amount,
        float $noSalary
    ): SupportContract {
        return new Support($amount, $noSalary);
    }

    protected function getEndowment(
        float $amount
    ): EndowmentContract {
        return new Endowment($amount);
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

    protected function getProvider(string $type): Provider
    {
        $useCase = new IndexProviderUseCase($this->providerRepository);

        $response = $useCase->execute(new IndexProviderRequest([
            'isActive' => true,
            'type' => $type,
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
                $employee->typeInstance()->code(),
                $employee->subTypeInstance()->code(),
                $agreement->typeInstance()->code(),
                (float)$agreement->salary(),
                (bool)$agreement->integralSalary(),
                (bool)$agreement->highRisk()
            );
        } catch (Exception $e) {
            throw new Exception('ePayroll agreement miss-configuration: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    protected function getClerk(Employee $employee, Agreement $agreement): EmployeeContract
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
                $this->getContract($employee, $agreement),
                // TODO: Add location data to Employee table
                $this->getLocation(
                    // $country->code(),
                    // $state->code(),
                    // $city->code(),
                    // 'es',
                    // $employee->address(),
                    $_ENV['ORGANIZATION_COUNTRY'],
                    $_ENV['ORGANIZATION_STATE'],
                    $_ENV['ORGANIZATION_CITY'],
                    'es',
                    $_ENV['ORGANIZATION_ADDRESS']
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

    protected function getEPayroll(Payroll $payroll, Provider $provider): ?\FlexPHP\ePayroll\PayrollInterface
    {
        try {
            if ($this->testingMode) {
                throw new Exception('Running in test mode');
            }

            $wsdl = \realpath(getcwd() . '/../vendor/flexphp/epayroll/resources/FTech.payroll.wsdl', );

            return new EPayroll($provider->id(), [
                'username' => $provider->username(),
                'password' => $provider->password(),
                'carrier' => new \FlexPHP\ePayroll\Carrier\SoapCarrier($wsdl, [
                    'location' => $provider->url(),
                ]),
            ]);
        } catch (Exception $e) {
            $payroll->setMessage($e->getMessage());
            $payroll->setStatus(PayrollStatus::PROCESSING);

            $this->updatePayroll($payroll);
        }

        return null;
    }

    protected function upload(EPayroll $ePayroll, Payroll $payroll, array $roll): Payroll
    {
        try {
            $response = $ePayroll->preview($roll + [
                // 'isTest' => $this->testingMode,
                'isTest' => true,
            ]);

            file_put_contents(getcwd() . '/../var/log/' . date('Y-m-d') . '-' . $roll['numeration']->number() . '.xml', $response);

            $response = $ePayroll->upload($roll + [
                // 'isTest' => $this->testingMode,
                'isTest' => true,
            ]);
        } catch (Exception $e) {
            $response = new UploadResponse(Status::FAILED, $e->getMessage(), null);

            if ($this->testingMode) {
                $response = new UploadResponse(Status::SUCCESS, $e->getMessage(), '123');
            }
        }

        file_put_contents(getcwd() . '/../var/log/' . date('Y-m-d') . '-' . $roll['numeration']->number() . '-R.xml', serialize( $response));

        if ($response->status() !== Status::SUCCESS) {
            $this->logger->warning('Payroll UPLOAD Response ' . serialize($response));

            $payroll->setStatus(PayrollStatus::REJECTED);
            $payroll->setMessage($this->convertEncoding($response->message()));
        } else {
            $payroll->setStatus(PayrollStatus::APPROVED);
            $payroll->setMessage($response->message());
            $payroll->setTraceId($response->traceId());
        }

        return $this->updatePayroll($payroll);
    }

    protected function status(EPayroll $ePayroll, Payroll $payroll): Payroll
    {
        try {
            $response = $ePayroll->getStatus($payroll->traceId());
        } catch (Exception $e) {
            $response = new StatusResponse(Status::FAILED, $e->getMessage(), null);

            if ($this->testingMode) {
                $response = new StatusResponse(Status::SUCCESS, $e->getMessage(), '123');
            }
        }

        $payroll->setMessage($response->message());

        if ($response->status() === Status::SUCCESS) {
            $payroll->setStatus(PayrollStatus::AVAILABLE);
        }

        return $this->updatePayroll($payroll);
    }

    protected function pdf(EPayroll $ePayroll, Payroll $payroll, string $prefix, string $number): Payroll
    {
        try {
            $path = \realpath(getcwd() . '/../payrolls/pdf');

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

        $payroll->setMessage($response->message());

        if ($response->status() === Status::SUCCESS) {
            $filename = \hash('sha256', $payroll->getNumeration() . '.pdf');

            \file_put_contents($path . \DIRECTORY_SEPARATOR . $filename, \base64_decode($response->resource()));

            $payroll->setPdfPath($path);
        }

        return $this->updatePayroll($payroll);
    }

    protected function xml(EPayroll $ePayroll, Payroll $payroll, string $prefix, string $number): Payroll
    {
        try {
            $path = \realpath(getcwd() . '/../payrolls/xml');

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

        $payroll->setMessage($response->message());

        if ($response->status() === Status::SUCCESS) {
            $base64 = \base64_decode($response->resource());
            $filename = \hash('sha256', $payroll->getNumeration() . '.xml');

            \file_put_contents($path . \DIRECTORY_SEPARATOR . $filename, $base64);

            $payroll->setXmlPath($path);
            $payroll->setHashType('CUFE-SHA384');
            $payroll->setHash($this->getHash($base64));
        }

        return $this->updatePayroll($payroll);
    }

    protected function updatePayroll(Payroll $payroll): Payroll
    {
        $useCase = new UpdatePayrollUseCase($this->payrollRepository);

        return $useCase->execute(new UpdatePayrollRequest($payroll->id(), $payroll->toArray(), -1))->payroll;
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
