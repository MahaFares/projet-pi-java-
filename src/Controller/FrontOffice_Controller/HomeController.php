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

    #[Route('/activites', name: 'app_activites')]
    public function activites(ActivityRepository $activityRepository): Response
    {
        try {
            $activities = $activityRepository->findAll();
        } catch (\Exception $e) {
            // Table may not exist yet, return empty array
            $activities = [];
        }
        
        return $this->render('FrontOffice/activities/blog.html.twig', [
            'activities' => $activities,
        ]);
    }


    #[Route('/transport', name: 'app_transport')]
    public function transport(TransportRepository $transportRepository, Request $request): Response
    {
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
    }

    #[Route('/boutique', name: 'app_boutique')]
    public function boutique(): Response
    {
        return $this->render('FrontOffice/boutique/boutique.html.twig', [
            'produits' => [],
        ]);
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