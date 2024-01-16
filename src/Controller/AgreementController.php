<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Controller;

use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\AgreementFilterFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\AgreementFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\CreateAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\DeleteAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementCurrencyRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\FindAgreementEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\IndexAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\ReadAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\UpdateAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\CreateAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\DeleteAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\FindAgreementAgreementPeriodUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\FindAgreementAgreementStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\FindAgreementAgreementTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\FindAgreementCurrencyUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\FindAgreementEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\IndexAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\ReadAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\UpdateAgreementUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AgreementController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENT_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexAgreementUseCase $useCase): Response
    {
        $filters = $request->isMethod('POST')
            ? $request->request->filter('agreement_filter_form', [])
            : $request->query->filter('agreement_filter_form', []);

        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/agreement/_ajax.html.twig' : '@FlexPHPPayroll/agreement/index.html.twig';

        $request = new IndexAgreementRequest($filters, (int)$request->query->get('page', 1), 50, $this->getUser()->timezone());

        $response = $useCase->execute($request);

        return $this->render($template, [
            'agreements' => $response->agreements,
            'filter' => ($this->createForm(AgreementFilterFormType::class))->createView(),
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENT_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(AgreementFormType::class);

        return $this->render('@FlexPHPPayroll/agreement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENT_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreateAgreementUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(AgreementFormType::class);
        $form->handleRequest($request);

        $request = new CreateAgreementRequest($form->getData(), $this->getUser()->id(), $this->getUser()->timezone());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'agreement'));

        return $this->redirectToRoute('flexphp.payroll.agreements.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENT_READ')", statusCode=401)
     */
    public function read(ReadAgreementUseCase $useCase, int $id): Response
    {
        $request = new ReadAgreementRequest($id);

        $response = $useCase->execute($request);

        if (!$response->agreement->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/agreement/show.html.twig', [
            'agreement' => $response->agreement,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENT_UPDATE')", statusCode=401)
     */
    public function edit(ReadAgreementUseCase $useCase, int $id): Response
    {
        $request = new ReadAgreementRequest($id);

        $response = $useCase->execute($request);

        if (!$response->agreement->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(AgreementFormType::class, $response->agreement);

        return $this->render('@FlexPHPPayroll/agreement/edit.html.twig', [
            'agreement' => $response->agreement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENT_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdateAgreementUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $form = $this->createForm(AgreementFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdateAgreementRequest($id, $form->getData(), $this->getUser()->id(), false, $this->getUser()->timezone());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'agreement'));

        return $this->redirectToRoute('flexphp.payroll.agreements.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENT_DELETE')", statusCode=401)
     */
    public function delete(DeleteAgreementUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $request = new DeleteAgreementRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'agreement'));

        return $this->redirectToRoute('flexphp.payroll.agreements.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_INDEX')", statusCode=401)
     */
    public function findEmployee(Request $request, FindAgreementEmployeeUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindAgreementEmployeeRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->employees,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTTYPE_INDEX')", statusCode=401)
     */
    public function findAgreementType(Request $request, FindAgreementAgreementTypeUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindAgreementAgreementTypeRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->agreementTypes,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTPERIOD_INDEX')", statusCode=401)
     */
    public function findAgreementPeriod(Request $request, FindAgreementAgreementPeriodUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindAgreementAgreementPeriodRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->agreementPeriods,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_CURRENCY_INDEX')", statusCode=401)
     */
    public function findCurrency(Request $request, FindAgreementCurrencyUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindAgreementCurrencyRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->currencies,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTSTATUS_INDEX')", statusCode=401)
     */
    public function findAgreementStatus(Request $request, FindAgreementAgreementStatusUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindAgreementAgreementStatusRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->agreementStatus,
            'pagination' => ['more' => false],
        ]);
    }
}
