<?php

namespace App\Validator;

use App\Entity\MailAccount;
use App\Entity\MailAlias;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EmailMatchesDomainValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EmailMatchesDomain) {
            throw new UnexpectedTypeException($constraint, EmailMatchesDomain::class);
        }

        if (!$value instanceof MailAccount && !$value instanceof MailAlias) {
            throw new UnexpectedTypeException($value, 'MailAccount or MailAlias');
        }

        $domain = $value->getDomain();
        if (!$domain) {
            return; // Domain validation will be handled by other constraints
        }

        $domainName = $domain->getDomainName();
        
        if ($value instanceof MailAccount) {
            $email = $value->getEmail();
        } else {
            $email = $value->getSource();
        }

        if (!$email) {
            return; // Email validation will be handled by other constraints
        }

        // Extract domain part from email
        $emailDomain = substr(strrchr($email, "@"), 1);
        
        if ($emailDomain !== $domainName) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ email }}', $email)
                ->setParameter('{{ domain }}', $domainName)
                ->addViolation();
        }
    }
}
