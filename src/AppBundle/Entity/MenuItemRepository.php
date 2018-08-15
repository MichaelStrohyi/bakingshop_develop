<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MenuItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MenuItemRepository extends EntityRepository
{

    /**
     * Remove menu items with given $url
     *
     * @param string $url
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function deleteMenuItems($url)
    {
        if (empty($url)) {
            return;
        }

        $query = $this->getEntityManager()
            ->createQuery(
                'DELETE FROM AppBundle:MenuItem m '
                . 'WHERE m.url = :url'
            )
            ->setParameters([
                'url' => $url,
            ]);
        $query->execute();
    }

    /**
     * Set url to $new_url for all menu items with url $old_url
     *
     * @param string $old_url
     * @param string $new_url
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function updateUrls($old_url, $new_url)
    {
        if (empty($old_url) || empty($new_url)) {
            return;
        }

        $query = $this->getEntityManager()
            ->createQuery(
                'UPDATE AppBundle:MenuItem m SET m.url = :new_url '
                . 'WHERE m.url = :old_url'
            )
            ->setParameters([
                'new_url' => $new_url,
                'old_url' => $old_url,
            ]);
        $query->execute();
    }

    /**
     * Set title to $new_title for all menu items with title $old_title
     *
     * @param string $old_title
     * @param string $new_title
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function updateTitles($old_title, $new_title)
    {
        if (empty($old_title) || empty($new_title)) {
            return;
        }

        $query = $this->getEntityManager()
            ->createQuery(
                'UPDATE AppBundle:MenuItem m SET m.title = :new_title '
                . 'WHERE m.title = :old_title'
            )
            ->setParameters([
                'new_title' => $new_title,
                'old_title' => $old_title,
            ]);
        $query->execute();
    }
}
