<?php

namespace App\Controller\FrontOffice_Controller;

use App\Repository\TransportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'app_home')]
    
    public function index(): Response
    {
        return $this->render(view: 'FrontOffice/home/index.html.twig');
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('FrontOffice/about_us/about.html.twig');
    }

    #[Route('/hebergement', name: 'app_hebergement')]
    public function hebergement(): Response
    {
        return $this->render('FrontOffice/transport/accomodation.html.twig');
    }

    #[Route('/activites', name: 'app_activites')]
    public function activites(): Response
    {
        return $this->render('FrontOffice/activities/blog.html.twig');
    }

    #[Route('/transport', name: 'app_transport')]
    public function transport(TransportRepository $transportRepository, \Symfony\Component\HttpFoundation\Request $request): Response
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
        return $this->render('FrontOffice/boutique/blog.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('FrontOffice/contact/contact.html.twig');
    }
}