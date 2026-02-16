<?php

namespace App\Controller\BackOffice_Controller;

use App\Repository\ChauffeurRepository;
use App\Repository\TransportCategoryRepository;
use App\Repository\TransportRepository;
use App\Repository\TrajetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ActivityRepository;
use App\Repository\ActivityCategoryRepository;
use App\Repository\ActivityScheduleRepository;
use App\Repository\GuideRepository;
use App\Repository\HebergementRepository;

class DashboardController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        ActivityRepository $activityRepo,
        ActivityCategoryRepository $categoryRepo,
        ActivityScheduleRepository $scheduleRepo,
        GuideRepository $guideRepo,
        TransportRepository $transportRepo,
        TransportCategoryRepository $transportCategoryRepo,
        ChauffeurRepository $chauffeurRepo,
        TrajetRepository $trajetRepo
    ): Response {
        // Activity Statistics
        $totalActivities = $activityRepo->count([]);
        $activeActivities = $activityRepo->count(['isActive' => true]);
        $totalCategories = $categoryRepo->count([]);
        $totalGuides = $guideRepo->count([]);
        
        // Upcoming schedules
        $upcomingSchedules = $scheduleRepo->findUpcomingSchedules(5);
        
        // Activities by category
        $activitiesByCategory = $categoryRepo->getActivitiesCountByCategory();
        
        // Recent activities
        $recentActivities = $activityRepo->findBy([], ['id' => 'DESC'], 5);
        
        // Top rated activities
        $topRatedActivities = $activityRepo->findTopRated(5);

        return $this->render('BackOffice/dashboard.html.twig', [
            'totalActivities' => $totalActivities,
            'activeActivities' => $activeActivities,
            'totalCategories' => $totalCategories,
            'totalGuides' => $totalGuides,
            'upcomingSchedules' => $upcomingSchedules,
            'activitiesByCategory' => $activitiesByCategory,
            'recentActivities' => $recentActivities,
            'topRatedActivities' => $topRatedActivities,
            'totalTransports' => $transportRepo->count([]),
            'totalTransportCategories' => $transportCategoryRepo->count([]),
            'totalChauffeurs' => $chauffeurRepo->count([]),
            'totalTrajets' => $trajetRepo->count([]),
        ]);
    }
}