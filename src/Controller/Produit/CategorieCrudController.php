<?php

namespace App\Controller\Produit;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
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
#[Route('/admin/categorie')]
class CategorieCrudController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CategorieRepository $repository,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Liste : requête paramétrée via Repository (pas de concaténation SQL).
     */
    #[Route('', name: 'app_categorie_index', methods: ['GET'])]
    public function index(): Response
    {
        $items = $this->repository->findBy([], ['nom' => 'ASC']);

        return $this->render('crud/categorie/index.html.twig', [
            'categories' => $items,
        ]);
    }

    #[Route('/new', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->validator->validate($categorie);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->render('crud/categorie/new.html.twig', [
                    'categorie' => $categorie,
                    'form' => $form,
                ]);
            }
            $this->em->persist($categorie);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie créée.');
            return $this->redirectToRoute('app_categorie_index');
        }

        return $this->render('crud/categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    /**
     * Show : ID récupéré depuis la route (int), pas depuis la requête en brut → requête find(id) paramétrée.
     */
    #[Route('/{id}', name: 'app_categorie_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): Response
    {
        $categorie = $this->repository->find($id);
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }

        return $this->render('crud/categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $categorie = $this->repository->find($id);
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->validator->validate($categorie);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->render('crud/categorie/edit.html.twig', [
                    'categorie' => $categorie,
                    'form' => $form,
                ]);
            }
            $this->em->flush();
            $this->addFlash('success', 'Catégorie mise à jour.');
            return $this->redirectToRoute('app_categorie_index');
        }

        return $this->render('crud/categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_categorie_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        $categorie = $this->repository->find($id);
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_categorie_' . $id, $token)) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_categorie_index');
        }

        $this->em->remove($categorie);
        $this->em->flush();
        $this->addFlash('success', 'Catégorie supprimée.');

        return $this->redirectToRoute('app_categorie_index');
    }
}
