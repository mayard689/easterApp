<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\RequestResetPassword;
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

            $this->addFlash('success', 'Votre mot de passe a été pris en compte. Vous pouvez 
                vous connecter dès maintenant.
            ');

            return $this->redirectToRoute('project_index');
        }

        return $this->render('security/changepassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/resetPasswordRequest", name="resetPasswordRequest")
     * @param Request $request
     * @param MailManager $mailManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     * @throws Exception
     */
    public function resetPasswordRequest(
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
                $this->addFlash('success', 'Votre demande à bien été pris en compte. Un mail va vous
                être envoyé afin que vous puissiez renouveller votre mot de passe. Le lien que vous
                recevrez sera valide 24h.');

                $date = new DateTime();
                $token = $tokenGenerator->generateToken();
                $user->setCreationDate($date);
                $user->setToken($tokenGenerator->generateToken());
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
                    'pageLink' => 'changePassword_index',
                    'buttonName' => 'Saisir mon mot de passe',
                    'userId' => $user->getId(),
                    'userToken' => $token
                ];

                $mailManager->sendMessage($sendParameter, 'user/notification/notification.html.twig', $bodyData);

                $this->redirectToRoute('resetPasswordRequest');
            } else {
                $this->addFlash('danger', 'L\'adresse email n\'existe pas, vérifiez l\'adresse 
                que vous avez renseignée.');

                $this->redirectToRoute('resetPasswordRequest');
            }
        }

        return $this->render('security/requestpassword.html.twig', [
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

    public function request(){}
}
