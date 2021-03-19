<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\Type\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
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
        private EntityManagerInterface $em,
        private IngredientRepository $ingredientRepository
    ) {}

    /**
     * Fetches all ingredients
     *
     * @Route("", methods="GET", format="json", name="api_ingredient_index")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all the ingredients",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Ingredient::class, groups={"api_ingredient"}))
     *     )
     * )
     * @OA\Tag(name="ingredients")
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
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ingredient ID",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns an ingredient",
     *     @OA\JsonContent(ref=@Model(type=Ingredient::class, groups={"api_ingredient"}))
     * )
     * @OA\Tag(name="ingredients")
     */
    public function show(Ingredient $ingredient): JsonResponse
    {
        return $this->json($ingredient, Response::HTTP_OK, [], ['groups' => 'api_ingredient']);
    }

    /**
     * Creates an ingredient
     *
     * @Route("", methods="POST", format="json", name="api_ingredient_add")
     *
     * @OA\RequestBody(
     *     description="Input data format",
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             type="object",
     *             required={"slug", "cost"},
     *             @OA\Property(
     *                 property="slug",
     *                 description="The ingredient slug",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="cost",
     *                 description="The ingredient cost",
     *                 type="number",
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Returns the created ingredient",
     *     @OA\JsonContent(ref=@Model(type=Ingredient::class, groups={"api_ingredient"}))
     * )
     * @OA\Tag(name="ingredients")
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
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the ingredient to update",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\RequestBody(
     *     description="Input data format",
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="slug",
     *                 description="The ingredient slug new value",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="cost",
     *                 description="The ingredient cost new value",
     *                 type="number"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the updated ingredient",
     *     @OA\JsonContent(ref=@Model(type=Ingredient::class, groups={"api_ingredient"}))
     * )
     * @OA\Tag(name="ingredients")
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
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the ingredient to delete",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns {deleted: true}"
     * )
     * @OA\Tag(name="ingredients")
     */
    public function delete(Ingredient $ingredient): JsonResponse
    {
        $this->em->remove($ingredient);
        $this->em->flush();

        return $this->json(['deleted' => true]);
    }
}
