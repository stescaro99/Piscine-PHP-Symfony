<?php

namespace ex09Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'form-control']
            ])
            ->add('name', TextType::class, [
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank(), new Email()],
                'attr' => ['class' => 'form-control']
            ])
            ->add('enable', CheckboxType::class, [
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('birthdate', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ]);
        // Add marital status field only if the column exists
        if (isset($options['has_marital_status']) && $options['has_marital_status']) {
            $builder->add('maritalStatus', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Single' => 'single',
                    'Married' => 'married',
                    'Widower' => 'widower'
                ],
                'placeholder' => 'Select marital status',
                'attr' => ['class' => 'form-control']
            ]);
        }
        $builder->add('submit', SubmitType::class, [
            'label' => 'Create Person',
            'attr' => ['class' => 'btn btn-primary']
        ]);
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'has_marital_status' => false,
        ]);
    }
}