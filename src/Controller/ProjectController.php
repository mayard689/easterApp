<?php

namespace App\Controller;

use App\Entity\Feature;
use App\Entity\Project;
use App\Entity\ProjectFeature;
use App\Entity\Quotation;
use App\Form\FeatureType;
use App\Form\ProjectType;
use App\Form\SpecificFeatureType;
use App\Repository\ProjectFeatureRepository;
use App\Repository\ProjectRepository;
use App\Repository\QuotationRepository;
use App\Service\ProjectCalculator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    const NUMBER_PER_PAGE = 10;

    /**
     * @Route("/", name="project_index", methods={"GET"})
     * @param ProjectRepository  $project
     * @param PaginatorInterface $paginator
     * @param Request            $request
     * @return Response
     */
    public function index(ProjectRepository $project, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $paginator->paginate(
                $project->findAll(),
                $request->query->getInt('page', 1),
                self::NUMBER_PER_PAGE
            ),
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
     * @Route("/{id}/edit/{variant<high|middle|low>}", name="project_edit", methods={"GET","POST"})
     * @param Request                  $request
     * @param Project                  $project
     * @param QuotationRepository      $quotationRepository
     * @param ProjectCalculator        $projectCalculator
     * @param ProjectRepository        $projectRepository
     * @param ProjectFeatureRepository $projectFeatureRepos
     * @param string                   $variant
     * @return Response
     */
    public function edit(
        Request $request,
        Project $project,
        QuotationRepository $quotationRepository,
        ProjectCalculator $projectCalculator,
        ProjectRepository $projectRepository,
        ProjectFeatureRepository $projectFeatureRepos,
        string $variant = 'high'
    ): Response {

        $form = $this->createForm(ProjectType::class, $project);
        $featuresToBeShown=$projectFeatureRepos->findProjectFeatures($project, $variant);
        $form->get('projectFeatures')->setData($featuresToBeShown);
        $form->handleRequest($request);

        $feature = new Feature();
        $formFeature = $this->createForm(FeatureType::class, $feature);
        $formFeature->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('project_edit', ['id' => $project->getId()]);
        }

        if ($formFeature->isSubmitted() && $formFeature->isValid()) {
            $projectFeature=new ProjectFeature();
            $projectFeature->setProject($project);
            $projectFeature->setFeature($feature);
            $projectFeature->setDescription($feature->getDescription());
            $projectFeature->setDay($feature->getDay());
            $projectFeature->setCategory($feature->getCategory());

            $projectFeature->setIsHigh(true);
            $projectFeature->setIsMiddle(true);
            $projectFeature->setIsLow(true);

            $feature->setIsStandard(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($projectFeature);
            $entityManager->persist($feature);
            $entityManager->flush();

            return $this->redirectToRoute('project_edit', ['id'=>$project->getId()]);
        }

        $load = $projectCalculator->calculateProjectLoad($project, $featuresToBeShown);
        $featureCategories=$projectRepository->getCategories($project);

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'load' => $load,
            'form' => $form->createView(),
            'formFeature' => $formFeature->createView(),
            'featureCategories' => $featureCategories,
            'variant' => $variant,
            'variants' => $quotationRepository->findAll(),
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
     * @Route("Feature/{id}/{variant<high|middle|low>}", name="project_feature_delete", methods="POST")
     * @param ProjectFeature         $projectFeature
     * @param EntityManagerInterface $entityManager
     * @param ProjectCalculator      $projectCalculator
     * @param string                 $variant
     * @return Response
     */
    public function deleteProjectFeature(
        ProjectFeature $projectFeature,
        EntityManagerInterface $entityManager,
        ProjectCalculator $projectCalculator,
        string $variant = 'high'
    ): Response {
        $variant=ucfirst($variant);
        $projectFeature->{'setIs'.$variant}(false);

        // check if the projectFeature is used in at least one variant
        // if projectFeature is not used anymore by any variant of the project, removes it
        if (!$projectCalculator->isActive($projectFeature)) {
            $entityManager->remove($projectFeature);
        }
        $entityManager->flush();
        /** @var Project */
        $project = $projectFeature->getProject();
        $projectId = $project->getId();
        return $this->redirectToRoute('project_edit', ['id' => $projectId, 'estimation'=>$variant]);
    }

    /**
     * @Route("/{id}/add-feature/{variant<high|middle|low>}", name="project_feature_add", methods={"GET", "POST"})
     * @param Request           $request
     * @param Project           $project
     * @param ProjectCalculator $projectCalculator
     * @param ProjectRepository $projectRepository
     * @return Response
     */
    public function addProjectFeature(
        Request $request,
        Project $project,
        ProjectCalculator $projectCalculator,
        ProjectRepository $projectRepository,
        string $variant = 'high'
    ): Response {

        $feature = new Feature();
        $form = $this->createForm(SpecificFeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectFeature=new ProjectFeature();
            $projectFeature->setProject($project);
            $projectFeature->setFeature($feature);
            $projectFeature->setDescription($feature->getDescription());
            $projectFeature->setDay($feature->getDay());
            $projectFeature->setCategory($feature->getCategory());

            $projectFeature->setIsHigh($form['isHigh']->getData());
            $projectFeature->setIsMiddle($form['isMiddle']->getData());
            $projectFeature->setIsLow($form['isLow']->getData());

            $feature->setIsStandard(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($projectFeature);
            $entityManager->persist($feature);
            $entityManager->flush();

            return $this->redirectToRoute('project_edit', ['id'=>$project->getId()]);
        }

        $form->get('is'.ucfirst($variant))->setData(true);

        return $this->render('feature/new.html.twig', [
            'feature' => $feature,
            'formFeature' => $form->createView(),
            'id'=>$project->getId(),
        ]);
    }
}
