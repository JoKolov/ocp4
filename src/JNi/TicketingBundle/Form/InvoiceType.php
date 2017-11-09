<?php

namespace JNi\TicketingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;
use JNi\TicketingBundle\Form\VisitorType;
use JNi\TicketingBundle\Form\PaymentType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InvoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',       Type\DateType::class)
            ->add('halfDay',    Type\CheckboxType::class, ['required' => false])
            ->add('email',      Type\EmailType::class)
            ->add('visitors',    CollectionType::class, [
                'entry_type'    => VisitorType::class,
                'allow_add'     => true,
                'allow_delete'  => true
                ]) // add form visitor
            ->add('valid',     Type\SubmitType::class)
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
