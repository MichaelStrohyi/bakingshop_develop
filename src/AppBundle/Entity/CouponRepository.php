<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CouponRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CouponRepository extends EntityRepository
{
    /**
     * Get link from db for coupon with given $id
     *
     * @param int $id
     *
     * @return string|null
     * @author Michael Strohyi
     **/
    public function findLinkById($id)
    {
        if (empty($id)) {
            return;
        }
        
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT c.link FROM AppBundle:Coupon c '
                . 'WHERE c.id = :id'
            )
            ->setParameters([
                'id' => $id,
            ]);

        return $this->getEntityManager()->getRepository('USPCPageBundle:Page')->getUrlFromRes($query->getOneOrNullResult()['link']);
    }

    /**
     * Deactivate expired coupons
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function deactivateExpired()
    {
        $date = new \DateTime();
        $date->setTime(0, 0, 0);
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb->update('AppBundle\Entity\Coupon', 'c')
            ->set('c.activity', 0)
            ->where('c.expireDate < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute();
    }

    /**
     * Delete startDate wich is older, than 2 years from current date
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function removeOldDates()
    {
        $date = new \DateTime();
        $date->setTime(0, 0, 0);
        $date->modify('-2 years');
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb->update('AppBundle\Entity\Coupon', 'c')
            ->set('c.startDate', 'null')
            ->where('c.startDate < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute();
    }

    /**
     * Find coupons, which are expired for more, than 2 weeks
     *
     * @return array
     * @author Michael Strohyi
     **/
    public function findExpired()
    {
        $date = new \DateTime();
        $date->setTime(0, 0, 0);
        $date->modify('-2 weeks');
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM AppBundle:Coupon c '
                . 'WHERE c.expireDate < :date'
            )
            ->setParameters([
                'date' => $date,
            ]);

        return $query->getResult();
    }

    /**
     * Return list off all coupons, which are associated with operator with given operator_id
     *
     * @param int $operator_id
     * @return array
     * @author Michael Strohyi
     **/
    public function findByOperator($operator_id)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM AppBundle:Coupon c '
                . 'WHERE c.addedBy = :operator_id'
            )
            ->setParameters([
                'operator_id' => $operator_id,
            ]);

        return $query->getResult();
    }

    /**
     * Activate coupons with startDate today and set them verifiedAt date today
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function activateStartedToday()
    {
        $date = new \DateTime();
        $date->setTime(0, 0, 0);
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb->update('AppBundle\Entity\Coupon', 'c')
            ->set('c.activity', 1)
            ->set('c.verifiedAt', 'CURRENT_TIMESTAMP()')
            ->where('c.startDate = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute();
    }
}
