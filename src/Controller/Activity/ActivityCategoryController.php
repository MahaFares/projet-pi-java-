<?php

namespace App\Controller\Activity;

use App\Entity\ActivityCategory;
use App\Form\ActivityCategoryType;
use App\Repository\ActivityCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activity/category')]
final class ActivityCategoryController extends AbstractController
{
    #[Route(name: 'app_activity_category_index', methods: ['GET'])]
    public function index(ActivityCategoryRepository $activityCategoryRepository): Response
    {
        return $this->render('ActivityTemplate/activity_category/index.html.twig', [
            'activity_categories' => $activityCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_activity_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activityCategory = new ActivityCategory();
        $form = $this->createForm(ActivityCategoryType::class, $activityCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activityCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ActivityTemplate/activity_category/new.html.twig', [
            'activity_category' => $activityCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_category_show', methods: ['GET'])]
    public function show(ActivityCategory $activityCategory): Response
    {
        return $this->render('ActivityTemplate/activity_category/show.html.twig', [
            'activity_category' => $activityCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_activity_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ActivityCategory $activityCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityCategoryType::class, $activityCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ActivityTemplate/activity_category/edit.html.twig', [
            'activity_category' => $activityCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_category_delete', methods: ['POST'])]
    public function delete(Request $request, ActivityCategory $activityCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activityCategory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($activityCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activity_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
