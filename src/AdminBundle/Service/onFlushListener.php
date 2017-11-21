<?php
namespace AdminBundle\Service;

use Doctrine\ORM\Event\onFlushEventArgs;

class onFlushListener
{
    public function onFlush(onFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $classMetadata = $em->getClassMetadata('AppBundle\Entity\StoreCoupon');
        $uow = $em->getUnitOfWork();
        $recalculate = false;

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if (get_class($entity) == 'AppBundle\Entity\StoreCoupon') {
                $entity->setJustVerified();
                $entity->setAddedBy($em->getRepository('AppBundle:Operator')->getRandomName());
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (get_class($entity) == 'AppBundle\Entity\StoreCoupon') {
                if (!$this->analyzeCouponChanges($entity, $uow->getEntityChangeSet($entity))) {
                    continue;
                }

                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
    }

    /**
     * Analize changes for given coupon and return true, if entity recompute is needed
     *
     * @return boolean
     * @author Michael Strohyi
     **/
    public function analyzeCouponChanges($coupon, $changes)
    {
        if (empty($changes) || empty($coupon)) {
            return;
        }

        $just_verified = false;
        foreach ($changes as $key => $value) {
            switch ($key) {
                case 'link':
                    $old_link = $coupon->transformData($value[0]);
                    if ($old_link != $value[1]) {
                        $just_verified = true;
                       break 2;
                    }
                    break;

                case 'activity':
                    if ($value[1] == 1 && $value[0] != $value[1]){
                        $just_verified = true;
                       break 2;
                    }
                    break;
                
                case 'position':
                    break;

                default:
                    $just_verified = true;
                    break 2;
            }
        }

        if ($just_verified) {
            $coupon->setJustVerified();
        }

        return $just_verified;
    }
}