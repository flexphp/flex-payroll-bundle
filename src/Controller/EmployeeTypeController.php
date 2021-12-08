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

use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\EmployeeTypeFilterFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\EmployeeTypeFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\CreateEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\DeleteEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\IndexEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\ReadEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\UpdateEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\UseCase\CreateEmployeeTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\UseCase\DeleteEmployeeTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\UseCase\IndexEmployeeTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\UseCase\ReadEmployeeTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\UseCase\UpdateEmployeeTypeUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EmployeeTypeController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEETYPE_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexEmployeeTypeUseCase $useCase): Response
    {
        $filters = $request->isMethod('POST')
            ? $request->request->filter('employeeType_filter_form', [])
            : $request->query->filter('employeeType_filter_form', []);

        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/employeeType/_ajax.html.twig' : '@FlexPHPPayroll/employeeType/index.html.twig';

        $request = new IndexEmployeeTypeRequest($filters, (int)$request->query->get('page', 1), 50, $this->getUser()->timezone());

        $response = $useCase->execute($request);

        return $this->render($template, [
            'employeeTypes' => $response->employeeTypes,
            'filter' => ($this->createForm(EmployeeTypeFilterFormType::class))->createView(),
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEETYPE_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(EmployeeTypeFormType::class);

        return $this->render('@FlexPHPPayroll/employeeType/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEETYPE_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreateEmployeeTypeUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(EmployeeTypeFormType::class);
        $form->handleRequest($request);

        $request = new CreateEmployeeTypeRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'employeeType'));

        return $this->redirectToRoute('flexphp.payroll.employee-types.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEETYPE_READ')", statusCode=401)
     */
    public function read(ReadEmployeeTypeUseCase $useCase, int $id): Response
    {
        $request = new ReadEmployeeTypeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->employeeType->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/employeeType/show.html.twig', [
            'employeeType' => $response->employeeType,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEETYPE_UPDATE')", statusCode=401)
     */
    public function edit(ReadEmployeeTypeUseCase $useCase, int $id): Response
    {
        $request = new ReadEmployeeTypeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->employeeType->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(EmployeeTypeFormType::class, $response->employeeType);

        return $this->render('@FlexPHPPayroll/employeeType/edit.html.twig', [
            'employeeType' => $response->employeeType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEETYPE_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdateEmployeeTypeUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $form = $this->createForm(EmployeeTypeFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdateEmployeeTypeRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'employeeType'));

        return $this->redirectToRoute('flexphp.payroll.employee-types.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEETYPE_DELETE')", statusCode=401)
     */
    public function delete(DeleteEmployeeTypeUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $request = new DeleteEmployeeTypeRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'employeeType'));

        return $this->redirectToRoute('flexphp.payroll.employee-types.index');
    }
}
