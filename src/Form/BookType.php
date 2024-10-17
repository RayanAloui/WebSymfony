<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
            ])
            ->add('publicationDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Publication date',
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Science Fiction' => 'Science Fiction',
                    'Mystery' => 'Mystery',
                    'Autobiography' => 'Autobiography',
                    'Romance' => 'Romance',
                ],
                'placeholder' => 'Select a category',
            ])
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name', // Affiche le nom de l'auteur
                'data' => $options['data']->getAuthor(), // Récupère l'auteur actuel
            ])
            ->add('nbrPages')
            ->add('save',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}

?>