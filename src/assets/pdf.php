<?php
include('bootstrap.php');

define('FPDF_FONTPATH', INSTALL . DIRECTORY_SEPARATOR . 'font' . DIRECTORY_SEPARATOR);
if (array_key_exists('attendance', $_GET)) {
    $pdf = new AttendancePdf();

} else if (array_key_exists('signin', $_GET)) {
    $pdf = new SigninPdf();
}
$pdf->setWeekOf($_GET[ 'week' ]);
$pdf->Output();
