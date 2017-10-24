<?php

namespace USPC\PageBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PageRepository extends EntityRepository
{
    /**
     * Mark all unused object urls as alias
     *
     * @param  string  $type
     * @param  object  $obj
     * @return void
     * @author Mykola Martynov
     **/
    public function makeAliasUrl($type, $obj)
    {
        if (!$this->isValidObject($obj)) {
            return;
        }

        $em = $this->getEntityManager();
        $query_params = [
                'type' => $type,
                'object_id' => $obj->getId(),
                'url' => $obj->getUrl(),
            ];

        # mark all urls with given {type,object_id}
        $query = $em
            ->createQuery(
                'UPDATE USPCPageBundle:Page p '
                . 'SET p.is_alias = true '
                . 'WHERE p.type = :type and p.object_id = :object_id and p.url != :url'
            )
            ->setParameters($query_params);
        $query->execute();

        # clear for the current object url
        $query = $em
            ->createquery(
                'UPDATE USPCPageBundle:Page p '
                . 'SET p.is_alias = false '
                . 'WHERE p.type = :type AND p.object_id = :object_id and p.url = :url'
            )
            ->setParameters($query_params);
    }

    /**
     * Find page with given object url.
     *
     * @param  string  $type
     * @param  object  $obj
     * @return null|Page
     * @author Mykola Martynov
     **/
    public function findObject($type, $obj)
    {
        if (!$this->isValidObject($obj)) {
            return;
        }

        $page = $this->findOneBy([
                'type' => $type,
                'url' => $obj->getUrl(),
                'object_id' => $obj->getId(),
            ]);

        return $page;
    }

    /**
     * Return true if given object valid to get id/url
     *
     * @return boolean
     * @author Mykola Martynov
     **/
    private function isValidObject($obj)
    {
        return is_object($obj) && method_exists($obj, 'getId') && method_exists($obj, 'getUrl');
    }

    /**
     * Delete all associated urls with the given object
     *
     * @param  string  $type
     * @param  object  $obj
     * @param  int  $obj_id
     *
     * @return void
     * @author Mykola Martynov
     **/
    public function deletePageUrls($type, $obj, $obj_id = null)
    {
        if (!$this->isValidObject($obj) || empty($obj_id) && empty($obj->getId())) {
            return;
        }

        $obj_id = empty($obj->getId) ? $obj_id : $obj->getId();
        $query = $this->getEntityManager()
            ->createQuery(
                'DELETE FROM USPCPageBundle:Page p '
                . 'WHERE p.type = :type and p.object_id = :object_id'
            )
            ->setParameters([
                'type' => $type,
                'object_id' => $obj_id,
            ]);
        $query->execute();
    }

    /**
     * Remove from all menu urls to given object
     *
     * @param string $type
     * @param object $obj
     * @param int $obj_id
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function deleteFromMenus($type, $obj, $obj_id = null)
    {
        if (!$this->isValidObject($obj) || empty($obj_id) && empty($obj->getId())) {
            return;
        }

        $obj_id = empty($obj->getId()) ? $obj_id : $obj->getId();
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT p.url FROM USPCPageBundle:Page p '
                . 'WHERE p.type = :type and p.object_id = :object_id'
            )
            ->setParameters([
                'type' => $type,
                'object_id' => $obj_id,
            ]);
        $obj_page_urls = $query->getResult();
        $obj_url = $obj->getUrl();
        if (empty($obj_page_urls) && empty($obj_url)) {
            return;
        }
        $item_repo = $this->getEntityManager()->getRepository('AppBundle:MenuItem');
        foreach ($obj_page_urls as $value) {
            if (!empty($value['url'])) {
                $url = $this->getUrlFromRes($value['url']);
                $item_repo->deleteMenuItems($url);
                if ($obj_url === $url) {
                    $obj_url = null;
                }
            }
        }

        if (empty($obj_url)) {
            return;
        }

        $item_repo->deleteMenuItems($obj_url);
    }

    /**
     * Try to get string from $res
     *
     * @param resource|string $res
     * @return mixed
     * @author Michael Strohyi
     **/
    public function getUrlFromRes($res)
    {
        if (is_resource($res) && get_resource_type($res) == 'stream') {
            return stream_get_contents($res, -1, 0);
        }

        if (is_string($res)) {
            return $res;
        }

        return null;
    }
}
