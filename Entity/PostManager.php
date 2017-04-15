<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Entity;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\ClassificationBundle\Model\CollectionInterface;

use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;

class PostManager extends BaseEntityManager
{
    /**
     * @param string $permalink
     *
     * @return PostInterface
     */
    public function findOneByPermalink($permalink, $permalinkGenerator)
    {
        $repository = $this->getRepository();

        $query = $repository->createQueryBuilder('p');

        $urlParameters = $permalinkGenerator->getParameters($permalink);

        $parameters = array();

        if (isset($urlParameters['year']) && isset($urlParameters['month']) && isset($urlParameters['day'])) {
            $pdqp = $this->getPublicationDateQueryParts(sprintf('%d-%d-%d', $urlParameters['year'], $urlParameters['month'], $urlParameters['day']), 'day');

            $parameters = array_merge($parameters, $pdqp['params']);

            $query->andWhere($pdqp['query']);
        }

        if (isset($urlParameters['slug'])) {
            $query->andWhere('p.slug = :slug');
            $parameters['slug'] = $urlParameters['slug'];
        }

        if (count($parameters) == 0) {
            return null;
        }

        $query->setParameters($parameters);

        $results = $query->getQuery()->getResult();

        if (count($results) > 0) {
            return $results[0];
        }

        return null;
    }


    /**
     * {@inheritdoc}
     *
     * Valid criteria are:
     *    enabled - boolean
     *    date - query
     *    author - 'NULL', 'NOT NULL', id, array of ids
     *    mode - string public|admin
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = array())
    {
        if (!isset($criteria['mode'])) {
            $criteria['mode'] = 'public';
        }

        $parameters = array();
        $query = $this->getRepository()
            ->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.publicationDateStart', 'DESC');

        if (!isset($criteria['enabled']) && $criteria['mode'] == 'public') {
            $criteria['enabled'] = true;
        }
        if (isset($criteria['enabled'])) {
            $query->andWhere('p.enabled = :enabled');
            $parameters['enabled'] = $criteria['enabled'];
        }

        if (isset($criteria['date']) && isset($criteria['date']['query']) && isset($criteria['date']['params'])) {
            $query->andWhere($criteria['date']['query']);
            $parameters = array_merge($parameters, $criteria['date']['params']);
        }

/*        if (isset($criteria['author'])) {
            if (!is_array($criteria['author']) && stristr($criteria['author'], 'NULL')) {
                $query->andWhere('p.author IS ' . $criteria['author']);
            } else {
                $query->andWhere(sprintf('p.author IN (%s)', implode((array) $criteria['author'], ',')));
            }
        }*/

        if (isset($criteria['collection']) && $criteria['collection'] instanceof CollectionInterface) {
            $query->andWhere('p.collection = :collectionid');
            $parameters['collectionid'] = $criteria['collection']->getId();
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    public function findPublishedNews(array $criteria, $orderBy = array(), $limit = null, $offset = null)
    {
        $repository = $this->getRepository();
        $qb = $repository->createQueryBuilder('p');
        $qb->where('p.publicationDateStart <= NOW()');

        $index = 0;
        foreach ($criteria as $k => $v) {
            $qb->andWhere('p.'.$k.' = :param'.$index);
            $qb->setParameter('param'.$index, $v);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($orderBy) {
            foreach ($orderBy as $k => $v){
                $qb->addOrderBy('p.'.$k, $v);
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function findByYear($year)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT p
            FROM Ok99PrivateZoneNewsBundle:Post p
            WHERE
                YEAR(p.publicationDateStart) = :year
                AND
                p.publicationDateStart <= NOW()
            ORDER BY
                p.publicationDateStart DESC
        ')->setParameter('year', $year);

        return $query->getResult();
    }

    public function getAllYears()
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT DISTINCT YEAR(p.publicationDateStart)
            FROM Ok99PrivateZoneNewsBundle:Post p
            WHERE
                p.publicationDateStart <= NOW()
            ORDER BY
                p.publicationDateStart DESC
        ');

        return $query->getArrayResult();
    }

    public function findPostBySlug($slug)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT p,pt
            FROM Ok99PrivateZoneNewsBundle:Post p
            LEFT JOIN p.translations pt
            WHERE
              pt.slug = :slug
        ')->setParameter('slug', $slug);

        return $query->getSingleResult();
    }

    /**
     * @param string $date  Date in format YYYY-MM-DD
     * @param string $step  Interval step: year|month|day
     * @param string $alias Table alias for the publicationDateStart column
     *
     * @return array
     */
    protected function getPublicationDateQueryParts($date, $step, $alias = 'p')
    {
        return array(
            'query'  => sprintf('%s.publicationDateStart >= :startDate AND %s.publicationDateStart < :endDate', $alias, $alias),
            'params' => array(
                'startDate' => new \DateTime($date),
                'endDate'   => new \DateTime($date . '+1 ' . $step)
            )
        );
    }

    /**
     * @param string $collection
     *
     * @return array
     */
    protected function getPublicationCollectionQueryParts($collection)
    {
        $pcqp = array('query' => '', 'params' => array());

        if (null === $collection) {
            $pcqp['query'] = 'p.collection IS NULL';
        } else {
            $pcqp['query'] = 'c.slug = :collection';
            $pcqp['params'] = array('collection' => $collection);
        }

        return $pcqp;
    }
}
