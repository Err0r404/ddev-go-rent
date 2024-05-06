<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class PasswordRequirements extends Compound
{
    
    /**
     * @inheritDoc
     */
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(groups: ['CreatePlainPassword']),
            new Assert\NotCompromisedPassword(groups: ['CreatePlainPassword', 'EditPlainPassword']),
            new Assert\Length(
                min: 12,
                minMessage: 'Password must be at least {{ limit }} characters long',
                groups: ['CreatePlainPassword', 'EditPlainPassword']
            ),
            new Assert\Regex(
                pattern: '/[a-z]/',
                message: 'Password must contain at least one lowercase letter',
                groups: ['CreatePlainPassword', 'EditPlainPassword']
            ),
            new Assert\Regex(
                pattern: '/[A-Z]/',
                message: 'Password must contain at least one uppercase letter',
                groups: ['CreatePlainPassword', 'EditPlainPassword']
            ),
            new Assert\Regex(
                pattern: '/[0-9]/',
                message: 'Password must contain at least one digit',
                groups: ['CreatePlainPassword', 'EditPlainPassword']
            ),
            new Assert\Regex(
                pattern: '/[^a-zA-Z0-9]/',
                message: 'Password must contain at least one special character',
                groups: ['CreatePlainPassword', 'EditPlainPassword']
            ),
        ];
    }
}