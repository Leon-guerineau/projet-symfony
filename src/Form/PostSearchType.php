<?php

namespace App\Form;

use App\Data\PostSearch;
use App\Entity\Game;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostSearchType extends AbstractType
{
    // Création du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Titre
            ->add('title', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => "Entrer un Titre"]
            ])
            // Auteur
            ->add('author', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => "Entrer un Pseudo"]
            ])
            // Date Maximum
            ->add('maxDate', DateType::class,[
                'label' => false,
                'required' => false,
                'format' => 'yyyy-MM-dd',
            ])
            // Date Minimum
            ->add('minDate', DateType::class,[
                'label' => false,
                'required' => false,
                'format' => 'yyyy-MM-dd',
            ])
            // Jeu
            ->add('games', EntityType::class,[
                'label' => false,
                'required' => false,
                'class' => Game::class,
                'multiple' => true,
            ])
        ;
    }

    // Options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'=> PostSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
