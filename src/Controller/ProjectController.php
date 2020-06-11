<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Project;
use App\Entity\ProjectFeature;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Service\ProjectCalculator;
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
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="project_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
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
        ObjectManager $manager
    ): Response {
        $featureCategories=$projectCalculator->getCategories($project);
        //$featureCategories= $manager->getRepository(Category::class)->findAll();
        //var_dump($featureCategories);exit();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_index');
        }

        $load=$projectCalculator->calculateProjectLoad($project);

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
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }

    /**
     * @Route("Feature/{id}", name="project_feature_delete", methods="POST")
     */
    public function deleteProjectFeature(
        ProjectFeature $projectFeature,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($projectFeature);
        $entityManager->flush();

        /** @var Project */
        $project=$projectFeature->getProject();
        $projectId=$project->getId();

        return $this->redirectToRoute('project_edit', ['id'=>$projectId]);
    }
}
