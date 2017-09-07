<?php

namespace Basefony\PageBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Basefony\UserBundle\BasefonyUserBundle;

class PageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $buttonName = $options['data']->getId() > 0 ? 'Duzenle' : 'Ekle';

        $builder
            ->add('title', TextType::class, ['label' => 'Sayfa Adı', 'required' => true])
            ->add('meta_title', TextType::class, ['label' => 'Meta Title', 'required' => true])
            ->add('meta_keywords', TextType::class, ['label' => 'Meta Keywords', 'required' => true])
            ->add('meta_description', TextareaType::class, ['label' => 'Meta Description', 'required' => true])
            ->add('slug', TextType::class, ['label' => 'Bağlantı (slug)', 'required' => true])
            ->add('content', TextareaType::class, ['label' => 'Sayfa İçeriği', 'required' => false, 'attr' => ['class' => 'ckeditor']])
            /*        ->add('user', EntityType::class,
                    array(
                        'class' => 'Basefony\UserBundle\Entity\User',
                        'label' => 'Kullanici'
                    ))
                    */
            ->add('user', Select2EntityType::class, [
                'multiple' => false,
                'remote_route' => 'basefony_admin_user_search_ajax',
                'class' => 'Basefony\UserBundle\Entity\User',
                'primary_key' => 'id',
                'text_property' => 'email',
                'minimum_input_length' => 2,
                'page_limit' => 10,
                'allow_clear' => true,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000,
                'language' => 'tr',
                'placeholder' => 'Kullanici Secin',
            ])
            ->add('submit', SubmitType::class, ['label' => $buttonName, 'attr' => ['class' => 'btn btn-primary']]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Basefony\PageBundle\Entity\Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'basefony_admin_page';
    }
}
