<?php
namespace Tag\V1\Rest\Tag;

use ZF\Apigility\Doctrine\Server\Resource\DoctrineResource;
use Zend\Stdlib\ArrayObject;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class TagResource extends DoctrineResource
{
    public function fetchAll($data = [])
    {
        $args = $this->getEventManager()->prepareArgs((array)$data);

        $this->getEventManager()->trigger('tag.search', null, $args);

        if (isset($args['found_tags'])) {
            return $args['found_tags'];
        }

        return parent::fetchAll($data);
    }

    public function create($data)
    {
        $data = $this->getEventManager()->prepareArgs((array)$data);
        $this->events->trigger(__CLASS__ . ':' . __FUNCTION__ . 'Pre', null, $data);

        $result = parent::create($data);

        $extractedResult = (new DoctrineObject($this->getObjectManager()))->extract($result);
        $extractedResult = $this->getEventManager()->prepareArgs($extractedResult);
        $this->events->trigger(__CLASS__ . ':' . __FUNCTION__ . 'Post', null, $extractedResult);

        return $result;
    }
}
