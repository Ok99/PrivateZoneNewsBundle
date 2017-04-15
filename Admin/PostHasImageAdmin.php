<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Admin;

use Ok99\PrivateZoneCore\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class PostHasImageAdmin extends BaseAdmin
{
    protected $translationDomain = 'SonataNewsBundle';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->trans('form_post_has_media.group_main_label'))
            ->add('media', 'ok99_privatezone_type_image', array(), array(
                'placeholder' => 'No image selected',
                'link_parameters' => array('context' => 'news'),
            ))
            ->add('position', 'hidden')
            ->end();
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('post')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('post')
            ->addIdentifier('media')
        ;
    }
}
