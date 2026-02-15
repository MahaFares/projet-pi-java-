<?php

namespace App\Controller\Activity;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activity')]
class ActivityVirtualTourController extends AbstractController
{
    #[Route('/{id}/virtual-tour', name: 'app_activity_virtual_tour', methods: ['GET'])]
    public function virtualTour(Activity $activity): Response
    {
        return $this->render('FrontOffice/activity/virtual_tour.html.twig', [
            'activity' => $activity,
        ]);
    }
}