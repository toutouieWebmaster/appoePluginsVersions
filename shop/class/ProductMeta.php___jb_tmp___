<?php

namespace App\Plugin\Shop;
class ProductMeta
{
    private $id;
    private $product_id;
    private $metaKey;
    private $metaValue;

    private $data;

    private $dbh = null;

    public function __construct($product_id = null)
    {

        if (is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($product_id)) {
            $this->product_id = intval($product_id);
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
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * @param null $product_id
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * @return mixed
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param mixed $metaKey
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }

    /**
     * @return mixed
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * @param mixed $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_shop_products_meta` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        PRIMARY KEY (`id`),
        `product_id` int(11) NOT NULL,
        `meta_key` varchar(150) NOT NULL,
        UNIQUE(`product_id`, `meta_key`),
        `meta_value` TEXT NOT NULL,
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
        $sql = 'SELECT * FROM appoe_plugin_shop_products_meta WHERE product_id = :product_id ORDER BY id ASC';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':product_id', $this->product_id, \PDO::PARAM_INT);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count > 0) {
                $this->data = $stmt->fetchAll(\PDO::FETCH_OBJ);
                return true;
            }
            return false;
        }
    }

    public function save()
    {
        $sql = 'INSERT INTO appoe_plugin_shop_products_meta (product_id, meta_key, meta_value) VALUES(:product_id, :meta_key, :meta_value)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':meta_key', $this->metaKey);
        $stmt->bindParam(':meta_value', $this->metaValue);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->id = $this->dbh->lastInsertId();
            return true;
        }
    }

    public function update()
    {
        $sql = 'UPDATE appoe_plugin_shop_products_meta SET meta_key = :meta_key, meta_value = :meta_value WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':meta_key', $this->metaKey);
        $stmt->bindParam(':meta_value', $this->metaValue);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->id = $this->dbh->lastInsertId();
            return true;
        }
    }

    public function exist($forUpdate = false)
    {
        $sql = 'SELECT * FROM appoe_plugin_shop_products_meta WHERE product_id = :product_id AND meta_key = :meta_key';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':meta_key', $this->metaKey);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count > 0) {
                if ($forUpdate) {
                    $data = $stmt->fetch(\PDO::FETCH_OBJ);
                    if ($this->product_id == $data->product_id) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = 'DELETE FROM appoe_plugin_shop_products_meta WHERE id = :id';

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
}