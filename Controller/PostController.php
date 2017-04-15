<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/news")
 */
class PostController extends Controller
{
    /**
     * @Route("/", name="ok99_privatezone_news_home")
     *
     * @return RedirectResponse
     */
    public function homeAction()
    {
        return $this->renderArchive();
    }

    /**
     * @param array $criteria
     * @param array $parameters
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderArchive(array $criteria = array(), array $parameters = array())
    {
        return new Response('Hybrid page');
    }

    /**
     * @Route("/archive", name="ok99_privatezone_news_archive")
     */
    public function archiveAction()
    {
        return $this->renderArchive();
    }

    /**
     * @Route("/archive/{year}", name="ok99_privatezone_news_archive_yearly")
     */
    public function archiveYearlyAction($year)
    {
        return $this->renderArchive(array(
            'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, 1, 1), 'year')
        ));
    }

    /**
     * @Route("/archive/{year}/{month}", name="ok99_privatezone_news_archive_monthly")
     */
    public function archiveMonthlyAction($year, $month)
    {
        return $this->renderArchive(array(
            'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, $month, 1), 'month')
        ));
    }

    /**
     * @Route("/c/{collection}", name="ok99_privatezone_news_collection")
     */
    public function collectionAction($collection = null)
    {
        if (is_null($collection)) {
            return $this->renderArchive(array(), array());
        }

        $collection = $this->get('sonata.classification.manager.collection')->findOneBy(array(
            'slug' => $collection,
            'context' => 'news',
            'enabled' => true
        ));

        if (!$collection) {
            throw new NotFoundHttpException('Unable to find the collection');
        }

        if (!$collection->getEnabled()) {
            throw new NotFoundHttpException('Unable to find the collection');
        }

        return $this->renderArchive(array('collection' => $collection), array('collection' => $collection));
    }

    /**
     * @Route("/{permalink}", name="ok99_privatezone_news_view", requirements={"permalink":".+?"})
     */
    public function viewAction($permalink)
    {
        return new Response('Hybrid page');
    }

    /**
     * @param integer $postId
     *
     * @return Response
     */
    public function commentsAction($postId)
    {
        $pager = $this->getCommentManager()
            ->getPager(array(
                'postId' => $postId,
                'status'  => CommentInterface::STATUS_VALID
            ), 1, 500); //no limit

        return $this->render('Ok99PrivateZoneNewsBundle:Post:comments.html.twig', array(
            'pager'  => $pager,
        ));
    }

    /**
     * @param $postId
     * @param bool $form
     *
     * @return Response
     */
    public function addCommentFormAction($postId, $form = false)
    {
        if (!$form) {
            $post = $this->getPostManager()->findOneBy(array(
                'id' => $postId
            ));

            $form = $this->getCommentForm($post);
        }

        return $this->render('Ok99PrivateZoneNewsBundle:Post:comment_form.html.twig', array(
            'form'      => $form->createView(),
            'post_id'   => $postId
        ));
    }

    /**
     * @param $post
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getCommentForm(PostInterface $post)
    {
        $comment = $this->getCommentManager()->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

        return $this->get('form.factory')->createNamed('comment', 'sonata_post_comment', $comment);
    }

    /**
     * @throws NotFoundHttpException
     *
     * @param string $id
     *
     * @return Response
     */
    public function addCommentAction($id)
    {
        $post = $this->getPostManager()->findOneBy(array(
            'id' => $id
        ));

        if (!$post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        if (!$post->isCommentable()) {
            // todo add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                'permalink'  => $this->getPermalinkGenerator()->generate($post)
            )));
        }

        $form = $this->getCommentForm($post);
        $form->bind($this->get('request'));

        if ($form->isValid()) {
            $comment = $form->getData();

            $this->getCommentManager()->save($comment);
            $this->get('sonata.news.mailer')->sendCommentNotification($comment);

            // todo : add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                'permalink'  => $this->getPermalinkGenerator()->generate($post)
            )));
        }

        return $this->render('Ok99PrivateZoneNewsBundle:Post:view.html.twig', array(
            'post' => $post,
            'form' => $form
        ));
    }

    /**
     * @return \Sonata\NewsBundle\Model\PostManagerInterface
     */
    protected function getPostManager()
    {
        return $this->get('ok99.privatezone.news.manager.post');
    }

    /**
     * @return \Sonata\NewsBundle\Model\CommentManagerInterface
     */
    protected function getCommentManager()
    {
        return $this->get('sonata.news.manager.comment');
    }

    /**
     * @param string $commentId
     * @param string $hash
     * @param string $status
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function commentModerationAction($commentId, $hash, $status)
    {
        $comment = $this->getCommentManager()->findOneBy(array('id' => $commentId));

        if (!$comment) {
            throw new AccessDeniedException();
        }

        $computedHash = $this->get('sonata.news.hash.generator')->generate($comment);

        if ($computedHash != $hash) {
            throw new AccessDeniedException();
        }

        $comment->setStatus($status);

        $this->getCommentManager()->save($comment);

        return new RedirectResponse($this->generateUrl('sonata_news_view', array(
            'permalink'  => $this->getPermalinkGenerator()->generate($comment->getPost())
        )));
    }

    protected function getPermalinkGenerator()
    {
        return $this->get('ok99.privatezone.news.permalink.generator');
    }
}
