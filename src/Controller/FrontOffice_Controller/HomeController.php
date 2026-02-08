<?php

namespace App\Controller\FrontOffice_Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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


    #[Route('/transport', name: 'app_transport')]
    public function transport(): Response
    {
        return $this->render('FrontOffice/transport/transport.html.twig');
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