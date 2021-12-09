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

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\AgreementStatusFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\CreateAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\DeleteAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\IndexAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\ReadAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\UpdateAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\UseCase\CreateAgreementStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\UseCase\DeleteAgreementStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\UseCase\IndexAgreementStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\UseCase\ReadAgreementStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\UseCase\UpdateAgreementStatusUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AgreementStatusController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTSTATUS_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexAgreementStatusUseCase $useCase): Response
    {
        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/agreementStatus/_ajax.html.twig' : '@FlexPHPPayroll/agreementStatus/index.html.twig';

        $request = new IndexAgreementStatusRequest($request->request->all(), (int)$request->query->get('page', 1));

        $response = $useCase->execute($request);

        return $this->render($template, [
            'agreementStatus' => $response->agreementStatus,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTSTATUS_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(AgreementStatusFormType::class);

        return $this->render('@FlexPHPPayroll/agreementStatus/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTSTATUS_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreateAgreementStatusUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(AgreementStatusFormType::class);
        $form->handleRequest($request);

        $request = new CreateAgreementStatusRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'agreementStatus'));

        return $this->redirectToRoute('flexphp.payroll.agreement-status.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTSTATUS_READ')", statusCode=401)
     */
    public function read(ReadAgreementStatusUseCase $useCase, string $id): Response
    {
        $request = new ReadAgreementStatusRequest($id);

        $response = $useCase->execute($request);

        if (!$response->agreementStatus->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/agreementStatus/show.html.twig', [
            'agreementStatus' => $response->agreementStatus,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTSTATUS_UPDATE')", statusCode=401)
     */
    public function edit(ReadAgreementStatusUseCase $useCase, string $id): Response
    {
        $request = new ReadAgreementStatusRequest($id);

        $response = $useCase->execute($request);

        if (!$response->agreementStatus->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(AgreementStatusFormType::class, $response->agreementStatus);

        return $this->render('@FlexPHPPayroll/agreementStatus/edit.html.twig', [
            'agreementStatus' => $response->agreementStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTSTATUS_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdateAgreementStatusUseCase $useCase, TranslatorInterface $trans, string $id): Response
    {
        $form = $this->createForm(AgreementStatusFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdateAgreementStatusRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'agreementStatus'));

        return $this->redirectToRoute('flexphp.payroll.agreement-status.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTSTATUS_DELETE')", statusCode=401)
     */
    public function delete(DeleteAgreementStatusUseCase $useCase, TranslatorInterface $trans, string $id): Response
    {
        $request = new DeleteAgreementStatusRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'agreementStatus'));

        return $this->redirectToRoute('flexphp.payroll.agreement-status.index');
    }
}
