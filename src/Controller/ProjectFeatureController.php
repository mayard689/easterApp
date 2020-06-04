<?php

namespace App\Controller;

use App\Entity\ProjectFeature;
use App\Form\ProjectFeatureType;
use App\Repository\ProjectFeatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project/feature")
 */
class ProjectFeatureController extends AbstractController
{
    /**
     * @Route("/", name="project_feature_index", methods={"GET"})
     */
    public function index(ProjectFeatureRepository $projectFeatureRepository): Response
    {
        return $this->render('project_feature/index.html.twig', [
            'project_features' => $projectFeatureRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="project_feature_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $projectFeature = new ProjectFeature();
        $form = $this->createForm(ProjectFeatureType::class, $projectFeature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($projectFeature);
            $entityManager->flush();

            return $this->redirectToRoute('project_feature_index');
        }

        return $this->render('project_feature/new.html.twig', [
            'project_feature' => $projectFeature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_feature_show", methods={"GET"})
     */
    public function show(ProjectFeature $projectFeature): Response
    {
        return $this->render('project_feature/show.html.twig', [
            'project_feature' => $projectFeature,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_feature_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProjectFeature $projectFeature): Response
    {
        $form = $this->createForm(ProjectFeatureType::class, $projectFeature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_feature_index');
        }

        return $this->render('project_feature/edit.html.twig', [
            'project_feature' => $projectFeature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_feature_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProjectFeature $projectFeature): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projectFeature->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($projectFeature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_feature_index');
    }
}
