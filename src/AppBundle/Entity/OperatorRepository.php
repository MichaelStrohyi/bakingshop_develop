<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * OperatorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OperatorRepository extends EntityRepository
{
    /**
     * Return random operator from db
     *
     * @return Operator|null
     * @author Michael Strohyi
     **/
    public function getRandomOperator()
    {
        $operators = $this->getAllOperators();

        return $this->getRandomItem($operators);
    }

    /**
     * Return all operator's id from db
     *
     * @return array|null
     * @author Michael Strohyi
     **/
    public function getAllOperators()
    {
        return $this->findAll();
    }

    /**
     * Return random item from given array
     *
     * @param array $items
     * @return Operator|null
     * @author Michael Strohyi
     **/
    public function getRandomItem($items)
    {
        return empty($items) ? null : $items[rand(0, count($items) - 1)];
    }

}
