<?php

namespace App\Controller;

use App\Entity\Feature;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeaturesController extends AbstractController
{
    /**
     * @Route("/features", name="features")
     * @return Response
     */
    public function index(): Response
    {
        $features = $this->getDoctrine()
            ->getRepository(Feature::class)
            ->findAll();

        if (!$features) {
            throw $this->createNotFoundException(
                'No features found in program\'s table.'
            );
        }
        return $this->render('features/index.html.twig', [
            'controller_name' => 'FeaturesController',
            'title' => 'Liste des Fonctionnalités Récurrentes',
            'features' => $features,
        ]);
    }
}
