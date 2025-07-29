<?php

namespace E02Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', TextType::class, [
                'label' => 'Message',
                'attr' => ['placeholder' => 'Enter your message...']
            ])
            ->add('includeTimestamp', ChoiceType::class, [
                'label' => 'Include timestamp',
                'choices' => [
                    'No' => 'No',
                    'Yes' => 'Yes'
                ],
                'data' => 'No',
                'expanded' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit Message'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
