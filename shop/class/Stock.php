<?php
namespace App\Plugin\Shop;
use App\DB;

class Stock
{
    private $id;
    private $product_id;
    private $limit_quantity = null;
    private $date_limit = null;
    private $status = 1;

    private $dbh = null;

    public function __construct($id_stock = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        if (!is_null($id_stock)) {
            $this->id = $id_stock;
            $this->show();
        }
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * @param mixed $product_id
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * @return null
     */
    public function getLimitQuantity()
    {
        return $this->limit_quantity;
    }

    /**
     * @param null $limit_quantity
     */
    public function setLimitQuantity($limit_quantity)
    {
        $this->limit_quantity = $limit_quantity;
    }

    /**
     * @return null
     */
    public function getDateLimit()
    {
        return $this->date_limit;
    }

    /**
     * @param null $date_limit
     */
    public function setDateLimit($date_limit)
    {
        $this->date_limit = $date_limit;
    }

    /**
     * @return bool
     */
    public function isStatus()
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

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_plugin_shop_stock` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        PRIMARY KEY (`id`),
        `product_id` int(11) NOT NULL,
        `limit_quantity` int(11) NULL DEFAULT NULL,
        `date_limit` date NULL DEFAULT NULL,
        `status` tinyint(1) NOT NULL DEFAULT 1,
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

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_stock WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $row = $stmt->fetch(\PDO::FETCH_OBJ);
            $this->feed($row);
            return true;
        }
    }

    /**
     * @return array|bool
     */
    public function showAll()
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_stock WHERE status = TRUE ORDER BY updated_at DESC';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }

    public function getStock()
    {
        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_stock WHERE product_id = :product_id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':product_id', $this->product_id);
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

        $sql = 'INSERT INTO '.TABLEPREFIX.'appoe_plugin_shop_stock (product_id, limit_quantity, date_limit, status) 
                VALUES (:product_id, :limit_quantity, :date_limit, :status)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':limit_quantity', $this->limit_quantity);
        $stmt->bindParam(':date_limit', $this->date_limit);
        $stmt->bindParam(':status', $this->status);
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

        $sql = 'UPDATE '.TABLEPREFIX.'appoe_plugin_shop_stock SET product_id = :product_id, limit_quantity = :limit_quantity, date_limit = :date_limit, status = :status WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':limit_quantity', $this->limit_quantity);
        $stmt->bindParam(':date_limit', $this->date_limit);
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
     * @return bool
     */
    public function exist()
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_stock WHERE product_id = :product_id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->execute();
        $count = $stmt->rowCount();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return 'Une erreur s\'est produite !';
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
    public function delete()
    {
        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_shop_stock WHERE id = :id';

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