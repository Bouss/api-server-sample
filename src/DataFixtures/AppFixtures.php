<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Pizza;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadIngredients($manager);
        $this->loadPizzas($manager);
    }

    private function loadIngredients(ObjectManager $manager): void
    {
        foreach ($this->getIngredientData() as [$slug, $cost, $translations]) {
            $ingredient = (new Ingredient())
                ->setSlug($slug)
                ->setCost($cost);

            foreach ($translations as $locale => $translation) {
                $ingredient->translate($locale)->setName($translation);
            }

            $ingredient->mergeNewTranslations();
            $manager->persist($ingredient);
            $this->addReference($slug, $ingredient);
        }

        $manager->flush();
    }

    private function loadPizzas(ObjectManager $manager): void
    {
        foreach ($this->getPizzaData() as [$slug, $ingredients, $translations]) {
            $pizza = (new Pizza())
                ->setSlug($slug);

            foreach ($ingredients as $ingredientSlug) {
                /** @var Ingredient $ingredient */
                $ingredient = $this->getReference($ingredientSlug);
                $pizza->addIngredient($ingredient);
            }

            foreach ($translations as $locale => $translation) {
                $pizza->translate($locale)->setName($translation);
            }

            $pizza->mergeNewTranslations();
            $manager->persist($pizza);
        }

        $manager->flush();
    }

    private function getIngredientData(): array
    {
        return [
            ['tomato', 0.5, ['en' => 'Tomato', 'fr' => 'Tomate']],
            ['sliced-mushrooms', 0.5, ['en' => 'Sliced mushrooms', 'fr' => 'Champignons en rondelles']],
            ['feta-cheese', 1, ['en' => 'Feta cheese', 'fr' => 'Feta']],
            ['sausages', 1, ['en' => 'Sausages', 'fr' => 'Saucissees']],
            ['sliced-onion', 0.5, ['en' => 'Sliced onion', 'fr' => 'Oignons en rondelles']],
            ['mozzarella-cheese', 0.5, ['en' => 'Mozzarella cheese', 'fr' => 'Mozzarella']],
            ['oregano', 1, ['en' => 'Oregano', 'fr' => 'Origan']],
            ['bacon', 1, ['en' => 'Bacon', 'fr' => 'Bacon']]
        ];
    }

    private function getPizzaData(): array
    {
        return [
            ['fun', ['tomato', 'sliced-mushrooms', 'feta-cheese', 'sausages', 'sliced-onion', 'mozzarella-cheese', 'oregano'], ['en' => 'Fun', 'fr' => 'Fun']],
            ['super-mushroom', ['tomato', 'bacon', 'mozzarella-cheese', 'sliced-onion', 'oregano'], ['en' => 'Super Mushroom', 'fr' => 'Super Champignons']]
        ];
    }
}
