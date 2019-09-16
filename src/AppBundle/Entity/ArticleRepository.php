<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{
    const FEATURED_LIST_LIMIT = 10;

    /**
     * Return list aff all article (_homepage included) ordered by header
     *
     * @return array
     * @author Michael Strohyi
     **/
    public function findAllByHeader($with_homepage = false)
    {
        if ($with_homepage) {
            return $this->findBy([], ['header' => 'asc']);
        }

        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM AppBundle:Article a '
                . 'WHERE a.id != 0 '
                . 'ORDER by a.header ASC'
            );
            
        return $query->getResult();
    }

    /**
     * Return article used for homepage
     *
     * @return Article
     * @author Mykola Martynov
     **/
    public function getHomepage()
    {
        $query = $this->getEntityManager()->createQuery(
                'SELECT a FROM AppBundle:Article a '
                . 'WHERE a.is_homepage = true '
                . 'AND a.id != 0'
            );

        return $query->getOneOrNullResult();
    }

    /**
     * Clear homepage flag for other
     *
     * @param  Article  $article
     * 
     * @return void
     * @author Mykola Martynov
     **/
    public function resetHomepage(Article $article)
    {
        $entity_manager = $this->getEntityManager();

        $query = $entity_manager
            ->createQuery(
                'UPDATE AppBundle:Article a '
                . 'SET a.is_homepage = false '
                . 'WHERE a.is_homepage = true '
                . 'AND a.id <> :id'
            )
            ->setParameters([
                    'id' => $article->getId(),
                ]);
        $query->execute();
    }

    /**
     * Get current url for $article from db
     *
     * @param Article $article
     *
     * @return string|null
     * @author Michael Strohyi
     **/
    public function getUrlFromDB($article)
    {
        if (empty($article->getId())) {
            return;
        }
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT a.url FROM AppBundle:Article a '
                . 'WHERE a.id = :id'
            )
            ->setParameters([
                'id' => $article->getId(),
            ]);

        return $this->getEntityManager()->getRepository('USPCPageBundle:Page')->getUrlFromRes($query->getOneOrNullResult()['url']);
    }

    /**
     * Return list off all articles with given $type ordered by header
     *
     * @return array
     * @author Michael Strohyi
     **/
    public function findAllByType($type)
    {
        $query = $this->getEntityManager()->createQuery(
                'SELECT a FROM AppBundle:Article a '
                . 'WHERE a.type = :type '
                . 'AND a.id != 0'
            )
            ->setParameters([
                'type' => $type,
            ]);

        return $query->getResult();
    }

    /**
     * Return list off all articles, which have $subname in header
     *
     * @param string $subname
     * @param int $limit
     * @return array
     * @author Michael Strohyi
     **/
    public function findBySubname($subname, $limit = null)
    {
        # trim from subname spaces and % symbols and replace all spaces for single '%'
        $subname = preg_replace(["/[\s]+/", "/[%]+/"], "%", trim($subname, " %"));
        # if length of search-string (subname) is < 2 return empty result
        if (strlen($subname) < 2) {
            return [];
        }

        $search_words = explode('%', $subname);
        # make requisitons for sql-query from $search_words array
        $params = ['type' => Article::PAGE_SUBTYPE_INFO];
        foreach ($search_words as $key => $value) {
            if ($key == 0) {
                $query_string = "WHERE a.header LIKE :param$key ";
            } else {
                $query_string .= "AND a.header LIKE :param$key ";
            }
            $params["param$key"] = "%$value%";

        }

        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM AppBundle:Article a '
                . $query_string
                . 'AND a.type != :type '
                . 'AND a.id != 0 '
                . 'ORDER by a.header ASC'
            )
            ->setParameters($params)
            ->setMaxResults($limit);
        # make regex pattern: all given words must be in string as a separate words
        $dividers = ['(', ')', '-', ':', '\\', "'", '&', ' ', '|'];
        $search_words = array_diff($search_words, $dividers);
        $pattern = '/^(?=.*\b' . implode('\b)(?=.*\b', $search_words) . '\b)';
        if (count($search_words) == 1) {
            # add to regex pattern OR if only one search word existsts: it may be as a beginning part of first word in string
            $pattern .= '|(?=^' . $search_words[0] . '.*\b)';
        } else {
            # add to regex pattern OR if more than one search word existsts: all words except last  must be in string as a separate words and last word may be as a beginning part word in string
            $pattern .= '|';
            $last_word = array_pop($search_words);
            foreach ($search_words as $key => $value) {
                $pattern .= '(?=.*\b' . $value . '\b)';
            }
            foreach ($search_words as $key => $value) {
                $pattern .= '(?=.*\b' . $last_word . '.*\b)';
            }
        }

        $pattern .= '.*$/i';
        $res = [];
        $articles = $query->getResult();
        # go through results from db and add to results only articles with headers which match regex pattern
        foreach ($articles as $article) {
            if (preg_match($pattern, $article->getHeader())) {
                $res[] = $article;
            }
        }

        return $res;
    }

    /**
     * Return list off all articles, which have $filename in body
     *
     * @param string $filename
     * @param int $limit
     * @return array
     * @author Michael Strohyi
     **/
    public function isFileUsed($filename)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT a.header FROM AppBundle:Article a '
                . 'WHERE a.body LIKE :filename '
                . 'AND a.id != 0'
            )
            ->setParameters([
                'filename' => '%' . $filename . '%',
            ]);

        return !empty($query->getResult());
    }

    /**
     * Return list off all articles, which have $url in prodBody
     *
     * @param string $url
     * @return array
     * @author Michael Strohyi
     **/
    public function findByUsedRedirect($url)
    {
        if (empty($url)) {
            return [];
        }

        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM AppBundle:Article a '
                . 'WHERE a.body LIKE :url'
                . 'AND a.id != 0'
            )
            ->setParameters([
                'url' => '%' . $url . '%'
            ]);

        $res = [];
        foreach ($query->getResult() as $article) {
            $res[$article->getId()] = $article;
        }

        return $res;
    }

    /**
     * Return _homepage
     *
     * @return Article
     * @author Michael Strohyi
     **/
    public function getHomepageInfo()
    {
        return $this->findOneBy(['id' => 0]);
    }

    /**
     * Get header from db for article with given $id
     *
     * @param int $id
     *
     * @return string|null
     * @author Michael Strohyi
     **/
    public function findHeaderById($id)
    {
        if (empty($id)) {
            return;
        }

        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT a.header FROM AppBundle:Article a '
                . 'WHERE a.id = :id'
            )
            ->setParameters([
                'id' => $id,
            ]);

        return $query->getOneOrNullResult()['header'];
    }

    /**
     * Return list off featured articles with id <> given id ordered randomly. List size is limited by given limit
     *
     * @param boolean $with_inactive
     * @return array
     * @author Michael Strohyi
     **/
    public function getFeaturedArticles($id = 0, $limit = self::FEATURED_LIST_LIMIT)
    {
        # get all articles (except info-articles) with not null body
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM AppBundle:Article a '
                . 'WHERE a.body is not null '
                . 'AND a.is_featured = true '
                . 'AND a.type <> :type '
                . 'AND a.id <> :id '
                . 'AND a.id <> 0 '
                . 'ORDER BY a.id ASC'
            )
            ->setParameters([
                    'type' => Article::PAGE_SUBTYPE_INFO,
                    'id' => $id,
                ]);
        $articles = $query->getResult();
        $res = [];
        # while articles list is not empty iterations count not > given limit
        while (!empty($articles) && $limit > 0) {
            # cut random article from articles list
            list($article, $articles) = $this->getRandomItem($articles);
            # add cutted article into result list
            $res[] = $article;
            # subtract this iteration from limit
            $limit--;
        }

        return $res;
    }

    /**
     * Get random item from given list and return this item and list without this item
     *
     * @return array|null
     * @author Michael Strohyi
     **/

    private function getRandomItem($items)
    {
        # if given items is emoty return
        $count = count($items);
        if ($count == 0) {
            return;
        }
        # get random item from items list
        $index = rand(0, $count-1);
        $item = $items[$index];
        # cut this item from items list
        array_splice($items, $index, 1);

        return [$item, $items];
    }
}
