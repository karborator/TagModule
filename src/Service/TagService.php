<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikolayyotsov
 * Date: 2/11/17
 * Time: 11:28 PM
 */

namespace Tag\Service;


use Doctrine\Common\Persistence\ObjectRepository;
use Tag\V1\Rest\Tag\TagResource;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Request;

/**
 * Class TagService
 *
 * Standalone service that perform tag related operations
 *
 * @package Tag\Service
 */
class TagService implements ListenerAggregateInterface, TagServiceInterface
{
    use ListenerAggregateTrait;

    /** @var ObjectRepository */
    protected $repository;

    /** @var  array */
    private $config;

    /** @var  */
    private $router;

    /** @var  EventManagerInterface */
    private $em;

    /**
     * TagService constructor.
     * @param ObjectRepository $repository
     * @param $config
     * @param $router
     */
    public function __construct(ObjectRepository $repository, $config, $router)
    {
        $this->repository = $repository;
        $this->config     = $config;
        $this->router     = $router;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->em = $events;

        $sharedEvents = $events->getSharedManager();

        $this->listeners[] = $sharedEvents->attach(
            '*',
            'tag.search',
            [$this, 'performTagSearch'],
            100
        );

        $this->listeners[] = $sharedEvents->attach(
            '*',
            TagResource::class . ':createPre',
            [$this, 'processTagValue'],
            100
        );

        $this->listeners[] = $sharedEvents->attach(
            '*',
            TagResource::class . ':createPre',
            [$this, 'processContextValue'],
            100
        );
    }

    /**
     * @param EventInterface $event
     */
    public function performTagSearch(EventInterface $event)
    {
        try {
            $tag = $event->getParam('tag');
            $resource = $event->getParam('resource');

            if (!is_string($tag)) {
                throw new \InvalidArgumentException(
                    "Param 'tag' must be type of string!"
                );
            }

            if (!is_string($resource)) {
                throw new \InvalidArgumentException(
                    "Param 'resource' must be type of string!"
                );
            }

            $result = $this->search($tag, $resource);

            $event->setParam('found_tags', $result);
        } catch (\Exception $ex) {
            return;
        }
    }

    /**
     * Map value for param 'context' to valid resource
     *
     * Request payload
     *
     *  {
     *   "context": "http://dari-backend.dev/content-article/31"
     *   "tag": "http://dari-backend.dev/media-image/219"
     *  }
     *
     * Response
     * {
     *   "tag": "Content\Service\ArticleContentProvider::31",
     *   "resource": "Media\Service\ImageContentProvider::id=219::media.image",
     *   "id": "2"
     * }
     *
     *
     * @param EventInterface $event
     */
    public function processContextValue(EventInterface $event)
    {
        $params = $event->getParams();

        //Sys tags must not be attached on context value.
        if (!isset($params['context'])) {
            return;
        }

        $event->setParam(
            'context', $this->mapContextValue2Resource($params['context'])
        );
    }

    /**
     * Map value of param 'tag' to resource
     *
     * Request payload
     *
     *  {
     *   "context": "http://dari-backend.dev/content-article/31"
     *   "tag": "http://dari-backend.dev/media-image/219"
     *  }
     *
     * Response
     * {
     *   "tag": "Content\Service\ArticleContentProvider::31",
     *   "resource": "Media\Service\ImageContentProvider::id=219::media.image",
     *   "id": "2"
     * }
     *
     *
     * @param EventInterface $event
     */
    public function processTagValue(EventInterface $event)
    {
        $params = $event->getParams();

        if (!isset($params['tag'])) {
            return;
        }

        $event->setParam(
            'tag', $this->mapTagValue2Resource($params['tag'])
        );
    }

    /**
     * @param string $context
     *
     * @return string
     */
    public function mapContextValue2Resource(string $context)
    {
        if (!($url = $this->parseUrl($context))) {
            return;
        }

        $resourceProvidersMap = $this->config['resource_providers_map'] ?? [];
        list($resource, $identifier) = $this->findResourceByRouteUrl($url);

        $namespaceResource  = $resourceProvidersMap[$resource] ?? $resource;
        $identifiedResource = $namespaceResource . '::' . $identifier;

        return $identifiedResource ?? $context;
    }

    /**
     * @param string $tag
     *
     * @return string
     */
    public function mapTagValue2Resource(string $tag)
    {
        if (!($url = $this->parseUrl($tag))) {
            return;
        }

        $resourceProvidersMap = $this->config['resource_providers_map'] ?? [];
        list($resource, $identifier) = $this->findResourceByRouteUrl($url);

        $namespaceResource  = $resourceProvidersMap[$resource] ?? $resource;
        $identifiedResource = $namespaceResource . '::id=' . $identifier;
        $identifiedSystemTaggedResource = $this->attachSystemTags2Resource(
            $resource, $identifiedResource
        );

        return $identifiedSystemTaggedResource ?? $tag;
    }


    /**
     * In short this method do search based on 'tag' ,'context' / resource .
     *
     * @param string|null $tag
     * @param string|null $context
     * @return array
     */
    public function search(string $tag, string $context)
    {
        return $this->repository->createQueryBuilder('t')
            ->where('t.context = :context')
            ->andWhere('t.tag LIKE :tag')
            ->setParameter('context', $context)
            ->setParameter('tag', '%' . $tag . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Triggers event 'tag.service.findResourceByRouteUrl.before.return'
     *
     * @param string $url
     * @return array resource (string) , resource identifier (string).
     */
    public function findResourceByRouteUrl(string $url)
    {
        if (!($routeMatch = $this->router->match((new Request())->setUri($url)))) {
            return;
        }

        $route      = $routeMatch->getParams();
        $controller = $route['controller'];

        $resource   = $this->config['zf-rest'][ $controller ]['listener'];
        $identifier = end($route);

        $result     = new \ArrayObject([$resource, $identifier]);
        $this->em->trigger('tag.service.findResourceByRouteUrl.before.return', null, $result);

        return $result->getArrayCopy();
    }

    /**
     * Adding system / meta tags . For example way we can find easily all resources tagged with image / video .
     *
     * Example system tag : media.image
     * Example alias of tag: image
     *
     * @param string $resourceNamespace
     * @param string $identifiedResource
     *
     * @return string
     */
    public function attachSystemTags2Resource(string $resourceNamespace, string $identifiedResource): string
    {
        $tags = $this->config['system_tags']  ?? [];

        foreach ($tags as $aliasOfTag => $tag) {
            strstr(strtolower($resourceNamespace), $aliasOfTag)
                ? $identifiedResource .= '::' . $tag
                : null;
        }

        return $identifiedResource;
    }

    /**
     * @param string $route
     * @return null
     */
    public function parseUrl(string $route)
    {
        return parse_url($route)['path'] ?? null;
    }

}
