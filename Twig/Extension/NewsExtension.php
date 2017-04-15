<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Ok99\PrivateZoneCore\NewsBundle\Entity\Post;

class NewsExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    private $generator;

    /**
     * @param RouterInterface  $router
     */
    public function __construct(RouterInterface $router, $generator)
    {
        $this->router     = $router;
        $this->generator  = $generator;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'ok99_privatezone_news_link_rss' => new \Twig_Function_Method($this, 'renderRss', array('is_safe' => array('html'))),
            'ok99_privatezone_news_permalink'    => new \Twig_Function_Method($this, 'generatePermalink')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ok99_privatezone_news';
    }

    /**
     * @return string
     */
    public function renderRss()
    {
        $rss = sprintf('<link href="%s" title="%s" type="application/rss+xml" rel="alternate" />',
                $this->router->generate('sonata_news_home', array('_format' => 'rss'), true),
                'News'
            );

        return $rss;
    }

    /**
     * @param \Ok99\PrivateZoneCore\NewsBundle\Entity\Post $post
     *
     * @return string|Exception
     */
    public function generatePermalink(Post $post)
    {
        return $this->generator->generate($post);
    }
}
