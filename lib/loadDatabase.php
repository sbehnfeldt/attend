<?php
$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$serviceContainer->initDatabaseMapFromDumps(array(
    'attend' =>
        array(
            'tablesByName'    =>
                array(
                    'accounts'               => '\\flapjack\\attend\\database\\Map\\AccountTableMap',
                    'attendance'             => '\\flapjack\\attend\\database\\Map\\AttendanceTableMap',
                    'classrooms'             => '\\flapjack\\attend\\database\\Map\\ClassroomTableMap',
                    'group_members'          => '\\flapjack\\attend\\database\\Map\\GroupMemberTableMap',
                    'group_permissions'      => '\\flapjack\\attend\\database\\Map\\GroupPermissionTableMap',
                    'groups'                 => '\\flapjack\\attend\\database\\Map\\GroupTableMap',
                    'individual_permissions' => '\\flapjack\\attend\\database\\Map\\IndividualPermissionTableMap',
                    'login_attempts'         => '\\flapjack\\attend\\database\\Map\\LoginAttemptTableMap',
                    'permissions'            => '\\flapjack\\attend\\database\\Map\\PermissionTableMap',
                    'schedules'              => '\\flapjack\\attend\\database\\Map\\ScheduleTableMap',
                    'students'               => '\\flapjack\\attend\\database\\Map\\StudentTableMap',
                    'token_auths'            => '\\flapjack\\attend\\database\\Map\\TokenAuthTableMap',
                ),
            'tablesByPhpName' =>
                array(
                    '\\Account'              => '\\flapjack\\attend\\database\\Map\\AccountTableMap',
                    '\\Attendance'           => '\\flapjack\\attend\\database\\Map\\AttendanceTableMap',
                    '\\Classroom'            => '\\flapjack\\attend\\database\\Map\\ClassroomTableMap',
                    '\\Group'                => '\\flapjack\\attend\\database\\Map\\GroupTableMap',
                    '\\GroupMember'          => '\\flapjack\\attend\\database\\Map\\GroupMemberTableMap',
                    '\\GroupPermission'      => '\\flapjack\\attend\\database\\Map\\GroupPermissionTableMap',
                    '\\IndividualPermission' => '\\flapjack\\attend\\database\\Map\\IndividualPermissionTableMap',
                    '\\LoginAttempt'         => '\\flapjack\\attend\\database\\Map\\LoginAttemptTableMap',
                    '\\Permission'           => '\\flapjack\\attend\\database\\Map\\PermissionTableMap',
                    '\\Schedule'             => '\\flapjack\\attend\\database\\Map\\ScheduleTableMap',
                    '\\Student'              => '\\flapjack\\attend\\database\\Map\\StudentTableMap',
                    '\\TokenAuth'            => '\\flapjack\\attend\\database\\Map\\TokenAuthTableMap',
                ),
        ),
));
