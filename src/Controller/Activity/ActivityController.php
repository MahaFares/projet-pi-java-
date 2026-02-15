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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/activity')]
final class ActivityController extends AbstractController
{
    #[Route('', name: 'app_activity_index', methods: ['GET'])]
    public function index(Request $request, ActivityRepository $activityRepository): Response
    {
        $q = $request->query->get('q');
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');
        $available = $request->query->get('available');

        $minPrice = $minPrice !== null && $minPrice !== '' ? (float) $minPrice : null;
        $maxPrice = $maxPrice !== null && $maxPrice !== '' ? (float) $maxPrice : null;
        $available = ($available === '1' ? true : ($available === '0' ? false : null));

        $activities = $activityRepository->findByFilters($q, $minPrice, $maxPrice, $available);

        return $this->render('ActivityTemplate/activity/index.html.twig', [
            'activities' => $activities,
            'filters' => [
                'q' => $q,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'available' => $available,
            ],
        ]);
    }

    #[Route('/new', name: 'app_activity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Create directory if it doesn't exist
                $filesystem = new Filesystem();
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/images/activities';
                if (!$filesystem->exists($uploadDir)) {
                    $filesystem->mkdir($uploadDir, 0755);
                }

                // Move the file
                $imageFile->move($uploadDir, $newFilename);
                
                // Store relative path in database
                $activity->setImage('images/activities/' . $newFilename);
            }

            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ActivityTemplate/activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Activity $activity): Response
    {
        return $this->render('ActivityTemplate/activity/show.html.twig', [
            'activity' => $activity,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_activity_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Activity $activity, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Delete old image if it exists
                $filesystem = new Filesystem();
                if ($activity->getImage()) {
                    $oldImagePath = $this->getParameter('kernel.project_dir') . '/public/' . $activity->getImage();
                    if ($filesystem->exists($oldImagePath)) {
                        $filesystem->remove($oldImagePath);
                    }
                }

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Create directory if it doesn't exist
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/images/activities';
                if (!$filesystem->exists($uploadDir)) {
                    $filesystem->mkdir($uploadDir, 0755);
                }

                // Move the file
                $imageFile->move($uploadDir, $newFilename);
                
                // Store relative path in database
                $activity->setImage('images/activities/' . $newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ActivityTemplate/activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
    }

    // /** Blog listing: all activities (optional price filter). */
    // #[Route('/activities', name: 'activities_blog', methods: ['GET'])]
    // public function activitiesBlog(
    //     Request $request,
    //     ActivityRepository $activityRepo,
    //     ActivityCategoryRepository $categoryRepo
    // ): Response {
    //     return $this->renderActivitiesBlog($request, $activityRepo, $categoryRepo, null);
    // }

    // /** Blog listing: activities filtered by category. */
    // #[Route('/activities/category/{categoryId}', name: 'activities_by_category', requirements: ['categoryId' => '\d+'], methods: ['GET'])]
    // public function activitiesByCategory(
    //     Request $request,
    //     ActivityRepository $activityRepo,
    //     ActivityCategoryRepository $categoryRepo,
    //     int $categoryId
    // ): Response {
    //     return $this->renderActivitiesBlog($request, $activityRepo, $categoryRepo, $categoryId);
    // }

    // private function renderActivitiesBlog(
    //     Request $request,
    //     ActivityRepository $activityRepo,
    //     ActivityCategoryRepository $categoryRepo,
    //     ?int $categoryId
    // ): Response {
    //     $sidebarNames = ['Camping', 'Équitation', 'Kayak', 'Randonnée', 'Yoga'];
    //     $sidebarCategories = [];
    //     foreach ($sidebarNames as $name) {
    //         $cat = $categoryRepo->findOneBy(['name' => $name]);
    //         if ($cat !== null) {
    //             $sidebarCategories[] = $cat;
    //         }
    //     }

    //     $minPrice = $request->query->get('minPrice') !== null && $request->query->get('minPrice') !== ''
    //         ? (float) $request->query->get('minPrice') : null;
    //     $maxPrice = $request->query->get('maxPrice') !== null && $request->query->get('maxPrice') !== ''
    //         ? (float) $request->query->get('maxPrice') : null;

    //     $activities = $activityRepo->findAllForBlog($categoryId, $minPrice, $maxPrice);

    //     return $this->render('FrontOffice/activities/blog.html.twig', [
    //         'activities' => $activities,
    //         'sidebarCategories' => $sidebarCategories,
    //         'selectedCategory' => $categoryId,
    //         'filterMinPrice' => $minPrice,
    //         'filterMaxPrice' => $maxPrice,
    //     ]);
    // }
}
