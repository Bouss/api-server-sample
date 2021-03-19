<?php

namespace App\Serializer;

use App\Entity\Ingredient;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class IngredientNormalizer implements ContextAwareNormalizerInterface
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

        // Change translations to this format: [<locale> => <name>]
        array_walk($data['translations'], static function(&$translation) {
            $translation = $translation['name'];
        });

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Ingredient;
    }
}
