<?php

namespace App\Form;

use App\Entity\Domain;
use App\Entity\MailAlias;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MailAliasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('source', TextType::class, [
                'label' => 'Source Address',
                'attr' => [
                    'placeholder' => 'alias@example.com',
                    'maxlength' => 180
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a source address',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address',
                    ]),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'Source address cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('destination', TextType::class, [
                'label' => 'Destination Address',
                'attr' => [
                    'placeholder' => 'user@example.com',
                    'maxlength' => 180
                ],
                'help' => 'Multiple email addresses can be separated by commas',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter at least one destination address',
                    ]),
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'Destination addresses cannot be longer than {{ limit }} characters total',
                    ]),
                    new Callback(function($value, ExecutionContext $context) {
                        $emails = array_map('trim', explode(',', $value));
                        foreach ($emails as $email) {
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $context->buildViolation('The email address "{{ email }}" is not valid')
                                    ->setParameter('{{ email }}', $email)
                                    ->addViolation();
                            }
                        }
                    }),
                ],
            ])
            ->add('domain', EntityType::class, [
                'class' => Domain::class,
                'choice_label' => 'domainName',
                'placeholder' => 'Select a domain',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a domain',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MailAlias::class,
        ]);
    }
}
