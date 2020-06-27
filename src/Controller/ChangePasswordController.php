<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/changePassword")
 */
class ChangePasswordController extends AbstractController
{
    /**
     * @Route("/{id}/{token}", name="changePassword_index")
     * @param User $user
     * @param mixed $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function index(User $user, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        /*
            We prohibit access to the page if the token is null
            or if it has been created for more than 10 minutes
        */
        if (is_null($user->getToken()) ||
            $token !== $user->getToken() ||
            !$this->isRequestInTime($user->getPasswordRequestedAt())
        ) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // reset the token to null so that it is no longer reusable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été pris en compte');

            return $this->redirectToRoute('project_index');
        }

        return $this->render('security/changepassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Check that the token was created less than 10 minutes ago
     *
     * @param mixed $passwordRequestedAt
     * @return bool
     */
    private function isRequestInTime($passwordRequestedAt = null)
    {
        if ($passwordRequestedAt == null) {
            return false;
        }

        $now = new DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();
        $daySeconds = 60 * 10;

        return $interval > $daySeconds ? false : true;
    }
}
