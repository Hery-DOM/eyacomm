<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class,[
                'label' => 'Nom de la société'
            ])
            ->add('firstname', TextType::class,[
                'label' => 'Prénom du contact',
                'required' => false
            ])
            ->add('lastname', TextType::class,[
                'label' => 'Nom du contact',
                'required' => false
            ])
            ->add('email', TextType::class,[
                'label' => 'Email'
            ])
            ->add('address', TextType::class,[
                'label' => 'Adresse',
                'required' => false
            ])
            ->add('phone', TextType::class,[
                'label' => 'Téléphone',
                'required' => false
            ])
            ->add('mobile', TextType::class,[
                'label' => 'Mobile',
                'required' => false
            ])
            ->add('siret', TextType::class,[
                'label' => 'SIRET',
                'required' => false
            ])
            ->add('ape', TextType::class,[
                'label' => 'APE',
                'required' => false
            ])
            ->add('iban', TextType::class,[
                'label' => 'IBAN',
                'required' => false
            ])
            ->add('bic', TextType::class,[
                'label' => 'BIC',
                'required' => false
            ])
            ->add('banque', TextType::class,[
                'label' => 'Banque',
                'required' => false
            ])
            ->add('sepa', TextType::class,[
                'label' => 'SEPA',
                'required' => false
            ])
            ->add('date_signature', DateType::class,[
                'widget' => 'single_text',
                'label' => 'Date de la signature',
                'required' => false
            ])
            ->add('ref', TextType::class,[
                'label' => 'Référence',
                'required' => false
            ])
            ->add('offre', TextareaType::class,[
                'label' => 'Offre',
                'required' => false
            ])
            ->add('htlocam', TextType::class,[
                'label' => 'HTLOCAM',
                'required' => false
            ])
            ->add('dtfinlocam', DateType::class,[
                'widget' => 'single_text',
                'label' => 'DTFINLOCAM',
                'required' => false
            ])
            ->add('ht', TextType::class,[
                'label' => 'HT par mois',
                'required' => false
            ])
            ->add('date_portabilite', DateType::class,[
                'widget' => 'single_text',
                'label' => 'Date de la portabilité',
                'required' => false
            ])
            ->add('Modifier', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
