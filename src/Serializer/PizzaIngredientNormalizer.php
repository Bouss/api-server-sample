<?php

namespace App\Serializer;

use App\Entity\PizzaIngredient;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PizzaIngredientNormalizer implements ContextAwareNormalizerInterface
{
    public function __construct(
        private ObjectNormalizer $normalizer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        // Flatten 'ingredient' array
        $data = array_merge($data, $data['ingredient']);
        unset($data['ingredient']);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof PizzaIngredient;
    }
}
