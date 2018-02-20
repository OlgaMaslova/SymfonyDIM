<?php

namespace AppBundle\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => false])
            ->add('category', EntityType::class, array(
            // query choices from this entity
                'class' => Category::class,
                'choice_label' => 'name',
            ))
            ->add('abstract')
            ->add('country', CountryType::class, ['preferred_choices'=>array('FR', 'US')]) //preferred countries
            ->add('releaseDate', DateType::class)
            ->add('mainPicture', FileType::class, ['label'=>'Main Picture'])
        ;
    }
}