<?php

namespace App\Controller\FrontOffice_Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'front_boutique')]
    public function index(ProduitRepository $repo): Response
    {
        try {
            $produits = $repo->findAll();
        } catch (\Exception $e) {
            $produits = [];
        }

        return $this->render('FrontOffice/boutique/categorie/index.html.twig', [
            'categories' => $produits
        ]);
    }
}

