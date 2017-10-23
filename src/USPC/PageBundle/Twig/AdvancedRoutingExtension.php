<?php

namespace USPC\PageBundle\Twig;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bridge\Twig\Extension\RoutingExtension;

/**
* Advanced Routing Twig Extension for current application
*/
class AdvancedRoutingExtension extends RoutingExtension
{
    private $baseUrl = null;

    public function __construct(UrlGeneratorInterface $generator)
    {
        parent::__construct($generator);

        $context = $generator->getContext();

        $this->baseUrl = $context->getBaseUrl();
    }

    /**
     * {@inheritDoc}
     * @author Mykola Martynov
     **/
    public function getPath($obj, $parameters = [], $relative = false)
    {
        if ($this->hasUrl($obj)) {
            return $this->objectUrl($obj, $parameters);
        }

        return parent::getPath($obj, $parameters, $relative);
    }

    /**
     * {@inheritDoc}
     * @author Mykola Martynov
     **/
    public function getUrl($obj, $parameters = [], $schemeRelative = false)
    {
        if ($this->hasUrl($obj)) {
            return $this->objectUrl($obj, $parameters);
        }

        return parent::getUrl($obj, $parameters, $schemeRelative);
    }

    /**
     * Return true if given $obj is an object and has getUrl method.
     *
     * @param  mixed  $obj
     * @return boolean
     * @author Mykola Martynov
     **/
    private function hasUrl($obj)
    {
        return is_object($obj) && method_exists($obj, 'getUrl');
    }

    /**
     * Return url for the given $obj
     *
     * ASSUMPTION: object url has no query parameters and starts with forwards slash
     *
     * @param  object  $obj
     * @param  array   $parameters
     * @return null|string
     * @author Mykola Martynov
     **/
    private function objectUrl($obj, $parameters = [])
    {
        # get url
        $url = $obj->getUrl();
        # get url prefix from parameters
        $prefix = '';
        if (array_key_exists('prefix', $parameters)) {
            $prefix = '/' . trim($parameters['prefix'], '/');
            unset($parameters['prefix']);
        }

        # add base url
        if (!empty($url)) {
            $url = $this->baseUrl . $prefix . $url;
        }
        
        # add parameters
        if (!empty($url) && !empty($parameters)) {
            $url .= '?' . http_build_query($parameters);
        }
        
        return $url;
    }
}
