;(function ( global, $ ) {
    'use strict';

    var Classrooms = (function () {
        var classrooms = [];

        function load() {
            $.ajax( {
                'url'   : 'api/classrooms',
                'method': 'get',

                'success': function ( json ) {
                    console.log( json );

                    // Must be expressed as "Classrooms.classrooms", rather than simply referring to "classrooms"
                    // (a la closure). Not exactly sure why, but "classrooms" alone doesn't work - it refers to
                    // a separate object somehow.
                    json.sort( function ( a, b ) {
                        if ( a.ordering > b.ordering ) return 1;
                        if ( a.ordering < b.ordering ) return -1;
                        return 0;
                    } );
                    Classrooms.classrooms = json;
                    AttendanceTab.build();
                },

                'error': function ( xhr ) {
                    console.log( xhr );
                }
            } )
        }

        return {
            'classrooms': classrooms,
            'load'      : load
        };
    })();


    var Students = (function () {
        var students = [];

        function load() {
            $.ajax( {
                'url'   : 'api/students',
                'method': 'get',

                'success': function ( json ) {
                    console.log( json );
                    for ( var i = 0; i < json.length; i++ ) {
                        var student = json[ i ];
                        if ( "1" !== student.enrolled ) continue;

                        if ( !(student.classroom_id in Students.students) ) {
                            Students.students[ student.classroom_id ] = [];
                        }
                        Students.students[ student.classroom_id ].push( student );
                    }

                    for ( i = 0; i < Students.students.length; i++ ) {
                        if ( !Students.students[ i ] ) continue;
                        Students.students[ i ].sort( function ( a, b ) {
                            if ( a.family_name > b.family_name ) return 1;
                            if ( a.family_name < b.family_name ) return -1;
                            if ( a.first_name > b.first_name ) return 1;
                            if ( a.first_name < b.first_name ) return -1;
                            return 0;
                        } );
                    }
                    AttendanceTab.build();

                },
                'error'  : function ( xhr ) {
                    console.log( xhr );
                }
            } );
        }

        return {
            'students': students,
            'load'    : load
        }
    })();


    var Schedules = (function () {
        var schedules = [];   // Schedules by student id

        function load() {
            $.ajax( {
                'url'   : 'api/schedules',
                'method': 'get',

                'success': function ( json ) {
                    console.log( json );
                    for ( var i = 0; i < json.length; i++ ) {
                        var sched = json[ i ];
                        if ( !( sched.student_id in Schedules.schedules) ) {
                            Schedules.schedules[ sched.student_id ] = [];
                        }
                        Schedules.schedules[ sched.student_id ].push( sched );
                    }
                    AttendanceTab.build();
                },
                'error'  : function ( xhr ) {
                    console.log( xhr );
                }
            } )
        }

        return {
            'schedules': schedules,
            'load'     : load
        };
    })();


    var AttendanceTab = (function () {
        var $tab,
            $weekOf,
            $attendance;

        function init( selector ) {
            $tab    = $( selector );
            $weekOf = $tab.find( '[name=week-of]' );
            $weekOf.datepicker();
            $attendance = $tab.find( '.attendance-page-schedules' );
        }

        function build() {
            if ( !Classrooms.classrooms.length ) {
                return;
            }
            if ( !Students.students.length ) {
                return;
            }
            if ( !Schedules.schedules.length ) {
                return;
            }
            buildAttendanceTables( Classrooms.classrooms, Students.students );
        }

        function buildAttendanceTables( classrooms, students ) {
            for ( var i = 0; i < classrooms.length; i++ ) {
                var classroom = classrooms[ i ];
                $attendance.append( $( '<h3>' ).text( classroom.label ) );

                var $table = $( '<table class="table table-striped table-bordered">' );
                var $thead = $( '<thead>' );
                $table.append( $thead );
                var $tr = $( '<tr>' );
                $tr.append( $( '<th>Name</th>' ) );
                $tr.append( $( '<th>Mon</th>' ) );
                $tr.append( $( '<th>Tue</th>' ) );
                $tr.append( $( '<th>Wed</th>' ) );
                $tr.append( $( '<th>Thu</th>' ) );
                $tr.append( $( '<th>Fri</th>' ) );
                $tr.append( $( '<th>Summary</th>' ) );
                $thead.append( $tr );

                var $tbody = $( '<tbody>' );
                $table.append( $tbody );
                if ( students[ classroom.id ] ) {
                    for ( var j = 0; j < students[ classroom.id ].length; j++ ) {
                        var student = students[ classroom.id ][ j ];
                        $tr         = $( '<tr>' );
                        $tr.append( $( '<td>' ).text( student.family_name + ', ' + student.first_name ) );
                        $tr.append( $( '<td>' ) );
                        $tr.append( $( '<td>' ) );
                        $tr.append( $( '<td>' ) );
                        $tr.append( $( '<td>' ) );
                        $tr.append( $( '<td>' ) );
                        $tr.append( $( '<td>' ) );
                        $tbody.append( $tr );
                    }
                }

                $attendance.append( $table );
                $table.DataTable( {
                    'searching': false,
                    'paging'   : false,
                    'ordering' : false,
                    'info'     : false
                } );
            }

        }

        return {
            'init' : init,
            'build': build
        };
    })();


    $( function () {
        console.log( "Index page ready" );
        $( '#tabs' ).tabs();
        AttendanceTab.init( '#attendance-tab' );
        Classrooms.load();
        Students.load();
        Schedules.load();
    } );


})( this, jQuery );
