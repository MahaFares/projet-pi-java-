<?php

namespace App\Controller\Produit;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Contrôle de saisie côté serveur :
 * - Validation des entrées : formulaire (Form Type + contraintes) puis ValidatorInterface->validate($entity).
 * - Pas de SQL brut : uniquement Repository->find($id) / findBy() (requêtes paramétrées Doctrine).
 * - ID route : requirements: ['id' => '\d+'] pour n'accepter que des entiers.
 * - Suppression : méthode POST + vérification du jeton CSRF (isCsrfTokenValid).
 */
#[Route('/admin/produit')]
class ProduitCrudController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProduitRepository $repository,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', name: 'app_produit_index', methods: ['GET'])]
    public function index(): Response
    {
        $items = $this->repository->findBy([], ['nom' => 'ASC']);

        return $this->render('FrontOffice/boutique/produit/index.html.twig', [
            'produits' => $items,
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->validator->validate($produit);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->render('FrontOffice/boutique/produit/new.html.twig', [
                    'produit' => $produit,
                    'form' => $form,
                ]);
            }
            $this->em->persist($produit);
            $this->em->flush();
            $this->addFlash('success', 'Produit créé.');
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('FrontOffice/boutique/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): Response
    {
        $produit = $this->repository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        return $this->render('FrontOffice/boutique/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $produit = $this->repository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->validator->validate($produit);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->render('FrontOffice/boutique/produit/edit.html.twig', [
                    'produit' => $produit,
                    'form' => $form,
                ]);
            }
            $this->em->flush();
            $this->addFlash('success', 'Produit mis à jour.');
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('FrontOffice/boutique/produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_produit_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        $produit = $this->repository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_produit_' . $id, $token)) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_produit_index');
        }

        $this->em->remove($produit);
        $this->em->flush();
        $this->addFlash('success', 'Produit supprimé.');

        return $this->redirectToRoute('app_produit_index');
    }
}
