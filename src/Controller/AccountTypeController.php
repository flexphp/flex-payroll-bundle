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

use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\AccountTypeFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\CreateAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\DeleteAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\IndexAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\ReadAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\UpdateAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\UseCase\CreateAccountTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\UseCase\DeleteAccountTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\UseCase\IndexAccountTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\UseCase\ReadAccountTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\UseCase\UpdateAccountTypeUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountTypeController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_ACCOUNTTYPE_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexAccountTypeUseCase $useCase): Response
    {
        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/accountType/_ajax.html.twig' : '@FlexPHPPayroll/accountType/index.html.twig';

        $request = new IndexAccountTypeRequest($request->request->all(), (int)$request->query->get('page', 1));

        $response = $useCase->execute($request);

        return $this->render($template, [
            'accountTypes' => $response->accountTypes,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_ACCOUNTTYPE_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(AccountTypeFormType::class);

        return $this->render('@FlexPHPPayroll/accountType/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_ACCOUNTTYPE_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreateAccountTypeUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(AccountTypeFormType::class);
        $form->handleRequest($request);

        $request = new CreateAccountTypeRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'accountType'));

        return $this->redirectToRoute('flexphp.payroll.account-types.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_ACCOUNTTYPE_READ')", statusCode=401)
     */
    public function read(ReadAccountTypeUseCase $useCase, string $id): Response
    {
        $request = new ReadAccountTypeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->accountType->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/accountType/show.html.twig', [
            'accountType' => $response->accountType,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_ACCOUNTTYPE_UPDATE')", statusCode=401)
     */
    public function edit(ReadAccountTypeUseCase $useCase, string $id): Response
    {
        $request = new ReadAccountTypeRequest($id);

        $response = $useCase->execute($request);

        if (!$response->accountType->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(AccountTypeFormType::class, $response->accountType);

        return $this->render('@FlexPHPPayroll/accountType/edit.html.twig', [
            'accountType' => $response->accountType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_ACCOUNTTYPE_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdateAccountTypeUseCase $useCase, TranslatorInterface $trans, string $id): Response
    {
        $form = $this->createForm(AccountTypeFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdateAccountTypeRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'accountType'));

        return $this->redirectToRoute('flexphp.payroll.account-types.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_ACCOUNTTYPE_DELETE')", statusCode=401)
     */
    public function delete(DeleteAccountTypeUseCase $useCase, TranslatorInterface $trans, string $id): Response
    {
        $request = new DeleteAccountTypeRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'accountType'));

        return $this->redirectToRoute('flexphp.payroll.account-types.index');
    }
}
