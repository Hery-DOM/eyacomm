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
                'label' => 'Nom de la société',
                'empty_data' => ' '
            ])
            ->add('firstname', TextType::class,[
                'label' => 'Prénom du contact',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('lastname', TextType::class,[
                'label' => 'Nom du contact',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('email', TextType::class,[
                'label' => 'Email',
                'empty_data' => ' '
            ])
            ->add('address', TextType::class,[
                'label' => 'Adresse',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('phone', TextType::class,[
                'label' => 'Téléphone',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('mobile', TextType::class,[
                'label' => 'Mobile',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('siret', TextType::class,[
                'label' => 'SIRET',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('ape', TextType::class,[
                'label' => 'APE',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('iban', TextType::class,[
                'label' => 'IBAN',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('bic', TextType::class,[
                'label' => 'BIC',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('banque', TextType::class,[
                'label' => 'Banque',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('sepa', TextType::class,[
                'label' => 'SEPA',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('date_signature', DateType::class,[
                'widget' => 'single_text',
                'label' => 'Date de la signature',
                'required' => false
            ])
            ->add('ref', TextType::class,[
                'label' => 'Référence',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('offre', TextareaType::class,[
                'label' => 'Offre',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('htlocam', TextType::class,[
                'label' => 'HTLOCAM',
                'required' => false,
                'empty_data' => ' '
            ])
            ->add('dtfinlocam', DateType::class,[
                'widget' => 'single_text',
                'label' => 'DTFINLOCAM',
                'required' => false
            ])
            ->add('ht', TextType::class,[
                'label' => 'HT par mois',
                'required' => false,
                'empty_data' => ' '
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
