<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Entity;

use Doctrine\ORM\Query;
use Sonata\NewsBundle\Entity\BasePostRepository;

class PostRepository extends BasePostRepository
{

    public function fetchInternalNewsQuery(): Query
    {
        return $this->createQueryBuilder('post')
            ->select('post, createdBy')
            ->leftJoin('post.createdBy', 'createdBy')
            ->andWhere('post.publishInternally = :publishInternally')->setParameter('publishInternally', true)
            ->andWhere('post.publishDate <= :now')->setParameter('now', new \DateTime())
            ->addOrderBy('post.publishDate', 'DESC')
            ->addOrderBy('post.createdAt', 'DESC')
            ->getQuery();
    }

}
