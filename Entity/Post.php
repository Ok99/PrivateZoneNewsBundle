<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\ClassificationBundle\Model\CollectionInterface;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    protected $abstract;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $summary;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $author;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $authorTitle;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $publicationDateStart;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Ok99\PrivateZoneCore\MediaBundle\Entity\Media", cascade={"remove","persist","refresh","merge","detach"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     */
    protected $image;


    /**
     * @ORM\OrderBy({"position" = "ASC"})
     * @ORM\OneToMany(targetEntity="PostHasImage", mappedBy="post", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $postHasImages;

    /**
     * @ORM\OrderBy({"position" = "ASC"})
     * @ORM\OneToMany(targetEntity="PostHasFile", mappedBy="post", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $postHasFiles;

    /**
     * @ORM\ManyToOne(targetEntity="Ok99\PrivateZoneCore\ClassificationBundle\Entity\Collection", cascade={"persist"})
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="id", nullable=true)
     */
    protected $collection;

    /**
     * @ORM\OneToMany(targetEntity="PostTranslation", mappedBy="object", indexBy="locale", cascade={"all"}, orphanRemoval=true)
     * @Assert\Valid
     */
    private $translations;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->postHasImages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->postHasFiles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setPublicationDateStart(new \DateTime);
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
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->getPublicationDateStart()) {
            $this->setPublicationDateStart(new \DateTime);
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        if (!$this->getPublicationDateStart()) {
            $this->setPublicationDateStart(new \DateTime);
        }
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Post
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set publicationDateStart
     *
     * @param \DateTime $publicationDateStart
     * @return Post
     */
    public function setPublicationDateStart(\DateTime $publicationDateStart = null)
    {
        $this->publicationDateStart = $publicationDateStart;

        return $this;
    }

    /**
     * Get publicationDateStart
     *
     * @return \DateTime
     */
    public function getPublicationDateStart()
    {
        return $this->publicationDateStart;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Post
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set image
     *
     * @param \Ok99\PrivateZoneCore\MediaBundle\Entity\Media $image
     * @return Post
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Ok99\PrivateZoneCore\MediaBundle\Entity\Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function setCollection(CollectionInterface $collection = null)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getYear()
    {
        return $this->getPublicationDateStart()->format('Y');
    }

    /**
     * {@inheritdoc}
     */
    public function getMonth()
    {
        return $this->getPublicationDateStart()->format('m');
    }

    /**
     * {@inheritdoc}
     */
    public function getDay()
    {
        return $this->getPublicationDateStart()->format('d');
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(PostTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setObject($this);
        }

        return $this;
    }

    public function removeTranslation(PostTranslation $translation)
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
        }
        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Post
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
     * @return Post
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
     * @return Post
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Add postHasImages
     *
     * @param \Ok99\PrivateZoneCore\NewsBundle\Entity\PostHasImage $postHasImages
     * @return Post
     */
    public function addPostHasImage(\Ok99\PrivateZoneCore\NewsBundle\Entity\PostHasImage $postHasImage)
    {
        $this->postHasImages[] = $postHasImage;

        return $this;
    }

    /**
     * Remove postHasImages
     *
     * @param \Ok99\PrivateZoneCore\NewsBundle\Entity\PostHasImage $postHasImages
     */
    public function removePostHasImage(\Ok99\PrivateZoneCore\NewsBundle\Entity\PostHasImage $postHasImage)
    {
        $this->postHasImages->removeElement($postHasImage);
    }

    /**
     * Get postHasImages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPostHasImages()
    {
        return $this->postHasImages;
    }

    /**
     * Add postHasFiles
     *
     * @param \Ok99\PrivateZoneCore\NewsBundle\Entity\PostHasFile $postHasFile
     * @return Post
     */
    public function addPropertyHasFile(\Ok99\PrivateZoneCore\NewsBundle\Entity\PostHasFile $postHasFile)
    {
        $this->postHasFiles[] = $postHasFile;

        return $this;
    }

    /**
     * Remove postHasFiles
     *
     * @param \Ok99\PrivateZoneCore\NewsBundle\Entity\PostHasFile $postHasFile
     */
    public function removePostHasFile(\Ok99\PrivateZoneCore\NewsBundle\Entity\PostHasFile $postHasFile)
    {
        $this->postHasFiles->removeElement($postHasFile);
    }

    /**
     * Get postHasFiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPostHasFiles()
    {
        return $this->postHasFiles;
    }
}
