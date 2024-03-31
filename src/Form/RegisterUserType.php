<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Pseudo",
                'label_attr' => [
                    'class' => 'd-block text-start'
                ],
                'attr' => ['class' => 'form-control mb-3', 'placeholder' => "Votre pseudo"],
                'required' => true
            ])
            ->add('email', EmailType::class,[
                'label' => "Adresse email",
                'label_attr' => [
                    'class' => 'd-block text-start'
                ],
                'attr' => ['class' => 'form-control mb-3', 'placeholder' => "Votre email"],
                'required' => true
            ])
            ->add('roles', ChoiceType::class, [
                'label' => "Rôle",
                'label_attr' => [
                    'class' => 'd-block text-start'
                ],
                'attr' => ['class' => 'form-control mb-3'],
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Utilisateur standart' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de passe', 'hash_property_path' => 'password', 'attr' => ['class' => 'form-control mb-3'], 'label_attr' => [
                    'class' => 'd-block text-start'],
                ],
                'second_options' => ['label' => 'Confirmez le mot de passe', 'attr' => ['class' => 'form-control mb-3'], 'label_attr' => [
                    'class' => 'd-block text-start'],
                ],
                'attr' => ['class' => 'form-control mb-3', 'placeholder' => "Votre mot de passe"],
                'required' => true,
                'mapped' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Créer l'utilisateur",
                'attr' => [
                    'class' => 'btn rounded-pill btn-light bg-done-title mx-auto my-2 w-75 w-md-50'
                ]
            ])
        ;
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email'
                ])
            ],
            'data_class' => User::class,
        ]);
    }
}
