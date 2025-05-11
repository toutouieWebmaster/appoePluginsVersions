<?php

namespace App\Plugin\Shop;
class Product
{
    private ?int $id;
    private $type;
    private string $name;
    private string $slug;
    private mixed $price;
    private mixed $poids = null;
    private mixed $dimension = null;
    private int $status = 1;

    private mixed $remainingQuantity = null;
    private mixed $orderedQuantity = null;
    private mixed $remainingDate = null;

    public mixed $content;
    public mixed $media;
    public mixed $meta;
    public mixed $categories;

    private ?\PDO $dbh = null;

    public function __construct(?int $id_produit = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($id_produit)) {
            $this->id = $id_produit;
            $this->show();
        }
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function setContent(mixed $content): void
    {
        $this->content = $content;
    }

    public function getMedia(): mixed
    {
        return $this->media;
    }

    public function setMedia(mixed $media): void
    {
        $this->media = $media;
    }

    public function getMeta(): mixed
    {
        return $this->meta;
    }

    public function setMeta(mixed $meta): void
    {
        $this->meta = $meta;
    }

    public function getCategories(): mixed
    {
        return $this->categories;
    }

    public function setCategories(mixed $categories): void
    {
        $this->categories = $categories;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPoids()
    {
        return $this->poids;
    }

    /**
     * @param mixed $poids
     */
    public function setPoids($poids)
    {
        $this->poids = $poids;
    }

    /**
     * @return mixed
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    /**
     * @param mixed $dimension
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;
    }


    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getRemainingQuantity()
    {
        return $this->remainingQuantity;
    }

    /**
     * @param mixed $remainingQuantity
     */
    public function setRemainingQuantity($remainingQuantity)
    {
        $this->remainingQuantity = $remainingQuantity;
    }

    /**
     * @return null
     */
    public function getOrderedQuantity()
    {
        return $this->orderedQuantity;
    }

    /**
     * @param null $orderedQuantity
     */
    public function setOrderedQuantity($orderedQuantity)
    {
        $this->orderedQuantity = $orderedQuantity;
    }

    /**
     * @return null
     */
    public function getRemainingDate()
    {
        return $this->remainingDate;
    }

    /**
     * @param null $remainingDate
     */
    public function setRemainingDate($remainingDate)
    {
        $this->remainingDate = $remainingDate;
    }


    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_plugin_shop_products` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        PRIMARY KEY (`id`),
        `type` varchar(150) NOT NULL,
        `name` varchar(150) NOT NULL,
        UNIQUE (`type`, `name`),
        `slug` VARCHAR(100) DEFAULT NULL,
  		UNIQUE (`slug`),
        `price` decimal(7,2) NOT NULL,
        `poids` int(11) DEFAULT NULL,
        `dimension` int(11) DEFAULT NULL,
        `status` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
     * @return bool
     */
    public function show()
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_products WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(\PDO::FETCH_OBJ);
                $this->feed($row);

                $this->getDateLimit();
                $this->getStockLimit();
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @return bool
     */
    public function showBySlug()
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_products WHERE slug = :slug';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':slug', $this->slug);
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
     * @param $idCategory
     * @param bool $parentId
     * @param bool $countProducts
     * @return bool|array
     */
    public function showByCategory($idCategory, $parentId = false, $countProducts = false)
    {
        $categorySQL = ' AND C.id = :idCategory ';
        if (true === $parentId) {
            $categorySQL = ' AND (C.id = :idCategory OR C.parentId = :idCategory) ';
        }

        $sql = 'SELECT DISTINCT PRO.*, C.id AS idCategory, C.name AS categoryName, PROCONTENT.resume, PROCONTENT.content
        FROM '.TABLEPREFIX.'appoe_categoryRelations AS CR 
        RIGHT JOIN '.TABLEPREFIX.'appoe_plugin_shop_products AS PRO 
        ON(CR.typeId = PRO.id) 
        INNER JOIN '.TABLEPREFIX.'appoe_categories AS C
        ON(C.id = CR.categoryId)
        INNER JOIN '.TABLEPREFIX.'appoe_plugin_shop_products_content AS PROCONTENT
        ON(PROCONTENT.product_id = PRO.id)
        WHERE CR.type = "SHOP" AND PRO.status > 0 AND C.status > 0 AND PROCONTENT.lang = :lang' . $categorySQL . '
        GROUP BY PRO.id ORDER BY PRO.status DESC, PROCONTENT.updated_at DESC';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCategory', $idCategory);
        $stmt->bindValue(':lang', APP_LANG);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return (!$countProducts) ? $stmt->fetchAll(\PDO::FETCH_OBJ) : array('count' => $count, 'products' => $stmt->fetchAll(\PDO::FETCH_OBJ));
        }
    }

    /**
     * * @param bool $countProducts
     * @return array|bool
     */
    public function showAll($countProducts = false)
    {

        $featured = $this->status == 1 ? ' status >= 1' : ' status = ' . $this->status . ' ';
        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_products WHERE ' . $featured . ' ORDER BY status DESC, created_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $data = $stmt->fetchAll(\PDO::FETCH_OBJ);
            return (!$countProducts) ? $data : $count;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO '.TABLEPREFIX.'appoe_plugin_shop_products (type, name, slug, price, poids, dimension, created_at) 
                VALUES (:type, :name, :slug, :price, :poids, :dimension, CURDATE())';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':poids', $this->poids);
        $stmt->bindParam(':dimension', $this->dimension);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->setId($this->dbh->lastInsertId());
            return true;
        }
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE '.TABLEPREFIX.'appoe_plugin_shop_products SET type = :type, name = :name, 
        slug = :slug, price = :price, poids = :poids, dimension = :dimension, status = :status WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':poids', $this->poids);
        $stmt->bindParam(':dimension', $this->dimension);
        $stmt->bindParam(':status', $this->status);
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
     * @return bool
     */
    public function exist($forUpdate = false)
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_products WHERE (type = :type AND name = :name) OR slug = :slug';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->execute();
        $count = $stmt->rowCount();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                $row = $stmt->fetch(\PDO::FETCH_OBJ);

                if ($forUpdate) {
                    if ($this->id == $row->id) {
                        return false;
                    }
                }
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
    public function getDateLimit()
    {
        $sql = 'SELECT date_limit FROM '.TABLEPREFIX.'appoe_plugin_shop_stock WHERE product_id = :id AND date_limit IS NOT NULL';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                $row = $stmt->fetch(\PDO::FETCH_OBJ);

                $date_limit = new \DateTime($row->date_limit);
                $date_now = new \DateTime();
                $interval = $date_now->diff($date_limit);

                if ($interval->format('%R%a') >= 0) {
                    $this->remainingDate = $interval->days + 1;
                } else {
                    $this->remainingDate = false;
                }
            }

            return true;
        }
    }

    /**
     * @return bool
     */
    public function getStockLimit()
    {
        $sql = 'SELECT s.limit_quantity, SUM(cd.quantity) AS orderedQuantity
                FROM '.TABLEPREFIX.'appoe_plugin_shop_stock AS s
                LEFT JOIN '.TABLEPREFIX.'appoe_plugin_shop_commandes_details AS cd
                ON(s.product_id = cd.product_id)
                WHERE s.product_id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count > 0) {
                $row = $stmt->fetch(\PDO::FETCH_OBJ);

                if (!is_null($row->limit_quantity)) {

                    if ($row->limit_quantity - $row->orderedQuantity >= 0) {

                        //stock restant
                        $this->remainingQuantity = $row->limit_quantity - $row->orderedQuantity;
                        $this->orderedQuantity = $row->orderedQuantity;
                    } else {

                        //stock indisponible
                        $this->remainingQuantity = false;
                        $this->orderedQuantity = false;
                    }
                }
            }
            return true;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_shop_products WHERE id = :id;';
        $sql .= 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_shop_products_content WHERE product_id = :id;';
        $sql .= 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_shop_products_meta WHERE product_id = :id;';
        $sql .= 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_shop_stock WHERE product_id = :id;';

        $stmt = $this->dbh->prepare($sql);
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
     * Feed class attributs
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