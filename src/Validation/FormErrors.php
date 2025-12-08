<?php

declare(strict_types=1);

namespace App\Validation;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

final class FormErrors
{
    /**
     * @param FormInterface $form
     *
     * @return array<string, array<int, string>>
     */
    public static function collect(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = [];
            }

            $errorIterator = $child->getErrors(true);

            foreach ($errorIterator as $error) {
                \assert(
                    $error instanceof FormError,
                    'Error is not an iterator!',
                );
                $errors[$child->getName()][] = $error->getMessage();
            }
        }

        return $errors;
    }
}
