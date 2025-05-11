<?php

namespace App\Plugin\Rating;
class Rating
{
    protected $id;
    protected $type;
    protected $typeId;
    protected $user;
    protected $score;
    protected $status;
    protected $updatedAt;

    private $data;
    private $dbh = null;

    public function __construct($type = null, $typeId = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

        if (!is_null($type) && !is_null($typeId)) {
            $this->type = $type;
            $this->typeId = $typeId;
            $this->showByType();
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
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param mixed $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
        $sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_plugin_rating` (
  					`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    PRIMARY KEY (`id`),
                    `type` VARCHAR(150) NOT NULL,
                    `typeId` INT(11) UNSIGNED NOT NULL,
                    `user` VARCHAR(255) NOT NULL,
                    UNIQUE (`type`, `typeId`, `user`),
                    `score` TINYINT(2) NOT NULL,
                    `status` TINYINT(1) NOT NULL DEFAULT 0,
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
        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_rating WHERE id = :id';
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
     * @param $countRating
     * * @param $status
     * @return array|int
     */
    public function showByType($countRating = false, $status = 1)
    {
        if (is_int($status)) {
            $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_rating WHERE type = :type AND typeId = :typeId AND status = :status ORDER BY updated_at DESC';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':type', $this->type);
            $stmt->bindParam(':typeId', $this->typeId);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            $count = $stmt->rowCount();
            $error = $stmt->errorInfo();
            if ($error[0] != '00000') {
                return false;
            } else {
                $this->data = $stmt->fetchAll(\PDO::FETCH_OBJ);

                return (!$countRating) ? $this->data : $count;
            }
        }
        return false;
    }

    /**
     * @param $countRating
     * @param $status
     * @return array|int
     */
    public function showAll($countRating = false, $status = 1)
    {
        if (is_int($status)) {
            $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_rating WHERE status = :status ORDER BY updated_at DESC';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            $count = $stmt->rowCount();
            $error = $stmt->errorInfo();
            if ($error[0] != '00000') {
                return false;
            } else {
                $this->data = $stmt->fetchAll(\PDO::FETCH_OBJ);

                return (!$countRating) ? $this->data : $count;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $sql = 'INSERT INTO '.TABLEPREFIX.'appoe_plugin_rating (type, typeId, user, score) VALUES (:type, :typeId, :user, :score)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':typeId', $this->typeId);
        $stmt->bindParam(':user', $this->user);
        $stmt->bindParam(':score', $this->score);
        $stmt->execute();

        $this->id = $this->dbh->lastInsertId();

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
    public function update()
    {
        $sql = 'UPDATE '.TABLEPREFIX.'appoe_plugin_rating SET type = :type, typeId = :typeId, user = :user, score = :score, status = :status WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':typeId', $this->typeId);
        $stmt->bindParam(':user', $this->user);
        $stmt->bindParam(':score', $this->score);
        $stmt->bindParam(':status', $this->status);
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
        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_rating WHERE id = :id';

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
     * @return bool
     */
    public function deleteAll()
    {
        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_rating WHERE type = :type AND typeId = :typeId';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':typeId', $this->typeId);
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
        $sql = 'SELECT id FROM '.TABLEPREFIX.'appoe_plugin_rating WHERE type = :type AND typeId = :typeId AND user = :user';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':typeId', $this->typeId);
        $stmt->bindParam(':user', $this->user);
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