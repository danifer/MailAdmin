<?php

namespace App\Validator;

use App\Repository\DomainRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SourceEmailDomainExistsValidator extends ConstraintValidator
{
    public function __construct(
        private DomainRepository $domainRepository,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SourceEmailDomainExists) {
            throw new UnexpectedTypeException($constraint, SourceEmailDomainExists::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        // Extract domain from email
        $emailParts = explode('@', $value);
        if (count($emailParts) !== 2) {
            return; // Let email validator handle invalid format
        }

        $domain = $emailParts[1];
        
        // Check if domain exists
        $domainEntity = $this->domainRepository->findOneBy(['domainName' => $domain]);
        
        if (!$domainEntity) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ email }}', $value)
                ->setParameter('{{ domain }}', $domain)
                ->addViolation();
        }
    }
}
