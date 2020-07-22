<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AvatarUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profilePictureFile', VichImageType::class, [
                'help'         =>
                    'Les fichiers autorisés sont uniquement de type '
                    . implode(', ', User::MIME_TYPES)
                    . ' et le poids maximal de ' . strtoupper(User::MAX_SIZE) . 'o',
                'required'     => false,
                'download_uri' => false,
                'allow_delete' => false,
                'image_uri' => false,
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['AvatarUpdate']
        ]);
    }
}
