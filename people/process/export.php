<?php
require_once('../main.php');
includePluginsFiles();
if (getUserIdSession()) {

    $_GET = cleanRequest($_GET);

    if (isset($_GET['csv'])) {

        $filename = !empty($_GET['filename']) ? $_GET['filename'] : 'document';
        $headers = array('Type', 'Civilité', 'Nom', 'Prénom', 'Intitulé', 'Âge', 'e-mail', 'Téléphone', 'Adresse', 'Code postal', 'Ville', 'Pays');
        $data = getPeopleData('', ['type', 'nature', 'name', 'firstName', 'entitled', 'birthDate', 'email', 'tel', 'address', 'zip', 'city', 'country']);
        if(is_array($data)) {
            exportCsv($headers, $data, $filename, ';');
        }
    }
}
