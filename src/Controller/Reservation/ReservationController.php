<?php

namespace App\Controller\Reservation;

use App\Entity\Reservation;
use App\Entity\Enum\ReservationStatus;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'admin_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $repository): Response
    {
        $reservations = $repository->findBy([], ['createdAt' => 'DESC']);
        
        return $this->render('back_office/reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/{id}', name: 'admin_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('back_office/reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/confirm', name: 'admin_reservation_confirm', methods: ['POST'])]
    public function confirm(
        Reservation $reservation,
        EntityManagerInterface $em
    ): Response {
        if ($reservation->getStatus() === ReservationStatus::PENDING) {
            $reservation->setStatus(ReservationStatus::CONFIRMED);
            $em->flush();
            
            $this->addFlash('success', 'Réservation confirmée avec succès !');
        } else {
            $this->addFlash('error', 'Cette réservation ne peut pas être confirmée.');
        }

        return $this->redirectToRoute('admin_reservation_show', ['id' => $reservation->getId()]);
    }

    #[Route('/{id}/cancel', name: 'admin_reservation_cancel', methods: ['POST'])]
    public function cancel(
        Reservation $reservation,
        EntityManagerInterface $em
    ): Response {
        if ($reservation->getStatus() !== ReservationStatus::CANCELLED) {
            $reservation->setStatus(ReservationStatus::CANCELLED);
            $em->flush();
            
            $this->addFlash('success', 'Réservation annulée avec succès !');
        } else {
            $this->addFlash('error', 'Cette réservation est déjà annulée.');
        }

        return $this->redirectToRoute('admin_reservation_show', ['id' => $reservation->getId()]);
    }

    #[Route('/{id}', name: 'admin_reservation_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Reservation $reservation,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            try {
                $em->remove($reservation);
                $em->flush();
                
                $this->addFlash('success', 'Réservation supprimée avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer cette réservation.');
            }
        }

        return $this->redirectToRoute('admin_reservation_index');
    }
}
