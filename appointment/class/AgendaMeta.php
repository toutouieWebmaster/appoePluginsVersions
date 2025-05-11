<?php

namespace App\Plugin\Appointment;

use App\DB;

class AgendaMeta
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_appointment_agendasmetas`';
    private $id;
    private $idAgenda;
    private $metaKey;
    private $metaValue;
    private $position;
    private $updatedAt;

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
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
    public function getIdAgenda()
    {
        return $this->idAgenda;
    }

    /**
     * @param mixed $idAgenda
     */
    public function setIdAgenda($idAgenda)
    {
        $this->idAgenda = $idAgenda;
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
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
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

    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' (
                `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `idAgenda` TINYINT UNSIGNED NOT NULL,
                `metaKey` VARCHAR(255) NOT NULL,
  				`metaValue` TEXT NOT NULL,
                UNIQUE (`idAgenda`, `metaKey`),
                `position` TINYINT UNSIGNED NOT NULL,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        return DB::exec($sql);
    }

    public function show()
    {
        return DB::show($this);
    }

    public function showAll()
    {
        return DB::showAll($this, ['idAgenda'], 'ORDER BY position ASC');
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (DB::save($this, ['idAgenda', 'metaKey', 'metaValue', 'position'])) {
            appLog('Add agendaMeta -> idAgenda: ' . $this->idAgenda . ' metaKey: ' . $this->metaKey . ' metaValue: ' . $this->metaValue. ' position: ' . $this->position);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        if (DB::update($this,['metaKey', 'metaValue', 'position'], ['id'])) {
            appLog('Update agendaMeta -> id: ' . $this->id . ' metaKey: ' . $this->metaKey. ' metaValue: ' . $this->metaValue. ' position: ' . $this->position);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        return DB::exist($this, ['idAgenda', 'metaKey']);
    }

    public function count()
    {
        return DB::count($this, ['idAgenda']);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (DB::delete($this, ['id'])) {
            appLog('Delete agendaMeta -> id: ' . $this->id);
            return true;
        }
        return false;
    }
}