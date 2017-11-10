<?php

namespace MottaPgBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PaginatorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page', HiddenType::class, array(
                'label' => 'Page:',
                'label' => 'pgform.labels.page'
            ))
            ->add('rowsPerPage', ChoiceType::class, array(
                'label' => 'Show:',
                'label' => 'pgform.labels.rows',
                'choices' => $options['rowsPerPageOptions']
            ))
            ->add('orderBy', ChoiceType::class, array(
                'label' => 'Order:',
                'label' => 'pgform.labels.order',
                'choices' => $options['orderByChoices']
            ))
            ->add('direction', ChoiceType::class, array(
                'choices' => array(
                    'Ascending'  => 'asc',
                    'Descending'  => 'desc',
                ),
                'choice_translation_domain' => true
            ))
            ->add('massIds', HiddenType::class)
            ->add('massAll', HiddenType::class)
            ->add('clean', HiddenType::class)
        ;
        // Campo opcional para formulario de búsqueda
        if (isset($options['filter']))
            $builder->add('filters', $options['filter']);

        if (isset($options['massactionForm']))
            $builder->add('massForm', $options['massactionForm']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'rowsPerPageOptions' => array(
                '10' => 10,
                '20' => 20,
                '30' => 30,
                '40' => 40,
                '50' => 50
            ),
        ));

        $resolver->setDefined(array(
            'filter',
            'rowsPerPageOptions',
            'massactionForm',
        ));
        $resolver->setRequired(array(
            'orderByChoices',
        ));
    }

    public function getBlockPrefix()
    {
        return 'mottapgbundle_paginatorformtype';
    }
}
