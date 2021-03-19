<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Pizza;
use App\Entity\PizzaIngredient;
use App\Form\Type\PizzaIngredientType;
use App\Repository\IngredientRepository;
use App\Repository\PizzaIngredientRepository;
use App\Repository\PizzaRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        private PizzaRepository $pizzaRepository,
        private PizzaIngredientRepository $pizzaIngredientRepository,
        private IngredientRepository $ingredientRepository,
        private EntityManagerInterface $em
    ) {}

    /**
     * Adds an ingredient to a pizza
     *
     * @Route("/{id}/ingredients", methods="POST", format="json", name="api_pizza_ingredient_add")
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
     * @Route("/{pizza}/ingredients/{ingredient}" methods="PUT", format="json", name="api_pizza_ingredient_edit")
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
