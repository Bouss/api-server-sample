<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\Type\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/ingredients")
 */
class IngredientApiController extends AbstractController
{
    public function __construct(
        private IngredientRepository $ingredientRepository,
        private EntityManagerInterface $em
    ) {}

    /**
     * Fetches all ingredients
     *
     * @Route("", methods="GET", format="json", name="api_ingredient_index")
     */
    public function index(): JsonResponse
    {
        $ingredients = $this->ingredientRepository->findAll();

        return $this->json($ingredients, Response::HTTP_OK, [], ['groups' => 'api_ingredient']);
    }

    /**
     * Fetches an ingredient
     *
     * @Route("/{id}", methods="GET", format="json", name="api_ingredient_show")
     */
    public function show(Ingredient $ingredient): JsonResponse
    {
        return $this->json($ingredient, Response::HTTP_OK, [], ['groups' => 'api_ingredient']);
    }

    /**
     * Creates an ingredient
     *
     * @Route("", methods="POST", format="json", name="api_ingredient_add")
     */
    public function add(Request $request): JsonResponse
    {
        $form = $this->createForm(IngredientType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->json(['error' => $form->getErrors(true)[0]->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $ingredient = $form->getData();
        $this->em->persist($ingredient);
        $this->em->flush();

        return $this->json($ingredient, Response::HTTP_CREATED, [], ['groups' => 'api_ingredient']);
    }

    /**
     * Updates an ingredient
     *
     * @Route("/{id}", methods="PUT", format="json", name="api_ingredient_edit")
     */
    public function edit(Request $request, Ingredient $ingredient): JsonResponse
    {

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->json(['error' => $form->getErrors(true)[0]->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $ingredient = $form->getData();
        $this->em->flush();

        return $this->json($ingredient, Response::HTTP_OK, [], ['groups' => 'api_ingredient']);
    }

    /**
     * Deletes an ingredient
     *
     * @Route("/{id}", methods="DELETE", format="json", name="api_ingredient_delete")
     */
    public function delete(Ingredient $ingredient): JsonResponse
    {
        $this->em->remove($ingredient);
        $this->em->flush();

        return $this->json(['deleted' => true]);
    }
}
