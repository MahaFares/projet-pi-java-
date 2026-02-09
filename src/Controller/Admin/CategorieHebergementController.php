<?php

namespace App\Controller\Admin;

use App\Entity\CategorieHebergement;
use App\Form\CategorieHebergementType;
use App\Repository\CategorieHebergementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/categorie_hebergement')]
final class CategorieHebergementController extends AbstractController
{
    #[Route('/', name: 'admin_categorie_hebergement_index', methods: ['GET'])]
    public function index(CategorieHebergementRepository $categorieHebergementRepository): Response
    {
        return $this->render('admin/categorie_hebergement/index.html.twig', [
            'categorie_hebergements' => $categorieHebergementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_categorie_hebergement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieHebergement = new CategorieHebergement();
        $form = $this->createForm(CategorieHebergementType::class, $categorieHebergement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieHebergement);
            $entityManager->flush();

            return $this->redirectToRoute('admin_categorie_hebergement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categorie_hebergement/new.html.twig', [
            'categorie_hebergement' => $categorieHebergement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_categorie_hebergement_show', methods: ['GET'])]
    public function show(CategorieHebergement $categorieHebergement): Response
    {
        return $this->render('admin/categorie_hebergement/show.html.twig', [
            'categorie_hebergement' => $categorieHebergement,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_categorie_hebergement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieHebergement $categorieHebergement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieHebergementType::class, $categorieHebergement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_categorie_hebergement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categorie_hebergement/edit.html.twig', [
            'categorie_hebergement' => $categorieHebergement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_categorie_hebergement_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieHebergement $categorieHebergement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieHebergement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categorieHebergement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_categorie_hebergement_index', [], Response::HTTP_SEE_OTHER);
    }
}