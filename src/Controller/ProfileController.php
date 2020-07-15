<?php

namespace App\Controller;

use App\Form\AvatarUpdateType;
use App\Form\ChangePasswordType;
use App\Form\UserUpdateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/profil", name="profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserUpdateType::class, $user);
        $formUpdateAvatar = $this->createForm(AvatarUpdateType::class, $user);

        $form->handleRequest($request);
        $formUpdateAvatar->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a bien été mis à jour.');

            return $this->redirectToRoute('profile_index');
        }

        if ($formUpdateAvatar->isSubmitted() && $formUpdateAvatar->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre photo de profil a bien été mise à jour.');

            return $this->redirectToRoute('profile_index');
        }


        return $this->render('security/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'formUpdateAvatar' => $formUpdateAvatar->createView()
        ]);
    }

    /**
     * @Route("/mot-de-passe", name="password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function changePassword(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre nouveau mot de passe a été pris en compte.');

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('security/profilepassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
