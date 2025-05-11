<?php
require_once( 'header.php' );
if ( checkAjaxRequest() ) {

	if ( getUserIdSession() ) {


		//Delete projet
		if ( ! empty( $_POST['idDeleteEvent'] ) ) {
			$Event = new \App\Plugin\EventManagement\Event( $_POST['idDeleteEvent'] );
			if ( $Event->delete() ) {
				echo json_encode( true );
			}
		}
	}
}