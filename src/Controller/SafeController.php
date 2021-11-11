<?php

namespace MartenaSoft\WarehouseSafe\Controller;

use MartenaSoft\WarehouseSafe\Entity\ApplaySafe;
use App\Entity\Product;
use MartenaSoft\WarehouseSafe\Entity\Safe;
use MartenaSoft\WarehouseSafe\Form\ApplaySafeType;
use MartenaSoft\WarehouseSafe\Form\SafeType;
use MartenaSoft\WarehouseSafe\Repository\SafeRepository;
use MartenaSoft\WarehouseSafe\Service\SafeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MartenaSoft\WarehouseReports\Entity\Operation;
/**
 * @Route("/safe")
 */
class SafeController extends AbstractController
{
    /**
     * @Route("/", name="safe_index", methods={"GET"})
     */
    public function index(SafeRepository $safeRepository): Response
    {
        return $this->render('safe/index.html.twig', [
            'safes' => $safeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="safe_new", methods={"GET","POST"})
     */
    public function new(Request $request, SafeService $safeService): Response
    {
        $safe = new Safe();
        $form = $this->createForm(SafeType::class, $safe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($safe);
            $entityManager->flush();
            $safeService->income($safe->getSum());

            return $this->redirectToRoute('safe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('safe/new.html.twig', [
            'safe' => $safe,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="safe_show", methods={"GET"})
     */
    public function show(Safe $safe): Response
    {
        return $this->render('safe/show.html.twig', [
            'safe' => $safe,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="safe_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Safe $safe, SafeService $safeService): Response
    {
        $oldSum = $safe->getSum();
        $form = $this->createForm(SafeType::class, $safe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $safeService->income($safe->getSum(), $oldSum);

            return $this->redirectToRoute('safe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('safe/edit.html.twig', [
            'safe' => $safe,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/income-product/{id}", name="safe_income_product", methods={"GET", "POST"})
     */
    public function safeStageMoneyProduct(Request $request, Product $product, SafeService $safeService): Response
    {
        $applaySafe = new ApplaySafe();
        $applaySafeType = $this->createForm(ApplaySafeType::class, $applaySafe);
        $applaySafeType->handleRequest($request);

        if ($applaySafeType->isSubmitted()) {

            $logName = $logDescription = $product->getName() .' '.$product->getStatus()->getName();
            $safe = $this->getDoctrine()->getManager()->find(Safe::class, $applaySafe->getTypes());
            $product->setSavedStatus(Product::STATUS_SUCCESS);
            if ($product->getStatus()->getSafeMoneyOperation() == Operation::TYPE_ADD) {
                $safe->setSum($safe->getSum() + $product->getBoughtPrice());
                $logName .= ' added to safe  %d (old sum: %d)';
                $logDescription .= ' added to safe %d (old sum: %d)';
                $this->getDoctrine()->getManager()->flush();
                $safeService->income($product->getBoughtPrice(), 0, $logName, $logDescription);
            }

            if ($product->getStatus()->getSafeMoneyOperation() == Operation::TYPE_DEDUCT) {
                $safe->setSum($safe->getSum() - $product->getBoughtPrice());
                $logName .= ' deducted to safe  %d (old sum: %d)';
                $logDescription .= ' deducted to safe %d (old sum: %d)';
                $this->getDoctrine()->getManager()->flush();
                $safeService->withdraw($product->getBoughtPrice(), 0, $logName, $logDescription);
            }

            return $this->redirectToRoute('product_index');
        }

        return $this->render('safe/income_product.html.twig', [
            'product' => $product,
            'form' => $applaySafeType->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="safe_delete", methods={"POST"})
     */
    public function delete(Request $request, Safe $safe): Response
    {
        if ($this->isCsrfTokenValid('delete'.$safe->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($safe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('safe_index', [], Response::HTTP_SEE_OTHER);
    }
}
