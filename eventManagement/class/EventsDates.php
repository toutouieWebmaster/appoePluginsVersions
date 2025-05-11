<?php
namespace App\Plugin\EventManagement;
class EventsDates {
	private $id;
	private $eventId;
	private $dateDebut;
	private $dateFin;
	private $localisation = null;

	private $dbh = null;

	public function __construct() {

        if(is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }
	}

	/**
	 * @return null
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param null $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getEventId() {
		return $this->eventId;
	}

	/**
	 * @param mixed $eventId
	 */
	public function setEventId( $eventId ) {
		$this->eventId = $eventId;
	}

	/**
	 * @return mixed
	 */
	public function getDateDebut() {
		return $this->dateDebut;
	}

	/**
	 * @param mixed $dateDebut
	 */
	public function setDateDebut( $dateDebut ) {
		$this->dateDebut = $dateDebut;
	}

	/**
	 * @return mixed
	 */
	public function getDateFin() {
		return $this->dateFin;
	}

	/**
	 * @param mixed $dateFin
	 */
	public function setDateFin( $dateFin ) {
		$this->dateFin = $dateFin;
	}

	/**
	 * @return mixed
	 */
	public function getLocalisation() {
		return $this->localisation;
	}

	/**
	 * @param mixed $localisation
	 */
	public function setLocalisation( $localisation ) {
		$this->localisation = $localisation;
	}

	/**
	 * @return bool
	 */
	public function createTable() {
		$sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_eventManagement_dates` (
  				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
  				`eventId` INT(11) UNSIGNED NOT NULL,
  				`dateDebut` DATETIME NOT NULL,
  				`dateFin` DATETIME NOT NULL,
  				`localisation` VARCHAR(5) DEFAULT NULL,
  				`created_at` DATE NOT NULL,
  				`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->execute();
		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function show() {

		$sql = 'SELECT * FROM appoe_plugin_eventManagement_dates WHERE id = :id';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':id', $this->id );
		$stmt->execute();

		$count = $stmt->rowCount();
		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			if ( $count == 1 ) {
				$row = $stmt->fetch(\PDO::FETCH_OBJ );
				$this->feed( $row );

				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * @return array|bool
	 */
	public function showAll() {

		$sql  = 'SELECT * FROM appoe_plugin_eventManagement_dates ORDER BY created_at DESC';
		$stmt = $this->dbh->prepare( $sql );
		$stmt->execute();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return $stmt->fetchAll(\PDO::FETCH_OBJ );

		}
	}

	/**
	 * @return array|bool
	 */
	public function showAllEvent() {

		$sql  = 'SELECT * FROM appoe_plugin_eventManagement_dates WHERE eventId = :eventId ORDER BY dateDebut ASC';
		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':eventId', $this->eventId );
		$stmt->execute();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return $stmt->fetchAll(\PDO::FETCH_OBJ );

		}
	}

	public function notExist() {

		$sql  = 'SELECT * FROM appoe_plugin_eventManagement_dates WHERE eventId = :eventId AND ((dateDebut BETWEEN :dateDebut AND :dateFin) OR (dateFin BETWEEN :dateDebut AND :dateFin))';
		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':eventId', $this->eventId );
		$stmt->bindParam( ':dateDebut', $this->dateDebut );
		$stmt->bindParam( ':dateFin', $this->dateFin );
		$stmt->execute();
		$count = $stmt->rowCount();
		$error = $stmt->errorInfo();

		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return !($count >= 1);
		}
	}


	/**
	 * @return bool
	 */
	public function save() {

		$sql = 'INSERT INTO appoe_plugin_eventManagement_dates (eventId, dateDebut, dateFin, localisation, created_at) 
                VALUES (:eventId, :dateDebut, :dateFin, :localisation, CURDATE())';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':eventId', $this->eventId );
		$stmt->bindParam( ':dateDebut', $this->dateDebut );
		$stmt->bindParam( ':dateFin', $this->dateFin );
		$stmt->bindParam( ':localisation', $this->localisation );
		$stmt->execute();

		$id = $this->dbh->lastInsertId();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			$this->setId( $id );

			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function update() {

		$sql = 'UPDATE appoe_plugin_eventManagement_dates SET eventId = :eventId, dateDebut = :dateDebut, dateFin = :dateFin, localisation = :localisation WHERE id = :id';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':id', $this->id );
		$stmt->bindParam( ':eventId', $this->eventId );
		$stmt->bindParam( ':dateDebut', $this->dateDebut );
		$stmt->bindParam( ':dateFin', $this->dateFin );
		$stmt->bindParam( ':localisation', $this->localisation );
		$stmt->execute();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return true;
		}
	}


	/**
	 * @return bool
	 */
	public function delete() {

		$sql = 'DELETE FROM appoe_plugin_eventManagement_dates WHERE id = :id';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':id', $this->id );
		$stmt->execute();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Feed class attributs
	 *
	 * @param $data
	 */
	public function feed( $data ) {
		foreach ( $data as $attribut => $value ) {
			$method = 'set' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $attribut ) ) );

			if ( is_callable( array( $this, $method ) ) ) {
				$this->$method( $value );
			}
		}
	}
}