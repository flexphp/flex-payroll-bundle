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

use FlexPHP\Bundle\PayrollBundle\Domain\Bank\BankFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\CreateBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\DeleteBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\IndexBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\ReadBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\Request\UpdateBankRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\UseCase\CreateBankUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\UseCase\DeleteBankUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\UseCase\IndexBankUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\UseCase\ReadBankUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Bank\UseCase\UpdateBankUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BankController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_BANK_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexBankUseCase $useCase): Response
    {
        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/bank/_ajax.html.twig' : '@FlexPHPPayroll/bank/index.html.twig';

        $request = new IndexBankRequest($request->request->all(), (int)$request->query->get('page', 1));

        $response = $useCase->execute($request);

        return $this->render($template, [
            'banks' => $response->banks,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_BANK_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        $form = $this->createForm(BankFormType::class);

        return $this->render('@FlexPHPPayroll/bank/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_BANK_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreateBankUseCase $useCase, TranslatorInterface $trans): Response
    {
        $form = $this->createForm(BankFormType::class);
        $form->handleRequest($request);

        $request = new CreateBankRequest($form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'bank'));

        return $this->redirectToRoute('flexphp.payroll.banks.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_BANK_READ')", statusCode=401)
     */
    public function read(ReadBankUseCase $useCase, int $id): Response
    {
        $request = new ReadBankRequest($id);

        $response = $useCase->execute($request);

        if (!$response->bank->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/bank/show.html.twig', [
            'bank' => $response->bank,
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_BANK_UPDATE')", statusCode=401)
     */
    public function edit(ReadBankUseCase $useCase, int $id): Response
    {
        $request = new ReadBankRequest($id);

        $response = $useCase->execute($request);

        if (!$response->bank->id()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(BankFormType::class, $response->bank);

        return $this->render('@FlexPHPPayroll/bank/edit.html.twig', [
            'bank' => $response->bank,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_BANK_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdateBankUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $form = $this->createForm(BankFormType::class);
        $form->submit($request->request->get($form->getName()));
        $form->handleRequest($request);

        $request = new UpdateBankRequest($id, $form->getData(), $this->getUser()->id());

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'bank'));

        return $this->redirectToRoute('flexphp.payroll.banks.index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_BANK_DELETE')", statusCode=401)
     */
    public function delete(DeleteBankUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $request = new DeleteBankRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'bank'));

        return $this->redirectToRoute('flexphp.payroll.banks.index');
    }
}
