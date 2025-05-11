<?php

use App\Plugin\Appointment\Agenda;
use App\Plugin\Appointment\Client;
use App\Plugin\Appointment\Rdv;
use App\Plugin\Appointment\RdvTypeForm;
use Random\RandomException;

require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
includePluginsFiles();

if (!empty($_POST['formType']) && valideAjaxToken()) {

    $_POST = cleanRequest($_POST);

    /************************ APPOINTMENT **********************/
    if ($_POST['formType'] == 'appointment' && !empty($_POST['idAgenda']) && !empty($_POST['idRdvType']) && !empty($_POST['rdvDate'])
        && !empty($_POST['rdvBegin']) && !empty($_POST['rdvEnd']) && !empty($_POST['appointment_lastName']) && !empty($_POST['appointment_firstName'])
        && !empty($_POST['appointment_email']) && isEmail($_POST['appointment_email']) && !empty($_POST['appointment_tel'])
        && isTel($_POST['appointment_tel']) && is_numeric($_POST['idAgenda']) && is_numeric($_POST['idRdvType'])
        && is_numeric($_POST['rdvBegin']) && is_numeric($_POST['rdvEnd'])) {

        if (isset($_POST['appointmentFromAdmin'])) {

            $start = $_POST['rdvBegin'];
            $end = $_POST['rdvEnd'];

            if ($end <= $start) {
                echo json_encode('Ce créneau n\'est pas disponible');
                exit();
            }


            $allRdv = appointment_getRdvByDate($_POST['idAgenda'], $_POST['rdvDate']);
            if (appointment_admin_isBooked($start, $allRdv, $end - $start)) {
                echo json_encode('Le créneau est occupé par un autre rendez-vous');
                exit();
            }
        }

        $Agenda = new Agenda();
        $Agenda->setId($_POST['idAgenda']);
        if(!$Agenda->show()){
            echo json_encode('Erreur inconnue');
            exit();
        }

        $clientEmail = $_POST['appointment_email'];
        $clientLastName = $_POST['appointment_lastName'];
        $clientFirstNameName = $_POST['appointment_firstName'];
        $clientTel = $_POST['appointment_tel'];

        //Get custom form
        $options = [];
        $RdvTypeForm = new RdvTypeForm();
        $RdvTypeForm->setIdRdvType($_POST['idRdvType']);
        if ($forms = $RdvTypeForm->showAll()) {
            foreach ($forms as $form) {
                if (($form->required && empty($_POST['appointment_' . $form->slug])) || !isset($_POST['appointment_' . $form->slug])) {
                    echo json_encode('<div class="appointmentAppoeReminder">Le champ <strong>' . $form->name . '</strong> est manquant</div>');
                    exit();
                }
                $options[$form->slug] = $_POST['appointment_' . $form->slug];
            }
        }

        //Prepare RDV
        $Rdv = new Rdv();
        $Rdv->setIdAgenda($_POST['idAgenda']);
        $Rdv->setIdTypeRdv($_POST['idRdvType']);
        $Rdv->setDate($_POST['rdvDate']);
        $Rdv->setStart($_POST['rdvBegin']);
        $Rdv->setEnd($_POST['rdvEnd']);
        $Rdv->setCreatedAt(date('Y-m-d H:i:s'));
        $Rdv->setOptions(serialize($options));

        //Save Client
        $Client = new Client();
        $Client->setEmail($clientEmail);
        if ($Client->exist()) {
            $Client->showByEmail();

            //Pas nécessaire a priori. Si le client n'est pas validé, il devra juste confirmer son adresse email afin de valider le rdv, et recevra un nouvel email
//            if (!$Client->getStatus()) {
//                echo json_encode('<div class="appointmentAppoeReminder">Impossible d\'enregistrer le rendez-vous tant que vous n\'avez pas confirmé votre adresse email !</div>');
//                exit();
//            }

             $Client->setLastName($clientLastName);
             $Client->setFirstName($clientFirstNameName);
             $Client->setTel($clientTel);
            $Client->setOptions(serialize(array_merge(unserialize($Client->getOptions()), $options)));
            $Client->update();

        } else {
            $Rdv->setStatus(0);

            $Client->setLastName($clientLastName);
            $Client->setFirstName($clientFirstNameName);
            $Client->setTel($clientTel);
            $Client->setOptions(serialize($options));
            $Client->save();
        }

        $Rdv->setIdClient($Client->getId());

        //Save RDV
        if (!$Rdv->exist()) {

            if (!empty($_POST['idRdvToRemove'])) {
                $Rdv->setId($_POST['idRdvToRemove']);
                $Rdv->delete();
            }

            if (!$Rdv->save()) {
                echo json_encode('<div class="appointmentAppoeReminder">Impossible d\'enregistrer le rendez-vous</div>');
                exit();
            }
        } else {
            echo json_encode('<div class="appointmentAppoeReminder">Un rendez-vous similaire est déjà enregistré</div>');
            exit();
        }

        unset($_SESSION['editRdv']);

        //Send infos or confirmation email
        if ($Client->getStatus() > 0) {

            if (appointment_sendInfosEmail($Rdv->getId(), urlAppointment(), isset($_POST['appointmentFromAdmin']))) {
                echo json_encode(true);
            }

        } else {

            $html = '<p>Bonjour,<br><br>Afin de finaliser notre premier rendez-vous, veuillez cliquer sur le bouton ci-dessous.
        <br>Faites vite ! Ce lien expirera dans 24 heures.<br>Le délai est dépassé ? <a href="' . urlAppointment() . '">Redemandez un rendez-vous</a> sur notre site.</p>';

            $data = array(
                'toEmail' => $clientEmail,
                'object' => 'Finalisez votre rendez-vous '.$Agenda->getName(),
                'message' => $html,
                'params' => ['idClient' => base64_encode($Client->getId())],
                'confirmationPageSlug' => basename(urlAppointment()),
                'confirmationBtnText' => 'Confirmer'
            );

            try {
                if (emailVerification($data, [], ['viewSenderSource' => false])) {
                    echo json_encode(true);
                }
            } catch (\PHPMailer\PHPMailer\Exception|RandomException $e) {
                error_log($e->getMessage());
            }
        }

        exit();
    }

}
echo json_encode(false);
exit();