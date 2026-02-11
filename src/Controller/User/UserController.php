<?php

namespace App\Controller\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[isGranted('ROLE_USER','ROLE_ADMIN')]
final class UserController extends AbstractController
{
    #[Route('/my-account', name: 'app_my_account', methods: ['GET'])]
    public function myAccount(): Response
    {
        return $this->render('user/my_account.html.twig');
    }

#[Route('/update-account', name: 'app_update_account', methods: ['GET','POST'])]
public function updateAccount(Request $request, EntityManagerInterface $em): Response
{
    /** @var User $user */
    $user = $this->getUser();

    if (!$user) {
        throw $this->createAccessDeniedException();
    }

    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        $this->addFlash('success', 'Vos informations ont été mises à jour.');

        return $this->redirectToRoute('app_my_account');
    }

    return $this->render('user/update_account.html.twig', [
        'form' => $form->createView(),
    ]);
}



#[Route('/change-password', name: 'app_change_password', methods: ['GET', 'POST'])]
public function changePassword(
    Request $request,
    UserPasswordHasherInterface $passwordHasher,
    EntityManagerInterface $em
): Response {
    /** @var User $user */
    $user = $this->getUser();

    if (!$user) {
        throw $this->createAccessDeniedException();
    }

    $form = $this->createForm(ChangePasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $currentPassword = $form->get('currentPassword')->getData();

        // Verify current password
        if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
            $form->get('currentPassword')->addError(
                new \Symfony\Component\Form\FormError('Mot de passe actuel incorrect.')
            );
        } else {
            $newPassword = $form->get('newPassword')->getData();

            $user->setPassword(
                $passwordHasher->hashPassword($user, $newPassword)
            );

            $em->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès.');

            return $this->redirectToRoute('app_my_account');
        }
    }

    return $this->render('user/change_password.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
