<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeRoleType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\MailManager;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * @Route("/utilisateur")
 */
class UserController extends AbstractController
{
    const NUMBER_PER_PAGE = 10;

    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $paginator->paginate(
                $userRepository->findBy([], ['lastname' => 'ASC', 'firstname' => 'ASC']),
                $request->query->getInt('page', 1),
                self::NUMBER_PER_PAGE
            )
        ]);
    }

    /**
     * @Route("/ajouter", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param MailManager $mailManager
     * @param UserRepository $userRepository
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function new(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        MailManager $mailManager,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $defaultPassword = random_bytes(10);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $defaultPassword
                )
            );

            $date = new DateTime();
            $token = $tokenGenerator->generateToken();
            $user->setCreationDate($date);
            $user->setToken($tokenGenerator->generateToken());
            $user->setPasswordRequestedAt($date);
            $user->setToken($token);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le compte de l\'utilisateur a été crée avec succès');

            $sendParameter = [
                'from' => 'no-reply <' . $this->getParameter('mailer_from') . '>',
                'to' => $user->getFirstname() . ' ' . $user->getLastname() . '<' . $user->getEmail() . '>',
                'subject' => 'Création de votre compte'
            ];

            $bodyData = [
                'title' => 'Bienvenue',
                'bodyText' => 'Pour finaliser la création de votre compte, il faut renseigner votre mot de passe. 
                Pour cela, vous pouvez cliquer sur le bouton ci-dessous.',
                'pageLink' => 'password_add',
                'buttonName' => 'Saisir mon mot de passe',
                'userId' => $userRepository->findLastInserted()['id'],
                'userToken' => $token
            ];

            $mailManager->sendMessage($sendParameter, 'user/notification/notification.html.twig', $bodyData);

            return $this->redirectToRoute('user_index');
        }


        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/modifier/droits", name="user_role", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function changeRole(Request $request, User $user): Response
    {
        $form = $this->createForm(ChangeRoleType::class, $user);
        $form->handleRequest($request);

        if ($user == $this->getUser()) {
            return $this->redirectToRoute('user_index');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le rôle de l\'utilisateur a bien été changé.');

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le compte de l\'utilisateur a été supprimé avec succès');
        }

        return $this->redirectToRoute('user_index');
    }
}
