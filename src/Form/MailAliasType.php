<?php

namespace App\Form;

use App\Entity\Domain;
use App\Entity\MailAlias;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailAliasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('source')
            ->add('destination')
            ->add('domain', EntityType::class, [
                'class' => Domain::class,
                'choice_label' => 'domainName',
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
