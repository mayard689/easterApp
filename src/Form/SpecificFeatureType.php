<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Feature;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecificFeatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('day')
            ->add('description')
            ->add('category', EntityType::class, ['class' => Category::class, 'choice_label' => 'name'])
            ->add('isHigh', CheckboxType::class, ['mapped' => false, 'required' => false, 'label' => 'High'])
            ->add('isMiddle', CheckboxType::class, ['mapped' => false, 'required' => false, 'label' => 'Middle'])
            ->add('isLow', CheckboxType::class, ['mapped' => false, 'required' => false, 'label' => 'Low']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feature::class,
        ]);
    }
}
