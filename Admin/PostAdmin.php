<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Admin;

use Ok99\PrivateZoneCore\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class PostAdmin extends BaseAdmin
{
    protected $translationDomain = 'SonataNewsBundle';

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('enabled')
            ->add('title')
            ->add('abstract')
            ->add('content', null, array('safe' => true))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array(
                    'class' => 'col-md-8'
                ))
                ->add('translations', 'ok99_privatezone_translations', array(
                    'translation_domain' => $this->translationDomain,
                    'label' => false,
                    'fields' => array(
                            'title' => array(),
                            'abstract' => array(
                                'field_type' => 'textarea',
                                'attr' => array(
                                    'rows' => '5',
                                )
                            ),
                            'content' => array(
                                'field_type' => 'ckeditor',
                                'config_name' => 'news'
                            ),
                            'summary' => array(),
                            'author' => array(),
                            'authorTitle' => array(),
                        ),
                    'exclude_fields' => array('slug')
                ))
            ->end()
                ->with('Options', array(
                        'class' => 'col-md-4'
                    ))
                    ->add('enabled', null, array('required' => false))
                    ->add('image', 'sonata_type_model_list', array('required' => false), array(
                        'placeholder' => 'No image selected',
                        'link_parameters' => array(
                            'context' => 'news'
                        )
                    ))

                    ->add('postHasImages', 'ok99_privatezone_type_media_collection', array(
                        'label' => 'Images',
                        'required' => false,
                        'context' => 'news',
                        'media_type' => 'image'
                    ), array(
                        'sortable' => 'position'
                    ))

                    ->add('postHasFiles', 'ok99_privatezone_type_media_collection', array(
                        'label' => 'Files',
                        'required' => false,
                        'context' => 'news',
                        'media_type' => 'file'
                    ), array(
                        'sortable' => 'position'
                    ))

                    ->add('publicationDateStart', 'ok99_privatezone_type_datetime_picker', array(
                        'dp_use_seconds' => false,
                        'dp_side_by_side' => true,
                        'format' => 'yyyy-MM-dd HH:mm'
                    ))
                    ->add('collection', 'sonata_type_model_list', array('required' => false), array(
                        'placeholder' => 'No collection selected',
                        'link_parameters' => array(
                            'context' => 'news'
                        )
                    ))
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('enabled')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('custom', 'string', array('template' => 'Ok99PrivateZoneNewsBundle:Admin:list_post_custom.html.twig', 'label' => 'Post'))
            ->add('publicationDateStart')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_edit_post', array(), 'SonataNewsBundle'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        if ($this->hasSubject() && $this->getSubject()->getId() !== null) {
            $menu->addChild(
                $this->trans('sidemenu.link_view_post'),
                array('uri' => $admin->getRouteGenerator()->generate('sonata_news_view', array('permalink' => $this->permalinkGenerator->generate($this->getSubject()))))
            );
        }
    }

    /**
     * @param PermalinkInterface $permalinkGenerator
     */
    public function setPermalinkGenerator($permalinkGenerator)
    {
        $this->permalinkGenerator = $permalinkGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        parent::prePersist($object);

        $translations = $object->getTranslations();

        foreach ($translations as $trans) {
            $trans->setObject($object);
        }

        foreach ($object->getPostHasImages() as $phi) {
            $phi->setPost($object);
        }

        foreach ($object->getPostHasFiles() as $phf) {
            $phf->setPost($object);
        }
    }

    /**
     * {@inheritdoc}
     */

    /**
     * @param \Ok99\PrivateZoneCore\PageBundle\Entity\Block $object
     * @return mixed|void
     */
    public function preUpdate($object)
    {
        parent::preUpdate($object);

        $translations = $object->getTranslations();

        foreach ($translations as $trans) {
            $trans->setObject($object);
        }

        foreach ($object->getPostHasImages() as $phi) {
            $phi->setPost($object);
        }

        foreach ($object->getPostHasFiles() as $phf) {
            $phf->setPost($object);
        }
    }
}
