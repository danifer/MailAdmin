<?php

namespace App\Form;

use App\Entity\Domain;
use App\Entity\MailAccount;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password')
            ->add('email')
            ->add('domain', EntityType::class, [
                'class' => Domain::class,
                'choice_label' => 'domainName',
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
