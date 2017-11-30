<?php

namespace JNi\TicketingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class VisitorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',   Type\TextType::class, ['label' => "Prénom"])
            ->add('lastName',    Type\TextType::class, ['label' => "Nom"])
            ->add('country',     Type\CountryType::class, ['label' => "Pays", 'placeholder' => "Sélectionnez votre pays", 'preferred_choices' => ['FR']])
            ->add('birthDate',   Type\BirthdayType::class, ['label' => 'Date de Naissance', 'widget' => 'single_text'])
            ->add('reducedRate', Type\CheckboxType::class, ['label' => "Tarif réduit (étudiant, employé du musée, d’un service du Ministère de la Culture, militaire…)", 'required' => false])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JNi\TicketingBundle\Entity\Visitor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'jni_ticketingbundle_visitor';
    }


}
