<?php
namespace App\Plugin\Shop;
class ProductContent
{
    private $id;
    private $idProduct;
    private $resume = null;
    private $content;
    private $lang;

    private $dbh = null;

    public function __construct($idProduct = null, $lang = null)
    {
        if(is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($idProduct) && !is_null($lang)) {
            $this->idProduct = $idProduct;
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
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * @param null $idProduct
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;
    }

    /**
     * @return mixed
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * @param mixed $resume
     */
    public function setResume($resume)
    {
        $this->resume = $resume;
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
        $sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_plugin_shop_products_content` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`product_id` INT(11) NOT NULL,
                	`resume` VARCHAR(255) NULL DEFAULT NULL,
  					`content` TEXT NOT NULL,
  					`lang` VARCHAR(10) NOT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                	UNIQUE (`product_id`, `lang`)
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

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_products_content WHERE product_id = :product_id AND lang = :lang';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':product_id', $this->idProduct);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(\PDO::FETCH_OBJ);
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

        $sql = 'INSERT INTO '.TABLEPREFIX.'appoe_plugin_shop_products_content (product_id, resume, content, lang) 
                VALUES (:product_id, :resume, :content, :lang)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':product_id', $this->idProduct);
        $stmt->bindParam(':resume', $this->resume);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $id = $this->dbh->lastInsertId();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->id = $id;

            return true;
        }
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE '.TABLEPREFIX.'appoe_plugin_shop_products_content SET resume = :resume, content = :content WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':resume', $this->resume);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {

        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_shop_products_content WHERE product_id = :product_id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':product_id', $this->idProduct);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return true;
        }
    }


    /**
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id, product_id, content, lang FROM '.TABLEPREFIX.'appoe_plugin_shop_products_content WHERE product_id = :product_id AND content = :content AND lang = :lang';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':product_id', $this->idProduct);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                if ($forUpdate) {
                    $data = $stmt->fetch(\PDO::FETCH_OBJ);
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