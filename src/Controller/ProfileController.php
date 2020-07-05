<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\UserUpdateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/profile", name="profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function index(Request $request, UserInterface $user): Response
    {
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a bien été mis à jour.');

            return $this->redirectToRoute('profile_index');
        }


        return $this->render('security/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/password", name="password")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function changePassword(
        Request $request,
        UserInterface $user,
        UserPasswordEncoderInterface $userPasswordEncoder
    ): Response {
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        return $this->render('security/profilepassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
