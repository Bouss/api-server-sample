<?php

namespace App\Tests\Entity;

use App\Entity\Ingredient;
use App\Entity\Pizza;
use App\Entity\PizzaIngredient;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PizzaTest extends TestCase
{
    use ProphecyTrait;

    public function testGetPriceReturnsTheCorrectPrice(): void
    {
        $tomato = $this->prophesize(Ingredient::class);
        $slicedMushrooms = $this->prophesize(Ingredient::class);
        $fetaCheese = $this->prophesize(Ingredient::class);
        $sausages = $this->prophesize(Ingredient::class);
        $slicedOnion = $this->prophesize(Ingredient::class);
        $mozzarellaCheese = $this->prophesize(Ingredient::class);
        $oregano = $this->prophesize(Ingredient::class);

        // Given
        $tomato->getCost()->willReturn(0.5);
        $slicedMushrooms->getCost()->willReturn(0.5);
        $fetaCheese->getCost()->willReturn(1);
        $sausages->getCost()->willReturn(1);
        $slicedOnion->getCost()->willReturn(0.5);
        $mozzarellaCheese->getCost()->willReturn(0.5);
        $oregano->getCost()->willReturn(1);

        $pizza = (new Pizza)
            ->setIngredients([
                $tomato->reveal(),
                $slicedMushrooms->reveal(),
                $fetaCheese->reveal(),
                $sausages->reveal(),
                $slicedOnion->reveal(),
                $mozzarellaCheese->reveal(),
                $oregano->reveal()
            ]);

        // When
        $price = $pizza->getPrice();

        // Then
        self::assertEquals(7.5, $price);
    }

    public function testAddIngredientRespectsTheOrder(): void
    {
        $tomato = $this->prophesize(Ingredient::class);
        $fetaCheese = $this->prophesize(Ingredient::class);
        $slicedMushrooms = $this->prophesize(Ingredient::class);

        // Given
        $pizza = (new Pizza);

        // When
        $pizza
            ->addIngredient($tomato->reveal())
            ->addIngredient($fetaCheese->reveal(), 3)
            ->addIngredient($slicedMushrooms->reveal(), 2);

        // Then
        $pizzaIngredients = $pizza->getPizzaIngredients();
        self::assertEquals(3, $pizzaIngredients->count());
        /** @var PizzaIngredient $i1 */
        $i1 = $pizzaIngredients->get(0);
        /** @var PizzaIngredient $i2 */
        $i2 = $pizzaIngredients->get(1);
        /** @var PizzaIngredient $i3 */
        $i3 = $pizzaIngredients->get(2);
        self::assertEquals($tomato->reveal(), $i1->getIngredient());
        self::assertEquals(1, $i1->getOrder());
        self::assertEquals($fetaCheese->reveal(), $i2->getIngredient());
        self::assertEquals(3, $i2->getOrder());
        self::assertEquals($slicedMushrooms->reveal(), $i3->getIngredient());
        self::assertEquals(2, $i3->getOrder());
    }
}
