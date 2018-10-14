<?php
include('../lib/bootstrap.php');

define('FPDF_FONTPATH', '../font/');
if (array_key_exists('attendance', $_GET)) {
    $pdf = new AttendancePdf();

} else if (array_key_exists('signin', $_GET)) {
    $pdf = new SigninPdf();
}
$pdo = new PDO('mysql:host=' . $config[ 'db' ][ 'host' ] . ';dbname=' . $config[ 'db' ][ 'dbname' ] . ';charset=' . $config[ 'db' ][ 'charset' ],
    $config[ 'db' ][ 'uname' ], $config[ 'db' ][ 'pword' ], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

$pdf->setPdo($pdo);

$pdf->setWeekOf($_GET[ 'week' ]);
$pdf->Output();
