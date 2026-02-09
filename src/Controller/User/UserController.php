<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    // #[Route('/user', name: 'app_user')]
    // public function index(): Response
    // {
    //     return $this->render('user/index.html.twig', [
    //         'controller_name' => 'UserController',
    //     ]);
    // }

    // #[Route('/sign-in', name: 'app_sign_in', methods: ['GET', 'POST'])]
    // public function signIn(AuthenticationUtils $authenticationUtils): Response
    // {
    //     if ($this->getUser()) {
    //         return $this->redirectToRoute('app_home');
    //     }

    //     $error = $authenticationUtils->getLastAuthenticationError();
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     return $this->render('user/sign_in.html.twig', [
    //         'last_username' => $lastUsername,
    //         'error' => $error,
    //     ]);
    // }

    // #[Route('/sign-up', name: 'app_sign_up', methods: ['GET', 'POST'])]
    // public function signUp(
    //     Request $request,
    //     UserPasswordHasherInterface $passwordHasher,
    //     EntityManagerInterface $entityManager,
    //     UserRepository $userRepository
    // ): Response {
    //     if ($this->getUser()) {
    //         return $this->redirectToRoute('app_home');
    //     }

    //     $error = null;
    //     $lastEmail = '';

    //     if ($request->isMethod('POST')) {
    //         $email = trim((string) $request->request->get('email', ''));
    //         $password = $request->request->get('password', '');
    //         $passwordConfirm = $request->request->get('password_confirm', '');

    //         if (!$this->isCsrfTokenValid('sign_up', (string) $request->request->get('_csrf_token'))) {
    //             $error = 'Token de sécurité invalide. Veuillez réessayer.';
    //         } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //             $error = 'Adresse email invalide.';
    //             $lastEmail = $email;
    //         } elseif (strlen($password) < 6) {
    //             $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    //             $lastEmail = $email;
    //         } elseif ($password !== $passwordConfirm) {
    //             $error = 'Les mots de passe ne correspondent pas.';
    //             $lastEmail = $email;
    //         } elseif ($userRepository->findOneBy(['email' => $email])) {
    //             $error = 'Un compte existe déjà avec cette adresse email.';
    //             $lastEmail = $email;
    //         } else {
    //             $user = new User();
    //             $user->setEmail($email);
    //             $user->setPassword($passwordHasher->hashPassword($user, $password));

    //             $entityManager->persist($user);
    //             $entityManager->flush();

    //             return $this->redirectToRoute('app_sign_in', ['success' => 1]);
    //         }
    //     }

    //     return $this->render('user/sign_up.html.twig', [
    //         'error' => $error,
    //         'last_email' => $lastEmail,
    //         'success' => false,
    //     ]);
    // }

    // #[Route('/sign-out', name: 'app_sign_out', methods: ['GET'])]
    // public function signOut(): never
    // {
    //     throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    // }

    #[Route('/my-account', name: 'app_my_account', methods: ['GET'])]
    public function myAccount(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/my_account.html.twig');
    }
}
