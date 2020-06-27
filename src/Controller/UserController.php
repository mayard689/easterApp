<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\MailManager;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findBy([], ['lastname' => 'ASC', 'firstname' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param MailManager $mailManager
     * @return Response
     * @throws Exception
     */
    public function new(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        MailManager $mailManager
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

            $user->setCreationDate(new DateTime());
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le compte de l\'utilisateur a été crée avec succès');

            $sendParameter = [
                'from' => 'no-reply <no-reply@easterapp.fr>',
                'to' => $user->getFirstname() . ' ' . $user->getLastname() . '<' . $user->getEmail() . '>',
                'subject' => 'Création de votre compte'
            ];

            $bodyData = [
                'title' => 'Bienvenue',
                'bodyText' => 'Pour finaliser la création de votre compte, il faut renseigner votre mot de passe. 
                Pour cela, vous pouvez cliquer sur le bouton ci-dessous.',
                'pageLink' => 'project_index',
                'buttonName' => 'Saisir mon mot de passe'
            ];

            $mailManager->sendMessage($sendParameter, 'user/notification/notification_account.html.twig', $bodyData);

            return $this->redirectToRoute('user_index');
        }


        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

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
