<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Entity;

use Sonata\NewsBundle\Entity\BaseComment as BaseComment;
use Doctrine\ORM\Mapping as ORM;
use Sonata\NewsBundle\Model\PostInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="news__comment")
 */
class Comment extends BaseComment
{
    /**
     * @var integer $id
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
     */
    protected $post;

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
     * Set post
     *
     * @param \Ok99\PrivateZoneCore\NewsBundle\Entity\Post $post
     * @return Comment
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Ok99\PrivateZoneCore\NewsBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }
}
