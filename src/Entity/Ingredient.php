<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"slug"}, errorPath="slug", message="An ingredient with this slug already exists")
 */
class Ingredient implements Stringable, TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_ingredient", "api_pizza"})
     */
    private int $id;

    /**
     * "slug" instead of "name": Human-readable technical identifier (back-side). The name (front-side) is translated in IngredientTranslation
     *
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message="Slug cannot be blank")
     * @Assert\Regex(pattern="/^[-a-z]+$/", message="Slug must be a lowercase slug")
     * @Groups({"api_ingredient", "api_pizza"})
     */
    private $slug;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Cost cannot be blank")
     * @Assert\Type(type="float", message="Cost must be a float")
     * @Groups({"api_ingredient", "api_pizza"})
     */
    private $cost;

    /**
     * {@inheritDoc}
     *
     * @Groups({"api_ingredient", "api_pizza"})
     */
    protected $translations;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): Ingredient
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function setCost(?float $cost): Ingredient
    {
        $this->cost = $cost;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->slug;
    }
}
