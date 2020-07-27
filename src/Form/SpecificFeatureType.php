<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Feature;
use App\Entity\ProjectFeature;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecificFeatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
                'attr' => [
                    'class' => 'form-control name_feat',
                    'autocomplete' => 'off',
                ]
            ])
            ->add('day', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => true,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('isHigh', CheckboxType::class, [
                'required' => false,
                'label' => 'High',
                'attr' => ['class' => 'form-control mr-2']
            ])
            ->add('isMiddle', CheckboxType::class, [
                'required' => false,
                'label' => 'Middle',
                'attr' => ['class' => 'form-control mr-2']
            ])
            ->add('isLow', CheckboxType::class, [
                'required' => false,
                'label' => 'Low',
                'attr' => ['class' => 'form-control mr-2']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectFeature::class,
        ]);
    }
}
