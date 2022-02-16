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

use FlexPHP\Bundle\PayrollBundle\Domain\Payment\Request\IndexPaymentRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payment\UseCase\IndexPaymentUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Paysheet;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\PaysheetFilterFormType;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreateEPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreatePaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\CreatePrepaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\DeletePaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetAlternativeProductRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetHistoryServiceRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetPaysheetStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\FindPaysheetWorkerRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\GetLastPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\IndexPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\ReadPaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\Request\UpdatePaysheetRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\CreateEPayrollUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\CreatePaysheetUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\CreatePrepaysheetUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\DeletePaysheetUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\FindPaysheetAlternativeProductUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\FindPaysheetEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\FindPaysheetHistoryServiceUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\FindPaysheetPaysheetStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\FindPaysheetAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\FindPaysheetWorkerUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\GetLastPaysheetUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\IndexPaysheetUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\ReadPaysheetUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Paysheet\UseCase\UpdatePaysheetUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PaysheetController extends AbstractController
{
    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_INDEX')", statusCode=401)
     */
    public function index(Request $request, IndexPaysheetUseCase $useCase): Response
    {
        $filters = $request->isMethod('POST')
            ? $request->request->filter('paysheet_filter_form', [])
            : $request->query->filter('paysheet_filter_form', []);

        $template = $request->isXmlHttpRequest() ? '@FlexPHPPayroll/paysheet/_ajax.html.twig' : '@FlexPHPPayroll/paysheet/index.html.twig';

        $request = new IndexPaysheetRequest($filters, (int)$request->query->get('page', 1), 50, $this->getUser()->timezone());

        $response = $useCase->execute($request);

        return $this->render($template, [
            'paysheets' => $response->paysheets,
            'filter' => ($this->createForm(PaysheetFilterFormType::class))->createView(),
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_CREATE')", statusCode=401)
     */
    public function new(): Response
    {
        return $this->render('@FlexPHPPayroll/paysheet/__paysheet_base.html.twig', [
            'paysheet' => new Paysheet(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_CREATE')", statusCode=401)
     */
    public function create(Request $request, CreatePaysheetUseCase $useCase, TranslatorInterface $trans): Response
    {
        if (!$this->isCsrfTokenValid('create.paysheet', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $request = new CreatePaysheetRequest($request->request->all(), $this->getUser()->id(), $this->getUser()->timezone());

        $response = $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.created', [], 'paysheet'));

        return $this->redirectToRoute('flexphp.payroll.paysheets.read', ['id' => $response->paysheet->id()], 201);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_READ')", statusCode=401)
     */
    public function read(ReadPaysheetUseCase $useCase, int $id): Response
    {
        $request = new ReadPaysheetRequest($id);

        $response = $useCase->execute($request);

        if (!$response->paysheet->id()) {
            throw $this->createNotFoundException();
        }

        $paysheet = $response->paysheet;

        // $request = new IndexPaymentRequest(['paysheetId' => $paysheet->id()], $this->getUser()->id());

        // $response = $indexPaymentUseCase->execute($request);

        // $payments = $response->payments;

        return $this->render('@FlexPHPPayroll/paysheet/show.html.twig', \compact('paysheet'));
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_UPDATE')", statusCode=401)
     */
    public function edit(ReadPaysheetUseCase $useCase, int $id): Response
    {
        $request = new ReadPaysheetRequest($id);

        $response = $useCase->execute($request);

        if (!$response->paysheet->id()) {
            throw $this->createNotFoundException();
        }

        return $this->render('@FlexPHPPayroll/paysheet/__paysheet_base.html.twig', [
            'paysheet' => $response->paysheet,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_UPDATE')", statusCode=401)
     */
    public function update(Request $request, UpdatePaysheetUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        if (!$this->isCsrfTokenValid('edit.paysheet', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $request = new UpdatePaysheetRequest($id, $request->request->all(), $this->getUser()->id(), $this->getUser()->timezone());

        $response = $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.updated', [], 'paysheet'));

        return $this->redirectToRoute('flexphp.payroll.paysheets.read', ['id' => $response->paysheet->id()]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_DELETE')", statusCode=401)
     */
    public function delete(DeletePaysheetUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $request = new DeletePaysheetRequest($id);

        $useCase->execute($request);

        $this->addFlash('success', $trans->trans('message.deleted', [], 'paysheet'));

        return $this->redirectToRoute('flexphp.payroll.paysheets.index');
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_EMPLOYEE_INDEX')", statusCode=401)
     */
    public function findEmployee(Request $request, FindPaysheetEmployeeUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindPaysheetEmployeeRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->employees ?? [],
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_AGREEMENT_INDEX')", statusCode=401)
     */
    public function findAgreement(Request $request, FindPaysheetAgreementUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindPaysheetAgreementRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->agreements,
            'pagination' => ['more' => false],
        ]);
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEETSTATUS_INDEX')", statusCode=401)
     */
    public function findPaysheetStatus(Request $request, FindPaysheetPaysheetStatusUseCase $useCase): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $request = new FindPaysheetPaysheetStatusRequest($request->request->all());

        $response = $useCase->execute($request);

        return new JsonResponse([
            'results' => $response->paysheetStatus,
            'pagination' => ['more' => false],
        ]);
    }

//     /**
//      * @Cache(smaxage="3600")
//      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_INDEX')", statusCode=401)
//      */
//     public function findHistoryService(Request $request, FindPaysheetHistoryServiceUseCase $useCase): Response
//     {
//         if (!$request->isXmlHttpRequest()) {
//             return new JsonResponse([], Response::HTTP_BAD_REQUEST);
//         }

//         $request = new FindPaysheetHistoryServiceRequest($request->request->all());

//         $response = $useCase->execute($request);

//         return new JsonResponse([
//             'results' => $response->historyServices ?? [],
//             'pagination' => ['more' => false],
//         ]);
//     }

//     /**
//      * @Cache(smaxage="3600")
//      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_INDEX')", statusCode=401)
//      */
//     public function getLast(Request $request, GetLastPaysheetUseCase $useCase): Response
//     {
//         if (!$request->isXmlHttpRequest()) {
//             return new JsonResponse([], Response::HTTP_BAD_REQUEST);
//         }

//         $request = new GetLastPaysheetRequest($request->request->all());

//         $response = $useCase->execute($request);

//         return new JsonResponse([
//             'results' => $response->paysheet,
//             'pagination' => ['more' => false],
//         ]);
//     }

//     /**
//      * @Cache(smaxage="3600")
//      * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_INDEX')", statusCode=401)
//      */
//     public function findAlternativeProducts(Request $request, FindPaysheetAlternativeProductUseCase $useCase): Response
//     {
//         if (!$request->isXmlHttpRequest()) {
//             return new JsonResponse([], Response::HTTP_BAD_REQUEST);
//         }

//         $request = new FindPaysheetAlternativeProductRequest($request->request->all());

//         $response = $useCase->execute($request);

//         return new JsonResponse([
//             'results' => $response->alternativeProducts,
//             'pagination' => ['more' => false],
//         ]);
//     }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_PAYSHEET_READ')", statusCode=401)
     */
    public function prepaysheet(CreatePrepaysheetUseCase $useCase, int $id): Response
    {
        $request = new CreatePrepaysheetRequest($id, $this->getUser()->timezone());

        $prepaysheet = $useCase->execute($request);

        $response = new Response($prepaysheet->content);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            $prepaysheet->filename
        ));

        return $response;
    }

    /**
     * @Cache(smaxage="3600")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_USER_BILL_CREATE')", statusCode=401)
     */
    public function epayroll(Request $request, CreateEPayrollUseCase $useCase, TranslatorInterface $trans, int $id): Response
    {
        $referer = $request->headers->get('referer');

        $request = new CreateEPayrollRequest($id, $this->getUser()->timezone());

        $epayroll = $useCase->execute($request);

        if (!$epayroll->content) {
            $message = $epayroll->message ? $epayroll->message : $trans->trans('message.payrolled', [], 'paysheet');
            $this->addFlash('danger', $message);

            if ($referer) {
                return $this->redirect($referer);
            }

            return $this->redirectToRoute('paysheets.index');
        }

        // Demo
        // $epayroll = new \stdClass;
        // $epayroll->content = file_get_contents('/var/www/html/atar/certification-factura-tech/payroll/exercises/s1_1_82401.pdf');
        // $epayroll->filename = '82401.pdf';

        $response = new Response($epayroll->content);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            $epayroll->filename
        ));

        return $response;
    }
}
