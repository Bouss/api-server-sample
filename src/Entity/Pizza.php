<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"slug"}, errorPath="slug", message="A pizza with this slug already exists")
 */
class Pizza implements Stringable, TranslatableInterface
{
    use TranslatableTrait;

    private const PREPARATION_COST_FACTOR = 1.5;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string
     *
     * "slug" instead of "name": Human-readable technical identifier (back-side). The name (front-side) is translated in PizzaTranslation
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message="Slug cannot be blank")
     * @Assert\Regex(pattern="/^[-a-z]+$/", message="Slug must be a lowercase slug")
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @var PizzaIngredient[]|Collection
     *
     * @ORM\OneToMany(targetEntity="PizzaIngredient", mappedBy="pizza", cascade={"persist", "remove"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $pizzaIngredients;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->pizzaIngredients = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): Pizza
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return PizzaIngredient[]|Collection
     */
    public function getPizzaIngredients(): Collection
    {
        return $this->pizzaIngredients;
    }

    /**
     * @return PizzaIngredient[]|Collection
     */
    public function setPizzaIngredients($pizzaIngredients): Pizza
    {
        $this->pizzaIngredients = $pizzaIngredients;

        return $this;
    }

    public function addIngredient(Ingredient $ingredient, int $order = null): Pizza
    {
        // If no order given, get the last order plus one
        if (null === $order) {
            $order = !$this->pizzaIngredients->isEmpty() ? $this->pizzaIngredients->last()->getOrder() + 1 : 1;
        }

        $ingredients = $this->pizzaIngredients->map(static function(PizzaIngredient $pizzaIngredient) {
            return $pizzaIngredient->getIngredient();
        });

        $orders = $this->pizzaIngredients->map(static function(PizzaIngredient $pizzaIngredient) {
            return $pizzaIngredient->getOrder();
        });

        // Add the ingredient only if it is not already present and if its order is available
        if (!$ingredients->contains($ingredient) && !$orders->contains($order)) {
            $this->pizzaIngredients->add((new PizzaIngredient)
                ->setPizza($this)
                ->setIngredient($ingredient)
                ->setOrder($order));
        }

        return $this;
    }

    /**
     * @param Ingredient[] $ingredients
     */
    public function setIngredients(array $ingredients): Pizza
    {
        $this->pizzaIngredients->clear();

        foreach ($ingredients as $ingredient) {
            $this->addIngredient($ingredient);
        }

        return $this;
    }

    public function getPrice(): float
    {
        $ingredientCosts = $this->pizzaIngredients->map(static function(PizzaIngredient $pizzaIngredient) {
            return $pizzaIngredient->getIngredient()->getCost();
        })->toArray();

        return array_sum($ingredientCosts) * self::PREPARATION_COST_FACTOR;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->slug;
    }
}
