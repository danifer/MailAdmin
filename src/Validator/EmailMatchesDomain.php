<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EmailMatchesDomain extends Constraint
{
    public string $message = 'The email "{{ email }}" does not match the selected domain "{{ domain }}".';
    
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
