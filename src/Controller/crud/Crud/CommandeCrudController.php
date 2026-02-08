<?php

namespace App\Controller\Crud;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
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
#[Route('/admin/commande')]
class CommandeCrudController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CommandeRepository $repository,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', name: 'app_commande_index', methods: ['GET'])]
    public function index(): Response
    {
        $items = $this->repository->findBy([], ['dateCommande' => 'DESC']);

        return $this->render('crud/commande/index.html.twig', [
            'commandes' => $items,
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->validator->validate($commande);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->render('crud/commande/new.html.twig', [
                    'commande' => $commande,
                    'form' => $form,
                ]);
            }
            $this->em->persist($commande);
            $this->em->flush();
            $this->addFlash('success', 'Commande créée.');
            return $this->redirectToRoute('app_commande_index');
        }

        return $this->render('crud/commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): Response
    {
        $commande = $this->repository->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        return $this->render('crud/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $commande = $this->repository->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->validator->validate($commande);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->render('crud/commande/edit.html.twig', [
                    'commande' => $commande,
                    'form' => $form,
                ]);
            }
            $this->em->flush();
            $this->addFlash('success', 'Commande mise à jour.');
            return $this->redirectToRoute('app_commande_index');
        }

        return $this->render('crud/commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_commande_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        $commande = $this->repository->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_commande_' . $id, $token)) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_commande_index');
        }

        $this->em->remove($commande);
        $this->em->flush();
        $this->addFlash('success', 'Commande supprimée.');

        return $this->redirectToRoute('app_commande_index');
    }
}
