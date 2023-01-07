<?php

namespace Ok99\PrivateZoneCore\NewsBundle\Controller;

use Ok99\PrivateZoneBundle\Controller\SecuredCRUDController;
use Ok99\PrivateZoneCore\NewsBundle\Entity\Post;
use Ok99\PrivateZoneCore\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostAdminController extends SecuredCRUDController
{
    /**
     * @param $id
     * @param Request $request
     * @param Post $object
     * @param $action
     * @return RedirectResponse|Response
     */
    protected function solveResponseIfNotObjectOwner($id, Request $request, $object, $action)
    {
        if (!$this->admin->isAdmin($object) && !$this->isUserValid($object)) {
            return $this->redirect($this->generateUrl('admin_privatezonecore_news_post_list'));
        }

        return $this->doAction($action, $id, $request);
    }

    /**
     * @param $id
     * @param Request $request
     * @param Post $object
     * @param $action
     * @return RedirectResponse|Response
     */
    protected function solveResponseIfNotValidRequest($id, Request $request, $object, $action)
    {
        if (!$this->admin->isAdmin() && !$this->isUserValid($object)) {
            return $this->redirect($this->generateUrl('admin_privatezonecore_news_post_list'));
        }

        return $this->doAction($action, $id, $request);
    }

    /**
     * @param Post $object
     * @return bool
     */
    protected function isUserValid($object)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        return $object && $user->getId() == $object->getCreatedBy()->getId();
    }
}
