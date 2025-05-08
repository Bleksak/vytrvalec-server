<?php

declare(strict_types=1);

namespace App\Validation;

use Symfony\Component\Form\FormInterface;

final class FormErrors
{
    /**
     * @param FormInterface<mixed> $form
     *
     * @return array<string, array<int, string>>
     */
    public static function collect(FormInterface $form): array
    {
        $errors = [];
        foreach ($form as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = [];
            }

            foreach ($child->getErrors(true) as $error) {
                $errors[$child->getName()][] = $error->getMessage();
            }
        }

        return $errors;
    }
}
