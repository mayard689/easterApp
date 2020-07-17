<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\FeatureRepository;
use App\Repository\ProjectFeatureRepository;
use App\Repository\ProjectRepository;
use App\Service\ProjectCalculator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorie")
 */
class CategoryController extends AbstractController
{
    const NUMBER_PER_PAGE = 10;
    const DIRECTION=['asc','desc'];
    const SORT=['name', 'date', 'quotation'];

    /**
     * @Route("/", name="category_index", methods={"GET"})
     * @param CategoryRepository $categoryRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(
        CategoryRepository $categoryRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $sort=$request->query->get('sort');
        $direction=$request->query->get('direction');

        if (!in_array($direction, self::DIRECTION)) {
            $direction = 'asc';
        }

        if (!in_array($sort, self::SORT)) {
            $sort = 'name';
        }
        return $this->render('category/index.html.twig', [
            'categories' => $paginator->paginate(
                $categoryRepository->findBy([], [$sort => strtoupper($direction)]),
                $request->query->getInt('page', 1),
                self::NUMBER_PER_PAGE
            ),
            'newDirection' => $direction=='asc'?'desc':'asc',
            'sort' => $sort
        ]);
    }

    /**
     * @Route("/ajouter", name="category_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été créée avec succès');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/editer", name="category_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La catégorie a été modifiée avec succès');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     * @param Request $request
     * @param Category $category
     * @param FeatureRepository $feature
     * @param ProjectFeatureRepository $projectFeature
     * @return Response
     */
    public function delete(
        Request $request,
        Category $category,
        FeatureRepository $feature,
        ProjectFeatureRepository $projectFeature
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $featureList = $feature->findBy(['category' => $category->getId()]);
            $projectFeatureList = $projectFeature->findBy(['category' => $category->getId()]);

            if (!empty($featureList) || !empty($projectFeatureList)) {
                $this->addFlash('danger', 'La catégorie ' . $category->getName() . ' ne peut 
                être supprimée, car elle est utilisée.');
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($category);
                $entityManager->flush();
                $this->addFlash('success', 'La catégorie ' . $category->getName() . ' a 
                été supprimée avec succès');
            }
        }

        return $this->redirectToRoute('category_index');
    }
}
