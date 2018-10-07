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
            if ( !Classrooms.classrooms ) {
                return;
            }
            buildAttendanceTables( Classrooms.classrooms );
        }

        function buildAttendanceTables( classrooms ) {
            for ( var i = 0; i < classrooms.length; i++ ) {
                var classroom = classrooms[ i ];
                $attendance.append( $( '<h3>' ).text( classroom.label ) );

                var $table = $( '<table class="table table-striped table-bordered">' );
                var $thead = $( '<thead>' );
                var $tr    = $( '<tr>' );
                $tr.append( $( '<th>Name</th>' ) );
                $tr.append( $( '<th>Mon</th>' ) );
                $tr.append( $( '<th>Tue</th>' ) );
                $tr.append( $( '<th>Wed</th>' ) );
                $tr.append( $( '<th>Thu</th>' ) );
                $tr.append( $( '<th>Fri</th>' ) );
                $tr.append( $( '<th>Summary</th>' ) );
                $thead.append( $tr );

                var $tbody = $( '<tbody>' );

                $table.append( $thead );
                $table.append( $tbody );
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

    } );


})( this, jQuery );
