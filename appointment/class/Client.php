<?php

namespace App\Plugin\Appointment;

use App\DB;

class Client
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_appointment_clients`';
    private $id;
    private $lastName;
    private $firstName;
    private $email;
    private $tel;
    private $options;
    private $status = 0;
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
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt(mixed $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function createTable(): bool
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `lastName` VARCHAR(100) NOT NULL,
                `firstName` VARCHAR(100) NOT NULL,
                `email` VARCHAR(100) NOT NULL,
                `tel` VARCHAR(30) NOT NULL,
                UNIQUE (`email`),
                `options` TEXT NULL DEFAULT NULL,
                `status` BOOLEAN NOT NULL DEFAULT FALSE,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        return DB::exec($sql);
    }

    public function show(): bool
    {
        return DB::show($this);
    }

    public function showByEmail(): bool
    {
        return DB::show($this, ['email']);
    }

    public function showAll(): false|array
    {
        return DB::showAll($this);
    }

    public function showPending(): false|array
    {
        return DB::showAll($this, ['status']);
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if ($return = DB::save($this, ['lastName', 'firstName', 'email', 'tel', 'options', 'status'])) {
            $lastInsertId = DB::lastInsertId();
            $this->setId($lastInsertId);
            appLog('Add Client -> lastName: ' . $this->lastName . ' firstName:' . $this->firstName . ' email:' . $this->email . ' tel:' . $this->tel . ' options:' . $this->options);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        if (DB::update($this, ['lastName', 'firstName', 'email', 'tel', 'options', 'status'], ['id'])) {
            appLog('Update Client -> lastName: ' . $this->lastName . ' firstName:' . $this->firstName . ' email:' . $this->email . ' tel:' . $this->tel . ' options:' . $this->options);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist(): bool
    {
        return DB::exist($this, ['email']);
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        if (DB::delete($this, ['id'])) {
            appLog('Delete Client -> id: ' . $this->id);
            return true;
        }
        return false;
    }
}