<?php

namespace App\Plugin\Shop;
class Commande
{
    private $id;
    private $client_id = null;
    private $total = null;
    private $total_transport = null;
    private $orderState = 2;
    private $deliveryState = 1;
    private $preBilling = null;
    private $billing = null;
    private $status = 1;
    private $createdAt;
    private $updatedAt;

    private $dbh = null;

    public function __construct($id_commande = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($id_commande)) {
            $this->id = $id_commande;
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
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param mixed $client_id
     */
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * Total : prix produits + TVA + Frais de transport
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getTotalTransport()
    {
        return $this->total_transport;
    }

    /**
     * @param mixed $total_transport
     */
    public function setTotalTransport($total_transport)
    {
        $this->total_transport = $total_transport;
    }

    /**
     * @return int
     */
    public function getOrderState()
    {
        return $this->orderState;
    }

    /**
     * @param int $orderState
     */
    public function setOrderState($orderState)
    {
        $this->orderState = $orderState;
    }

    /**
     * @return int
     */
    public function getDeliveryState()
    {
        return $this->deliveryState;
    }

    /**
     * @param int $deliveryState
     */
    public function setDeliveryState($deliveryState)
    {
        $this->deliveryState = $deliveryState;
    }

    /**
     * @return null
     */
    public function getPreBilling()
    {
        return $this->preBilling;
    }

    /**
     * @param null $preBilling
     */
    public function setPreBilling($preBilling)
    {
        $this->preBilling = $preBilling;
    }

    /**
     * @return null
     */
    public function getBilling()
    {
        return $this->billing;
    }

    /**
     * @param null $billing
     */
    public function setBilling($billing)
    {
        $this->billing = $billing;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_plugin_shop_commandes` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `client_id` int(11) NULL DEFAULT NULL,
                `total` decimal(10,2) NULL DEFAULT NULL,
                `total_transport` decimal(6,2) NULL DEFAULT NULL,
                `deliveryState` smallint(1) NOT NULL DEFAULT 2,
                `orderState` smallint(1) NOT NULL DEFAULT 1,
                `preBilling` varchar(2) NOT NULL,
                `billing` int(11) UNSIGNED NOT NULL,
                `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
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

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_commandes WHERE id = :id';

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
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @param $status
     * @param $minStatus
     * @param $countCommandes
     * @param int|bool $orderState
     * @return array|bool
     */
    public function showAll($status = 1, $minStatus = false, $countCommandes = false, $orderState = false)
    {
        $orderStateSql = '';
        if (is_int($orderState)) {
            $orderStateSql = ' AND orderState = ' . $orderState;
        }
        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_commandes WHERE status ' . ($minStatus ? '>=' : '=') . ' :status ' . $orderStateSql . ' ORDER BY created_at DESC';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countCommandes ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @return array|bool
     */
    public function clearIncompletCommandes()
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_shop_commandes WHERE orderState = 2 AND created_at < current_timestamp - interval "600" second';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            while ($row = $stmt->fetch(\PDO::FETCH_OBJ)) {
                $this->id = $row->id;
                $this->delete();
            }
            return true;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->checkBilling();

        if (!is_null($this->billing) && !is_null($this->preBilling)) {

            $sql = 'INSERT INTO '.TABLEPREFIX.'appoe_plugin_shop_commandes (client_id, total, total_transport, orderState, deliveryState, preBilling, billing, created_at) 
                VALUES (:client_id, :total, :total_transport, :orderState, :deliveryState, :preBilling, :billing, NOW())';

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':client_id', $this->client_id);
            $stmt->bindParam(':total', $this->total);
            $stmt->bindParam(':total_transport', $this->total_transport);
            $stmt->bindParam(':orderState', $this->orderState);
            $stmt->bindParam(':deliveryState', $this->deliveryState);
            $stmt->bindParam(':preBilling', $this->preBilling);
            $stmt->bindParam(':billing', $this->billing);
            $stmt->execute();

            $this->setId($this->dbh->lastInsertId());

            $error = $stmt->errorInfo();
            if ($error[0] != '00000') {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE '.TABLEPREFIX.'appoe_plugin_shop_commandes SET client_id = :client_id, total = :total, total_transport = :total_transport, orderState = :orderState, deliveryState = :deliveryState, status = :status WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':client_id', $this->client_id);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':total_transport', $this->total_transport);
        $stmt->bindParam(':orderState', $this->orderState);
        $stmt->bindParam(':deliveryState', $this->deliveryState);
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
    public function delete()
    {
        $sql = 'UPDATE '.TABLEPREFIX.'appoe_plugin_shop_commandes SET orderState = 1, status = 0 WHERE id = :id;';
        $sql .= 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_shop_commandes_details WHERE commandeId = :id;';

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

    public function checkBilling()
    {
        $this->preBilling = date('y');

        $sql = 'SELECT preBilling, billing FROM '.TABLEPREFIX.'appoe_plugin_shop_commandes WHERE preBilling = :preBilling ORDER BY billing DESC LIMIT 0, 1';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':preBilling', $this->preBilling);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $row = $stmt->fetch(\PDO::FETCH_OBJ);
            if ($count == 1) {
                $this->billing = $row->billing + 1;
            } else {
                $this->billing = 1;
            }
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