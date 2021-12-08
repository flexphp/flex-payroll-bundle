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

use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\EmployeeSubTypeFilterFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\EmployeeSubTypeFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\CreateEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\DeleteEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\IndexEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\ReadEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\UpdateEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\UseCase\CreateEmployeeSubTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\UseCase\DeleteEmployeeSubTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\UseCase\IndexEmployeeSubTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\UseCase\ReadEmployeeSubTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\UseCase\UpdateEmployeeSubTypeUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EmployeeSubTypeController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEESUBTYPE_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexEmployeeSubTypeUseCase $useCase): Response
    {
        $filters = $request->isMethod('POST')
            ? $request->request->filter('employeeSubType_filter_form', [])
            : $request->query->filter('employeeSubType_filter_form', []);

        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/employeeSubType/_ajax.html.twig' : '@FlexPHPPayroll/employeeSubType/index.html.twig';

        $request = new IndexEmployeeSubTypeRequest($filters, (int)$request->query->get('page', 1), 50, $this->getUser()->timezone());

        $response = $useCase->execute($request);

        return $this->render($template, [
            'employeeSubTypes' => $response->employeeSubTypes,
            'filter' => ($this->createForm(EmployeeSubTypeFilterFormType::class))->createView(),
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEESUBTYPE_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(EmployeeSubTypeFormType::class);

        return $this->render('@FlexPHPPayroll/employeeSubType/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEESUBTYPE_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreateEmployeeSubTypeUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(EmployeeSubTypeFormType::class);
        $form->handleRequest($request);

        $request = new CreateEmployeeSubTypeRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'employeeSubType'));

        return $this->redirectToRoute('flexphp.payroll.employee-sub-types.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEESUBTYPE_READ')", statusCode=401)
     */
    public function read(ReadEmployeeSubTypeUseCase $useCase, int $id): Response
    {
        $request = new ReadEmployeeSubTypeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->employeeSubType->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/employeeSubType/show.html.twig', [
            'employeeSubType' => $response->employeeSubType,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEESUBTYPE_UPDATE')", statusCode=401)
     */
    public function edit(ReadEmployeeSubTypeUseCase $useCase, int $id): Response
    {
        $request = new ReadEmployeeSubTypeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->employeeSubType->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(EmployeeSubTypeFormType::class, $response->employeeSubType);

        return $this->render('@FlexPHPPayroll/employeeSubType/edit.html.twig', [
            'employeeSubType' => $response->employeeSubType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEESUBTYPE_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdateEmployeeSubTypeUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $form = $this->createForm(EmployeeSubTypeFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdateEmployeeSubTypeRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'employeeSubType'));

        return $this->redirectToRoute('flexphp.payroll.employee-sub-types.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEESUBTYPE_DELETE')", statusCode=401)
     */
    public function delete(DeleteEmployeeSubTypeUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $request = new DeleteEmployeeSubTypeRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'employeeSubType'));

        return $this->redirectToRoute('flexphp.payroll.employee-sub-types.index');
    }
}
