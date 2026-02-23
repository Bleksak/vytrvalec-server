<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @type AnonymizedUserDtoType = array{firstName: string, lastName: null|string, anonymize: null|bool}
 */
final readonly class AnonymizedUser
{
    public ?string $lastName;
    public ?bool $anonymize;

    public function __construct(
        public string $firstName,
        ?string $lastName,
        ?bool $anonymize,
    ) {
        $lastNameAnonymized = null;

        if ($anonymize || $anonymize === null) {
            $this->anonymize = true;
        } else {
            $this->anonymize = false;
            $lastNameAnonymized = $lastName;
        }

        $this->lastName = $lastNameAnonymized;
    }

    /**
     * @param AnonymizedUserDtoType $data
     */
    public static function fromArray(array $data): self
    {
        $anonymize = false;

        if ($data['anonymize'] ?? null) {
            $anonymize = true;
        }

        if (($data['anonymize'] ?? null) === null && $data['lastName']) {
            $anonymize = false;
        }

        return new self(
            $data['firstName'],
            $data['lastName'] ?? null,
            $anonymize,
        );
    }

    /**
     * @return AnonymizedUserDtoType
     */
    public function toArray(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'anonymize' => $this->anonymize,
        ];
    }
}
