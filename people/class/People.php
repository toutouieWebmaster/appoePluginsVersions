<?php

namespace App\Plugin\People;

use App\DB;
use PDO;

class People
{
    protected $id;
    protected $type;
    protected $nature = null;
    protected $name;
    protected $firstName = null;
    protected $entitled = null;
    protected $birthDate = null;
    protected $email = null;
    protected $tel = null;
    protected $address = null;
    protected $zip = null;
    protected $city = null;
    protected $country = null;
    protected $idUser = null;
    protected $options = null;
    protected $status = 1;
    protected $createdAt;
    protected $updatedAt;

    private $data;
    protected $dbh = null;

    public function __construct($idPerson = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        if (!is_null($idPerson)) {
            $this->id = $idPerson;
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
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * @param mixed $nature
     */
    public function setNature($nature)
    {
        $this->nature = $nature;
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
     * @return null
     */
    public function getEntitled()
    {
        return $this->entitled;
    }

    /**
     * @param null $entitled
     */
    public function setEntitled($entitled)
    {
        $this->entitled = $entitled;
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param mixed $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
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
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return null
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param null $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    /**
     * @return null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param null $options
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
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param int $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
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
        $sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_plugin_people` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `type` VARCHAR(150) NOT NULL,
                `nature` VARCHAR(150) DEFAULT NULL,
                `name` VARCHAR(150) NOT NULL,
                `firstName` VARCHAR(150) DEFAULT NULL,
                `entitled` VARCHAR(350) DEFAULT NULL,
                `birthDate` DATE DEFAULT NULL,
                `email` VARCHAR(255) DEFAULT NULL,
                UNIQUE (`type`, `name`, `firstName`, `email`, `address`),
                `tel` VARCHAR(15) DEFAULT NULL,
                `address` VARCHAR(255) DEFAULT NULL,
                `zip` VARCHAR(7) DEFAULT NULL,
                `city` VARCHAR(100) DEFAULT NULL,
                `country` VARCHAR(100) DEFAULT NULL,
                `idUser` INT(11) DEFAULT NULL,
                `options` TEXT,
                `status` TINYINT(1) NOT NULL DEFAULT 1,
                `createdAt` DATETIME NOT NULL,
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

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_people WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                return true;

            } else {

                return false;
            }
        }
    }

    /**
     * @param $countPeople
     * @return array|int
     */
    public function showAll($countPeople = false)
    {
        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_people WHERE status = :status ORDER BY name ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);

            return (!$countPeople) ? $data : $count;
        }
    }

    /**
     * @param $countPeople
     * @return array|int
     */
    public function showByType($countPeople = false)
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_people WHERE type = :type AND status = :status ORDER BY name ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);

            return (!$countPeople) ? $data : $count;
        }
    }

    /**
     * @return array|bool
     */
    public function showTypes()
    {

        $sql = 'SELECT type FROM '.TABLEPREFIX.'appoe_plugin_people GROUP BY type ORDER BY name ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] == '00000') {
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        return false;
    }

    /**
     * @param array $data
     * @return array|int
     */
    public function showDataForExport(array $data)
    {
        $type = !empty($this->type) ? ' type = :type AND ' : '';
        $sql = 'SELECT ' . implode(', ', $data) . ' FROM '.TABLEPREFIX.'appoe_plugin_people WHERE ' . $type . ' status = :status ORDER BY name ASC';
        $stmt = $this->dbh->prepare($sql);

        if (!empty($this->type)) {
            $stmt->bindParam(':type', $this->type);
        }
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->entitled = $this->name . ' ' . $this->firstName;

        $sql = 'INSERT INTO '.TABLEPREFIX.'appoe_plugin_people 
                (type, nature, name, firstName, entitled, birthDate, email, tel, address, zip, city, idUser, country, options, status, createdAt) 
                VALUES (:type, :nature, :name, :firstName, :entitled, :birthDate, :email, :tel, :address, :zip, :city, :idUser, :country, :options, :status, NOW())';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':nature', $this->nature);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':entitled', $this->entitled);
        $stmt->bindParam(':firstName', $this->firstName);
        $stmt->bindParam(':birthDate', $this->birthDate);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':tel', $this->tel);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':zip', $this->zip);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':idUser', $this->idUser);
        $stmt->bindParam(':options', $this->options);
        $stmt->bindParam(':status', $this->status);
        $stmt->execute();

        $personId = $this->dbh->lastInsertId();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->id = $personId;

            return true;
        }
    }

    /**
     * @return bool
     */
    public function update()
    {

        $this->entitled = $this->name . ' ' . $this->firstName;

        $sql = 'UPDATE '.TABLEPREFIX.'appoe_plugin_people SET type = :type, nature = :nature, name = :name, firstName = :firstName, 
                entitled = :entitled, birthDate = :birthDate, email = :email, tel = :tel, address = :address, zip = :zip, 
                city = :city, country = :country, idUser = :idUser, options = :options, status = :status 
                WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':nature', $this->nature);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':firstName', $this->firstName);
        $stmt->bindParam(':entitled', $this->entitled);
        $stmt->bindParam(':birthDate', $this->birthDate);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':tel', $this->tel);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':zip', $this->zip);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':idUser', $this->idUser);
        $stmt->bindParam(':options', $this->options);
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
        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_people WHERE id = :id';

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
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id FROM '.TABLEPREFIX.'appoe_plugin_people WHERE type = :type AND name = :name 
        AND firstName = :firstName AND email = :email AND address = :address';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':firstName', $this->firstName);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':address', $this->address);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                if ($forUpdate) {
                    $data = $stmt->fetch(PDO::FETCH_OBJ);
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
     * Auth people by password in options
     * @return bool
     */
    public function authPeople()
    {
        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_people WHERE type = :type AND email = :email';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':type', $this->type);
        $stmt->execute();
        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $options = unserialize($row->options);
                if (password_verify($this->options, $options['password'])) {
                    $this->feed($row);

                    return true;
                } else {
                    return false; // Le mot de passe n'est pas correct;
                }
            } else {
                return false; // La personne n'existe pas;
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