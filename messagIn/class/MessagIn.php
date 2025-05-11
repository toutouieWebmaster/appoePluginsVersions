<?php
namespace App\Plugin\MessagIn;
class MessagIn {

	private $id;
	private $fromUser;
	private $toUser;
	private $text;
	private $statut = false;

	private $data;
	private $dbh = null;

	public function __construct( $idUser = null ) {

        if(is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

		if ( ! is_null( $idUser ) ) {
			$this->toUser = $idUser;
			$this->showAllUnread();
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
	public function getFromUser() {
		return $this->fromUser;
	}

	/**
	 * @param mixed $fromUser
	 */
	public function setFromUser( $fromUser ) {
		$this->fromUser = $fromUser;
	}

	/**
	 * @return mixed
	 */
	public function getToUser() {
		return $this->toUser;
	}

	/**
	 * @param mixed $toUser
	 */
	public function setToUser( $toUser ) {
		$this->toUser = $toUser;
	}

	/**
	 * @return mixed
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * @param mixed $text
	 */
	public function setText( $text ) {
		$this->text = $text;
	}

	/**
	 * @return mixed
	 */
	public function getStatut() {
		return $this->statut;
	}

	/**
	 * @param mixed $statut
	 */
	public function setStatut( $statut ) {
		$this->statut = $statut;
	}

	/**
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData( $data ) {
		$this->data = $data;
	}

	public function createTable() {
		$sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_plugin_messagIn` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`fromUser` INT(11) UNSIGNED NOT NULL,
                	`toUser` INT(11) UNSIGNED NOT NULL,
                	`text` TEXT NOT NULL,
                	`statut` TINYINT(1) NOT NULL DEFAULT 0,
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

		$sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_messagIn WHERE id = :id';

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

		$sql  = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_messagIn WHERE toUser = :toUser ORDER BY statut DESC, created_at DESC';
		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':toUser', $this->toUser );
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
	public function showAllUnread() {

		$sql  = 'SELECT * FROM '.TABLEPREFIX.'appoe_plugin_messagIn WHERE toUser = :toUser AND statut = FALSE ORDER BY created_at';
		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':toUser', $this->toUser );
		$stmt->execute();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			$this->data = $stmt->fetchAll(\PDO::FETCH_OBJ );

			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function save() {

		$sql = 'INSERT INTO '.TABLEPREFIX.'appoe_plugin_messagIn (fromUser, toUser, text, created_at) 
                VALUES (:fromUser, :toUser, :text, CURDATE())';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':fromUser', $this->fromUser );
		$stmt->bindParam( ':toUser', $this->toUser );
		$stmt->bindParam( ':text', $this->text );
		$stmt->execute();

		$clientId = $this->dbh->lastInsertId();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			$this->setId( $clientId );

			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function update() {

		$sql = 'UPDATE '.TABLEPREFIX.'appoe_plugin_messagIn SET fromUser = :fromUser, toUser = :toUser, text = :text, statut = :statut WHERE id = :id';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':fromUser', $this->fromUser );
		$stmt->bindParam( ':toUser', $this->toUser );
		$stmt->bindParam( ':text', $this->text );
		$stmt->bindParam( ':statut', $this->statut );
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
	 * @return bool
	 */
	public function delete() {

		$sql = 'DELETE FROM '.TABLEPREFIX.'appoe_plugin_messagIn WHERE id = :id';

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