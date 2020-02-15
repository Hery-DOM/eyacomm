<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Nom du produit'
            ])
            ->add('picture',FileType::class,[
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ]
                    ])
                ]
            ])
            ->add('description', CKEditorType::class,[
                'required' => false,
                'empty_data' => ' '
            ])
             ->add('category', EntityType::class,[
                 'class' => Category::class,
                 'choice_label' => 'name',
                 'label' => 'Catégorie'
             ])
            ->add('Enregistrer', SubmitType::class)


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
