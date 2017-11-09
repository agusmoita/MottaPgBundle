<?php

namespace MottaPgBundle\Util\Paginator;

/**
 * FormRemember
 *
 */
class FormRemember
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Session $session
     * @param RequestStack $request
     */
    public function __construct($session, $request)
    {
        $this->session = $session;
        $this->request = $request->getCurrentRequest();
    }

    /**
     * @param Form $form
     * @param boolean $remember
     * @return boolean
     */
    public function remember(&$form, $remember)
    {
        $index = 'form_remember_' . $this->getName();
        if ($this->request->isMethod('POST')) {
            $form->handleRequest($this->request);
            $data = $form->getData();
            if ($data["clean"] === true || $data["clean"] === "true") {
                return true;
            }
            $this->session->set($index, $this->request);
        }
        elseif ($remember && $this->session->has($index)) {
            $form->handleRequest($this->session->get($index));
        }

        return false;

    }

    /**
     * @param Form $form
     */
    public function remove(&$form)
    {
        $index = 'form_remember_' . $this->getName();

        $this->session->remove($index);
    }

    /**
     * @return string
     */
    private function getName()
    {
        $uri = $this->request->getPathInfo();
        return $uri;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
