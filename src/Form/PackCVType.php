<?php

namespace App\Form;

use App\Entity\PackCV;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as TP;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PackCVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intitule',TP\TextType::class,[
                'attr' => ['class' => 'form-control mt-3'],
                'label' => 'Intitule'
            ])
            ->add('prix',TP\TextType::class,[
                'attr' => ['class' => 'form-control mt-3'],
                'label' => 'Prix'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PackCV::class,
        ]);
    }
}
