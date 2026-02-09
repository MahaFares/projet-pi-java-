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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $activityCategory = new ActivityCategory();
        $form = $this->createForm(ActivityCategoryType::class, $activityCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $iconFile = $form->get('icon')->getData();
            if ($iconFile) {
                $originalFilename = pathinfo($iconFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $iconFile->guessExtension();

                // Create directory if it doesn't exist
                $filesystem = new Filesystem();
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/images/icons';
                if (!$filesystem->exists($uploadDir)) {
                    $filesystem->mkdir($uploadDir, 0755);
                }

                // Move the file
                $iconFile->move($uploadDir, $newFilename);
                
                // Store relative path in database
                $activityCategory->setIcon('images/icons/' . $newFilename);
            }

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
    public function edit(Request $request, ActivityCategory $activityCategory, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ActivityCategoryType::class, $activityCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $iconFile = $form->get('icon')->getData();
            if ($iconFile) {
                // Delete old icon if it exists
                $filesystem = new Filesystem();
                if ($activityCategory->getIcon()) {
                    $oldIconPath = $this->getParameter('kernel.project_dir') . '/public/' . $activityCategory->getIcon();
                    if ($filesystem->exists($oldIconPath)) {
                        $filesystem->remove($oldIconPath);
                    }
                }

                $originalFilename = pathinfo($iconFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $iconFile->guessExtension();

                // Create directory if it doesn't exist
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/images/icons';
                if (!$filesystem->exists($uploadDir)) {
                    $filesystem->mkdir($uploadDir, 0755);
                }

                // Move the file
                $iconFile->move($uploadDir, $newFilename);
                
                // Store relative path in database
                $activityCategory->setIcon('images/icons/' . $newFilename);
            }

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
