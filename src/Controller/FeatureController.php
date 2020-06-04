<?php

namespace App\Controller;

use App\Entity\Feature;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeatureController extends AbstractController
{
    /**
     * @Route("/features", name="features")
     * @return Response
     */
    public function index(): Response
    {
        $features = $this->getDoctrine()
            ->getRepository(Feature::class)
            ->findBy(
                [],
                ['name' => 'ASC'],
                10
            );

        return $this->render('feature/index.html.twig', [
            'features' => $features,
        ]);
    }
}
