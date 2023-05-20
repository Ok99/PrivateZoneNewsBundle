<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Ok99\PrivateZoneCore\AdminBundle\Admin\Admin as BaseAdmin;
use Ok99\PrivateZoneCore\NewsBundle\Entity\Post;
use Ok99\PrivateZoneCore\UserBundle\Entity\User;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class PostAdmin extends BaseAdmin
{
    public static $ROLE_ADMIN = 'ROLE_OK99_PRIVATEZONE_NEWS_ADMIN_POST_ADMIN';
    public static $ROLE_EDITOR = 'ROLE_OK99_PRIVATEZONE_NEWS_ADMIN_POST_EDITOR';

    protected $baseRouteName = 'admin_privatezonecore_news_post';

    protected $baseRoutePattern = 'klub/aktuality';

    protected $datagridValues = [
        '_page' => 1,
        '_sort_by' => 'publishDate',
        '_sort_order' => 'desc',
    ];

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);
        return $actions;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->remove('export');
        $collection->remove('acl');
        $collection->remove('history');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $clubConfigurationPool = $this->getConfigurationPool()->getContainer()->get('ok99.privatezone.club_configuration_pool');

        $formMapper
            ->with('General', array(
                    'class' => 'col-md-8'
                ))
                ->add('title', null, array(
                    'required' => true,
                ))
                ->add('perex', 'textarea', array(
                    'required' => true,
                    'help' => 'form.help_perex',
                    'attr' => [
                        'rows' => '2',
                    ]
                ))
                ->add('content', 'ckeditor', array(
                    'config_name' => 'news',
                    'required' => true,
                ))
            ->end()
            ->with('Options', array(
                'class' => 'col-md-4'
            ))
        ;

        if ($clubConfigurationPool->hasNewsPublishableOnWeb()) {
            $formMapper
                ->add('publishOnWeb', null, array(
                    'required' => false,
                    'label' => 'form.label_publish_on_web',
                ))
                ->add('publishInternally', null, array(
                    'required' => false,
                    'label' => 'form.label_publish_internally',
                ));
        } else {
            $formMapper
                ->add('publishInternally', null, array(
                    'required' => false,
                    'label' => 'form.label_publish',
                ));
        }

        $formMapper
            ->add('publishDate', 'ok99_privatezone_type_datetime_picker', array(
                'required' => false,
                'dp_use_seconds' => false,
                'dp_side_by_side' => true,
                'format' => 'yyyy-MM-dd HH:mm',
                'label' => 'form.label_publish_date',
            ))

            ->add('stayOnTop', null, array(
                'required' => false,
                'label' => 'form.label_stay_on_top',
            ))

            ->add('image', 'sonata_type_model_list', array('required' => false), array(
                'placeholder' => 'No image selected',
                'link_parameters' => array(
                    'context' => 'news',
                    'category' => 13,
                    'provider' => 'sonata.media.provider.image',
                ),
                'admin_code' => 'ok99.privatezone.media.admin.media',
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
        ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $clubConfigurationPool = $this->getConfigurationPool()->getContainer()->get('ok99.privatezone.club_configuration_pool');

        $datagridMapper
            ->add('title');

        if ($clubConfigurationPool->hasNewsPublishableOnWeb()) {
            $datagridMapper
                ->add('publishInternally', null, [
                    'label' => 'filter.label_publish_internally',
                ])
                ->add('publishOnWeb', null, [
                    'label' => 'filter.label_publish_on_web',
                ]);
        } else {
            $datagridMapper
                ->add('publishInternally', null, [
                    'label' => 'filter.label_publish',
                ]);
        }

        $datagridMapper
            ->add('stayOnTop');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $clubConfigurationPool = $this->getConfigurationPool()->getContainer()->get('ok99.privatezone.club_configuration_pool');

        $listMapper
            ->add('custom', 'string', array('template' => 'Ok99PrivateZoneNewsBundle:Admin:list_post_custom.html.twig', 'label' => 'Post'));

        if ($clubConfigurationPool->hasNewsPublishableOnWeb()) {
            $listMapper
                ->add('publishInternally', null, [
                    'label' => 'list.label_publish_internally',
                ])
                ->add('publishOnWeb', null, [
                    'label' => 'list.label_publish_on_web',
                ]);
        } else {
            $listMapper
                ->add('publishInternally', null, [
                    'label' => 'list.label_publish',
                ]);
        }

        $listMapper
            ->add('publishDate')
            ->add('stayOnTop')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
    }

    /**
     * @param PermalinkInterface $permalinkGenerator
     */
    public function setPermalinkGenerator($permalinkGenerator)
    {
        $this->permalinkGenerator = $permalinkGenerator;
    }

    /**
     * @inheritdoc
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        if (
            !$this->isGranted(self::$ROLE_ADMIN) &&
            !$this->isGranted('ROLE_SUPER_ADMIN')
        ) {
            $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
            $query
                ->andWhere($query->getRootAlias() . '.createdBy = :user')
                ->setParameter('user', $user)
            ;
        }

        return $query;
    }

    public function isAdmin($object = null)
    {
        return
            $this->isGranted('ROLE_SUPER_ADMIN') ||
            $this->isGranted(self::$ROLE_ADMIN) ||
            ($object ? $this->isGranted('ADMIN', $object) : $this->isGranted('ADMIN'));
    }

    /**
     * {@inheritdoc}
     * @var Post $object
     */
    public function isGranted($name, $object = null)
    {
        $isAdmin = false;

        switch($name) {
            case 'ROLE_SUPER_ADMIN':
                break;
            default:
                $isAdmin =
                    (!$object && parent::isGranted('ADMIN'))
                    ||
                    ($object && parent::isGranted('ADMIN', $object))
                    ||
                    parent::isGranted(self::$ROLE_ADMIN)
                    ||
                    parent::isGranted('ROLE_SUPER_ADMIN');

                if ($isAdmin) {
                    return true;
                }
        }

        if (!is_null($object) && !$isAdmin) {
            $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
            if (
                $object->getCreatedBy() === null ||
                $user->getId() == $object->getCreatedBy()->getId()
            ) {
                return true;
            }
        }

        if ($isAdmin && in_array(is_string($name) ? strtoupper($name) : $name, array('EDIT','DELETE'))) {
            return true;
        }

        return parent::isGranted($name, $object);
    }

    /**
     * @param \Ok99\PrivateZoneCore\NewsBundle\Entity\Post $object
     * @return void
     */
    public function prePersist($object)
    {
        parent::prePersist($object);

        /** @var User $user */
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $object->setCreatedBy($user);

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
     * @param \Ok99\PrivateZoneCore\NewsBundle\Entity\Post $object
     * @return void
     */
    public function preUpdate($object)
    {
        parent::preUpdate($object);

        /** @var User $user */
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $object->setUpdatedBy($user);

        foreach ($object->getPostHasImages() as $phi) {
            $phi->setPost($object);
        }

        foreach ($object->getPostHasFiles() as $phf) {
            $phf->setPost($object);
        }
    }
}
