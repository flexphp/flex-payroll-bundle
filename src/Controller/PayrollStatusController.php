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

use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\PayrollStatusFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\CreatePayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\DeletePayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\IndexPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\ReadPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\UpdatePayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\UseCase\CreatePayrollStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\UseCase\DeletePayrollStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\UseCase\IndexPayrollStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\UseCase\ReadPayrollStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\UseCase\UpdatePayrollStatusUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PayrollStatusController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLSTATUS_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexPayrollStatusUseCase $useCase): Response
    {
        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/payrollStatus/_ajax.html.twig' : '@FlexPHPPayroll/payrollStatus/index.html.twig';

        $request = new IndexPayrollStatusRequest($request->request->all(), (int)$request->query->get('page', 1));

        $response = $useCase->execute($request);

        return $this->render($template, [
            'payrollStatus' => $response->payrollStatus,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLSTATUS_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(PayrollStatusFormType::class);

        return $this->render('@FlexPHPPayroll/payrollStatus/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLSTATUS_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreatePayrollStatusUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(PayrollStatusFormType::class);
        $form->handleRequest($request);

        $request = new CreatePayrollStatusRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'payrollStatus'));

        return $this->redirectToRoute('flexphp.payroll.payroll-status.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLSTATUS_READ')", statusCode=401)
     */
    public function read(ReadPayrollStatusUseCase $useCase, string $id): Response
    {
        $request = new ReadPayrollStatusRequest($id);

        $response = $useCase->execute($request);

        if (!$response->payrollStatus->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/payrollStatus/show.html.twig', [
            'payrollStatus' => $response->payrollStatus,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLSTATUS_UPDATE')", statusCode=401)
     */
    public function edit(ReadPayrollStatusUseCase $useCase, string $id): Response
    {
        $request = new ReadPayrollStatusRequest($id);

        $response = $useCase->execute($request);

        if (!$response->payrollStatus->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PayrollStatusFormType::class, $response->payrollStatus);

        return $this->render('@FlexPHPPayroll/payrollStatus/edit.html.twig', [
            'payrollStatus' => $response->payrollStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLSTATUS_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdatePayrollStatusUseCase $useCase, TranslatorInterface $trans, string $id): Response
    {
        $form = $this->createForm(PayrollStatusFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdatePayrollStatusRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'payrollStatus'));

        return $this->redirectToRoute('flexphp.payroll.payroll-status.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYROLLSTATUS_DELETE')", statusCode=401)
     */
    public function delete(DeletePayrollStatusUseCase $useCase, TranslatorInterface $trans, string $id): Response
    {
        $request = new DeletePayrollStatusRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'payrollStatus'));

        return $this->redirectToRoute('flexphp.payroll.payroll-status.index');
    }
}
