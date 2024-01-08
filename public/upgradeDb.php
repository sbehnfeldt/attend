<?php
require("../lib/bootstrap.php");

try {
    $pdo = new PDO('mysql:host=' . $config[ 'db' ][ 'host' ] . ';dbname=' . $config[ 'db' ][ 'dbname' ] . ';charset=' . $config[ 'db' ][ 'charset' ],
        $config[ 'db' ][ 'uname' ], $config[ 'db' ][ 'pword' ], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
} catch (PDOException $e) {
    print('Unable to create database connection: ' . $e->getMessage());
    die ('Unable to connect to API.  Contact system administrator for awesome assistance.');
}

$sql = 'select * from schedules';
print("$sql<br />");

$sth = $pdo->prepare($sql);
$sth->execute();
$schedules = $sth->fetchAll();

$map = [
    'mon_am' => 0x01,
    'tue_am' => 0x02,
    'wed_am' => 0x04,
    'thu_am' => 0x08,
    'fri_am' => 0x10,

    'mon_noon' => 0x20,
    'tue_noon' => 0x40,
    'wed_noon' => 0x80,
    'thu_noon' => 0x100,
    'fri_noon' => 0x200,

    'mon_pm' => 0x400,
    'tue_pm' => 0x800,
    'wed_pm' => 0x1000,
    'thu_pm' => 0x2000,
    'fri_pm' => 0x4000,

];
foreach ($schedules as $schedule) {
//    var_dump($schedule);
    $sched = 0;
    foreach ($map as $k => $v) {
        if (1 == $schedule[ $k ]) {
            $sched = $sched + $v;
        }
    }
    $sql = 'update schedules set schedule = ? where id = ?';
    print "$sql<br />";
    print "$sched, ${schedule['id']}<br />";
    $sth = $pdo->prepare($sql);
    $sth->execute([$sched, $schedule[ 'id' ]]);


    $sql = 'ALTER TABLE `attend`.`schedules`
DROP COLUMN `fri_pm`,
DROP COLUMN `fri_noon`,
DROP COLUMN `fri_am`,
DROP COLUMN `thu_pm`,
DROP COLUMN `thu_noon`,
DROP COLUMN `thu_am`,
DROP COLUMN `wed_pm`,
DROP COLUMN `wed_noon`,
DROP COLUMN `wed_am`,
DROP COLUMN `tue_pm`,
DROP COLUMN `tue_noon`,
DROP COLUMN `tue_am`,
DROP COLUMN `mon_pm`,
DROP COLUMN `mon_noon`,
DROP COLUMN `mon_am`';
    $sth = $pdo->prepare($sql);
    $sth->execute();
}
