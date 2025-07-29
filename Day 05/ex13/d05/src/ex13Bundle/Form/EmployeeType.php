<?php
namespace ex13Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use ex13Bundle\Entity\Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['required' => true])
            ->add('lastname', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('birthdate', DateType::class, [
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('active', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'required' => true,
                'choices_as_values' => true
            ])
            ->add('employed_since', DateType::class, [
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('employed_until', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('hours', ChoiceType::class, [
                'choices' => ['8' => '8', '6' => '6', '4' => '4'],
                'required' => true
            ])
            ->add('salary', IntegerType::class, ['required' => true])
            ->add('position', ChoiceType::class, [
                'choices' => [
                    'Manager' => 'manager',
                    'Account Manager' => 'account_manager',
                    'QA Manager' => 'qa_manager',
                    'Dev Manager' => 'dev_manager',
                    'CEO' => 'ceo',
                    'COO' => 'coo',
                    'Backend Dev' => 'backend_dev',
                    'Frontend Dev' => 'frontend_dev',
                    'QA Tester' => 'qa_tester',
                ],
                'required' => true,
                'choices_as_values' => true
            ])
            ->add('manager', EntityType::class, [
                'class' => 'ex13Bundle\Entity\Employee',
                'choice_label' => function ($employee) {
                    return $employee->getFirstname() . ' ' . $employee->getLastname();
                },
                'required' => false,
                'placeholder' => 'Select manager',
            ])
            ->add('save', SubmitType::class, ['label' => 'Save Employee']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
