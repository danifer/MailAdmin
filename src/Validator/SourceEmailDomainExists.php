<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SourceEmailDomainExists extends Constraint
{
    public string $message = 'The domain "{{ domain }}" from email "{{ email }}" is not registered in the system.';
}
