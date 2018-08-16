<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FeedAffiliate
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeedAffiliateRepository extends EntityRepository
{    
    /**
     * Return all feed affiliates if their Ids are absent in given exclusions
     *
     * @param array $exclusions
     *
     * @return array
     * @author Michael Strohyi
     **/
    public function getAllFeedAffiliates($exclusions = [])
    {
        $q = 'SELECT f FROM AppBundle:FeedAffiliate f';
        if (!empty($exclusions)) {
            $exclusions = '(' . implode(', ', $exclusions) . ')';
            $q .= ' WHERE f.id NOT IN ' . $exclusions;
        }

        $query = $this->getEntityManager()->createQuery($q);

        return $query->getResult();
    }
    /**
     * Return all feed affiliates values as a named array
     *
     * @return array
     * @author Michael Strohyi
     **/
    public function getAllAsNamedArray()
    {
        $res = [];
        $q = 'SELECT f FROM AppBundle:FeedAffiliate f';
        if (!empty($exclusions)) {
            $exclusions = '(' . implode(', ', $exclusions) . ')';
            $q .= ' WHERE f.id NOT IN ' . $exclusions;
        }

        $query = $this->getEntityManager()->createQuery($q);
        foreach ($query->getResult() as $feed_affiliate) {
            $res[strtolower($feed_affiliate->getNetwork())] = [
                'affiliate_id' => $feed_affiliate->getAffiliateId(),
                'feed_affiliate_id' => $feed_affiliate->getFeedAffiliateId(),
            ];
        };

        return $res;
    }
}