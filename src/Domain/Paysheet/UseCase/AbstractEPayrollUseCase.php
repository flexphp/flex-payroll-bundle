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
use FlexPHP\Bundle\LocationBundle\Domain\City\City;
use FlexPHP\Bundle\LocationBundle\Domain\City\CityRepository;
use FlexPHP\Bundle\LocationBundle\Domain\City\Request\ReadCityRequest;
use FlexPHP\Bundle\LocationBundle\Domain\City\UseCase\ReadCityUseCase;
use FlexPHP\Bundle\LocationBundle\Domain\Country\Country;
use FlexPHP\Bundle\LocationBundle\Domain\Country\CountryRepository;
use FlexPHP\Bundle\LocationBundle\Domain\Country\Request\ReadCountryRequest;
use FlexPHP\Bundle\LocationBundle\Domain\Country\UseCase\ReadCountryUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\Customer;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\CustomerRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\Request\ReadCustomerRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Customer\UseCase\ReadCustomerUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Paysheet;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Response\CreateEPayrollResponse;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\PaysheetDetailRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\Request\IndexPaysheetDetailRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PaysheetDetail\UseCase\IndexPaysheetDetailUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Payment;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\PaymentRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Request\IndexPaymentRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\UseCase\IndexPaymentUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\ProductRepository;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\Request\ReadProductRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Product\UseCase\ReadProductUseCase;
use FlexPHP\Bundle\LocationBundle\Domain\State\Request\ReadStateRequest;
use FlexPHP\Bundle\LocationBundle\Domain\State\State;
use FlexPHP\Bundle\LocationBundle\Domain\State\StateRepository;
use FlexPHP\Bundle\LocationBundle\Domain\State\UseCase\ReadStateUseCase;
use Exception;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Bill;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\BillRepository;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\Request\UpdateBillRequest;
use FlexPHP\Bundle\InvoiceBundle\Domain\Bill\UseCase\UpdateBillUseCase;
use FlexPHP\Bundle\InvoiceBundle\Domain\BillStatus\BillStatus;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\Numeration as Setting;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\NumerationRepository;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\Request\IndexNumerationRequest;
use FlexPHP\Bundle\NumerationBundle\Domain\Numeration\UseCase\IndexNumerationUseCase;
use FlexPHP\Bundle\NumerationBundle\Domain\Provider\Provider;
use FlexPHP\Bundle\NumerationBundle\Domain\Provider\ProviderRepository;
use FlexPHP\Bundle\NumerationBundle\Domain\Provider\Request\IndexProviderRequest;
use FlexPHP\Bundle\NumerationBundle\Domain\Provider\UseCase\IndexProviderUseCase;
use FlexPHP\eInvoice\Constant\AnexType;
use FlexPHP\eInvoice\Constant\BillSubType as EBillSubType;
use FlexPHP\eInvoice\Constant\BillType as EBillType;
use FlexPHP\eInvoice\Constant\ContactType;
use FlexPHP\eInvoice\Constant\CurrencyCode;
use FlexPHP\eInvoice\Constant\ItemType;
use FlexPHP\eInvoice\Constant\PaymentMeanCode;
use FlexPHP\eInvoice\Constant\Status;
use FlexPHP\eInvoice\Constant\TaxType;
use FlexPHP\eInvoice\Contract\BillAnexContract;
use FlexPHP\eInvoice\Contract\BillContract as InvoiceContract;
use FlexPHP\eInvoice\Contract\ContactContract;
use FlexPHP\eInvoice\Contract\CurrencyExchangeContract;
use FlexPHP\eInvoice\Contract\ItemContract;
use FlexPHP\eInvoice\Contract\LocationContract;
use FlexPHP\eInvoice\Contract\MerchantContract;
use FlexPHP\eInvoice\Contract\NumerationContract;
use FlexPHP\eInvoice\Contract\PaymentContract;
use FlexPHP\eInvoice\Contract\TaxExchangeContract;
use FlexPHP\eInvoice\Invoice as EPayroll;
use FlexPHP\eInvoice\Provider\Response\DownloadResponse;
use FlexPHP\eInvoice\Provider\Response\StatusResponse;
use FlexPHP\eInvoice\Provider\Response\UploadResponse;
use FlexPHP\eInvoice\Struct\Bill as Invoice;
use FlexPHP\eInvoice\Struct\BillAnex;
use FlexPHP\eInvoice\Struct\Contact;
use FlexPHP\eInvoice\Struct\Item;
use FlexPHP\eInvoice\Struct\Location;
use FlexPHP\eInvoice\Struct\Merchant;
use FlexPHP\eInvoice\Struct\Numeration;
use FlexPHP\eInvoice\Struct\Payment as Deposit;
use FlexPHP\eInvoice\Struct\TaxExchange;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractEPayrollUseCase
{
    protected const CONTACT_TYPE_MAP = [
        1 => ContactType::PERSON,
        2 => ContactType::OFFICE,
        3 => ContactType::ACCOUNT,
        4 => ContactType::SALES,
    ];

    protected PaysheetRepository $orderRepository;

    protected ProviderRepository $providerRepository;

    protected NumerationRepository $numerationRepository;

    protected CustomerRepository $customerRepository;

    protected CityRepository $cityRepository;

    protected StateRepository $stateRepository;

    protected CountryRepository $countryRepository;

    protected BillRepository $billRepository;

    protected PaysheetDetailRepository $orderDetailRepository;

    protected ProductRepository $productRepository;

    protected PaymentRepository $paymentRepository;

    protected LoggerInterface $logger;

    protected bool $testingMode = false;

    public function __construct(
        PaysheetRepository $orderRepository,
        ProviderRepository $providerRepository,
        NumerationRepository $numerationRepository,
        CustomerRepository $customerRepository,
        CityRepository $cityRepository,
        StateRepository $stateRepository,
        CountryRepository $countryRepository,
        BillRepository $billRepository,
        PaysheetDetailRepository $orderDetailRepository,
        ProductRepository $productRepository,
        PaymentRepository $paymentRepository,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->providerRepository = $providerRepository;
        $this->numerationRepository = $numerationRepository;
        $this->customerRepository = $customerRepository;
        $this->cityRepository = $cityRepository;
        $this->stateRepository = $stateRepository;
        $this->countryRepository = $countryRepository;
        $this->billRepository = $billRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->productRepository = $productRepository;
        $this->paymentRepository = $paymentRepository;
        $this->logger = $logger;
    }

    protected function processEPayroll(
        InvoiceContract $invoice,
        Bill $bill,
        Provider $provider,
        MerchantContract $sender,
        MerchantContract $receiver
    ): CreateEPayrollResponse {
        $invoice->numeration()->setCurrentNumber($bill->number());

        $eInvoice = $this->getEPayroll($bill, $provider);

        if (!$eInvoice) {
            return new CreateEPayrollResponse($bill->status(), $bill->message());
        }

        $sleep = false;

        if (\in_array($bill->status(), [BillStatus::PENDING, BillStatus::PROCESSING, BillStatus::REJECTED])) {
            $sleep = true;
            $bill = $this->upload($eInvoice, $bill, $sender, $receiver, $invoice);
        }

        if (!$bill->traceId()) {
            return new CreateEPayrollResponse($bill->status(), $bill->message());
        }

        if ($bill->status() !== BillStatus::AVAILABLE) {
            $bill = $this->status($eInvoice, $bill);
        }

        if ($bill->status() !== BillStatus::AVAILABLE) {
            return new CreateEPayrollResponse($bill->status(), $bill->message());
        }

        if ($sleep) {
            \sleep(5);
        }

        if (!$bill->pdfPath()) {
            $bill = $this->pdf($eInvoice, $bill, $bill->prefix(), (string)$bill->number());
        }

        if (!$bill->pdfPath()) {
            return new CreateEPayrollResponse($bill->status(), $bill->message());
        }

        if (!$bill->xmlPath()) {
            $bill = $this->xml($eInvoice, $bill, $bill->prefix(), (string)$bill->number());
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

    protected function getTaxExchange(
        string $taxpayerType
    ): TaxExchangeContract {
        $taxExchange = new TaxExchange();
        $taxExchange->setTaxpayerType($taxpayerType);

        return $taxExchange;
    }

    protected function getLocation(
        string $address,
        string $stateCode,
        string $cityCode,
        string $countryCode,
        string $zipCode
    ): LocationContract {
        $location = new Location($countryCode);
        $location->setAddress($address);
        $location->setStateCode($stateCode);
        $location->setCityCode($cityCode);
        $location->setZipCode($zipCode);

        return $location;
    }

    protected function getContact(
        string $type,
        string $name,
        string $telephone,
        string $email
    ): ContactContract {
        $contact = new Contact();
        $contact->setType($type);
        $contact->setName($name);
        $contact->setTelephone($telephone);
        $contact->setEmail($email);

        return $contact;
    }

    protected function getDeposit(
        string $paymentMethodCode,
        string $paymentMeanCode
    ): PaymentContract {
        $deposit = new Deposit($paymentMeanCode);
        $deposit->setMethodCode($paymentMethodCode);

        return $deposit;
    }

    protected function getItem(
        string $code,
        float $quantity,
        string $unitCode,
        float $unitValue,
        string $currencyCode,
        string $name,
        string $description,
        array $taxes = [],
        string $type = ItemType::NORMAL,
        string $lote = '',
        array $discounts = [],
        array $chargers = []
    ): ItemContract {
        $item = new Item();
        $item->setCode($code);
        $item->setQuantity($quantity);
        $item->setUnitCode($unitCode);
        $item->setUnitValue($unitValue);
        $item->setCurrencyCode($currencyCode);
        $item->setName($name);
        $item->setDescription($description);
        $item->setType($type);
        $item->setLote($lote);
        $item->addTaxes($taxes);
        $item->addDiscounts($discounts);
        $item->addChargers($chargers);

        return $item;
    }

    protected function getMerchant(
        string $type,
        string $document,
        string $documentType,
        string $brandName,
        string $legalName,
        string $registrationNumber,
        bool $taxResponsable,
        ?LocationContract $location = null,
        ?TaxExchangeContract $taxExchange = null,
        array $contacts = []
    ): MerchantContract {
        $merchant = new Merchant();
        $merchant->setType($type);
        $merchant->setDocument($document);
        $merchant->setDocumentType($documentType);
        $merchant->setBrandName($brandName);
        $merchant->setLegalName($legalName);
        $merchant->setRegistrationNumber($registrationNumber);
        $merchant->setTaxResponsable($taxResponsable);

        if ($location) {
            $merchant->setLocation($location);
        }

        if ($taxExchange) {
            $merchant->setTaxExchange($taxExchange);
        }

        if ($contacts) {
            $merchant->addContacts($contacts);
        }

        return $merchant;
    }

    protected function getInvoice(
        DateTimeInterface $date,
        NumerationContract $numeration,
        array $items = [],
        array $payments = [],
        ?DateTimeInterface $expirationDate = null,
        ?string $notes = null,
        string $currencyCode = CurrencyCode::COP,
        CurrencyExchangeContract $currencyExchange = null
    ): InvoiceContract {
        $invoice = new Invoice();
        $invoice->setDate($date);
        $invoice->setNumeration($numeration);
        $invoice->addPayments($payments);
        $invoice->setCurrencyCode($currencyCode);

        if ($currencyExchange) {
            $invoice->setCurrencyExchange($currencyExchange);
        }

        if ($expirationDate) {
            $invoice->setExpirationDate($expirationDate);
        }

        if ($notes) {
            $invoice->setNotes($notes);
        }

        $invoice->addItems($items);

        return $invoice;
    }

    protected function getInvoiceND(
        Bill $bill,
        DateTimeInterface $date,
        NumerationContract $numeration,
        array $items = [],
        array $payments = [],
        ?DateTimeInterface $expirationDate = null,
        ?string $notes = null,
        string $currencyCode = CurrencyCode::COP,
        CurrencyExchangeContract $currencyExchange = null
    ): InvoiceContract {
        $invoice = $this->getInvoice(
            $date,
            $numeration,
            $items,
            $payments,
            $expirationDate,
            $notes,
            $currencyCode,
            $currencyExchange
        );

        $invoice->setType(EBillType::DEBIT);
        $invoice->setSubType(EBillSubType::DREFINVOICE);

        $invoice->setBillAnex(
            $this->getBillAnex(
                AnexType::_IV_,
                $bill->getNumeration(),
                $bill->downloadedAt(),
                $bill->hash(),
                $bill->hashType()
            )
        );

        return $invoice;
    }

    protected function getNumeration(
        string $identifier,
        int $min,
        int $current,
        int $max,
        DateTimeInterface $start,
        DateTimeInterface $end,
        string $prefix
    ): NumerationContract {
        $numeration = new Numeration();
        $numeration->setIdentifier($identifier);
        $numeration->setMinNumber($min);
        $numeration->setCurrentNumber($current);
        $numeration->setMaxNumber($max);
        $numeration->setDateStart($start);
        $numeration->setDateEnd($end);
        $numeration->setPrefix($prefix);

        return $numeration;
    }

    protected function getSender(): MerchantContract
    {
        $this->validateSender();
        $this->validateContactSender();

        try {
            return $this->getMerchant(
                (string)$_ENV['ORGANIZATION_TYPE'],
                $_ENV['ORGANIZATION_DOCUMENT'],
                $_ENV['ORGANIZATION_DOCUMENT_TYPE'],
                $_ENV['ORGANIZATION_BRAND_NAME'],
                $_ENV['ORGANIZATION_LEGAL_NAME'],
                $_ENV['ORGANIZATION_REGISTRATION'],
                (bool)$_ENV['ORGANIZATION_RESPONSABLE'],
                $this->getLocation(
                    $_ENV['ORGANIZATION_ADDRESS'],
                    $_ENV['ORGANIZATION_STATE'],
                    $_ENV['ORGANIZATION_CITY'],
                    $_ENV['ORGANIZATION_COUNTRY'],
                    $_ENV['ORGANIZATION_ZIPCODE']
                ),
                $this->getTaxExchange($_ENV['ORGANIZATION_TAXPAYER']),
                [
                    $this->getContact(
                        self::CONTACT_TYPE_MAP[$_ENV['CONTACT_TYPE']],
                        $_ENV['CONTACT_NAME'],
                        $_ENV['CONTACT_PHONE'],
                        $_ENV['CONTACT_EMAIL']
                    ),
                ]
            );
        } catch (Exception $e) {
            throw new Exception('Sender miss-configuration: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function validateSender(): void
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
                    'Sender miss-configuration: %s is required',
                    \implode(',', $missing)
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    protected function validateContactSender(): void
    {
        $required = [
            'CONTACT_TYPE',
            'CONTACT_NAME',
            'CONTACT_PHONE',
            'CONTACT_EMAIL',
        ];

        $missing = $this->getMissingKeys($required, $_ENV);

        if (\count($missing) > 0) {
            throw new Exception(
                \sprintf(
                    'Contact sender miss-configuration: %s is required',
                    \implode(',', $missing)
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if (!\array_key_exists($_ENV['CONTACT_TYPE'], self::CONTACT_TYPE_MAP)) {
            throw new Exception('Contact sender type miss-configuration', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function getProvider(): Provider
    {
        $useCase = new IndexProviderUseCase($this->providerRepository);

        $response = $useCase->execute(new IndexProviderRequest([
            'isActive' => true,
        ], 1));

        if (\count($response->providers) === 0) {
            throw new Exception('eInvoice provider miss-configuration: None defined', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (\count($response->providers) > 1) {
            throw new Exception('eInvoice provider miss-configuration: Many actives', Response::HTTP_INTERNAL_SERVER_ERROR);
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
            throw new Exception('eInvoice numeration miss-configuration: None defined', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (\count($response->numerations) > 1) {
            throw new Exception('eInvoice numeration miss-configuration: Many actives', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $numeration = \end($response->numerations);

        if ($numeration->currentNumber() >= $numeration->toNumber()) {
            throw new Exception('eInvoice numeration miss-configuration: Numeration exceeds', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $numeration;
    }

    protected function getReceiver(int $customerId): Merchant
    {
        $customer = (new ReadCustomerUseCase($this->customerRepository))->execute(
            new ReadCustomerRequest($customerId)
        )->customer;

        $this->validateCustomer($customer);

        $city = (new ReadCityUseCase($this->cityRepository))->execute(
            new ReadCityRequest($customer->cityId())
        )->city;

        $this->validateCity($city);

        $state = (new ReadStateUseCase($this->stateRepository))->execute(
            new ReadStateRequest($city->stateId())
        )->state;

        $this->validateState($state);

        $country = (new ReadCountryUseCase($this->countryRepository))->execute(
            new ReadCountryRequest($state->countryId())
        )->country;

        $this->validateCountry($country);

        try {
            return $this->getMerchant(
                $customer->type(),
                $customer->documentNumber(),
                $customer->documentTypeId(),
                $customer->commercialName(),
                $customer->name(),
                $customer->registrationNumber(),
                $customer->regimeType() == 48,
                $this->getLocation(
                    $customer->address(),
                    $state->code(),
                    $city->code(),
                    $country->code(),
                    $customer->zipCode() ?? '',
                ),
                $this->getTaxExchange('N'),
                [
                    $this->getContact(
                        ContactType::PERSON,
                        $customer->name(),
                        $customer->phoneNumber(),
                        $customer->email()
                    ),
                ]
            // $this->getTaxExchange($customer->taxpayerType())
            );
        } catch (Exception $e) {
            throw new Exception('eInvoice customer miss-configuration: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
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

    protected function validateCustomer(Customer $customer): void
    {
        $errors = [];

        if (!$customer->invoicingAllowed()) {
            $errors[] = 'not enable';
        }

        if (!$customer->type()) {
            $errors[] = 'type';
        }

        if (!$customer->documentNumber()) {
            $errors[] = 'document';
        }

        if (!$customer->documentTypeId()) {
            $errors[] = 'document type';
        }

        if (!$customer->name()) {
            $errors[] = 'name';
        }

        if (!$customer->commercialName()) {
            $errors[] = 'commercial name';
        }

        if (!$customer->registrationNumber()) {
            $errors[] = 'registration';
        }

        if (!$customer->regimeType()) {
            $errors[] = 'regime type';
        }

        if (!$customer->phoneNumber()) {
            $errors[] = 'phone';
        }

        if (!$customer->email()) {
            $errors[] = 'email';
        }

        if (!$customer->address()) {
            $errors[] = 'address';
        }

        if (!$customer->cityId()) {
            $errors[] = 'city';
        }

        if (!$customer->taxpayerType()) {
            $errors[] = 'tax payer';
        }

        if (\count($errors)) {
            throw new Exception(
                \sprintf('eInvoice customer miss-configuration: %s are invalid', \implode(', ', $errors)),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    protected function validateCity(City $city): void
    {
        if (!$city->code()) {
            throw new Exception('eInvoice customer city miss-configuration: code is required', Response::HTTP_BAD_REQUEST);
        }
    }

    protected function validateState(State $state): void
    {
        if (!$state->code()) {
            throw new Exception('eInvoice customer state miss-configuration: code is required', Response::HTTP_BAD_REQUEST);
        }
    }

    protected function validateCountry(Country $country): void
    {
        if (!$country->code()) {
            throw new Exception('eInvoice customer country miss-configuration: code is required', Response::HTTP_BAD_REQUEST);
        }
    }

    protected function getItems(Paysheet $order): array
    {
        $details = (new IndexPaysheetDetailUseCase($this->orderDetailRepository))->execute(
            new IndexPaysheetDetailRequest([
                'orderId' => $order->id(),
            ], 1)
        )->orderDetails;

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
            throw new Exception('eInvoice without items is not allowed', Response::HTTP_BAD_REQUEST);
        }

        return $items;
    }

    protected function getDeposits(Paysheet $order): array
    {
        $payments = (new IndexPaymentUseCase($this->paymentRepository))->execute(
            new IndexPaymentRequest([
                'orderId' => $order->id(),
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

    protected function getEPayroll(Bill $bill, Provider $provider): ?\FlexPHP\eInvoice\InvoiceInterface
    {
        try {
            if ($this->testingMode) {
                throw new Exception('Running in test mode');
            }

            $wsdl = \realpath(__DIR__ . '/../../../vendor/flexphp/einvoice/resources/FacturaTech.v21.wsdl');

            return new EPayroll($provider->id(), [
                'username' => $provider->username(),
                'password' => $provider->password(),
                'carrier' => new \FlexPHP\eInvoice\Carrier\SoapCarrier($wsdl, [
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

    protected function upload(EPayroll $eInvoice, Bill $bill, Merchant $sender, Merchant $receiver, InvoiceContract $invoice): Bill
    {
        try {
            $response = $eInvoice->upload([
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

    protected function status(EPayroll $eInvoice, Bill $bill): Bill
    {
        try {
            $response = $eInvoice->getStatus($bill->traceId());
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

    protected function pdf(EPayroll $eInvoice, Bill $bill, string $prefix, string $number): Bill
    {
        try {
            $path = \realpath(__DIR__ . '/../../../invoices/pdf');

            if (!\is_dir($path)) {
                throw new Exception('Carpeta PDF no encontrada');
            }

            $response = $eInvoice->getPDF($prefix, $number);
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

    protected function xml(EPayroll $eInvoice, Bill $bill, string $prefix, string $number): Bill
    {
        try {
            $path = \realpath(__DIR__ . '/../../../invoices/xml');

            if (!\is_dir($path)) {
                throw new Exception('Carpeta XML no encontrada');
            }

            $response = $eInvoice->getXML($prefix, $number);
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

    private function getBillAnex(string $type, string $number, DateTimeInterface $date, string $hash, string $hashType): BillAnexContract
    {
        $billAnex = new BillAnex;
        $billAnex->setType($type);
        $billAnex->setNumber($number);
        $billAnex->setDate($date);
        $billAnex->setHash($hash);
        $billAnex->setHashType($hashType);

        return $billAnex;
    }
}
