<?php

namespace App\Controller;

use App\Entity\Feature;
use App\Entity\Project;
use App\Entity\ProjectFeature;
use App\Form\FeatureType;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Service\ProjectCalculator;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="project_index", methods={"GET"})
     * @param ProjectRepository $projectRepository
     * @return Response
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="project_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $project->setDate(new DateTime());
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_edit', ['id' => $project->getId()]);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Project $project,
        ProjectCalculator $projectCalculator,
        ProjectRepository $projectRepository
    ): Response {

        $featureCategories=$projectRepository->getCategories($project);

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_edit', ['id' => $project->getId()]);
        }

        $load = $projectCalculator->calculateProjectLoad($project);

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'load' => $load,
            'form' => $form->createView(),
            'featureCategories' => $featureCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }

    /**
     * @Route("Feature/{id}", name="project_feature_delete", methods="POST")
     * @param ProjectFeature         $projectFeature
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteProjectFeature(
        ProjectFeature $projectFeature,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($projectFeature);
        $entityManager->flush();

        /** @var Project */
        $project = $projectFeature->getProject();
        $projectId = $project->getId();

        return $this->redirectToRoute('project_edit', ['id' => $projectId]);
    }

    /**
     * @Route("Project/{id}/add-feature", name="project_feature_add", methods={"GET", "POST"})
     */
    public function addProjectFeature(Project $project, Request $request): Response
    {
        $feature = new Feature();
        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectFeature=new ProjectFeature();
            $projectFeature->setProject($project);
            $projectFeature->setFeature($feature);
            $projectFeature->setDescription($feature->getDescription());
            $projectFeature->setDay($feature->getDay());
            $projectFeature->setCategory($feature->getCategory());

            $feature->setIsStandard(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($projectFeature);
            $entityManager->persist($feature);
            $entityManager->flush();

            return $this->redirectToRoute('project_edit', ['id'=>$project->getId()]);
        }

        return $this->render('feature/new.html.twig', [
            'feature' => $feature,
            'form' => $form->createView(),
        ]);
    }
}
