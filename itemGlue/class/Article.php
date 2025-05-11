<?php

namespace App\Plugin\ItemGlue;

use App\DB;
use PDO;

class Article
{
    private $id;
    private string $name;
    private mixed $description = null;
    private string $slug;
    private string $content;
    private $statut = 1;
    private $userId;
    private $createdAt;
    private $updatedAt;
    private ?array $medias = [];
    private ?array $metas = [];
    private ?array $categoriesDetails = [];
    private ?array $categories = [];

    private $lang;

    public function __construct($idArticle = null, $lang = LANG)
    {
        $this->userId = getUserIdSession();
        $this->lang = $lang;

        if (!is_null($idArticle)) {
            $this->id = $idArticle;
            $this->show();
        }
    }

    /**
     * @return bool
     */
    public function show()
    {
        $sql = 'SELECT C.*,
        (SELECT cc1.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = C.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = C.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = C.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = C.id AND cc4.lang = :lang) AS content
        FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS C
        WHERE C.id = :id';

        $params = array(':id' => $this->id, ':lang' => $this->lang);

        $return = DB::exec($sql, $params);
        if ($return) {

            if ($return->rowCount() == 1) {

                $this->feed($return->fetch(PDO::FETCH_OBJ));
                return true;
            }
        }
        return false;
    }

    /**
     * Feed class attributs
     *
     * @param $data
     */
    public function feed($data)
    {
        foreach ($data as $attribut => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));

            if (is_callable(array($this, $method))) {
                $this->$method($value);
            }
        }
    }

    public function getMedias(): ?array
    {
        return $this->medias;
    }

    public function setMedias(?array $medias): void
    {
        $this->medias = $medias;
    }

    public function getMetas(): ?array
    {
        return $this->metas;
    }

    public function setMetas(?array $metas): void
    {
        $this->metas = $metas;
    }

    public function getCategoriesDetails(): ?array
    {
        return $this->categoriesDetails;
    }

    public function setCategoriesDetails(?array $categoriesDetails): void
    {
        $this->categoriesDetails = $categoriesDetails;
    }

    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function setCategories(?array $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return bool|mixed|string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param bool|mixed|string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
  					`statut` BOOLEAN NOT NULL DEFAULT TRUE,
  					`userId` INT(11) NOT NULL,
                	`created_at` DATE NOT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

        $return = DB::exec($sql);
        if ($return) {
            return true;
        }

        return false;
    }

    /**
     * @return bool|array
     */
    public function getBySlug()
    {

        $sql = 'SELECT AC.*, A.statut FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS AC
        INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS A
        ON(AC.idArticle = A.id)
        WHERE AC.type = "SLUG" AND AC.content = :slug AND A.statut >= :statut';

        if ($return = DB::exec($sql, [':slug' => $this->slug, ':statut' => $this->statut])) {
            return $return->fetch(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function showBySlug()
    {

        $sql = 'SELECT C.*,
        (SELECT cc1.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = C.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = C.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = C.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = C.id AND cc4.lang = :lang) AS content
        FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS C
        WHERE C.id = (SELECT idArticle FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content WHERE type = "SLUG" AND content = :slug AND lang = :lang) 
        AND C.statut >= :statut';

        $params = array(':lang' => $this->lang, ':slug' => $this->slug, ':statut' => $this->statut);

        $return = DB::exec($sql, $params);
        if ($return) {

            if ($return->rowCount() == 1) {

                $this->feed($return->fetch(PDO::FETCH_OBJ));
                return true;
            }
        }
        return false;
    }

    /**
     * @param $idCategory
     * @param bool $showParent
     * @param string $lang
     * @return bool|array
     */
    public function showByCategory($idCategory, $showParent = false, $lang = LANG)
    {
        $categorySQL = $showParent ? ' AND (C.id = :idCategory OR (C.parentId = :idCategory OR (C2.parentId >= 10 AND C2.parentId = :idCategory))) ' : ' AND C.id = :idCategory ';
        $sql = 'SELECT DISTINCT ART.id, 
         (SELECT cc1.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content,
         ART.userId, ART.created_at, ART.updated_at, ART.statut, 
        GROUP_CONCAT(DISTINCT C.id SEPARATOR "||") AS categoryIds, GROUP_CONCAT(DISTINCT C.name SEPARATOR "||") AS categoryNames
        FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS ART 
        LEFT JOIN ' . TABLEPREFIX . 'appoe_categoryRelations AS CR
        ON(CR.typeId = ART.id) 
        LEFT JOIN ' . TABLEPREFIX . 'appoe_categories AS C
        ON(C.id = CR.categoryId)
        ' . ($showParent ? ' LEFT JOIN ' . TABLEPREFIX . 'appoe_categories C2 ON(C2.id = C.parentId) ' : '') . '
        INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS AC
        ON(AC.idArticle = ART.id)
        WHERE CR.type = "ITEMGLUE" AND ART.statut > 0 AND C.status > 0 AND AC.lang = :lang ' . $categorySQL . '
        GROUP BY ART.id ORDER BY ART.statut DESC, ART.created_at DESC';

        $params = array(':idCategory' => $idCategory, ':lang' => $lang);

        $return = DB::exec($sql, $params);
        if ($return) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @param $parentId
     * @return bool|array
     */
    public function showWithPosition($parentId)
    {
        $sql = 'SELECT name, position FROM `appoe_categories` WHERE `parentId` = :parentId';


        $params = array(':parentId' => $parentId);


        $return = DB::exec($sql, $params);
        if ($return) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }


    /**
     * @param bool $countArticles
     * @param bool $length
     * @return array|bool
     */
    public function showAll($countArticles = false, $length = false)
    {
        $limit = $length ? ' LIMIT ' . $length . ' OFFSET 0' : '';
        $featured = $this->statut == 1 ? ' statut >= 1' : ' statut = ' . $this->statut . ' ';

        $sql = 'SELECT ART.*,
         (SELECT cc1.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content 
         FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS ART
        WHERE ' . $featured . ' ORDER BY statut DESC, name ASC ' . $limit;

        $params = array(':lang' => $this->lang);
        $return = DB::exec($sql, $params);

        if ($return) {

            return (!$countArticles) ? $return->fetchAll(PDO::FETCH_OBJ) : $return->rowCount();
        }
        return false;
    }

    /**
     * @param bool $length
     * @param bool $lang
     * @return array|bool
     */
    public function showAllByLang($length = false, $lang = LANG)
    {
        $limit = $length ? ' LIMIT ' . $length . ' OFFSET 0' : '';
        $featured = $this->statut == 1 ? ' ART.statut >= 1' : ' ART.statut = ' . $this->statut . ' ';

        $sql = 'SELECT ART.*,
        (SELECT cc1.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content
        FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS ART 
        WHERE ' . $featured . '
        ORDER BY ART.statut DESC, ART.created_at DESC ' . $limit;

        $params = array(':lang' => $lang);
        $return = DB::exec($sql, $params);

        if ($return) {

            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @param int $year
     * @param bool|int $month
     * @param bool $length
     * @param bool $lang
     * @param bool|int $idCategory
     * @param bool $parentCategory
     * @return array|bool
     */
    public function showArchives($year, $month = false, $length = false, $lang = LANG, $idCategory = false, $parentCategory = false)
    {
        if (!is_numeric($year) || strlen($year) != 4) {
            $year = date('Y');
        }

        $sqlArchives = ' AND YEAR(ART.created_at) = :year ';

        if ($month && is_numeric($month) && checkdate($month, 1, $year)) {
            $sqlArchives = ' AND YEAR(ART.created_at) = :year  AND MONTH(ART.created_at) = :month ';
        }

        $categorySQL = '';
        if ($idCategory) {
            $categorySQL = ' AND C.id = :idCategory ';
            if ($parentCategory) {
                $categorySQL = ' AND (C.id = :idCategory OR C.parentId = :idCategory) ';
            }
        }

        $limit = $length ? ' LIMIT ' . $length . ' OFFSET 0' : '';
        $featured = $this->statut == 1 ? ' ART.statut >= 1' : ' ART.statut = ' . $this->statut . ' ';

        $sql = 'SELECT DISTINCT ART.id, 
         (SELECT cc1.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content,
         ART.userId, ART.created_at, ART.updated_at, ART.statut, 
        GROUP_CONCAT(DISTINCT C.id SEPARATOR "||") AS categoryIds, GROUP_CONCAT(DISTINCT C.name SEPARATOR "||") AS categoryNames
        FROM ' . TABLEPREFIX . 'appoe_categoryRelations AS CR 
        RIGHT JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS ART 
        ON(CR.typeId = ART.id) 
        INNER JOIN ' . TABLEPREFIX . 'appoe_categories AS C
        ON(C.id = CR.categoryId)
        INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS AC
        ON(AC.idArticle = ART.id)
        WHERE ' . $featured . ' AND CR.type = "ITEMGLUE" ' . $sqlArchives . ' AND ART.statut > 0 AND C.status > 0 AND AC.lang = :lang' . $categorySQL . '
        GROUP BY ART.id ORDER BY ART.statut DESC, name DESC ' . $limit;

        $params = array(':lang' => $lang, ':year' => $year);

        if ($month) {
            $params[':month'] = $month;
        }

        if ($idCategory) {
            $params[':idCategory'] = $idCategory;
        }

        $return = DB::exec($sql, $params);

        if ($return) {

            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @param $lang
     * @param $idCategory
     * @param $parent
     * @return bool
     */
    public function showNextArticle($lang = LANG, $idCategory = false, $parent = false)
    {
        $addSql = '';
        if (false !== $idCategory) {
            $addSql = ' AND C.id = :idCategory ';
            if (true === $parent) {
                $addSql = ' AND (C.id = :idCategory OR C.parentId = :idCategory) ';
            }
        }

        $sql = 'SELECT ART.* FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS ART
        INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS AC
        ON(ART.id = AC.idArticle)
        INNER JOIN ' . TABLEPREFIX . 'appoe_categoryRelations AS CR 
        ON(CR.typeId = ART.id) 
        INNER JOIN ' . TABLEPREFIX . 'appoe_categories AS C
        ON(C.id = CR.categoryId)
        WHERE ART.id = (
        SELECT MIN(ART.id) FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS ART 
        INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS AC
        ON(ART.id = AC.idArticle)
        INNER JOIN ' . TABLEPREFIX . 'appoe_categoryRelations AS CR 
        ON(CR.typeId = ART.id) 
        INNER JOIN ' . TABLEPREFIX . 'appoe_categories AS C
        ON(C.id = CR.categoryId)
        WHERE ART.id > :id AND CR.type = "ITEMGLUE" AND ART.statut >= 1 AND C.status > 0 AND AC.lang = :lang ' . $addSql . ')
        GROUP BY ART.id LIMIT 1';

        $params = array(':id' => $this->id, ':lang' => $lang);

        if (false !== $idCategory) {
            $params[':idCategory'] = $idCategory;
        }

        if ($return = DB::exec($sql, $params)) {
            if ($return->rowCount() == 1) {
                $this->feed($return->fetch(PDO::FETCH_OBJ));
                return true;
            }
        }
        return false;
    }

    /**
     * @param $lang
     * @param $idCategory
     * @param $parent
     * @return bool
     */
    public function showPreviousArticle($lang = LANG, $idCategory = false, $parent = false)
    {
        $addSql = '';
        if (false !== $idCategory) {
            $addSql = ' AND C.id = :idCategory ';
            if (true === $parent) {
                $addSql = ' AND (C.id = :idCategory OR C.parentId = :idCategory) ';
            }
        }

        $sql = 'SELECT ART.* FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS ART
        INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS AC
        ON(ART.id = AC.idArticle)
        INNER JOIN ' . TABLEPREFIX . 'appoe_categoryRelations AS CR 
        ON(CR.typeId = ART.id) 
        INNER JOIN ' . TABLEPREFIX . 'appoe_categories AS C
        ON(C.id = CR.categoryId)
        WHERE ART.id = (
        SELECT MAX(ART.id) FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS ART 
        INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS AC
        ON(ART.id = AC.idArticle)
        INNER JOIN ' . TABLEPREFIX . 'appoe_categoryRelations AS CR 
        ON(CR.typeId = ART.id) 
        INNER JOIN ' . TABLEPREFIX . 'appoe_categories AS C
        ON(C.id = CR.categoryId)
        WHERE ART.id < :id AND CR.type = "ITEMGLUE" AND ART.statut >= 1 AND C.status > 0 AND AC.lang = :lang ' . $addSql . ')
        GROUP BY ART.id LIMIT 1';

        $params = array(':id' => $this->id, ':lang' => $lang);

        if (false !== $idCategory) {
            $params[':idCategory'] = $idCategory;
        }

        if ($return = DB::exec($sql, $params)) {
            if ($return->rowCount() == 1) {
                $this->feed($return->fetch(PDO::FETCH_OBJ));
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $searching
     * @param string $lang
     * @return array|bool
     */
    public function searchFor($searching, $lang = LANG)
    {
        $featured = $this->statut == 1 ? ' ART.statut >= 1' : ' ART.statut = ' . $this->statut . ' ';

        $sql = 'SELECT DISTINCT ART.id, 
         (SELECT cc1.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc1 WHERE cc1.type = "NAME" AND cc1.idArticle = ART.id AND cc1.lang = :lang) AS name,
        (SELECT cc2.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc2 WHERE cc2.type = "DESCRIPTION" AND cc2.idArticle = ART.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc3 WHERE cc3.type = "SLUG" AND cc3.idArticle = ART.id AND cc3.lang = :lang) AS slug,
        (SELECT cc4.content FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS cc4 WHERE cc4.type = "BODY" AND cc4.idArticle = ART.id AND cc4.lang = :lang) AS content,
         ART.userId, ART.created_at, ART.updated_at, ART.statut, 
        GROUP_CONCAT(DISTINCT C.id SEPARATOR "||") AS categoryIds, GROUP_CONCAT(DISTINCT C.name SEPARATOR "||") AS categoryNames
        FROM ' . TABLEPREFIX . 'appoe_categoryRelations AS CR 
        RIGHT JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS ART 
        ON(CR.typeId = ART.id) 
        INNER JOIN ' . TABLEPREFIX . 'appoe_categories AS C
        ON(C.id = CR.categoryId)
        INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content AS AC
        ON(AC.idArticle = ART.id)
        WHERE ' . $featured . ' AND CR.type = "ITEMGLUE" AND C.status > 0 AND (name LIKE :search OR content LIKE :search) 
        AND AC.lang = :lang GROUP BY ART.id ORDER BY ART.statut DESC, ART.created_at DESC ';

        $params = array(':search' => '%' . $searching . '%', ':lang' => $lang);

        $return = DB::exec($sql, $params);
        if ($return) {

            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles (statut, userId, created_at) 
                VALUES (:statut, :userId, CURDATE())';

        $params = array(':statut' => $this->statut, ':userId' => $this->userId);
        $return = DB::exec($sql, $params);

        if ($return) {
           $id = DB::lastInsertId();
            if ($id) {
                $this->setId($this->id);
                appLog('Creating Article -> id: ' . $this->id);
                return true;
            }
        }
       return false;
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles SET statut = :statut, userId = :userId, created_at = :created_at WHERE id = :id';

        $params = array(':statut' => $this->statut, ':userId' => $this->userId, ':created_at' => $this->createdAt, ':id' => $this->id);
        $return = DB::exec($sql, $params);

        if ($return) {

            appLog('Updating Article -> id: ' . $this->id . ' statut: ' . $this->statut);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        //Get Media of Article
        $ArticleMedia = new ArticleMedia($this->id);
        $allMedia = $ArticleMedia->showFiles();

        if ($allMedia) {
            foreach ($allMedia as $media) {
                $ArticleMedia->setId($media->id);
                $ArticleMedia->setName($media->name);
                $ArticleMedia->delete();
            }
        }

        $sql = 'DELETE FROM ' . TABLEPREFIX . 'appoe_categoryRelations WHERE type = "ITEMGLUE" AND typeId = :id;';
        $sql .= 'DELETE FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_meta WHERE idArticle = :id;';
        $sql .= 'DELETE FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content WHERE idArticle = :id;';
        $sql .= 'DELETE FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles WHERE id = :id;';

        $params = array(':id' => $this->id);
        $return = DB::exec($sql, $params);

        if ($return) {

            appLog('Deleting Article -> id: ' . $this->id);
            return true;
        }
        return false;
    }
}