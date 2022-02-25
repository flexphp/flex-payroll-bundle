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

use FlexPHP\Bundle\PayrollBundle\Domain\Employee\EmployeeFilterFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\EmployeeFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\CreateEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\DeleteEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeDocumentTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeeEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\FindEmployeePaymentMethodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\IndexEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\ReadEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\UpdateEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\CreateEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\DeleteEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\FindEmployeeAccountTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\FindEmployeeBankUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\FindEmployeeDocumentTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\FindEmployeeEmployeeSubTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\FindEmployeeEmployeeTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\FindEmployeePaymentMethodUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\IndexEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\ReadEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\UpdateEmployeeUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EmployeeController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexEmployeeUseCase $useCase): Response
    {
        $filters = $request->isMethod('POST')
            ? $request->request->filter('employee_filter_form', [])
            : $request->query->filter('employee_filter_form', []);

        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/employee/_ajax.html.twig' : '@FlexPHPPayroll/employee/index.html.twig';

        $request = new IndexEmployeeRequest($filters, (int)$request->query->get('page', 1), 50, $this->getUser()->timezone());

        $response = $useCase->execute($request);

        return $this->render($template, [
            'employees' => $response->employees,
            'filter' => ($this->createForm(EmployeeFilterFormType::class))->createView(),
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(EmployeeFormType::class);

        return $this->render('@FlexPHPPayroll/employee/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreateEmployeeUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(EmployeeFormType::class);
        $form->handleRequest($request);

        $request = new CreateEmployeeRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'employee'));

        return $this->redirectToRoute('flexphp.payroll.employees.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_READ')", statusCode=401)
     */
    public function read(ReadEmployeeUseCase $useCase, int $id): Response
    {
        $request = new ReadEmployeeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->employee->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/employee/show.html.twig', [
            'employee' => $response->employee,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_UPDATE')", statusCode=401)
     */
    public function edit(ReadEmployeeUseCase $useCase, int $id): Response
    {
        $request = new ReadEmployeeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->employee->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(EmployeeFormType::class, $response->employee);

        return $this->render('@FlexPHPPayroll/employee/edit.html.twig', [
            'employee' => $response->employee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdateEmployeeUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $form = $this->createForm(EmployeeFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdateEmployeeRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'employee'));

        return $this->redirectToRoute('flexphp.payroll.employees.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_DELETE')", statusCode=401)
     */
    public function delete(DeleteEmployeeUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $request = new DeleteEmployeeRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'employee'));

        return $this->redirectToRoute('flexphp.payroll.employees.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_DOCUMENTTYPE_INDEX')", statusCode=401)
     */
    public function findDocumentType(Request $request, FindEmployeeDocumentTypeUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindEmployeeDocumentTypeRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->documentTypes,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEETYPE_INDEX')", statusCode=401)
     */
    public function findEmployeeType(Request $request, FindEmployeeEmployeeTypeUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindEmployeeEmployeeTypeRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->employeeTypes,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEESUBTYPE_INDEX')", statusCode=401)
     */
    public function findEmployeeSubType(Request $request, FindEmployeeEmployeeSubTypeUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindEmployeeEmployeeSubTypeRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->employeeSubTypes,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYMENTMETHOD_INDEX')", statusCode=401)
     */
    public function findPaymentMethod(Request $request, FindEmployeePaymentMethodUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindEmployeePaymentMethodRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->paymentMethods,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_ACCOUNTTYPE_INDEX')", statusCode=401)
     */
    public function findAccountType(Request $request, FindEmployeeAccountTypeUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindEmployeeAccountTypeRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->accountTypes,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_BANK_INDEX')", statusCode=401)
     */
    public function findBank(Request $request, FindEmployeeBankUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindEmployeeBankRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->banks,
            'pagination' => ['more' => false],
        ]);
    }
}
