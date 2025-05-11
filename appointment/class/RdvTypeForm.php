<?php

namespace App\Plugin\Appointment;

use App\DB;

class RdvTypeForm
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_appointment_rdvtypesform`';
    private $id;
    private $idAgenda;
    private $idRdvType;
    private $name;
    private $slug;
    private $type;
    private $placeholder;
    private $required = 1;
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
    public function getIdRdvType()
    {
        return $this->idRdvType;
    }

    /**
     * @param mixed $idRdvType
     */
    public function setIdRdvType($idRdvType)
    {
        $this->idRdvType = $idRdvType;
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
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param mixed $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return int
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param int $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
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
                `idRdvType` TINYINT UNSIGNED NOT NULL,
                `name` VARCHAR(100) NOT NULL,
                `slug` VARCHAR(100) NOT NULL,
                `type` VARCHAR(100) NOT NULL,
                `placeholder` VARCHAR(250) NULL DEFAULT NULL,
                `required` BOOLEAN NOT NULL DEFAULT TRUE,
                `position` TINYINT UNSIGNED NOT NULL,
                UNIQUE (`idAgenda`,`idRdvType`,`name`),
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
        return DB::showAll($this, ['idRdvType'], 'ORDER BY position ASC');
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (DB::save($this, ['idAgenda', 'idRdvType', 'name', 'slug', 'type', 'placeholder', 'required', 'position'])) {
            appLog('Add RdvTypeForm -> idAgenda: ' . $this->idAgenda . ' idRdvType:' . $this->idRdvType . ' name:' . $this->name);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        if (DB::update($this, ['name', 'slug', 'type', 'placeholder', 'required', 'position'], ['id'])) {
            appLog('Update RdvTypeForm -> id: ' . $this->id . ' name:' . $this->name);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        return DB::exist($this, ['idAgenda', 'idRdvType', 'slug']);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (DB::delete($this, ['id'])) {
            appLog('Delete RdvTypeForm -> id: ' . $this->id);
            return true;
        }
        return false;
    }
}