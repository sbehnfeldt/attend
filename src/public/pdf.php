<?php
include('../lib/bootstrap.php');

define('FPDF_FONTPATH', '../font/');
if (array_key_exists('attendance', $_GET)) {
    $pdf = new AttendancePdf();

} else if (array_key_exists('signin', $_GET)) {
    $pdf = new SigninPdf();
}
$pdf->setWeekOf($_GET[ 'week' ]);
$pdf->Output();
