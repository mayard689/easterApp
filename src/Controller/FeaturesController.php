<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FeaturesController extends AbstractController
{
    /**
     * @Route("/features", name="features")
     */
    public function index()
    {
        return $this->render('features/index.html.twig', [
            'controller_name' => 'FeaturesController',
            'title' => 'Liste des Fonctionnalités Récurrentes'
        ]);
    }
}
