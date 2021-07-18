<?php

namespace App\Form;

use App\Entity\Constructeur;
use App\Entity\Telephone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TelephoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modelName')
            ->add('constructeur',EntityType::class,["class" => Constructeur::class,"choice_label" =>"name"])
            ->add('price')
            ->add('description')
            ->add('stockage')
            ->add('image', FileType::class, [
                'label' => 'image du téléphone',
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3072k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => "Merci d'envoyer une vraie image",
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Telephone::class,
        ]);
    }
}
