<?php

namespace App\Controller\Produit;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/produit')]
class ProduitCrudController extends AbstractController
{
    #[Route('', name: 'app_produit_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $q = $request->query->get('q');
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');
        $available = $request->query->get('available');

        $minPrice = $minPrice !== null && $minPrice !== '' ? (float) $minPrice : null;
        $maxPrice = $maxPrice !== null && $maxPrice !== '' ? (float) $maxPrice : null;
        $available = ($available === '1' ? true : ($available === '0' ? false : null));

        $items = $this->repository->findByFilters($q, $minPrice, $maxPrice, $available);

        return $this->render('ProductTemplate/produit/index.html.twig', [
            'produits' => $items,
            'filters' => [
                'q' => $q,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'available' => $available,
            ],
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Contrôle de saisie côté serveur pour le nom du produit
            $nom = $produit->getNom();
            
            // Vérifier que le nom n'est pas vide
            if (empty(trim($nom))) {
                $this->addFlash('error', 'Le nom du produit ne peut pas être vide.');
                return $this->render('ProductTemplate/produit/new.html.twig', [
                    'produit' => $produit,
                    'form' => $form,
                ]);
            }
            
            // Vérifier que le nom ne contient pas que des chiffres
            if (preg_match('/^\d+$/', trim($nom))) {
                $this->addFlash('error', 'Le nom du produit ne peut pas contenir uniquement des chiffres.');
                return $this->render('ProductTemplate/produit/new.html.twig', [
                    'produit' => $produit,
                    'form' => $form,
                ]);
            }

            $errors = $this->validator->validate($produit);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->render('ProductTemplate/produit/new.html.twig', [
                    'produit' => $produit,
                    'form' => $form,
                ]);
            }
            $this->em->persist($produit);
            $this->em->flush();
            $this->addFlash('success', 'Produit créé.');
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('ProductTemplate/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        $produit = $this->repository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        return $this->render('ProductTemplate/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Contrôle de saisie côté serveur pour le nom du produit
            $nom = $produit->getNom();
            
            // Vérifier que le nom n'est pas vide
            if (empty(trim($nom))) {
                $this->addFlash('error', 'Le nom du produit ne peut pas être vide.');
                return $this->render('ProductTemplate/produit/edit.html.twig', [
                    'produit' => $produit,
                    'form' => $form,
                ]);
            }
            
            // Vérifier que le nom ne contient pas que des chiffres
            if (preg_match('/^\d+$/', trim($nom))) {
                $this->addFlash('error', 'Le nom du produit ne peut pas contenir uniquement des chiffres.');
                return $this->render('ProductTemplate/produit/edit.html.twig', [
                    'produit' => $produit,
                    'form' => $form,
                ]);
            }

            $errors = $this->validator->validate($produit);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->render('ProductTemplate/produit/edit.html.twig', [
                    'produit' => $produit,
                    'form' => $form,
                ]);
            }
            $this->em->flush();
            $this->addFlash('success', 'Produit mis à jour.');
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('ProductTemplate/produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_produit_'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}