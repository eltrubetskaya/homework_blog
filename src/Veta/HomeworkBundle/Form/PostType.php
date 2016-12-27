<?php

namespace Veta\HomeworkBundle\Form;

use Veta\HomeworkBundle\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Veta\HomeworkBundle\Entity\Post;
use Veta\HomeworkBundle\Entity\Tag;

class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label'=>'Title'])
            ->add('discription', TextareaType::class, ['label'=>'Discription'])
            ->add('text', TextareaType::class, ['label'=>'Text'])
            ->add('dateCreate', DateTimeType::class, ['label' => 'Date'])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Active' => true,
                    'No Active' => false,
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'veta_homeworkbundle_post';
    }
}
