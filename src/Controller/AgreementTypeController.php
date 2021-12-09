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

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\AgreementTypeFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\CreateAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\DeleteAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\IndexAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\ReadAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\UpdateAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\UseCase\CreateAgreementTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\UseCase\DeleteAgreementTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\UseCase\IndexAgreementTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\UseCase\ReadAgreementTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\UseCase\UpdateAgreementTypeUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AgreementTypeController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTTYPE_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexAgreementTypeUseCase $useCase): Response
    {
        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/agreementType/_ajax.html.twig' : '@FlexPHPPayroll/agreementType/index.html.twig';

        $request = new IndexAgreementTypeRequest($request->request->all(), (int)$request->query->get('page', 1));

        $response = $useCase->execute($request);

        return $this->render($template, [
            'agreementTypes' => $response->agreementTypes,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTTYPE_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(AgreementTypeFormType::class);

        return $this->render('@FlexPHPPayroll/agreementType/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTTYPE_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreateAgreementTypeUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(AgreementTypeFormType::class);
        $form->handleRequest($request);

        $request = new CreateAgreementTypeRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'agreementType'));

        return $this->redirectToRoute('flexphp.payroll.agreement-types.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTTYPE_READ')", statusCode=401)
     */
    public function read(ReadAgreementTypeUseCase $useCase, int $id): Response
    {
        $request = new ReadAgreementTypeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->agreementType->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/agreementType/show.html.twig', [
            'agreementType' => $response->agreementType,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTTYPE_UPDATE')", statusCode=401)
     */
    public function edit(ReadAgreementTypeUseCase $useCase, int $id): Response
    {
        $request = new ReadAgreementTypeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->agreementType->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(AgreementTypeFormType::class, $response->agreementType);

        return $this->render('@FlexPHPPayroll/agreementType/edit.html.twig', [
            'agreementType' => $response->agreementType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTTYPE_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdateAgreementTypeUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $form = $this->createForm(AgreementTypeFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdateAgreementTypeRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'agreementType'));

        return $this->redirectToRoute('flexphp.payroll.agreement-types.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTTYPE_DELETE')", statusCode=401)
     */
    public function delete(DeleteAgreementTypeUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $request = new DeleteAgreementTypeRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'agreementType'));

        return $this->redirectToRoute('flexphp.payroll.agreement-types.index');
    }
}
