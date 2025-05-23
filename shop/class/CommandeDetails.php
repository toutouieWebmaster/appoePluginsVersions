<?php

namespace App\Plugin\Shop;
class CommandeDetails
{
    private $commandeId;
    private $product_id;
    private $quantity;
    private $price;
    private $poids;

    private $dbh = null;

    public function __construct($commandeId = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($commandeId)) {
            $this->commandeId = $commandeId;
        }
    }


    /**
     * @return mixed
     */
    public function getCommandeId()
    {
        return $this->commandeId;
    }

    /**
     * @param mixed $commandeId
     */
    public function setCommandeId($commandeId)
    {
        $this->commandeId = $commandeId;
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
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
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

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_plugin_shop_commandes_details` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`),
          `commandeId` int(11) NOT NULL,
          `product_id` int(11) NOT NULL,
          UNIQUE (`commandeId`, `product_id`),
          `quantity` int(11) NOT NULL,
          `price` decimal(10,2) NOT NULL,
          `poids` int(11) NOT NULL,
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
     * @param $idProduct // check if product exist in command
     * @return array|bool
     */
    public function show($idProduct = false)
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_commandes_details WHERE commandeId = :commandeId ';

        if ($idProduct) {
            $sql .= ' AND product_id = :idProduct';
        }

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':commandeId', $this->commandeId, \PDO::PARAM_INT);

        if ($idProduct) {
            $stmt->bindParam(':idProduct', $idProduct, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {

            if (!$idProduct) {
                return $stmt->fetchAll(\PDO::FETCH_OBJ);
            }
            return $count == 1;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO '.TABLEPREFIX.'appoe_plugin_shop_commandes_details (commandeId, product_id, quantity, price, poids, created_at) 
                VALUES (:commandeId, :product_id, :quantity, :price, :poids, NOW())';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':commandeId', $this->commandeId);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':poids', $this->poids);
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
        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_shop_commandes_details WHERE commandeId = :commandeId';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':commandeId', $this->commandeId);
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