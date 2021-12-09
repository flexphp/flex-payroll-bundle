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

use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\PayrollFilterFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\PayrollFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\CreatePayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\DeletePayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\FindPayrollProviderRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\IndexPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\ReadPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\UpdatePayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\CreatePayrollUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\DeletePayrollUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\FindPayrollEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\FindPayrollPayrollStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\FindPayrollPayrollTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\FindPayrollPayrollUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\FindPayrollProviderUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\IndexPayrollUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\ReadPayrollUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\UpdatePayrollUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PayrollController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLL_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexPayrollUseCase $useCase): Response
    {
        $filters = $request->isMethod('POST')
            ? $request->request->filter('payroll_filter_form', [])
            : $request->query->filter('payroll_filter_form', []);

        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/payroll/_ajax.html.twig' : '@FlexPHPPayroll/payroll/index.html.twig';

        $request = new IndexPayrollRequest($filters, (int)$request->query->get('page', 1), 50, $this->getUser()->timezone());

        $response = $useCase->execute($request);

        return $this->render($template, [
            'payrolls' => $response->payrolls,
            'filter' => ($this->createForm(PayrollFilterFormType::class))->createView(),
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLL_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(PayrollFormType::class);

        return $this->render('@FlexPHPPayroll/payroll/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLL_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreatePayrollUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(PayrollFormType::class);
        $form->handleRequest($request);

        $request = new CreatePayrollRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'payroll'));

        return $this->redirectToRoute('flexphp.payroll.payrolls.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLL_READ')", statusCode=401)
     */
    public function read(ReadPayrollUseCase $useCase, int $id): Response
    {
        $request = new ReadPayrollRequest($id);

        $response = $useCase->execute($request);

        if (!$response->payroll->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/payroll/show.html.twig', [
            'payroll' => $response->payroll,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLL_UPDATE')", statusCode=401)
     */
    public function edit(ReadPayrollUseCase $useCase, int $id): Response
    {
        $request = new ReadPayrollRequest($id);

        $response = $useCase->execute($request);

        if (!$response->payroll->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PayrollFormType::class, $response->payroll);

        return $this->render('@FlexPHPPayroll/payroll/edit.html.twig', [
            'payroll' => $response->payroll,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLL_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdatePayrollUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $form = $this->createForm(PayrollFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdatePayrollRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'payroll'));

        return $this->redirectToRoute('flexphp.payroll.payrolls.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLL_DELETE')", statusCode=401)
     */
    public function delete(DeletePayrollUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $request = new DeletePayrollRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'payroll'));

        return $this->redirectToRoute('flexphp.payroll.payrolls.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_INDEX')", statusCode=401)
     */
    public function findEmployee(Request $request, FindPayrollEmployeeUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindPayrollEmployeeRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->employees,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PROVIDER_INDEX')", statusCode=401)
     */
    public function findProvider(Request $request, FindPayrollProviderUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindPayrollProviderRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->providers,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLSTATUS_INDEX')", statusCode=401)
     */
    public function findPayrollStatus(Request $request, FindPayrollPayrollStatusUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindPayrollPayrollStatusRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->payrollStatus,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLTYPE_INDEX')", statusCode=401)
     */
    public function findPayrollType(Request $request, FindPayrollPayrollTypeUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindPayrollPayrollTypeRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->payrollTypes,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLL_INDEX')", statusCode=401)
     */
    public function findPayroll(Request $request, FindPayrollPayrollUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindPayrollPayrollRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->payrolls,
            'pagination' => ['more' => false],
        ]);
    }
}
