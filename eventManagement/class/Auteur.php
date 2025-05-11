<?php

namespace App\Plugin\EventManagement;
class Auteur extends \App\Plugin\People\People
{
    function __construct($idAuteur = null)
    {
        parent::__construct();
        $this->type = 'EVENTMANAGEMENT';

        if (!is_null($idAuteur)) {
            $this->id = $idAuteur;
            $this->show();
        }

    }

    /**
     * Check if auteur does not exist
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id FROM appoe_plugin_people WHERE type = :type AND name = :name';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
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