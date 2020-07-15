<?php

namespace App\Controller;

use App\Entity\Feature;
use App\Form\FeatureType;
use App\Repository\FeatureRepository;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/fonctionnalite")
 */
class FeatureController extends AbstractController
{
    const NUMBER_PER_PAGE = 10;
    const DIRECTION=['asc','desc'];
    const SORT=['name', 'day', 'category.name'];

    /**
     * @Route("/", name="feature_index", methods={"GET"})
     * @param FeatureRepository  $feature
     * @param PaginatorInterface $paginator
     * @param Request            $request
     * @return Response
     */
    public function index(FeatureRepository $feature, PaginatorInterface $paginator, Request $request): Response
    {
        $sort=$request->query->get('sort');
        $direction=$request->query->get('direction');

        if (!in_array($direction, self::DIRECTION)) {
            $direction = 'desc';
        }

        if (!in_array($sort, self::SORT)) {
            $sort = 'name';
        }

        return $this->render('feature/index.html.twig', [
            'features' => $paginator->paginate(
                $feature->findBy(
                    ['isStandard'=>true],
                    ['name' => 'ASC']
                ),
                $request->query->getInt('page', 1),
                self::NUMBER_PER_PAGE
            ),
            'newDirection' => $direction=='asc'?'desc':'asc',
            'sort' => $sort
        ]);
    }

    /**
     * @Route("/ajouter", name="feature_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $feature = new Feature();
        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feature->setIsStandard(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($feature);
            $entityManager->flush();
            $this->addFlash('success', 'La fonctionnalité a été ajoutée avec succès');

            return $this->redirectToRoute('feature_index');
        }

        return $this->render('feature/new.html.twig', [
            'feature' => $feature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/editer", name="feature_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Feature $feature): Response
    {
        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La fonctionnalité a été modifiée avec succès');

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
            $this->addFlash('success', 'La fonctionnalité a été supprimée avec succès');
        }

        return $this->redirectToRoute('feature_index');
    }
}
