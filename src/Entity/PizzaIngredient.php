<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Many-to-many relationship with an extra field ("order") justifies this class
 *
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="pizza_ingredient_unique", columns={"pizza_id", "ingredient_id"}),
 *     @ORM\UniqueConstraint(name="pizza_order_unique", columns={"pizza_id", "`order`"})
 * })
 * @UniqueEntity(fields={"pizza", "ingredient"}, errorPath="ingredient", message="The pizza has already this ingredient")
 * @UniqueEntity(fields={"pizza", "order"}, errorPath="order", message="This order is already set for an other ingredient")
 */
class PizzaIngredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Pizza", inversedBy="pizzaIngredients", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank(message="Pizza cannot be blank")
     */
    private Pizza $pizza;

    /**
     * @ORM\ManyToOne(targetEntity="Ingredient", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank(message="Ingredient cannot be blank")
     */
    private Ingredient $ingredient;

    /**
     * @var int
     *
     * @ORM\Column(name="`order`", type="integer", options={"default": 1})
     * @Assert\NotBlank(message="Order cannot be blank")
     * @Assert\Type(type="integer", message="Order must be an integer")
     */
    private $order = 1;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPizza(): Pizza
    {
        return $this->pizza;
    }

    public function setPizza(Pizza $pizza): PizzaIngredient
    {
        $this->pizza = $pizza;

        return $this;
    }

    public function getIngredient(): Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(Ingredient $ingredient): PizzaIngredient
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order): PizzaIngredient
    {
        $this->order = $order;

        return $this;
    }
}
