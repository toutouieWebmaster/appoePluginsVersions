<?php
require_once( 'header.php' );
if ( checkAjaxRequest() ) {

	if ( getUserIdSession() ) {

		if ( ! empty( $_POST['idDeleteAuteur'] ) ) {
			$Auteur = new \App\Plugin\EventManagement\Auteur();
			$Auteur->setId( $_POST['idDeleteAuteur'] );
			if ( $Auteur->show() ) {
				if ( $Auteur->delete() ) {
					echo json_encode( true );
				}
			}
		}
	}
}