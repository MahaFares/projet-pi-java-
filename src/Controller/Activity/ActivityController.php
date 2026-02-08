<?php

namespace App\Controller\Activity;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use App\Repository\ActivityCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activity')]
final class ActivityController extends AbstractController
{
    #[Route(name: 'app_activity_index', methods: ['GET'])]
    public function index(ActivityRepository $activityRepository): Response
    {
        return $this->render('activity/index.html.twig', [
            'activities' => $activityRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_activity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_show', methods: ['GET'])]
    public function show(Activity $activity): Response
    {
        return $this->render('activity/show.html.twig', [
            'activity' => $activity,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_activity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_delete', methods: ['POST'])]
    public function delete(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
    }

    /** Blog listing: all activities (optional price filter). */
    #[Route('/activities', name: 'activities_blog', methods: ['GET'])]
    public function activitiesBlog(
        Request $request,
        ActivityRepository $activityRepo,
        ActivityCategoryRepository $categoryRepo
    ): Response {
        return $this->renderActivitiesBlog($request, $activityRepo, $categoryRepo, null);
    }

    /** Blog listing: activities filtered by category. */
    #[Route('/activities/category/{categoryId}', name: 'activities_by_category', requirements: ['categoryId' => '\d+'], methods: ['GET'])]
    public function activitiesByCategory(
        Request $request,
        ActivityRepository $activityRepo,
        ActivityCategoryRepository $categoryRepo,
        int $categoryId
    ): Response {
        return $this->renderActivitiesBlog($request, $activityRepo, $categoryRepo, $categoryId);
    }

    private function renderActivitiesBlog(
        Request $request,
        ActivityRepository $activityRepo,
        ActivityCategoryRepository $categoryRepo,
        ?int $categoryId
    ): Response {
        $sidebarNames = ['Camping', 'Équitation', 'Kayak', 'Randonnée', 'Yoga'];
        $sidebarCategories = [];
        foreach ($sidebarNames as $name) {
            $cat = $categoryRepo->findOneBy(['name' => $name]);
            if ($cat !== null) {
                $sidebarCategories[] = $cat;
            }
        }

        $minPrice = $request->query->get('minPrice') !== null && $request->query->get('minPrice') !== ''
            ? (float) $request->query->get('minPrice') : null;
        $maxPrice = $request->query->get('maxPrice') !== null && $request->query->get('maxPrice') !== ''
            ? (float) $request->query->get('maxPrice') : null;

        $activities = $activityRepo->findAllForBlog($categoryId, $minPrice, $maxPrice);

        return $this->render('FrontOffice/activities/blog.html.twig', [
            'activities' => $activities,
            'sidebarCategories' => $sidebarCategories,
            'selectedCategory' => $categoryId,
            'filterMinPrice' => $minPrice,
            'filterMaxPrice' => $maxPrice,
        ]);
    }




}
