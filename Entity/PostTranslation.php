<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Gedmo\Mapping\Annotation as Gedmo;
use Ok99\PrivateZoneCore\ClassificationBundle\Entity\Tag;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *    @ORM\UniqueConstraint(name="post_translation_unique_idx", columns={"locale", "object_id"})
 * })
 */
class PostTranslation extends AbstractTranslation
{

    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $locale
     *
     * @ORM\Column(type="string", length=8)
     */
    protected $locale;

    /**
    * @ORM\ManyToOne(targetEntity="Post", inversedBy="translations")
    * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
    */
    protected $object;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $abstract;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $summary;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $authorTitle;
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $slug;

    public function __construct($locale = null, $title = null, $abstract = null, $content = null)
    {
        $this->locale = $locale;
        $this->title = $title;
        $this->abstract = $abstract;
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $slugGen = new Slugify();

        $this->setSlug($slugGen->slugify($title));
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * {@inheritdoc}
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return PostTranslation
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return PostTranslation
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set authorTitle
     *
     * @param string $authorTitle
     * @return PostTranslation
     */
    public function setAuthorTitle($authorTitle)
    {
        $this->authorTitle = $authorTitle;

        return $this;
    }

    /**
     * Get authorTitle
     *
     * @return string
     */
    public function getAuthorTitle()
    {
        return $this->authorTitle;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return PostTranslation
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set object
     *
     * @param \Ok99\PrivateZoneCore\NewsBundle\Entity\Post $object
     * @return PostTranslation
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return \Ok99\PrivateZoneCore\NewsBundle\Entity\Post
     */
    public function getObject()
    {
        return $this->object;
    }
}
