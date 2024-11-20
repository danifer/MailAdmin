<?php

namespace App\Form;

use App\Entity\Domain;
use App\Entity\MailAccount;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MailAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'attr' => [
                    'placeholder' => 'user@example.com',
                    'maxlength' => 180
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email address',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address',
                    ]),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'Email cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr' => [
                    'placeholder' => 'Enter password',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Password must be at least {{ limit }} characters long',
                    ]),
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
            'data_class' => MailAccount::class,
        ]);
    }
}
