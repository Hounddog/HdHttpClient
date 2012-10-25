<?php

namespace HdHttpClient\Message;

use Buzz\Message\Response as BaseResponse;

use EdpGithub\HttpClient\Exception\ApiLimitExceedException;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;

class Response extends BaseResponse implements EventManagerAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function getContent()
    {
        $content = parent::getContent();

        $this->getEventManager()->trigger('get.content', $content);

        return $content;
    }

    public function setHeaders(array $headers)
    {
        parent::setHeaders($headers);
        $this->getEventManager()->trigger('set.header', $this);
    }

      public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->events = $events;
        return $this;
    }

    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
