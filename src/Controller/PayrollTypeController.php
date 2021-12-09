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

use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\PayrollTypeFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\CreatePayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\DeletePayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\IndexPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\ReadPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\UpdatePayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\UseCase\CreatePayrollTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\UseCase\DeletePayrollTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\UseCase\IndexPayrollTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\UseCase\ReadPayrollTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\UseCase\UpdatePayrollTypeUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PayrollTypeController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLTYPE_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexPayrollTypeUseCase $useCase): Response
    {
        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/payrollType/_ajax.html.twig' : '@FlexPHPPayroll/payrollType/index.html.twig';

        $request = new IndexPayrollTypeRequest($request->request->all(), (int)$request->query->get('page', 1));

        $response = $useCase->execute($request);

        return $this->render($template, [
            'payrollTypes' => $response->payrollTypes,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLTYPE_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(PayrollTypeFormType::class);

        return $this->render('@FlexPHPPayroll/payrollType/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLTYPE_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreatePayrollTypeUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(PayrollTypeFormType::class);
        $form->handleRequest($request);

        $request = new CreatePayrollTypeRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'payrollType'));

        return $this->redirectToRoute('flexphp.payroll.payroll-types.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLTYPE_READ')", statusCode=401)
     */
    public function read(ReadPayrollTypeUseCase $useCase, string $id): Response
    {
        $request = new ReadPayrollTypeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->payrollType->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/payrollType/show.html.twig', [
            'payrollType' => $response->payrollType,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLTYPE_UPDATE')", statusCode=401)
     */
    public function edit(ReadPayrollTypeUseCase $useCase, string $id): Response
    {
        $request = new ReadPayrollTypeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->payrollType->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PayrollTypeFormType::class, $response->payrollType);

        return $this->render('@FlexPHPPayroll/payrollType/edit.html.twig', [
            'payrollType' => $response->payrollType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLTYPE_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdatePayrollTypeUseCase $useCase, TranslatorInterface $trans, string $id): Response
    {
        $form = $this->createForm(PayrollTypeFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdatePayrollTypeRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'payrollType'));

        return $this->redirectToRoute('flexphp.payroll.payroll-types.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLTYPE_DELETE')", statusCode=401)
     */
    public function delete(DeletePayrollTypeUseCase $useCase, TranslatorInterface $trans, string $id): Response
    {
        $request = new DeletePayrollTypeRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'payrollType'));

        return $this->redirectToRoute('flexphp.payroll.payroll-types.index');
    }
}
