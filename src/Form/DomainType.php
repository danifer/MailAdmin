<?php

namespace App\Form;

use App\Entity\Domain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class DomainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('domainName', TextType::class, [
                'label' => 'Domain Name',
                'attr' => [
                    'placeholder' => 'example.com',
                    'maxlength' => 255
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a domain name',
                    ]),
                    new Length([
                        'min' => 4,
                        'max' => 255,
                        'minMessage' => 'Domain name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Domain name cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/',
                        'message' => 'Please enter a valid domain name',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Domain::class,
        ]);
    }
}
