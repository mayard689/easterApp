<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Nouveau mot de passe'),
                'second_options' => array('label' => 'Confirmer le mot de passe'),
                'invalid_message' => 'Les 2 mots de passe ne sont pas identiques.',
                'constraints' => [
                    new NotBlank(
                        ['message' => 'Merci de saisir un mot de passe']
                    ),
                    new Length(
                        [
                            'min' => 8,
                            'minMessage' => 'Le mot de passe doit contenir minimum 8 caractères'
                        ]
                    ),
                    new Regex(
                        [
                            'pattern' => '/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?\W).*$/',
                            'message' => 'Le mot de passe doit contenir au minimum 1
                                chiffre, 1 majuscule, et un caractère spécial.'
                        ]
                    )
                ],
                'help' => 'Le mot de passe doit contenir 8 caractères, dont 1
                            chiffre, 1 majuscule, et un caractère spécial.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
