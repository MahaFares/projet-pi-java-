<?php

namespace App\Controller\FrontOffice_Controller;

use App\Repository\TransportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ActivityRepository;
use App\Repository\ActivityCategoryRepository;
use App\Repository\ActivityScheduleRepository;
use App\Repository\GuideRepository;




class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('FrontOffice/home/index.html.twig');
    }

    #[Route('/a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('FrontOffice/about_us/about.html.twig');
    }

    #[Route('/hebergement', name: 'app_hebergement')]
    public function hebergement(): Response
    {
        return $this->render('FrontOffice/hebergement/accomodation.html.twig');
    }

    #[Route('/activities', name: 'app_activites')]
    public function activites(
<<<<<<< HEAD
        ActivityRepository $activityRepository, 
        ActivityCategoryRepository $categoryRepository, 
        ActivityScheduleRepository $scheduleRepository, 
        GuideRepository $guideRepository
    ): Response {
        return $this->render('FrontOffice/activities/blog.html.twig', [
            'activities' => $activityRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'schedules'  => $scheduleRepository->findAll(), 
            'guides'     => $guideRepository->findAll(),
        ]);
    }

=======
        Request $request,
        ActivityRepository $activityRepository, 
        ActivityCategoryRepository $categoryRepository, 
        ActivityScheduleRepository $scheduleRepository, 
        GuideRepository $guideRepository): Response {
        
        $minPrice = $request->query->get('minPrice') !== null && $request->query->get('minPrice') !== ''
            ? (float) $request->query->get('minPrice') : null;
        $maxPrice = $request->query->get('maxPrice') !== null && $request->query->get('maxPrice') !== ''
            ? (float) $request->query->get('maxPrice') : null;

        $activities = $activityRepository->findByPriceRange($minPrice, $maxPrice);
        $sidebarCategories = $categoryRepository->findAll();

        return $this->render('FrontOffice/activities/blog.html.twig', [
            'activities' => $activities,
            'sidebarCategories' => $sidebarCategories,
            'selectedCategory' => null,
            'filterMinPrice' => $minPrice,
            'filterMaxPrice' => $maxPrice,
        ]);
    }

    #[Route('/activities/category/{categoryId}', name: 'activities_by_category', requirements: ['categoryId' => '\d+'])]
    public function activitiesByCategory(
        Request $request,
        int $categoryId,
        ActivityRepository $activityRepository, 
        ActivityCategoryRepository $categoryRepository): Response {
        
        $minPrice = $request->query->get('minPrice') !== null && $request->query->get('minPrice') !== ''
            ? (float) $request->query->get('minPrice') : null;
        $maxPrice = $request->query->get('maxPrice') !== null && $request->query->get('maxPrice') !== ''
            ? (float) $request->query->get('maxPrice') : null;

        $activities = $activityRepository->findByCategoryAndPrice($categoryId, $minPrice, $maxPrice);
        $sidebarCategories = $categoryRepository->findAll();

        return $this->render('FrontOffice/activities/blog.html.twig', [
            'activities' => $activities,
            'sidebarCategories' => $sidebarCategories,
            'selectedCategory' => $categoryId,
            'filterMinPrice' => $minPrice,
            'filterMaxPrice' => $maxPrice,
        ]);
    }

>>>>>>> f5ab5f2b8143340c9833c9379b76af33954bf087

    #[Route('/transport', name: 'app_transport')]
    public function transport(TransportRepository $transportRepository, \Symfony\Component\HttpFoundation\Request $request): Response
    {
<<<<<<< HEAD
        $type = $request->query->get('type');
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');
        $minCapacity = $request->query->get('minCapacity');
        $available = $request->query->get('available');

        $minPrice = $minPrice !== null && $minPrice !== '' ? (float) $minPrice : null;
        $maxPrice = $maxPrice !== null && $maxPrice !== '' ? (float) $maxPrice : null;
        $minCapacity = $minCapacity !== null && $minCapacity !== '' ? (int) $minCapacity : null;
        $available = ($available === '1' ? true : ($available === '0' ? false : null));

        $transports = $transportRepository->findByFilters($type, $minPrice, $maxPrice, $minCapacity, $available);

        return $this->render('FrontOffice/transport/accomodation.html.twig', [
            'transports' => $transports,
            'filters' => [
                'type' => $type,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'minCapacity' => $minCapacity,
                'available' => $available,
            ],
        ]);
=======
        return $this->render('FrontOffice/transport/transport.html.twig');
>>>>>>> f5ab5f2b8143340c9833c9379b76af33954bf087
    }

    #[Route('/boutique', name: 'app_boutique')]
    public function boutique(): Response
    {
        return $this->render('FrontOffice/boutique/boutique.html.twig');
    }

    #[Route('/se-connecter', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('FrontOffice/se_connecter/login.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('FrontOffice/contact/contact.html.twig');
    }
}