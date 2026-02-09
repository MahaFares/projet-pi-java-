<?php

namespace App\Controller\Admin;

use App\Entity\Hebergement;
use App\Form\HebergementType;
use App\Repository\HebergementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/hebergement')]
final class HebergementController extends AbstractController
{
<<<<<<< HEAD
    #[Route('/', name: 'admin_hebergement_index', methods: ['GET'])]
    public function index(HebergementRepository $hebergementRepository): Response
    {
        return $this->render('HebergementTemplate/hebergement/index.html.twig', [
=======
    #[Route(name: 'admin_hebergement_index', methods: ['GET'])]
    public function index(HebergementRepository $hebergementRepository): Response
    {
        return $this->render('hebergement/index.html.twig', [
>>>>>>> 4b79dc1d5c719fe365ccfcf5adf42753684afacd
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
<<<<<<< HEAD
            //$hebergement->setCreatedAt(new \DateTime());
            $hebergement->setActif(true);
=======
>>>>>>> 4b79dc1d5c719fe365ccfcf5adf42753684afacd
            $entityManager->persist($hebergement);
            $entityManager->flush();

            return $this->redirectToRoute('admin_hebergement_index', [], Response::HTTP_SEE_OTHER);
        }

<<<<<<< HEAD
        return $this->render('HebergementTemplate/hebergement/new.html.twig', [
            'hebergement' => $hebergement,
            'form' => $form->createView(),
=======
        return $this->render('hebergement/new.html.twig', [
            'hebergement' => $hebergement,
            'form' => $form,
>>>>>>> 4b79dc1d5c719fe365ccfcf5adf42753684afacd
        ]);
    }

    #[Route('/{id}', name: 'admin_hebergement_show', methods: ['GET'])]
    public function show(Hebergement $hebergement): Response
    {
<<<<<<< HEAD
        return $this->render('HebergementTemplate/hebergement/show.html.twig', [
=======
        return $this->render('hebergement/show.html.twig', [
>>>>>>> 4b79dc1d5c719fe365ccfcf5adf42753684afacd
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

<<<<<<< HEAD
        return $this->render('HebergementTemplate/hebergement/edit.html.twig', [
            'hebergement' => $hebergement,
            'form' => $form->createView(),
=======
        return $this->render('hebergement/edit.html.twig', [
            'hebergement' => $hebergement,
            'form' => $form,
>>>>>>> 4b79dc1d5c719fe365ccfcf5adf42753684afacd
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
<<<<<<< HEAD
}
=======
}
>>>>>>> 4b79dc1d5c719fe365ccfcf5adf42753684afacd
