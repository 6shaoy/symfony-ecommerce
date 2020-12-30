<?php

namespace App\Form;

use App\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'nom complet',
                'attr' => [
                    'placeholder' => 'Nom complet pour la livraison.'
                ]
            ])
            ->add('address', TextareaType::class, [
                'label' => 'address complete',
                'attr' => [
                    'placeholder' => 'Address complete pour la livraison.'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'code postal',
                'attr' => [
                    'placeholder' => 'code postal pour la livraison.'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'ville',
                'attr' => [
                    'placeholder' => 'ville pour la livraison.'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class
        ]);
    }
}
