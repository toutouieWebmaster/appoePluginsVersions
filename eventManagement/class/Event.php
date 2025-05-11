<?php
namespace App\Plugin\EventManagement;
class Event {
	private $id;
	private $auteurId;
	private $titre;
	private $description;
	private $pitch = null;
	private $participation = null;
	private $duree = null;
	private $spectacleType = 1;
	private $indoor = 1;
	private $image = null;
	private $statut = true;

	private $dbh = null;

	public function __construct( $idEvent = null ) {

        if(is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

		if ( ! is_null( $idEvent ) ) {
			$this->id = $idEvent;
			$this->show();
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
	public function getAuteurId() {
		return $this->auteurId;
	}

	/**
	 * @param mixed $auteurId
	 */
	public function setAuteurId( $auteurId ) {
		$this->auteurId = $auteurId;
	}

	/**
	 * @return mixed
	 */
	public function getTitre() {
		return $this->titre;
	}

	/**
	 * @param mixed $titre
	 */
	public function setTitre( $titre ) {
		$this->titre = $titre;
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;
	}

	/**
	 * @return null
	 */
	public function getPitch() {
		return $this->pitch;
	}

	/**
	 * @param null $pitch
	 */
	public function setPitch( $pitch ) {
		$this->pitch = $pitch;
	}

	/**
	 * @return null
	 */
	public function getParticipation() {
		return $this->participation;
	}

	/**
	 * @param null $participation
	 */
	public function setParticipation( $participation ) {
		$this->participation = $participation;
	}

	/**
	 * @return null
	 */
	public function getDuree() {
		return $this->duree;
	}

	/**
	 * @param null $duree
	 */
	public function setDuree( $duree ) {
		$this->duree = $duree;
	}

	/**
	 * @return null
	 */
	public function getSpectacleType() {
		return $this->spectacleType;
	}

	/**
	 * @param null $spectacleType
	 */
	public function setSpectacleType( $spectacleType ) {
		$this->spectacleType = $spectacleType;
	}

	/**
	 * @return int
	 */
	public function getIndoor() {
		return $this->indoor;
	}

	/**
	 * @param int $indoor
	 */
	public function setIndoor( $indoor ) {
		$this->indoor = $indoor;
	}

	/**
	 * @return null
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @param null $image
	 */
	public function setImage( $image ) {
		$this->image = $image;
	}


	/**
	 * @return null
	 */
	public function getStatut() {
		return $this->statut;
	}

	/**
	 * @param null $statut
	 */
	public function setStatut( $statut ) {
		$this->statut = $statut;
	}

	/**
	 * @return bool
	 */
	public function createTable() {
		$sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_eventManagement` (
  				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
  				`auteurId` INT(11) UNSIGNED NOT NULL,
  				`titre` VARCHAR(250) NOT NULL,
  				`description` TEXT NOT NULL,
  				`pitch` VARCHAR(255) DEFAULT NULL,
  				`participation` TEXT,
  				`duree` VARCHAR(4) DEFAULT NULL,
  				`spectacleType` SMALLINT(1) UNSIGNED NOT NULL DEFAULT 1,
  				`indoor` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  				`image` VARCHAR(255) DEFAULT NULL,
  				`statut` TINYINT(1) NOT NULL DEFAULT 1,
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

		$sql = 'SELECT * FROM appoe_plugin_eventManagement WHERE id = :id';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':id', $this->id );
		$stmt->execute();

		$count = $stmt->rowCount();
		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			if ( $count == 1 ) {
				$row = $stmt->fetch( \PDO::FETCH_OBJ );
				$this->feed( $row );

				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * @param $statut
	 *
	 * @return array|bool
	 */
	public function showAll( $statut = true ) {

		$sql  = 'SELECT * FROM appoe_plugin_eventManagement WHERE statut = :statut ORDER BY created_at DESC';
		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':statut', $statut );
		$stmt->execute();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return $stmt->fetchAll( \PDO::FETCH_OBJ );

		}
	}


	/**
	 * @return bool
	 */
	public function save() {

		$sql = 'INSERT INTO appoe_plugin_eventManagement (auteurId, titre, description, pitch, participation, duree, spectacleType, indoor, image, created_at) VALUES (:auteurId, :titre, :description, :pitch, :participation, :duree, :spectacleType, :indoor, :image, CURDATE())';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':auteurId', $this->auteurId );
		$stmt->bindParam( ':titre', $this->titre );
		$stmt->bindParam( ':description', $this->description );
		$stmt->bindParam( ':pitch', $this->pitch );
		$stmt->bindParam( ':participation', $this->participation );
		$stmt->bindParam( ':duree', $this->duree );
		$stmt->bindParam( ':spectacleType', $this->spectacleType );
		$stmt->bindParam( ':indoor', $this->indoor );
		$stmt->bindParam( ':image', $this->image );
		$stmt->execute();

		$this->id = $this->dbh->lastInsertId();

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
	public function update() {

		$sql = 'UPDATE appoe_plugin_eventManagement SET auteurId = :auteurId, titre = :titre, description = :description, pitch = :pitch, participation = :participation, spectacleType = :spectacleType, indoor = :indoor, image = :image, statut = :statut WHERE id = :id';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':id', $this->id );
		$stmt->bindParam( ':auteurId', $this->auteurId );
		$stmt->bindParam( ':titre', $this->titre );
		$stmt->bindParam( ':description', $this->description );
		$stmt->bindParam( ':pitch', $this->pitch );
		$stmt->bindParam( ':participation', $this->participation );
		$stmt->bindParam( ':spectacleType', $this->spectacleType );
		$stmt->bindParam( ':indoor', $this->indoor );
		$stmt->bindParam( ':image', $this->image );
		$stmt->bindParam( ':statut', $this->statut );
		$stmt->execute();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return true;
		}
	}

	public function notExist() {

		$sql  = 'SELECT * FROM appoe_plugin_eventManagement WHERE titre = :titre AND statut  = TRUE';
		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':titre', $this->titre );
		$stmt->execute();
		$count = $stmt->rowCount();
		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return !($count == 1);
		}
	}


	/**
	 * @return bool
	 */
	public function delete() {
		$this->statut = 0;

		if ( $this->update() ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	public function uploadFile( $file ) {

		$upload_dir = FILE_DIR_PATH;
		if ( ! empty( $file['name'] ) ) {
			$error = $file['error'];
			if ( $error == UPLOAD_ERR_OK ) {
				$tmp_name = $file['tmp_name'];
				$filename = $this->cleanText( $file['name'] );
				$type     = $file['type'];
				$size     = $file['size'];
				if ( $size <= 2500000 ) {
					if ( $type == 'image/jpeg' || $type == 'image/png' || $type == 'image/gif' || $type == 'image/jpg' ) {
						if ( ! file_exists( $upload_dir . $filename ) ) {
							if ( move_uploaded_file( $tmp_name, $upload_dir . $filename ) === false ) {
								return false;
							}
						}
						$this->image = $filename;

						return true;
					}
				}
			}

		}

		return false;
	}

	/**
	 * @param $filename
	 *
	 * @return string
	 */
	public function cleanText( $filename ) {

		$special = array(
			' ',
			'\'',
			'à',
			'á',
			'â',
			'ã',
			'ä',
			'å',
			'ç',
			'è',
			'é',
			'ê',
			'ë',
			'ì',
			'í',
			'î',
			'ï',
			'ñ',
			'ò',
			'ó',
			'ô',
			'õ',
			'ö',
			'ù',
			'ú',
			'û',
			'ü',
			'ý',
			'ÿ',
			'À',
			'Á',
			'Â',
			'Ã',
			'Ä',
			'Å',
			'Ç',
			'È',
			'É',
			'Ê',
			'Ë',
			'Ì',
			'Í',
			'Î',
			'Ï',
			'Ñ',
			'Ò',
			'Ó',
			'Ô',
			'Õ',
			'Ö',
			'Ù',
			'Ú',
			'Û',
			'Ü',
			'Ý'
		);

		$normal = array(
			'_',
			'',
			'a',
			'a',
			'a',
			'a',
			'a',
			'a',
			'c',
			'e',
			'e',
			'e',
			'e',
			'i',
			'i',
			'i',
			'i',
			'n',
			'o',
			'o',
			'o',
			'o',
			'o',
			'u',
			'u',
			'u',
			'u',
			'y',
			'y',
			'A',
			'A',
			'A',
			'A',
			'A',
			'A',
			'C',
			'E',
			'E',
			'E',
			'E',
			'E',
			'I',
			'I',
			'I',
			'I',
			'N',
			'O',
			'O',
			'O',
			'O',
			'O',
			'U',
			'U',
			'U',
			'U',
			'Y'
		);

		$filename = str_replace( $special, $normal, $filename );

		return 'event_' . strtoupper($filename);
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