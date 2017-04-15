<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Permalink;

use Ok99\PrivateZoneCore\NewsBundle\Entity\Post;

class DatePermalink
{
    protected $pattern;

    /**
     * @param $pattern
     */
    public function __construct($pattern = '%1$04d/%2$d/%3$d/%4$s')
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(Post $post)
    {
        return sprintf($this->pattern,
            $post->getYear(),
            $post->getMonth(),
            $post->getDay(),
            $post->getSlug()
        );
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @param string $permalink
     *
     * @return array
     */
    public function getParameters($permalink)
    {
        $parameters = explode('/', $permalink);

        if (count($parameters) != 4) {
            throw new \InvalidArgumentException('wrong permalink format');
        }

        list($year, $month, $day, $slug) = $parameters;

        return array(
            'year'  => (int) $year,
            'month' => (int) $month,
            'day'   => (int) $day,
            'slug'  => $slug
        );
    }
}
