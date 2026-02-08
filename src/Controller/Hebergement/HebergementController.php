<?php

namespace App\Controller\Hebergement;

use App\Entity\Hebergement;
use App\Form\HebergementType;
use App\Repository\HebergementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/hebergement')]
final class HebergementController extends AbstractController
{
    #[Route(name: 'admin_hebergement_index', methods: ['GET'])]
    public function index(HebergementRepository $hebergementRepository): Response
    {
        return $this->render('HebergementTemplate/hebergement/index.html.twig', [
            'hebergements' => $hebergementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_hebergement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hebergement = new Hebergement();
        $form = $this->createForm(HebergementType::class, $hebergement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hebergement);
            $entityManager->flush();

            return $this->redirectToRoute('admin_hebergement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('HebergementTemplate/hebergement/new.html.twig', [
            'hebergement' => $hebergement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_hebergement_show', methods: ['GET'])]
    public function show(Hebergement $hebergement): Response
    {
        return $this->render('HebergementTemplate/hebergement/show.html.twig', [
            'hebergement' => $hebergement,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_hebergement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hebergement $hebergement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HebergementType::class, $hebergement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_hebergement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('HebergementTemplate/hebergement/edit.html.twig', [
            'hebergement' => $hebergement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_hebergement_delete', methods: ['POST'])]
    public function delete(Request $request, Hebergement $hebergement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hebergement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($hebergement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_hebergement_index', [], Response::HTTP_SEE_OTHER);
    }
}
