<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/plugin/pdf/include/functions_pdf.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $orientation = !empty($_POST['pdfTemplateOrientation']) ? $_POST['pdfTemplateOrientation'] : 'P';
    $templateFile = !empty($_POST['pdfTemplateFilename']) ? $_POST['pdfTemplateFilename'] : '';
    $pdfOutputName = !empty($_POST['pdfOutputName']) ? $_POST['pdfOutputName'] : '';
    $destination = !empty($_POST['pdfDestination']) ? $_POST['pdfDestination'] : 'I';

    //Delete useless keys
    unset($_POST['pdfTemplateOrientation']);
    unset($_POST['pdfTemplateFilename']);
    unset($_POST['pdfOutputName']);
    unset($_POST['pdfDestination']);


    getPdf($templateFile, $_POST, $orientation, $pdfOutputName, $destination, isset($_POST['vueHtml']));
}