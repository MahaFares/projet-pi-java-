<?php

namespace App\Controller\Reservation;

use App\Entity\PaymentReservation;
use App\Entity\Enum\PaymentStatus;
use App\Repository\PaymentReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/payment-reservation')]
class PaymentReservationController extends AbstractController
{
    #[Route('/', name: 'admin_payment_reservation_index', methods: ['GET'])]
    public function index(PaymentReservationRepository $repository): Response
    {
        $payments = $repository->findBy([], ['createdAt' => 'DESC']);
        
        return $this->render('back_office/payment_reservation/index.html.twig', [
            'payments' => $payments,
        ]);
    }

    #[Route('/pending', name: 'admin_payment_reservation_pending', methods: ['GET'])]
    public function pending(PaymentReservationRepository $repository): Response
    {
        $payments = $repository->findPendingPayments();
        
        return $this->render('back_office/payment_reservation/index.html.twig', [
            'payments' => $payments,
            'filter' => 'pending',
        ]);
    }

    #[Route('/completed', name: 'admin_payment_reservation_completed', methods: ['GET'])]
    public function completed(PaymentReservationRepository $repository): Response
    {
        $payments = $repository->findCompletedPayments();
        
        return $this->render('back_office/payment_reservation/index.html.twig', [
            'payments' => $payments,
            'filter' => 'completed',
        ]);
    }

    #[Route('/{id}', name: 'admin_payment_reservation_show', methods: ['GET'])]
    public function show(PaymentReservation $payment): Response
    {
        return $this->render('back_office/payment_reservation/show.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/{id}/validate', name: 'admin_payment_reservation_validate', methods: ['POST'])]
    public function validate(
        PaymentReservation $payment,
        EntityManagerInterface $em
    ): Response {
        if ($payment->getPaymentStatus() === PaymentStatus::PENDING) {
            $payment->setPaymentStatus(PaymentStatus::COMPLETED);
            $payment->setPaidAt(new \DateTimeImmutable());
            
            // Also confirm the reservation
            $reservation = $payment->getReservation();
            if ($reservation) {
                $reservation->setStatus(\App\Entity\Enum\ReservationStatus::CONFIRMED);
            }
            
            $em->flush();
            
            $this->addFlash('success', 'Paiement validé avec succès !');
        } else {
            $this->addFlash('error', 'Ce paiement ne peut pas être validé.');
        }

        return $this->redirectToRoute('admin_payment_reservation_show', ['id' => $payment->getId()]);
    }

    #[Route('/{id}/fail', name: 'admin_payment_reservation_fail', methods: ['POST'])]
    public function fail(
        PaymentReservation $payment,
        EntityManagerInterface $em
    ): Response {
        if ($payment->getPaymentStatus() === PaymentStatus::PENDING) {
            $payment->setPaymentStatus(PaymentStatus::FAILED);
            $em->flush();
            
            $this->addFlash('success', 'Paiement marqué comme échoué.');
        } else {
            $this->addFlash('error', 'Ce paiement ne peut pas être marqué comme échoué.');
        }

        return $this->redirectToRoute('admin_payment_reservation_show', ['id' => $payment->getId()]);
    }

    #[Route('/{id}', name: 'admin_payment_reservation_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        PaymentReservation $payment,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->request->get('_token'))) {
            try {
                $em->remove($payment);
                $em->flush();
                
                $this->addFlash('success', 'Paiement supprimé avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer ce paiement.');
            }
        }

        return $this->redirectToRoute('admin_payment_reservation_index');
    }
}
