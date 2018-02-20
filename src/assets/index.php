<?php

function my_autoload($className)
{

    $slash     = "\\";
    $className = ltrim($className, $slash);
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, $slash)) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace($slash, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    $require = INSTALL . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $fileName;
    require $require;
}


/********************************************************************************
 * Main Script
 ********************************************************************************/

define('INSTALL', dirname(__DIR__));

ini_set('error_log', INSTALL . '/logs/php_errors.log');
$config  = parse_ini_file('../config.ini', true);
$webroot = $config[ 'app' ][ 'root' ];

spl_autoload_register('my_autoload');
session_save_path(INSTALL . '/sessions');
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.min.css">

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css">
    <link rel="stylesheet" type="text/css" href="css/attend.css">
    <link rel="stylesheet" type="text/css" media="print" href="css/print.css">
    <title>Attend</title>
</head>

<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="">Attend</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="tabs nav navbar-nav" role="menu">
                <li><a href="#attendance-page" class="tab">Attendance</a></li>
                <li><a href="#signin-page" class="tab">Sign In</a></li>
                <!-- li><a href="#checkin-page" class="tab">Checkin</a></li -->
                <!-- li><a href="#reports-page" class="tab">Reports</a></li -->
                <li><a href="#enrollment-page" class="tab">Enrollment</a></li>
                <li><a href="#classrooms-page" class="tab">Classes</a></li>
                <!-- li><a href="#teachers-page" class="tab">Teachers</a></li-->
            </ul>
        </div>
    </div>
</nav>

<main>
    <!-- Attendance Page -->
    <section class="page container" id="attendance-page">
        <header class="page-header">
            <h2>Attendance</h2>
            <div class="controls">
                Week Of: <input type="text" name="week-of"/>
                <a id="pdf-attendance" href="pdf.php?attendance">PDF</a>
            </div>
        </header>

        <div class="attendance-page-schedules"></div>
    </section><!-- end attendance page -->

    <!-- Sign-In Page -->
    <section class="page container" id="signin-page">
        <header class="page-header">
            <h2>Sign In</h2>
            <div class="controls">
                Week of: <input type="text" name="week-of"/>
                <a id="pdf-signin" href="pdf.php?signin">PDF</a>
            </div>
        </header>
        <div class="signin-page-contents"></div>
    </section><!-- end signin page -->

    <!-- Checkin Page -->
    <section class="page container" id="checkin-page">
        <header>
            <h2>Check-In</h2>
        </header>
        <div class="row time-date">
            <span class="clock"></span>
            <span class="glyphicon glyphicon-time"></span>
            <span class="calendar"></span>
        </div>

        <div class="row attendance-report attendance-checkin">
            <div class="btn-group btn-group-xs btn-group-toggle" role="group">
                <button type="button" class="btn btn-default btn-selected" data-toggle="scheduled">Scheduled</button>
                <button type="button" class="btn btn-default" data-toggle="enrolled">Active</button>
            </div>

            <select class="filter-select">
                <option value="">Show All</option>
                <option value="0">Unassigned</option>
            </select>

            <table class='clearfix' id="attendance-checkin-table">
                <thead>
                <tr>
                    <th>Controls</th>
                    <th>Student</th>
                    <th>Checked In</th>
                    <th>Checked Out</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
            <a href="#attendance">top</a>
        </div>
    </section><!-- Checkin Page -->

    <!-- Reports Page -->
    <section class="page container" id="reports">
        <header>
            <h2>Reports</h2>
        </header>
        <div class="row">
            <div class="col-lg-2 report-list">
                <div>
                    <select name="report-list">
                        <option value="">Select a Report</option>
                    </select>
                </div>
            </div>
            <div class="container panel" id="attendance-report">
                <h3>Attendance</h3>
                <table id="attendance-report-table">
                    <thead>
                    <tr>
                    </tr>
                    <tr>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="container panel" id="student-report">
                <h3>Students</h3>
            </div>
        </div>
    </section><!-- Reports Page -->

    <!-- Enrollment Page -->
    <section class="page container" id="enrollment-page">
        <header>
            <div class="row">
                <div class="col-xs-12">
                    <h2>Enrollment</h2>
                </div>
            </div>
        </header>

        <div class="row">
            <div class="col-xs-12">
                <table class="students-table display row-border stripe compact hover" cellspacing="0"
                       id="students-table">
                    <thead>
                    <tr>
                        <th>Family Name</th>
                        <th>First Name</th>
                        <th>Classroom</th>
                        <th>Enrolled</th>
                        <th class="button-column">Edit</th>
                        <th class="button-column">Schedule</th>
                        <th class="button-column">Delete</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button class="new-record"><span class="glyphicon glyphicon-plus"> <span>New</span></span></button>
                <button class="refresh-records"><span class="glyphicon glyphicon-refresh"> <span>Refresh</span></span>
                </button>
            </div>
        </div>

        <div id="student-dlg" title="Student Data">
            <h3>Personal Data</h3>
            <p class="update-tips"></p>
            <form name="studentData">
                <input type="hidden" name="id">
                <div class="row">
                    <div class="col-sm-12 student-detail">
                        <div class="row">
                            <div class="col-xs-3"><label class="pull-right">Family Name: </label></div>
                            <div class="col-xs-9"><input type="text" name="family_name"/></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3">
                                <label class="pull-right">First Name: </label>
                            </div>
                            <div class="col-xs-9"><input type="text" name="first_name"/></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3">
                                <label class="pull-right">Class:</label>
                            </div>
                            <div class="col-xs-9">
                                <select name="classroom_id">
                                    <option value="0">No class selected</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3">
                                <label class="pull-right">Active: </label>
                            </div>
                            <div class="col-xs-9">
                                <input type="checkbox" name="enrolled" value="1">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div id="schedule-dlg" title="Student Schedule">
            <h3>Schedule</h3>
            <p class="student-name"></p>
            <p class="update-tips"></p>
            <form name="schedules">
                <input type="hidden" name="student_id">
                <select name="id"></select>
                <div>
                    <button class="checkAll">Check All</button>
                </div>
                <table id="student-schedule-table">
                    <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class="checkcontrol">
                            <button class="sched-group" type="button" value="0x421">Mon</button>
                        </th>
                        <th class="checkcontrol">
                            <button class="sched-group" type="button" value="0x842">Tue</button>
                        </th>
                        <th class="checkcontrol">
                            <button class="sched-group" type="button" value="0x1084">Wed</button>
                        </th>
                        <th class="checkcontrol">
                            <button class="sched-group" type="button" value="0x2108">Thu</button>
                        </th>
                        <th class="checkcontrol">
                            <button class="sched-group" type="button" value="0x4210">Fri</button>
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr data-day-part="Am">
                        <td class="checkcontrol">
                            <button class="sched-group" type="button" value="0x1f">AM</button>
                        </td>
                        <td><input class="scheds" type="checkbox" value="0x1"/></td>
                        <td><input class="scheds" type="checkbox" value="0x2"/></td>
                        <td><input class="scheds" type="checkbox" value="0x4"/></td>
                        <td><input class="scheds" type="checkbox" value="0x8"/></td>
                        <td><input class="scheds" type="checkbox" value="0x10"/></td>
                    </tr>
                    <tr data-day-part="Noon">
                        <td class="checkcontrol">
                            <button class="sched-group" type="button" value="0x3e0">Lunch</button>
                        </td>
                        <td><input class="scheds" type="checkbox" value="0x20"/></td>
                        <td><input class="scheds" type="checkbox" value="0x40"/></td>
                        <td><input class="scheds" type="checkbox" value="0x80"/></td>
                        <td><input class="scheds" type="checkbox" value="0x100"/></td>
                        <td><input class="scheds" type="checkbox" value="0x200"/></td>
                    </tr>
                    <tr data-day-part="Pm">
                        <td class="checkcontrol">
                            <button class="sched-group" type="button" value="0x7c00">PM</button>
                        </td>
                        <td><input class="scheds" type="checkbox" value="0x400"/></td>
                        <td><input class="scheds" type="checkbox" value="0x800"/></td>
                        <td><input class="scheds" type="checkbox" value="0x1000"/></td>
                        <td><input class="scheds" type="checkbox" value="0x2000"/></td>
                        <td><input class="scheds" type="checkbox" value="0x4000"/></td>
                    </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-xs-2">
                        <label class="pull-right">Start on:</label>
                    </div>
                    <div class="col-xs-4">
                        <input type="text" name="start_date" class="datepicker" disabled>
                    </div>

                    <div class="col-xs-2">
                        <label class="pull-right">End on:</label>
                    </div>
                    <div class="col-xs-4">
                        <input type="text" name="endDate" class="datepicker">
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Classrooms Page -->
    <section class="page container" id="classrooms-page">
        <header>
            <div class="row">
                <div class="col-xs-12">
                    <h2>Classes</h2>
                </div>
            </div>
        </header>

        <div class="row">
            <div class="col-xs-6">
                <table class="classrooms-table display row-border stripe compact hover" cellspacing="0"
                       id="classrooms-table">
                    <thead>
                    <tr>
                        <th>Classroom</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button class="new-record"><span class="glyphicon glyphicon-plus"> <span>New</span></span></button>
                <button class="refresh-records"><span class="glyphicon glyphicon-refresh"> <span>Refresh</span></span>
                </button>
            </div>
        </div>

        <div id="classroom-dlg" title="Add/Edit Classroom">
            <form>
                <p class="update-tips"></p>
                <input type="hidden" name="id">
                <label>Classroom Name</label>
                <input type="text" name="label">
            </form>
        </div>
    </section>

    <!-- Teachers Page -->
    <section class="page" id="teachers">
        Define Teachers
    </section>
</main>

<script type="application/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="application/javascript" src="js/jquery-ui.js"></script>
<script type="application/javascript" src="js/jquery.dataTables.min.js"></script>
<script type="application/javascript" src="js/bootstrap.min.js"></script>
<script type="application/javascript" src="js/attend.js"></script>

</body>
</html>

