<?php

namespace App\Serializer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ErrorNormalizer implements NormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var FlattenException $exception */
        $exception = $object;

        return [
            'error' => Response::HTTP_NOT_FOUND === $exception->getStatusCode() ?
                'The resource does not exit' : $exception->getMessage()
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof FlattenException;
    }
}
