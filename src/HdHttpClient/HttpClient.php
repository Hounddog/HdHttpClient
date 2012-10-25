<?php

namespace HdHttpClient;

use Buzz\Client\ClientInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Listener\ListenerInterface;

use HdHttpClient\Message\Request;
use HdHttpClient\Message\Response;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;

class HttpClient implements HttpClientInterface, EventManagerAwareInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $headers = array();


    /**
     * @var Response
     */
    private $response;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var EventManager
     */
    protected $events;

    /**
     * @param array           $options
     * @param ClientInterface $client
     */
    public function __construct()
    {
        $this->clearHeaders();
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = array(), array $headers = array())
    {
        if (0 < count($parameters)) {
            $path .= (false === strpos($path, '?') ? '?' : '&').http_build_query($parameters, '', '&');
        }

        return $this->request($path, array(), 'GET', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'POST', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'DELETE', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, array $headers = array())
    {
        return $this->request($path, array(), 'PUT', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function request($path, array $parameters = array(), $httpMethod = 'GET', array $headers = array())
    {
        $path = trim($this->options['base_url'].$path, '/');

        $request = $this->getRequest($httpMethod, $path);
        $request->addHeaders($headers);
        $request->setContent(json_encode($parameters));

        $this->getEventManager()->trigger('pre.send', $request);

        $response = $this->getResponse();

        try {
            $this->client->send($request, $response);
        } catch (\LogicException $e) {
            throw new ErrorException($e->getMessage());
        } catch (\RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }

        $this->getEventManager()->trigger('post.send', $request);


        return $response;
    }

    /**
     * @param string $httpMethod
     * @param string $url
     *
     * @return Request
     */
    private function createRequest($httpMethod, $url)
    {
        $sm = $this->getServiceManager('HdHttpClient\Request');
        $request->setMethod($httpMethod);
        $request->setHeaders($this->headers);
        $request->fromUrl($url);

        return $request;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Clears used headers
     */
    public function clearHeaders()
    {
        $this->headers = array(
            sprintf('Accept: application/vnd.github.%s+json', $this->options['api_version'])
        );
    }

    /**
     * Get Response
     *
     * @return response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set Response
     *
     * @param response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Get Request
     *
     * @return request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set Request
     *
     * @param request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Set Event Manager
     *
     * @param  EventManagerInterface $events
     * @return HybridAuth
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->events = $events;
        return $this;
    }

    /**
     * Get Event Manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }
}