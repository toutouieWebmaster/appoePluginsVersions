<?php

namespace App\Plugin\ItemGlue;

use App\DB;
use PDO;

class ArticleContent
{
    private $id;
    private $idArticle;
    private $type = 'BODY';
    private $content;
    private $lang = APP_LANG;

    private $dbh = null;

    public function __construct($idArticle = null, $type = null, $lang = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        if (!is_null($idArticle) && !is_null($type) && !is_null($lang)) {
            $this->idArticle = $idArticle;
            $this->type = $type;
            $this->lang = $lang;
            $this->show();
        }
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
     * @return null
     */
    public function getIdArticle()
    {
        return $this->idArticle;
    }

    /**
     * @param null $idArticle
     */
    public function setIdArticle($idArticle)
    {
        $this->idArticle = $idArticle;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return null
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param null $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }


    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`idArticle` INT(11) NOT NULL,
                	`type` VARCHAR(25) NOT NULL DEFAULT "BODY",
  					`content` TEXT NOT NULL,
  					`lang` VARCHAR(10) NOT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                	UNIQUE (`idArticle`, `type`, `lang`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        }

        return true;
    }

    /**
     * @return array|bool
     */
    public function show()
    {

        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content WHERE idArticle = :idArticle AND type = :type AND lang = :lang';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                return true;

            } else {

                return false;
            }
        }
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content (idArticle, type, content, lang) 
                VALUES (:idArticle, :type, :content, :lang)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $id = $this->dbh->lastInsertId();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->id = $id;
            appLog('Creating Article content -> idArticle: ' . $this->idArticle . ' type: ' . $this->type . ' lang: ' . $this->lang);
            return true;
        }
    }

    /**
     * @param $headers
     * @return bool
     */
    public function saveHeaders($headers)
    {

        $authorizedHeaders = array('NAME', 'DESCRIPTION', 'SLUG');

        if (!isArrayEmpty($headers)) {


            $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content (idArticle, type, content, lang) 
                VALUES (?, ?, ?, ?)';

            $stmt = $this->dbh->prepare($sql);

            foreach (getLangs() as $lang => $longLang) {
                foreach ($headers as $type => $content) {
                    if (in_array($type, $authorizedHeaders)) {
                        $stmt->execute(array($this->idArticle, $type, $content, $lang));
                    }
                }
            }

            $error = $stmt->errorInfo();
            if ($error[0] == '00000') {
                appLog('Creating article headers -> idArticle: ' . $this->idArticle);
            }
            return true;


        }

        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content SET content = :content WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Updating Article content -> id: ' . $this->id);
            return true;
        }
    }

    /**
     * @param $headers
     * @return bool
     */
    public function updateHeaders($headers)
    {

        $authorizedHeaders = array('NAME', 'DESCRIPTION', 'SLUG');

        if (!isArrayEmpty($headers)) {

            $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content 
            SET content = :content WHERE idArticle = :idArticle AND type = :type AND lang = :lang';

            $stmt = $this->dbh->prepare($sql);


            foreach ($headers as $type => $content) {
                if (in_array($type, $authorizedHeaders)) {
                    $stmt->execute(array(':content' => $content, ':idArticle' => $this->idArticle, ':type' => $type, ':lang' => $this->lang));
                }
            }

            $error = $stmt->errorInfo();
            if ($error[0] == '00000') {
                appLog('Updating article headers -> idArticle: ' . $this->idArticle . ' lang: ' . $this->lang);
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function delete()
    {

        $sql = 'DELETE FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content WHERE idArticle = :idArticle';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Deleting Article content -> idArticle: ' . $this->idArticle);
            return true;
        }
    }

    /**
     * @param $slug
     * @return bool
     */
    public function usedSlug($slug)
    {
        $sql = 'SELECT id FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content WHERE type = "SLUG" AND content = :slug AND lang = :lang';
        return DB::exec($sql, [':slug' => $slug, ':lang' => $this->lang])->rowCount() > 0;
    }


    /**
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_content WHERE idArticle = :idArticle AND type = :type AND lang = :lang';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idArticle', $this->idArticle);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                if ($forUpdate) {
                    $data = $stmt->fetch(PDO::FETCH_OBJ);
                    if ($data->id == $this->id) {
                        return true;
                    }
                }

                return false;
            } else {
                return true;
            }
        }
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
}