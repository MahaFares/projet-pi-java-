<?php

namespace App\Controller\FrontOffice_Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        // ⚠️ CORRECTION : c'était 'transport/accomodation' au lieu de 'hebrgement/accomodation'
        return $this->render('FrontOffice/hebrgement/accomodation.html.twig');
    }

    #[Route('/activites', name: 'app_activites')]
    public function activites(): Response
    {
        return $this->render('FrontOffice/activities/blog.html.twig');
    }

    #[Route('/transport', name: 'app_transport')]
    public function transport(): Response
    {
        // ⚠️ CORRECTION : transport devrait pointer vers transport.html.twig pas accomodation
        return $this->render('FrontOffice/transport/transport.html.twig');
    }

    #[Route('/boutique', name: 'app_boutique')]
    public function boutique(): Response
    {
        return $this->render('FrontOffice/boutique/blog.html.twig');
    }

    #[Route('/se-connecter', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('FrontOffice/se_connecter/login.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('FrontOffice/se_connecter/contact.html.twig');
    }
}