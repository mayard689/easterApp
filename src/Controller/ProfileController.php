<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     * @param UserInterface $user
     * @return Response
     */
    public function index(UserInterface $user): Response
    {
        return $this->render('security/profile.html.twig', [
            'user' => $user
        ]);
    }
}
