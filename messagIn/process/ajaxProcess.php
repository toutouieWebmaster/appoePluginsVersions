<?php

use App\Plugin\MessagIn\MessagIn;

require_once( '../main.php' );
if ( checkAjaxRequest() ) {

	if ( getUserIdSession() ) {

		$MessagIn = new MessagIn();

		if ( ! empty( $_POST['idMessageToDelete'] ) ) {
            $MessagIn->setId( $_POST['idMessageToDelete'] );
			if ( $MessagIn->delete() ) {
				echo 'true';
			}
		}

		if ( ! empty( $_POST['idMessageTochangeStatut'] ) && isset( $_POST['statutMessage'] ) ) {
            $MessagIn->setId( $_POST['idMessageTochangeStatut'] );
            $MessagIn->show();
            $MessagIn->setStatut( $_POST['statutMessage'] );
			if ( $MessagIn->update() ) {
				echo 'true';
			}
		}
	}
}