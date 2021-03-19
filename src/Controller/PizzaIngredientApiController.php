<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Pizza;
use App\Entity\PizzaIngredient;
use App\Form\Type\PizzaIngredientType;
use App\Repository\PizzaIngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/pizzas")
 */
class PizzaIngredientApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private PizzaIngredientRepository $pizzaIngredientRepository
    ) {}

    /**
     * Adds an ingredient to a pizza
     *
     * @Route("/{id}/ingredients", methods="POST", format="json", name="api_pizza_ingredient_add")
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The pizza ID",
     *     @OA\Schema(type="integer")
     * )
     * @OA\RequestBody(
     *     description="Input data format",
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             type="object",
     *             required={"ingredient", "order"},
     *             @OA\Property(
     *                 property="ingredient",
     *                 description="The ID of the ingredient to add",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="order",
     *                 description="The order of the ingredient to add",
     *                 type="integer"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Returns the created pizza",
     *     @OA\JsonContent(ref=@Model(type=Pizza::class, groups={"api_pizza"}))
     * )
     * @OA\Tag(name="pizzas")
     */
    public function add(Request $request, Pizza $pizza): JsonResponse
    {
        $form = $this->createForm(PizzaIngredientType::class, (new PizzaIngredient())->setPizza($pizza));
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->json(['error' => $form->getErrors(true)[0]->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $pizzaIngredient = $form->getData();
        $this->em->persist($pizzaIngredient);
        $this->em->flush();

        return $this->json($pizza, Response::HTTP_CREATED, [], ['groups' => 'api_pizza']);
    }

    /**
     * Changes the order of an ingredient in a pizza
     *
     * @Route("/{pizza}/ingredients/{ingredient}", methods="PUT", format="json", name="api_pizza_ingredient_edit")
     *
     * @OA\Parameter(
     *     name="pizza",
     *     in="path",
     *     description="The pizza ID",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="ingredient",
     *     in="path",
     *     description="The ingredient ID",
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
     *                 property="order",
     *                 description="The new order of the ingredient in the pizza",
     *                 type="integer"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the updated pizza",
     *     @OA\JsonContent(ref=@Model(type=Pizza::class, groups={"api_pizza"}))
     * )
     * @OA\Tag(name="pizzas")
     */
    public function edit(Request $request, Pizza $pizza, Ingredient $ingredient): JsonResponse
    {
        $pizzaIngredient = $this->pizzaIngredientRepository->findOneBy(['pizza' => $pizza, 'ingredient' => $ingredient]);
        if (null === $pizzaIngredient) {
            return $this->json(['error' => 'The pizza has not this ingredient'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PizzaIngredientType::class, $pizzaIngredient);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->json(['error' => $form->getErrors(true)[0]->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        /** @var PizzaIngredient $pizzaIngredient */
        $pizzaIngredient = $form->getData();
        $this->em->persist($pizzaIngredient);
        $this->em->flush();

        return $this->json($pizzaIngredient->getPizza(), Response::HTTP_OK, [], ['groups' => 'api_pizza']);
    }

    /**
     * Deletes an ingredient from a pizza
     *
     * @Route("/{pizza}/ingredients/{ingredient}", methods="DELETE", format="json", name="api_pizza_ingredient_delete")
     *
     * @OA\Parameter(
     *     name="pizza",
     *     in="path",
     *     description="The pizza ID",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="ingredient",
     *     in="path",
     *     description="The ingredient ID",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns {deleted: true}"
     * )
     * @OA\Tag(name="pizzas")
     */
    public function delete(Pizza $pizza, Ingredient $ingredient): JsonResponse
    {
        $pizzaIngredient = $this->pizzaIngredientRepository->findOneBy(['pizza' => $pizza, 'ingredient' => $ingredient]);
        if (null === $pizzaIngredient) {
            return $this->json(['error' => 'The pizza has not this ingredient'], Response::HTTP_NOT_FOUND);
        }

        $pizza = $pizzaIngredient->getPizza();
        $this->em->remove($pizzaIngredient);
        $this->em->flush();

        return $this->json($pizza);
    }
}
