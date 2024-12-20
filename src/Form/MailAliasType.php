<?php

namespace App\Form;

use App\Entity\MailAlias;
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
                    new Assert\Email([
                        'message' => 'Please enter a valid email address',
                    ]),
                    new Length([
                        'max' => 254,
                        'maxMessage' => 'Source address cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('destination', TextType::class, [
                'label' => 'Destination Addresses',
                'attr' => [
                    'placeholder' => 'user1@example.com, user2@example.com',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter at least one destination address',
                    ]),
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        $emails = array_map('trim', explode(',', $value));
                        foreach ($emails as $email) {
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $context->buildViolation('The email {{ value }} is not a valid email address.')
                                    ->setParameter('{{ value }}', $email)
                                    ->addViolation();
                            }
                        }
                    }),
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
