<?php

namespace App\Controller\FrontOffice_Controller;

use App\Entity\Activity;
use App\Entity\Commande;
use App\Entity\Enum\PaymentMethod;
use App\Entity\Enum\PaymentStatus;
use App\Entity\Enum\ReservationType;
use App\Entity\Hebergement;
use App\Entity\LigneDeCommande;
use App\Entity\Paiement;
use App\Entity\PaymentReservation;
use App\Entity\Produit;
use App\Entity\Reservation;
use App\Entity\Transport;
use App\Repository\ActivityRepository;
use App\Repository\HebergementRepository;
use App\Repository\ProduitRepository;
use App\Repository\TransportRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('', name: 'app_panier_index', methods: ['GET'])]
    public function index(
        CartService $cartService,
        HebergementRepository $hebergementRepo,
        ActivityRepository $activityRepo,
        TransportRepository $transportRepo,
        ProduitRepository $produitRepo
    ): Response {
        $cart = $cartService->getCart();
        $items = [];
        $types = [
            'hebergements' => ['type' => 'hebergement', 'repo' => $hebergementRepo, 'idField' => 'id'],
            'activities' => ['type' => 'activity', 'repo' => $activityRepo, 'idField' => 'id'],
            'transports' => ['type' => 'transport', 'repo' => $transportRepo, 'idField' => 'id'],
            'produits' => ['type' => 'produit', 'repo' => $produitRepo, 'idField' => 'idProduit'],
        ];
        foreach ($types as $key => $config) {
            foreach ($cart[$key] ?? [] as $cartKey => $data) {
                $id = $data['id'] ?? 0;
                $entity = $config['repo']->find($id);
                $items[] = [
                    'cartKey' => $cartKey,
                    'type' => $config['type'],
                    'entity' => $entity,
                    'data' => $data,
                ];
            }
        }
        return $this->render('FrontOffice/panier/index.html.twig', [
            'cartItems' => $items,
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/add/hebergement/{id}', name: 'app_panier_add_hebergement', methods: ['POST'])]
    public function addHebergement(int $id, Request $request, CartService $cartService, HebergementRepository $repo): JsonResponse
    {
        $hebergement = $repo->find($id);
        if (!$hebergement) {
            return new JsonResponse(['success' => false, 'message' => 'Hébergement introuvable'], 404);
        }
        $nights = max(1, (int) ($request->request->get('nights', 1)));
        $price = 0;
        foreach ($hebergement->getChambres() as $chambre) {
            if ($chambre->getPrixParNuit() && (!$price || $chambre->getPrixParNuit() < $price)) {
                $price = $chambre->getPrixParNuit();
            }
        }
        if ($price <= 0) {
            $price = 50; // fallback
        }
        $cartService->addHebergement($id, $price, $hebergement->getNom(), $nights);
        return new JsonResponse(['success' => true, 'count' => $cartService->getCount()]);
    }

    #[Route('/add/activity/{id}', name: 'app_panier_add_activity', methods: ['POST'])]
    public function addActivity(int $id, CartService $cartService, ActivityRepository $repo): JsonResponse
    {
        $activity = $repo->find($id);
        if (!$activity) {
            return new JsonResponse(['success' => false, 'message' => 'Activité introuvable'], 404);
        }
        $price = (float) $activity->getPrice();
        $cartService->addActivity($id, $price, $activity->getTitle());
        return new JsonResponse(['success' => true, 'count' => $cartService->getCount()]);
    }

    #[Route('/add/transport/{id}', name: 'app_panier_add_transport', methods: ['POST'])]
    public function addTransport(int $id, CartService $cartService, TransportRepository $repo): JsonResponse
    {
        $transport = $repo->find($id);
        if (!$transport) {
            return new JsonResponse(['success' => false, 'message' => 'Transport introuvable'], 404);
        }
        $price = (float) $transport->getPrixparpersonne();
        $cartService->addTransport($id, $price, $transport->getType());
        return new JsonResponse(['success' => true, 'count' => $cartService->getCount()]);
    }

    #[Route('/add/produit/{id}', name: 'app_panier_add_produit', methods: ['POST'])]
    public function addProduit(int $id, Request $request, CartService $cartService, ProduitRepository $repo): JsonResponse
    {
        $produit = $repo->find($id);
        if (!$produit || $produit->getStock() <= 0) {
            return new JsonResponse(['success' => false, 'message' => 'Produit indisponible'], 404);
        }
        $quantity = max(1, min($produit->getStock(), (int) ($request->request->get('quantity', 1))));
        $price = (float) $produit->getPrix();
        $cartService->addProduit($id, $price, $produit->getNom(), $quantity);
        return new JsonResponse(['success' => true, 'count' => $cartService->getCount()]);
    }

    #[Route('/remove/{type}/{key}', name: 'app_panier_remove', methods: ['POST'])]
    public function remove(string $type, string $key, CartService $cartService): JsonResponse
    {
        $typeMap = ['hebergement' => 'hebergements', 'activity' => 'activities', 'transport' => 'transports', 'produit' => 'produits'];
        $cartType = $typeMap[$type] ?? $type . 's';
        $cartService->remove($cartType, $key);
        return new JsonResponse(['success' => true, 'count' => $cartService->getCount(), 'total' => $cartService->getTotal()]);
    }

    #[Route('/update-quantity/{key}', name: 'app_panier_update_quantity', methods: ['POST'])]
    public function updateQuantity(string $key, Request $request, CartService $cartService, ProduitRepository $produitRepo): JsonResponse
    {
        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $cart = $cartService->getCart();
        $data = $cart['produits'][$key] ?? null;
        if (!$data) {
            return new JsonResponse(['success' => false], 404);
        }
        $produit = $produitRepo->find($data['id']);
        if (!$produit || $quantity > $produit->getStock()) {
            return new JsonResponse(['success' => false, 'message' => 'Stock insuffisant']);
        }
        $cartService->updateProduitQuantity($key, $quantity);
        return new JsonResponse(['success' => true, 'total' => $cartService->getTotal()]);
    }

    #[Route('/checkout', name: 'app_panier_checkout', methods: ['GET'])]
    public function checkout(CartService $cartService): Response
    {
        if ($cartService->isEmpty()) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_index');
        }
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Veuillez vous connecter pour procéder au paiement.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('FrontOffice/panier/checkout.html.twig', [
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/payment', name: 'app_panier_payment', methods: ['GET', 'POST'])]
    public function payment(Request $request, CartService $cartService, EntityManagerInterface $em): Response
    {
        if ($cartService->isEmpty()) {
            return $this->redirectToRoute('app_panier_index');
        }
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('panier_payment', $request->request->get('_token'))) {
                $this->addFlash('error', 'Token de sécurité invalide.');
                return $this->redirectToRoute('app_panier_payment');
            }
            $method = $request->request->get('method', 'CARD');
            $methodEnum = PaymentMethod::tryFrom($method) ?? PaymentMethod::CARD;

            // Si paiement par carte, création d'une session Stripe Checkout
            if ($methodEnum === PaymentMethod::CARD) {
                try {
                    $total = (float) $cartService->getTotal();
                    if ($total <= 0) {
                        $this->addFlash('error', 'Montant invalide pour le paiement.');
                        return $this->redirectToRoute('app_panier_payment');
                    }

                    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY'] ?? '');

                    $session = StripeSession::create([
                        'mode' => 'payment',
                        'payment_method_types' => ['card'],
                        'line_items' => [[
                            'price_data' => [
                                'currency' => 'usd',
                                'product_data' => [
                                    'name' => 'Commande EcoTrip',
                                ],
                                'unit_amount' => (int) round($total * 100),
                            ],
                            'quantity' => 1,
                        ]],
                        'success_url' => $this->generateUrl('app_panier_stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        'cancel_url' => $this->generateUrl('app_panier_stripe_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ]);

                    return $this->redirect($session->url);
                } catch (\Throwable $e) {
                    $this->addFlash('error', 'Erreur lors de la création du paiement Stripe : ' . $e->getMessage());
                    return $this->redirectToRoute('app_panier_payment');
                }
            }

            // Paiement classique (espèces, etc.) traité directement côté application
            $this->finalizePayment($methodEnum, $cartService, $em, $user);
            $this->addFlash('success', 'Paiement effectué avec succès ! Merci pour votre commande.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('FrontOffice/panier/payment.html.twig', [
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/stripe/success', name: 'app_panier_stripe_success', methods: ['GET'])]
    public function stripeSuccess(CartService $cartService, EntityManagerInterface $em): Response
    {
        if ($cartService->isEmpty()) {
            $this->addFlash('warning', 'Votre panier est vide ou le paiement a déjà été traité.');
            return $this->redirectToRoute('app_panier_index');
        }

        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Veuillez vous connecter pour finaliser le paiement.');
            return $this->redirectToRoute('app_login');
        }

        // On garde le total avant de vider le panier
        $total = (float) $cartService->getTotal();

        // Finaliser la commande et vider le panier
        $commande = $this->finalizePayment(PaymentMethod::CARD, $cartService, $em, $user);

        return $this->render('FrontOffice/panier/success.html.twig', [
            'total' => $total,
            'commande' => $commande,
        ]);
    }

    #[Route('/stripe/cancel', name: 'app_panier_stripe_cancel', methods: ['GET'])]
    public function stripeCancel(): Response
    {
        $this->addFlash('warning', 'Le paiement Stripe a été annulé. Vous pouvez réessayer.');
        return $this->redirectToRoute('app_panier_payment');
    }

    #[Route('/facture/{id}/pdf', name: 'app_panier_facture_pdf', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function facturePdf(Commande $commande): Response
    {
        $user = $this->getUser();
        if (!$user || $commande->getIdUser() !== $user->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette facture.');
        }

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        $html = $this->renderView('FrontOffice/panier/facture_pdf.html.twig', [
            'commande' => $commande,
            'generatedAt' => new \DateTime(),
            'user' => $user,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = sprintf('facture-%s.pdf', $commande->getIdCommande());

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    /**
     * Finalise la commande et enregistre les paiements après confirmation (Stripe succès ou paiement classique).
     */
    private function finalizePayment(PaymentMethod $methodEnum, CartService $cartService, EntityManagerInterface $em, $user): ?Commande
    {
        $cart = $cartService->getCart();

        foreach ($cart['hebergements'] ?? [] as $key => $data) {
            $hebergement = $em->getRepository(Hebergement::class)->find($data['id']);
            if ($hebergement) {
                $price = ($data['price'] ?? 0) * ($data['nights'] ?? 1);
                $reservation = new Reservation();
                $reservation->setUser($user);
                $reservation->setReservationType(ReservationType::HEBERGEMENT);
                $reservation->setReservationId($data['id']);
                $reservation->setTotalPrice($price);
                $em->persist($reservation);
                $payment = new PaymentReservation();
                $payment->setReservation($reservation);
                $payment->setAmount($price);
                $payment->setPaymentMethod($methodEnum);
                $payment->setPaymentStatus(PaymentStatus::COMPLETED);
                $payment->setPaidAt(new \DateTimeImmutable());
                $em->persist($payment);
            }
        }

        foreach ($cart['activities'] ?? [] as $key => $data) {
            $price = ($data['price'] ?? 0) * ($data['quantity'] ?? 1);
            $reservation = new Reservation();
            $reservation->setUser($user);
            $reservation->setReservationType(ReservationType::ACTIVITY);
            $reservation->setReservationId($data['id']);
            $reservation->setTotalPrice($price);
            $em->persist($reservation);
            $payment = new PaymentReservation();
            $payment->setReservation($reservation);
            $payment->setAmount($price);
            $payment->setPaymentMethod($methodEnum);
            $payment->setPaymentStatus(PaymentStatus::COMPLETED);
            $payment->setPaidAt(new \DateTimeImmutable());
            $em->persist($payment);
        }

        foreach ($cart['transports'] ?? [] as $key => $data) {
            $price = ($data['price'] ?? 0) * ($data['quantity'] ?? 1);
            $reservation = new Reservation();
            $reservation->setUser($user);
            $reservation->setReservationType(ReservationType::TRANSPORT);
            $reservation->setReservationId($data['id']);
            $reservation->setTotalPrice($price);
            $em->persist($reservation);
            $payment = new PaymentReservation();
            $payment->setReservation($reservation);
            $payment->setAmount($price);
            $payment->setPaymentMethod($methodEnum);
            $payment->setPaymentStatus(PaymentStatus::COMPLETED);
            $em->persist($payment);
        }

        $produitsTotal = 0;
        $commande = null;
        foreach ($cart['produits'] ?? [] as $key => $data) {
            $produit = $em->getRepository(Produit::class)->find($data['id']);
            if ($produit && ($data['quantity'] ?? 0) > 0 && $produit->getStock() >= $data['quantity']) {
                $subtotal = ($data['price'] ?? 0) * $data['quantity'];
                $produitsTotal += $subtotal;
                if (!$commande) {
                    $commande = new Commande();
                    $commande->setIdUser($user->getId());
                    $commande->setProduit($produit);
                    $commande->setDateCommande(new \DateTime());
                    $commande->setQuantite(0);
                    $commande->setPrixUnitaire('0');
                    $commande->setTotal('0');
                    $em->persist($commande);
                }
                $ligne = new LigneDeCommande();
                $ligne->setIdProduct($produit);
                $ligne->setIdCommande($commande);
                $ligne->setQuantite($data['quantity']);
                $ligne->setUnitPrice((int) round($data['price']));
                $ligne->setSubtotal((int) round($subtotal));
                $em->persist($ligne);
                $commande->addLigneDeCommande($ligne);
                $produit->setStock($produit->getStock() - $data['quantity']);
            }
        }

        if ($commande) {
            $commande->setQuantite(1);
            $commande->setPrixUnitaire((string) $produitsTotal);
            $commande->setTotal((string) $produitsTotal);
            $paiement = new Paiement();
            $paiement->setCommande($commande);
            $paiement->setMontant((string) $produitsTotal);
            $paiement->setMethodePaiement($methodEnum === PaymentMethod::CARD ? 'Par carte' : ($methodEnum === PaymentMethod::CASH ? 'Par espèces' : 'Par carte'));
            $paiement->setDatePaiement(new \DateTime());
            $em->persist($paiement);
        }

        $em->flush();
        $cartService->clear();

        return $commande;
    }
}
