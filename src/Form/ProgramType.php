<?php

namespace App\Form;

use App\Entity\Program;
use App\Entity\Actor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('synopsis', TextareaType::class)
            ->add('posterFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true, // not mandatory, default is true
                'download_uri' => true, // not mandatory, default is true
            ])
            ->add('country', TextType::class)
            ->add('year', NumberType::class)
            ->add('category', null, ['choice_label' => 'name'])
//            ->add('actors', EntityType::class, [
//                'class' => Actor::class,
//                'choice_label' => 'firstname',
//                'multiple' => true,
//                'expanded' => true,
//                'by_reference' => false
//            ])
            ->add('actors_input', TextType::class, [
                'required' => false, // Le champ n'est pas obligatoire
                'mapped' => false,   // Ne pas mapper le champ vers l'entité
                'attr' => [
                    'class' => 'actor-input',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
