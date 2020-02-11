<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
                'label' => 'Prénom du contact'
            ])
            ->add('lastname', TextType::class,[
                'label' => 'Nom du contact'
            ])
            ->add('email', TextType::class,[
                'label' => 'Email'
            ])
            ->add('address', TextType::class,[
                'label' => 'Adresse'
            ])
            ->add('phone', TextType::class,[
                'label' => 'Téléphone'
            ])
            ->add('mobile', TextType::class,[
                'label' => 'Mobile'
            ])
            ->add('siret', TextType::class,[
                'label' => 'SIRET'
            ])
            ->add('ape', TextType::class,[
                'label' => 'APE'
            ])
            ->add('iban', TextType::class,[
                'label' => 'IBAN'
            ])
            ->add('bic', TextType::class,[
                'label' => 'BIC'
            ])
            ->add('banque', TextType::class,[
                'label' => 'Banque'
            ])
            ->add('sepa', TextType::class,[
                'label' => 'SEPA'
            ])
            ->add('date_signature', DateType::class,[
                'widget' => 'single_text',
                'label' => 'Date de la signature'
            ])
            ->add('ref', TextType::class,[
                'label' => 'Référence'
            ])
            ->add('offre', TextType::class,[
                'label' => 'Offre'
            ])
            ->add('htlocam', TextType::class,[
                'label' => 'HTLOCAM'
            ])
            ->add('dtfinlocam', DateType::class,[
                'widget' => 'single_text',
                'label' => 'DTFINLOCAM'
            ])
            ->add('ht', TextType::class,[
                'label' => 'HT par mois'
            ])
            ->add('date_portabilite', DateType::class,[
                'widget' => 'single_text',
                'label' => 'Date de la portabilité'
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
