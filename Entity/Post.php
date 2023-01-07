<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Ok99\PrivateZoneCore\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\ClassificationBundle\Model\CollectionInterface;

/**
 * @ORM\Table(name="news")
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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    protected $perex;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    protected $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @ORM\Column(name="publish_internally", type="boolean")
     */
    protected $publishInternally = false;

    /**
     * @ORM\Column(name="publish_on_web", type="boolean")
     */
    protected $publishOnWeb = false;

    /**
     * @ORM\Column(name="publish_date", type="datetime", nullable=true)
     */
    protected $publishDate;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Ok99\PrivateZoneCore\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id", onDelete="RESTRICT", nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Ok99\PrivateZoneCore\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $updatedBy;

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


    public function __construct()
    {
        $this->postHasImages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->postHasFiles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setPublishDate(new \DateTime);
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
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
    public function setPerex($perex)
    {
        $this->perex = $perex;
    }

    /**
     * {@inheritdoc}
     */
    public function getPerex()
    {
        return $this->perex;
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
        if (!$this->getPublishDate()) {
            $this->setPublishDate(new \DateTime);
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        if (!$this->getPublishDate()) {
            $this->setPublishDate(new \DateTime);
        }
    }

    /**
     * Set publishInternally
     *
     * @param boolean $publishInternally
     * @return Post
     */
    public function setPublishInternally($publishInternally)
    {
        $this->publishInternally = $publishInternally;

        return $this;
    }

    /**
     * Get publishInternally
     *
     * @return boolean
     */
    public function getPublishInternally()
    {
        return $this->publishInternally;
    }

    /**
     * Set publishOnWeb
     *
     * @param boolean $publishOnWeb
     * @return Post
     */
    public function setPublishOnWeb($publishOnWeb)
    {
        $this->publishOnWeb = $publishOnWeb;

        return $this;
    }

    /**
     * Get publishOnWeb
     *
     * @return boolean
     */
    public function getPublishOnWeb()
    {
        return $this->publishOnWeb;
    }

    /**
     * Set publishDate
     *
     * @param \DateTime $publishDate
     * @return Post
     */
    public function setPublishDate(\DateTime $publishDate = null)
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    /**
     * Get publishDate
     *
     * @return \DateTime
     */
    public function getPublishDate()
    {
        return $this->publishDate;
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
     * Set createdBy
     *
     * @param User $createdBy
     * @return Post
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User|int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
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
     * Set updatedBy
     *
     * @param User|null $updatedBy
     * @return Post
     */
    public function setUpdatedBy(?User $updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return User|int
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
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
    public function getYear()
    {
        return $this->getPublishDate()->format('Y');
    }

    /**
     * {@inheritdoc}
     */
    public function getMonth()
    {
        return $this->getPublishDate()->format('m');
    }

    /**
     * {@inheritdoc}
     */
    public function getDay()
    {
        return $this->getPublishDate()->format('d');
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
