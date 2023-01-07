<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="news_has_file")
 * @ORM\Entity
 */
class PostHasFile
{

    /**
     * @var integer $id
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer $position
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="postHasFiles")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $post;

    /**
     * @ORM\ManyToOne(targetEntity="Ok99\PrivateZoneCore\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $media;

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
     * Set position
     *
     * @param integer $position
     * @return PropertyHasFile
     */
    public function setPosition($position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set post
     *
     * @param Post $post
     * @return PostHasFile
     */
    public function setPost(Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Ok99\PrivateZoneCore\MediaBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set post
     *
     * @param \Ok99\PrivateZoneCore\MediaBundle\Entity\Media $media
     * @return PropertyHasMedia
     */
    public function setMedia(\Ok99\PrivateZoneCore\MediaBundle\Entity\Media $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Ok99\PrivateZoneCore\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }
}
