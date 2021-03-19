<?php

namespace App\Controller;

use App\Entity\Pizza;
use App\Form\Type\PizzaType;
use App\Repository\PizzaRepository;
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
class PizzaApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private PizzaRepository $pizzaRepository
    ) {}

    /**
     * Fetches all pizzas
     *
     * @Route("", methods="GET", format="json", name="api_pizza_index")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all the pizzas",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Pizza::class, groups={"api_pizza"}))
     *     )
     * )
     * @OA\Tag(name="pizzas")
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
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The pizza ID",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns a pizza",
     *     @OA\JsonContent(ref=@Model(type=Pizza::class, groups={"api_pizza"}))
     * )
     * @OA\Tag(name="pizzas")
     */
    public function show(Pizza $pizza): JsonResponse
    {
        return $this->json($pizza, Response::HTTP_OK, [], ['groups' => 'api_pizza']);
    }

    /**
     * Creates a pizza
     *
     * @Route("", methods="POST", format="json", name="api_pizza_add")
     *
     * @OA\RequestBody(
     *     description="Input data format",
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             type="object",
     *             required={"slug"},
     *             @OA\Property(
     *                 property="slug",
     *                 description="The pizza slug",
     *                 type="string"
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
     *                 description="The pizza slug new value",
     *                 type="string"
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
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the pizza to delete",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns {deleted: true}"
     * )
     * @OA\Tag(name="pizzas")
     */
    public function delete(Pizza $pizza): JsonResponse
    {
        $this->em->remove($pizza);
        $this->em->flush();

        return $this->json(['deleted' => true]);
    }
}
