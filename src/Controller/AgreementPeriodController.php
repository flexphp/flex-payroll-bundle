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

use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\AgreementPeriodFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\CreateAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\DeleteAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\IndexAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\ReadAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\UpdateAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\UseCase\CreateAgreementPeriodUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\UseCase\DeleteAgreementPeriodUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\UseCase\IndexAgreementPeriodUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\UseCase\ReadAgreementPeriodUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\UseCase\UpdateAgreementPeriodUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AgreementPeriodController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTPERIOD_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexAgreementPeriodUseCase $useCase): Response
    {
        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/agreementPeriod/_ajax.html.twig' : '@FlexPHPPayroll/agreementPeriod/index.html.twig';

        $request = new IndexAgreementPeriodRequest($request->request->all(), (int)$request->query->get('page', 1));

        $response = $useCase->execute($request);

        return $this->render($template, [
            'agreementPeriods' => $response->agreementPeriods,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTPERIOD_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(AgreementPeriodFormType::class);

        return $this->render('@FlexPHPPayroll/agreementPeriod/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTPERIOD_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreateAgreementPeriodUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(AgreementPeriodFormType::class);
        $form->handleRequest($request);

        $request = new CreateAgreementPeriodRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'agreementPeriod'));

        return $this->redirectToRoute('flexphp.payroll.agreement-periods.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTPERIOD_READ')", statusCode=401)
     */
    public function read(ReadAgreementPeriodUseCase $useCase, string $id): Response
    {
        $request = new ReadAgreementPeriodRequest($id);

        $response = $useCase->execute($request);

        if (!$response->agreementPeriod->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/agreementPeriod/show.html.twig', [
            'agreementPeriod' => $response->agreementPeriod,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTPERIOD_UPDATE')", statusCode=401)
     */
    public function edit(ReadAgreementPeriodUseCase $useCase, string $id): Response
    {
        $request = new ReadAgreementPeriodRequest($id);

        $response = $useCase->execute($request);

        if (!$response->agreementPeriod->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(AgreementPeriodFormType::class, $response->agreementPeriod);

        return $this->render('@FlexPHPPayroll/agreementPeriod/edit.html.twig', [
            'agreementPeriod' => $response->agreementPeriod,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTPERIOD_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdateAgreementPeriodUseCase $useCase, TranslatorInterface $trans, string $id): Response
    {
        $form = $this->createForm(AgreementPeriodFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdateAgreementPeriodRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'agreementPeriod'));

        return $this->redirectToRoute('flexphp.payroll.agreement-periods.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENTPERIOD_DELETE')", statusCode=401)
     */
    public function delete(DeleteAgreementPeriodUseCase $useCase, TranslatorInterface $trans, string $id): Response
    {
        $request = new DeleteAgreementPeriodRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'agreementPeriod'));

        return $this->redirectToRoute('flexphp.payroll.agreement-periods.index');
    }
}
