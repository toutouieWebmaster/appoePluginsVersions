<?php

namespace App\Plugin\InteractiveMap;
use App\DB;

/**
 *
 */
class InteractiveMap
{

    /**
     * @var mixed
     */
    private $id;
    /**
     * @var
     */
    private $title;
    /**
     * @var
     */
    private $data;
    /**
     * @var null
     */
    private $options = null;
    /**
     * @var
     */
    private $width;
    /**
     * @var
     */
    private $height;
    /**
     * @var null
     */
    private $status = null;

    /**
     * @var \PDO|null
     */
    private $dbh = null;

    /**
     * @param $id
     */
    public function __construct($id = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        if (!is_null($id)) {
            $this->id = $id;
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

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param null $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_plugin_interactiveMap` (
  					`id` mediumint(9) NOT NULL AUTO_INCREMENT,
  					PRIMARY KEY (`id`),
                    `title` varchar(250) NOT NULL,
                    UNIQUE (`title`),
                    `data` mediumtext NOT NULL,
                    `options` TEXT NULL DEFAULT NULL,
                    `width` smallint(6) NOT NULL DEFAULT 0,
                    `height` smallint(6) NOT NULL DEFAULT 0,
                    `status` tinyint(4) NOT NULL DEFAULT 1,
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

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_interactiveMap WHERE id = :id';

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
     * @param $countMap
     * @return array|bool
     */
    public function showAll($countMap = false)
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_interactiveMap WHERE status = 1 ORDER BY updated_at DESC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return !$countMap ? $stmt->fetchAll(\PDO::FETCH_OBJ) : $count;
        }
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $sql = 'INSERT INTO '.TABLEPREFIX.'appoe_plugin_interactiveMap (title, data, options, width, height) 
                VALUES (:title, :data, :options, :width, :height)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':data', $this->data);
        $stmt->bindParam(':options', $this->options);
        $stmt->bindParam(':width', $this->width);
        $stmt->bindParam(':height', $this->height);
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
    public function update()
    {

        $sql = 'UPDATE '.TABLEPREFIX.'appoe_plugin_interactiveMap SET options = :options, title = :title, width = :width, height = :height, status = :status WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':options', $this->options);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':width', $this->width);
        $stmt->bindParam(':height', $this->height);
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
    public function updateData()
    {
        $sql = 'UPDATE '.TABLEPREFIX.'appoe_plugin_interactiveMap SET data = :data WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':data', $this->data);
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

        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_interactiveMap WHERE id = :id';

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
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist($forUpdate = false): bool
    {

        $sql = 'SELECT id, title FROM '.TABLEPREFIX.'appoe_plugin_interactiveMap WHERE title = :title';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':title', $this->title);
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
     * @param $title
     * @return bool
     */
    public function usedTitle($title): bool
    {
        $sql = 'SELECT id FROM ' . TABLEPREFIX . 'appoe_plugin_interactiveMap WHERE title = :title';
        return DB::exec($sql, [':title' => $title])->rowCount() > 0;
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