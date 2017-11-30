<?php

namespace JNi\TicketingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;
use JNi\TicketingBundle\Form\VisitorType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InvoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',       Type\DateType::class, ['label' => "Date de la visite", 'widget' => 'single_text'])
            ->add('halfDay',    Type\CheckboxType::class, ['label' => "Entrée à partir de 14h", 'required' => false])
            ->add('email',      Type\EmailType::class, ['label' => "Email de confirmation"])
            ->add('visitors',    CollectionType::class, [
                'label'         => "Liste des visiteurs",
                'entry_type'    => VisitorType::class,
                'allow_add'     => true,
                'allow_delete'  => true
                ]) // add form visitor
            ->add('valid',     Type\SubmitType::class, ['label' => "Valider"])
        ;    
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JNi\TicketingBundle\Entity\Invoice'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'jni_ticketingbundle_invoice';
    }


}
