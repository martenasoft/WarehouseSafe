<?php

namespace MartenaSoft\WarehouseSafe\Controller;

use MartenaSoft\WarehouseSafe\Entity\Safe;
use MartenaSoft\WarehouseSafe\Form\SafeType;
use MartenaSoft\WarehouseSafe\Repository\SafeRepository;
use MartenaSoft\WarehouseSafe\Service\SafeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

        return $this->renderForm('@Safe/safe/new.html.twig', [
            'safe' => $safe,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="safe_show", methods={"GET"})
     */
    public function show(Safe $safe): Response
    {
        return $this->render('@Safe/safe/show.html.twig', [
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

        return $this->renderForm('@Safe/safe/edit.html.twig', [
            'safe' => $safe,
            'form' => $form,
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
