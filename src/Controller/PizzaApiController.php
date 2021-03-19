<?php

namespace App\Controller;

use App\Entity\Pizza;
use App\Form\Type\IngredientType;
use App\Form\Type\PizzaType;
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
class PizzaApiController extends AbstractController
{
    public function __construct(
        private PizzaRepository $pizzaRepository,
        private EntityManagerInterface $em
    ) {}

    /**
     * Fetches all pizzas
     *
     * @Route("", methods="GET", format="json", name="api_pizza_index")
     */
    public function index(): JsonResponse
    {
        $pizzas = $this->pizzaRepository->findAll();

        return $this->json($pizzas, Response::HTTP_OK, [], ['groups' => 'api_pizza']);
    }

    /**
     * Fetches a pizza
     *
     * @Route("/{id}", methods="GET", format="json", name="api_pizza_show")
     */
    public function show(Pizza $pizza): JsonResponse
    {
        return $this->json($pizza, Response::HTTP_OK, [], ['groups' => 'api_pizza']);
    }


    /**
     * Creates a pizza
     *
     * @Route("", methods="POST", format="json", name="api_pizza_add")
     */
    public function add(Request $request): JsonResponse
    {
        $form = $this->createForm(PizzaType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->json(['error' => $form->getErrors(true)[0]->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $pizza = $form->getData();
        $this->em->persist($pizza);
        $this->em->flush();

        return $this->json($pizza, Response::HTTP_CREATED, [], ['groups' => 'api_pizza']);
    }

    /**
     * Updates a pizza
     *
     * @Route("/{id}", methods="PUT", format="json", name="api_pizza_edit")
     */
    public function edit(Request $request, Pizza $pizza): JsonResponse
    {
        $form = $this->createForm(PizzaType::class, $pizza);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->json(['error' => $form->getErrors(true)[0]->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $pizza = $form->getData();
        $this->em->flush();

        return $this->json($pizza, Response::HTTP_OK, [], ['groups' => 'api_pizza']);
    }

    /**
     * Deletes a pizza
     *
     * @Route("/{id}", methods="DELETE", format="json", name="api_pizza_delete")
     */
    public function delete(Pizza $pizza): JsonResponse
    {
        $this->em->remove($pizza);
        $this->em->flush();

        return $this->json(['deleted' => true]);
    }
}
