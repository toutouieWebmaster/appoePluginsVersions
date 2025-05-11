<?php

namespace App\Plugin\Shop;
class Client extends \App\Plugin\People\People
{

    function __construct($idClient = null)
    {
        parent::__construct();
        $this->type = 'SHOP';

        if (!is_null($idClient)) {
            $this->id = $idClient;
            $this->show();
        }

    }

    /**
     * Check if client does not exist
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id FROM '.TABLEPREFIX.'appoe_plugin_people WHERE type = :type AND email = :email';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':email', $this->email);
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
}