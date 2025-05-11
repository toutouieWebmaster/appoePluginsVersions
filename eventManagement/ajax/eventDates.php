<?php
require_once( 'header.php' );
if ( checkAjaxRequest() ) {

	if ( getUserIdSession() ) {

		//delete Event Date
		if ( ! empty( $_POST['deleteDateEvent'] )) {
			$EventDates = new \App\Plugin\EventManagement\EventsDates();
			$EventDates->setId( $_POST['deleteDateEvent'] );
			if ( $EventDates->delete() ) {
				echo 'true';
			}
		}

		//add Event Date
		if (
			! empty( $_POST['addDateEvent'] )
			&& ! empty( $_POST['eventId'] )
			&& ! empty( $_POST['dateDebut'] )
			&& ! empty( $_POST['heureDebut'] )
			&& ! empty( $_POST['_token'] )
			&& $_POST['_token'] == $_SESSION['_token']
		) {

			$Event = new \App\Plugin\EventManagement\Event( $_POST['eventId'] );

			$DateDebut = new DateTime( $_POST['dateDebut'] . $_POST['heureDebut'] );
			$DateFin   = new DateTime( $_POST['dateDebut'] . $_POST['heureDebut'] );

			$localisation = ! empty( $_POST['localisation'] ) ? $_POST['localisation'] : null;

			$dureeEvent = $Event->getDuree();
			$dureelng   = strlen( $dureeEvent );

			if ( $dureelng == 2 ) {
				$interval = $dureeEvent . 'M';
			} else {
				list( $heures, $minutes ) = explode( 'h', $dureeEvent );
				$interval = $heures . 'H' . $minutes . 'M';
			}

			$DateFin->add( new DateInterval( 'PT' . $interval ) );

			$EventDates = new \App\Plugin\EventManagement\EventsDates();
			$EventDates->setEventId( $Event->getId() );
			$EventDates->setDateDebut( $DateDebut->format( 'Y-m-d H:i:s' ) );
			$EventDates->setDateFin( $DateFin->format( 'Y-m-d H:i:s' ) );
			$EventDates->setLocalisation( $localisation );
			if ( $EventDates->notExist() ) {
				if ( $EventDates->save() ) {
					echo 'true';
				} else {
					echo 'Impossible d\'enregistrer la date';
				}
			} else {
				echo $Event->getTitre() . ' occupe déjà cette date';
			}
		}
	}
}