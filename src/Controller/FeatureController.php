<?php

namespace App\Controller;

use App\Entity\Feature;
use App\Form\FeatureType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/feature")
 */
class FeatureController extends AbstractController
{
    const MAX_PER_PAGE = 10;

    /**
     * @Route("/", name="feature_index", methods={"GET"})
     */
    public function index(): Response
    {
        $features = $this->getDoctrine()
            ->getRepository(Feature::class)
            ->findBy(
                [],
                ['name' => 'ASC'],
                self::MAX_PER_PAGE
            );

        return $this->render('feature/index.html.twig', [
            'features' => $features,
        ]);
    }

    /**
     * @Route("/new", name="feature_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $feature = new Feature();
        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($feature);
            $entityManager->flush();

            return $this->redirectToRoute('feature_index');
        }

        return $this->render('feature/new.html.twig', [
            'feature' => $feature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="feature_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Feature $feature): Response
    {
        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('feature_index');
        }

        return $this->render('feature/edit.html.twig', [
            'feature' => $feature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="feature_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Feature $feature): Response
    {
        if ($this->isCsrfTokenValid('delete' . $feature->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($feature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('feature_index');
    }
}
