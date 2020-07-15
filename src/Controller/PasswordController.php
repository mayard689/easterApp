<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\RequestResetPassword;
use App\Form\ResetPasswordType;
use App\Service\MailManager;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * @Route("/mot-de-passe")
 */
class PasswordController extends AbstractController
{
    /**
     * @Route("/forgot", name="password_forgot_request")
     * @param Request $request
     * @param MailManager $mailManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     * @throws Exception
     */
    public function forgotPasswordRequest(
        Request $request,
        MailManager $mailManager,
        TokenGeneratorInterface $tokenGenerator
    ): Response {
        $form = $this->createForm(RequestResetPassword::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(
                [
                    'email' => $form->getData()['email']
                ]
            );

            if ($user instanceof User) {
                $this->addFlash('success', 'Votre demande à bien été prise en compte. Un mail va vous
                être envoyé afin que vous puissiez renouveller votre mot de passe. Le lien que vous
                recevrez sera valide 3h.');

                $date = new DateTime();
                $token = $tokenGenerator->generateToken();
                $user->setPasswordRequestedAt($date);
                $user->setToken($token);

                $entityManager->persist($user);
                $entityManager->flush();

                $sendParameter = [
                    'from' => 'no-reply <' . $this->getParameter('mailer_from') . '>',
                    'to' => $user->getEmail(),
                    'subject' => 'Renouvellement du mot de passe'
                ];

                $bodyData = [
                    'title' => 'Bonjour',
                    'bodyText' => 'Pour modifier votre mot de passe, vous pouvez cliquer sur le bouton ci-dessous.',
                    'pageLink' => 'password_reset',
                    'buttonName' => 'Saisir le mot de passe',
                    'userId' => $user->getId(),
                    'userToken' => $token
                ];

                $mailManager->sendMessage($sendParameter, 'user/notification/notification.html.twig', $bodyData);

                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('danger', 'L\'adresse email n\'existe pas, vérifiez l\'adresse 
                que vous avez renseignée.');

                return $this->redirectToRoute('password_forgot_request');
            }
        }

        return $this->render('security/requestpassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/ajouter/{token}", name="password_add")
     * @param User $user
     * @param mixed $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function add(User $user, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        /*
            We prohibit access to the page if the token is null
            or if it has been created for more than 10 minutes
        */
        if (!$this->isRequestInTime(10, $user->getPasswordRequestedAt())
            || $this->checkToken($token, $user->getToken() == false)
        ) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $form->getData()->getPassword());
            $user->setPassword($password);

            // reset the token to null so that it is no longer reusable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été pris en compte. Vous pouvez 
                vous connecter dès maintenant.
            ');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/password.html.twig', [
            'form' => $form->createView(),
            'title' => 'Saisissez votre mot de passe'
        ]);
    }

    /**
     * @Route("{/id}/reinitialiser/{token}", name="password_reset")
     * @param User $user
     * @param TokenGeneratorInterface $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function reset(
        User $user,
        $token,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        /*
            We prohibit access to the page if the token is null
            or if it has been created for more than 3 hours
        */
        if (!$this->isRequestInTime(180, $user->getPasswordRequestedAt())
            || $this->checkToken($token, $user->getToken() == false)
        ) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $form->getData()->getPassword());
            $user->setPassword($password);

            // reset the token to null so that it is no longer reusable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/password.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier votre mot de passe'
        ]);
    }

    /**
     * Check that the token is still valid
     *
     * @param mixed $passwordRequestedAt
     * @param mixed $tokenTimeValid
     * @return bool
     */
    private function isRequestInTime($tokenTimeValid, $passwordRequestedAt = null)
    {
        if ($passwordRequestedAt == null) {
            return false;
        }

        $now = new DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();
        $daySeconds = 60 * $tokenTimeValid;

        return $interval > $daySeconds ? false : true;
    }

    /**
     * Check that the token is correct and that it is identical to that of the user
     *
     * @param mixed $token
     * @param mixed $userToken
     * @return bool
     */
    private function checkToken($token, $userToken): bool
    {
        if (is_null($userToken) || $token !== $userToken) {
            return false;
        }
    }
}
