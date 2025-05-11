<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
includePluginsFiles();

if (checkAjaxRequest() && !bot_detected()) {

    $_POST = cleanRequest($_POST);

    /** BACK **/
    if (isset($_POST['getAdminAgendas'])) {
        echo appointment_agenda_admin_getAll();
        exit();
    }

    if (isset($_POST['setAdminAgendas']) && !empty($_POST['agendaName'])) {
        if (appointment_addAgenda($_POST['agendaName'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['changeNameAdminAgendas']) && !empty($_POST['idAgenda'])
        && !empty($_POST['agendaName']) && is_numeric($_POST['idAgenda'])) {
        if (appointment_changeAgendaName($_POST['idAgenda'], $_POST['agendaName'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['adminAgendaWorkingHoliday']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])) {
        if (appointment_preferenceHolidayWorking($_POST['idAgenda'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['changeNameAdminRdvType']) && !empty($_POST['idRdvType'])
        && !empty($_POST['rdvTypeName']) && is_numeric($_POST['idRdvType'])) {
        if (appointment_changeRdvTypeName($_POST['idRdvType'], $_POST['rdvTypeName'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['changeDurationAdminRdvType']) && !empty($_POST['idRdvType'])
        && !empty($_POST['duration']) && is_numeric($_POST['idRdvType'])) {
        if (appointment_changeRdvTypeDuration($_POST['idRdvType'], $_POST['duration'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['changeInformationAdminRdvType']) && !empty($_POST['idRdvType'])
        && isset($_POST['information']) && is_numeric($_POST['idRdvType'])) {
        if (appointment_changeRdvTypeInformation($_POST['idRdvType'], $_POST['information'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['changeStatusAdminAgendas']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])) {
        if (appointment_changeAgendaStatus($_POST['idAgenda'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['deleteAdminAgendas']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])) {
        if (appointment_deleteAgenda($_POST['idAgenda'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['getManageList']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])) {

        switch ($_POST['getManageList']) {
            case 'informations':
                echo appointment_informations_admin_getAll($_POST['idAgenda']);
                break;
            case 'availabilities':
                echo appointment_availabilities_admin_getAll($_POST['idAgenda']);
                break;
            case 'typeRdv':
                echo appointment_typeRdv_admin_getAll($_POST['idAgenda']);
                break;
            case 'typeRdvForm':
                if (!empty($_POST['idRdvType'])) {
                    echo appointment_rdvTypeForm_admin_getAll($_POST['idAgenda'], $_POST['idRdvType']);
                }
                break;
            case 'rdv':
                echo appointment_rdv_admin_getRdvTypes($_POST['idAgenda']);
                break;
            default :
                echo '...';
                break;
        }
        exit();
    }

    if (isset($_POST['setAdminAvailability']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])
        && isset($_POST['day']) && isset($_POST['start']) && !empty($_POST['end']) && is_numeric($_POST['start'])
        && is_numeric($_POST['end']) && is_numeric($_POST['day'])) {

        $start = $_POST['start'];
        $end = $_POST['end'];

        if ($end == 0 || $start === $end || $end < $start) {
            echo 'L\'heure du début doit être antérieure à l\'heure de fin';
        } else {
            $addAvailability = appointment_addAvailability($_POST['idAgenda'], $_POST['day'], $start, $end);
            if (true === $addAvailability) {
                echo 'true';
            } else {
                echo $addAvailability;
            }
            exit();
        }
        echo 'Certaines données sont manquantes au créneau';
        exit();
    }

    if (isset($_POST['adminAddRdvType']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])
        && !empty($_POST['name']) && !empty($_POST['duration']) && isset($_POST['information'])) {

        $addRdvType = appointment_addRdvType($_POST['idAgenda'], $_POST['name'], $_POST['duration'], $_POST['information']);
        if (true === $addRdvType) {
            echo 'true';
        }

        exit();
    }

    if (isset($_POST['adminAddAgendaMeta']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])
        && !empty($_POST['metaKey']) && !empty($_POST['metaVal']) && isset($_POST['position'])) {

        if (true === appointment_addAgendaMeta($_POST['idAgenda'], $_POST['metaKey'], $_POST['metaVal'], $_POST['position'])) {
            echo 'true';
        }

        exit();
    }

    if (isset($_POST['adminAddRdvTypeForm']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])
        && !empty($_POST['idRdvType']) && is_numeric($_POST['idRdvType']) && !empty($_POST['name'])
        && !empty($_POST['type']) && isset($_POST['placeholder']) && isset($_POST['required']) && !empty($_POST['position'])) {

        $slug = slugify($_POST['name']);
        $addRdvTypeForm = appointment_addRdvTypeForm($_POST['idAgenda'], $_POST['idRdvType'], $_POST['name'], $slug, $_POST['type'], $_POST['placeholder'], $_POST['required'], $_POST['position']);
        if (true === $addRdvTypeForm) {
            echo 'true';
        }

        exit();
    }

    if (isset($_POST['adminUpdateRdvTypeForm']) && !empty($_POST['idRdvTypeForm']) && is_numeric($_POST['idRdvTypeForm'])
        && !empty($_POST['name']) && !empty($_POST['type']) && isset($_POST['placeholder']) && isset($_POST['required'])
        && !empty($_POST['position'])) {

        $slug = slugify($_POST['name']);
        $updateRdvTypeForm = appointment_updateRdvTypeForm($_POST['idRdvTypeForm'], $_POST['name'], $slug, $_POST['type'], $_POST['placeholder'], $_POST['required'], $_POST['position']);
        if (true === $updateRdvTypeForm) {
            echo 'true';
        }

        exit();
    }

    if (isset($_POST['deleteAdminAgendaMeta']) && !empty($_POST['idMeta']) && is_numeric($_POST['idMeta'])) {

        if (appointment_deleteAgendaMeta($_POST['idMeta'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['deleteAdminAvailability']) && !empty($_POST['idAvailability']) && is_numeric($_POST['idAvailability'])) {

        if (appointment_deleteAvailability($_POST['idAvailability'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['deleteAdminARdvTypeForm']) && !empty($_POST['idRdvTypeForm']) && is_numeric($_POST['idRdvTypeForm'])) {

        if (appointment_deleteRdvTypeForm($_POST['idRdvTypeForm'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['deleteAdminRdvType']) && !empty($_POST['idRdvType']) && is_numeric($_POST['idRdvType'])) {

        if (appointment_deleteRdvType($_POST['idRdvType'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['deleteAdminRdv']) && !empty($_POST['idRdv']) && is_numeric($_POST['idRdv'])) {

        if (appointment_deleteRdv($_POST['idRdv'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['confirmAdminRdv']) && !empty($_POST['idRdv']) && is_numeric($_POST['idRdv'])) {

        if (appointment_confirmRdv($_POST['idRdv'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['confirmAdminClient']) && !empty($_POST['idClient']) && is_numeric($_POST['idClient'])) {

        if (appointment_confirmClient($_POST['idClient'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['makeTheTimeSlotUnavailable']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])
        && !empty($_POST['date']) && isset($_POST['start']) && !empty($_POST['end']) && is_numeric($_POST['start'])
        && is_numeric($_POST['end'])) {

        if ($_POST['start'] < 1440 && $_POST['end'] > 0 && appointment_makeTheTimeSlotUnavailable($_POST['idAgenda'], $_POST['date'], $_POST['start'], $_POST['end'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['makeTheTimeSlotAvailable']) && !empty($_POST['idsException'])) {
        if (appointment_makeTheTimeSlotAvailable($_POST['idsException'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['makeTheDayAvailable']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])
        && !empty($_POST['date'])) {

        if (appointment_makeTheDayAvailable($_POST['idAgenda'], $_POST['date'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['changeStatusAdminRdvType']) && !empty($_POST['idRdvType']) && is_numeric($_POST['idRdvType'])) {
        if (appointment_changeRdvTypeStatus($_POST['idRdvType'])) {
            echo 'true';
        }
        exit();
    }

    if (isset($_POST['getRdvGrid']) && !empty($_POST['idRdvType']) && is_numeric($_POST['idRdvType'])
        && isset($_POST['year']) && isset($_POST['month']) && checkdate($_POST['month'], 1, $_POST['year'])) {
        echo appointment_rdv_admin_getGrid($_POST['idRdvType'], $_POST['year'], $_POST['month']);
        exit();
    }

    if (isset($_POST['getRdvAvailabilities']) && !empty($_POST['idRdvType']) && is_numeric($_POST['idRdvType'])
        && !empty($_POST['date'])) {
        echo appointment_rdv_admin_getAvailabilities($_POST['idRdvType'], $_POST['date']);
        exit();
    }

    if (isset($_POST['getClientData']) && !empty($_POST['idClient']) && is_numeric($_POST['idClient'])) {
        echo json_encode(appointment_getFormClientById($_POST['idClient']));
        exit();
    }

    if (isset($_POST['adminAgendaSetting']) && !empty($_POST['key']) && isset($_POST['val'])) {
        if (appointment_updateAgendaSetting($_POST['key'], $_POST['val'])) {
            echo 'true';
        }
        exit();
    }


    /** FRONT **/
    if (isset($_POST['getRdvTypeByAgenda']) && !empty($_POST['idAgenda']) && is_numeric($_POST['idAgenda'])) {
        echo appointment_rdvType_getBtns($_POST['idAgenda']);
        exit();
    }

    if (isset($_POST['getDateByRdvType']) && !empty($_POST['idAgenda']) && !empty($_POST['idRdvType'])
        && is_numeric($_POST['idAgenda']) && is_numeric($_POST['idRdvType'])) {
        echo appointment_dates_get($_POST['idAgenda'], $_POST['idRdvType']);
        exit();
    }

    if (isset($_POST['getAvailabilitiesByDate']) && !empty($_POST['idAgenda']) && !empty($_POST['dateChoice'])
        && !empty($_POST['rdvTypeDuration']) && is_numeric($_POST['idAgenda']) && is_numeric($_POST['rdvTypeDuration'])) {
        echo appointment_availabilities_get($_POST['idAgenda'], $_POST['dateChoice'], $_POST['rdvTypeDuration']);
        exit();
    }

    if (isset($_POST['getFormByRdvType']) && !empty($_POST['idRdvType']) && is_numeric($_POST['idRdvType'])) {
        echo appointment_rdvTypeForm_get($_POST['idRdvType']);
        exit();
    }

    if (isset($_POST['checkClientKnown']) && !empty($_POST['email']) && isEmail($_POST['email'])) {
        echo appointment_client_check($_POST['email']);
        exit();
    }
}
echo json_encode(false);