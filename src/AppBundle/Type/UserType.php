<?php
namespace AppBundle\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullname', TextType::class, ['label'=>'Full name'])
            ->add('username', EmailType::class, ['label'=>'Email'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password must match.',
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ])
            ->add('roles', TextType::class, ['label'=>'Roles separated by commas'])
            ->add('Save', SubmitType::class)
        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesAsArray) {
                    //From Model to view -| Array to String
                    if (!empty($rolesAsArray)) {
                        return implode(', ', $rolesAsArray);
                    }
                },
                function ($rolesAsString) {
                    //From Model to view -| String to Array
                    return explode(', ', $rolesAsString);
                }
            ))
        ;
    }
}