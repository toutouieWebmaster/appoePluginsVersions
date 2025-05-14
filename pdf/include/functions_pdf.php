<?php
/*
 * $html2pdf->output($name, $dest);
 * I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
 * D: send to the browser and force a file download with the name given by name.
 * F: save to a local server file with the name given by name.
 * S: return the document as a string (name is ignored).
 * FI: equivalent to F + I option
 * FD: equivalent to F + D option
 * E: return the document as base64 mime multi-part email attachment (RFC 2045)
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');

require WEB_PLUGIN_PATH . 'pdf/vendor/autoload.php';

use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Security\Security;

/**
 * @param $templateSlug
 * @param $params
 * @param string $orientation
 * @param string $pdfName
 * @param string $destination
 * @param bool $vueHtml
 */
function getPdf($templateSlug, $params, string $orientation = 'P', string $pdfName = 'appoe', string $destination = 'I', bool $vueHtml = false)
{
    try {
        $html2pdf = new Html2Pdf($orientation, 'A4', 'fr', true, 'UTF-8', 12);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->pdf->SetCreator('APPOE | P&P Communication');
        $html2pdf->pdf->SetAuthor('APPOE | P&P Communication');
        $html2pdf->pdf->SetTitle($pdfName);
        $html2pdf->pdf->SetSubject($pdfName);
        $html2pdf->pdf->SetKeywords($pdfName);

        $html2pdf->writeHTML(getPdfTemplate($templateSlug, $params));
        $html2pdf->Output($pdfName . '.pdf', $destination);
        exit;
    } catch (Html2PdfException $e) {
        $html2pdf->clean();
        $formatter = new ExceptionFormatter($e);
        echo $formatter->getHtmlMessage();
    }
}


/**
 * @param string $templateSlug
 * @param mixed $params
 * @return string
 */
function getPdfTemplate(string $templateSlug, mixed $params = null)
{
    if (defined('PDF_TEMPLATE_PATH')) {
        return getFileContent(PDF_TEMPLATE_PATH . $templateSlug . '.php', $params);
    } else {
        return 'Aucun emplacement des templates pdf, n\'est défini.';
    }
}