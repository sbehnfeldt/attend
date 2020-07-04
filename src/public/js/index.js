;(function ( global, $ ) {
    'use strict';
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    var Classrooms = (function () {
        var classrooms = [];

        function load() {
            Attend.loadAnother();
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
                    SigninTab.build();
                    Attend.doneLoading();
                },

                'error': function ( xhr ) {
                    console.log( xhr );
                    Attend.doneLoading();
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
            Attend.loadAnother();
            $.ajax( {
                'url'   : 'api/students',
                'method': 'get',

                'success': function ( json ) {
                    console.log( json );
                    for ( var i = 0; i < json.length; i++ ) {
                        var student = json[ i ];
                        if ( 1 !== student.Enrolled ) continue;

                        if ( !(student.ClassroomId in Students.students) ) {
                            Students.students[ student.ClassroomId ] = [];
                        }
                        Students.students[ student.ClassroomId ].push( student );
                    }

                    for ( i = 0; i < Students.students.length; i++ ) {
                        if ( !Students.students[ i ] ) continue;
                        Students.students[ i ].sort( function ( a, b ) {
                            if ( a.FamilyName > b.FamilyName ) return 1;
                            if ( a.FamilyName < b.FamilyName ) return -1;
                            if ( a.FirstName > b.FirstName ) return 1;
                            if ( a.FirstName < b.FirstName ) return -1;
                            return 0;
                        } );
                    }
                    AttendanceTab.build();
                    SigninTab.build();
                    Attend.doneLoading();
                },
                'error'  : function ( xhr ) {
                    console.log( xhr );
                    Attend.doneLoading();
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
            Attend.loadAnother();
            $.ajax( {
                'url'   : 'api/schedules',
                'method': 'get',

                'success': function ( json ) {
                    console.log( json );
                    for ( var i = 0; i < json.length; i++ ) {
                        var sched = json[ i ];
                        if ( !( sched.StudentId in Schedules.schedules) ) {
                            Schedules.schedules[ sched.StudentId ] = [];
                        }
                        Schedules.schedules[ sched.StudentId ].push( sched );
                    }
                    AttendanceTab.build();
                    SigninTab.build();
                    Attend.doneLoading();
                },
                'error'  : function ( xhr ) {
                    console.log( xhr );
                    Attend.doneLoading();
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

            $weekOf.datepicker( 'setDate', Attend.getMonday( new Date() ) );
            $( '#pdf-attendance' ).attr( 'href', 'pdf.php?attendance&week=' + $weekOf.val() );
            $weekOf.on( 'change', function () {
                $( this ).datepicker( 'setDate', Attend.getMonday( new Date( $( this ).val() ) ) );
                $( this ).blur();
                $( '#pdf-attendance' ).attr( 'href', 'pdf.php?attendance&week=' + $( this ).val() );
                build();
            } );
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
            buildAttendanceTables( Classrooms.classrooms, Students.students, Schedules.schedules );
        }

        function buildAttendanceTables( classrooms, students, schedules ) {
            console.log( 'Building attendance tables' );
            $attendance.empty();
            Attend.loadAnother();

            for ( var i = 0; i < classrooms.length; i++ ) {
                $attendance.append($('<h3>').text(classrooms[i].Label));
                var $table = buildAttendanceTable2(classrooms[i], students[classrooms[i].Id], schedules);
                $table.find('td').each(function () {
                    if (!$(this).text().trim().length) {
                        ($(this).addClass('dark'));
                    }
                });

                $table.DataTable({
                    'searching': false,
                    'paging': false,
                    'ordering': false,
                    'info': false
                });

                $attendance.append($table);
            }
            Attend.doneLoading();
        }

        function buildAttendanceTable2(classroom, students, schedules) {
            let notes;
            let dailies = [0, 0, 0, 0, 0];


            Handlebars.registerHelper('decode', function (i, sched) {
                var decoder = [
                    [0x0001, 0x0020, 0x0400],
                    [0x0002, 0x0040, 0x0800],
                    [0x0004, 0x0080, 0x1000],
                    [0x0008, 0x0100, 0x2000],
                    [0x0010, 0x0200, 0x4000]
                ];
                let r;
                if (0 === i) {
                    notes = {
                        'HD': 0,
                        'FD': 0,
                        'HDL': 0
                    };
                }

                if ((sched & decoder[i][0]) && (sched & decoder[i][2])) {
                    r = 'FD';
                } else if (sched & decoder[i][0]) {
                    if (sched & decoder[i][1]) {
                        r = 'HDL';
                    } else {
                        r = 'HD';
                    }

                } else if (sched & decoder[i][2]) {
                    if (sched & decoder[i][1]) {
                        r = 'HDL';
                    } else {
                        r = 'HD';
                    }
                } else {
                    r = '';
                }
                if (r) {
                    notes[r]++;
                    dailies[i]++;
                }
                return r;
            });
            Handlebars.registerHelper('summary', function () {
                var summary = [];
                if (notes['FD']) {
                    summary.push(notes['FD'] + 'FD');
                }
                if (notes['HD']) {
                    summary.push(notes['HD'] + 'HD');
                }
                if (notes['HDL']) {
                    summary.push(notes['HDL'] + 'HDL');
                }
                return summary.join();
            });

            Handlebars.registerHelper('dailies', function (i) {
                return dailies[i];
            });

            for (let i = 0; i < students.length; i++) {
                for (let j in schedules) {
                    if (schedules[j][0].StudentId === students[i].Id) {
                        students[i].Schedule = schedules[j][0];
                        break;   // Break inner loop, next student
                    }
                }
            }

            var source = document.getElementById("attendance-table").innerHTML;
            var template = Handlebars.compile(source);
            var context = {students: students, dates: []};

            var weekOf = $weekOf.val();
            var cur = new Date(weekOf);
            for (var i = 0; i < 5; i++) {
                cur = cur.addDays(1);
                context.dates[i] = months[cur.getMonth()] + ' ' + cur.getDate();
            }

            var html = template(context);
            return $(html);
        }

        return {
            'init' : init,
            'build': build
        };
    })();


    var SigninTab = (function () {
        var $tab,
            $weekOf,
            $signin
                ;

        function init( selector ) {
            $tab    = $( selector );
            $weekOf = $tab.find( '[name=week-of]' );
            $weekOf.datepicker();
            $signin = $tab.find( '.attendance-page-signin' );

            $weekOf.datepicker( 'setDate', Attend.getMonday( new Date() ) );
            $( '#pdf-signin' ).attr( 'href', 'pdf.php?signin&week=' + $weekOf.val() );

            $weekOf.on( 'change', function onChange_weekOf() {
                $( this ).datepicker( 'setDate', Attend.getMonday( new Date( $( this ).val() ) ) );
                $( this ).blur();
                $( '#pdf-signin' ).attr( 'href', 'pdf.php?signin&week=' + $( this ).val() );
                build();
            } );
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
            buildSigninTables( Classrooms.classrooms, Students.students, Schedules.schedules );
        }

        function buildSigninTables( classrooms, students, schedules ) {
            console.log( "Building sign-in tables" );
            $signin.empty();
            Attend.loadAnother();

            for ( var i = 0; i < classrooms.length; i++ ) {
                $signin.append($('<h3>').text(classrooms[i].Label));
                var $table = buildSigninTable(classrooms[i], students[classrooms[i].Id], schedules);

                $table.DataTable( {
                    'searching': false,
                    'paging': false,
                    'ordering': false,
                    'info': false
                });

                $signin.append($table);
            }
            Attend.doneLoading();
        }


        function buildSigninTable(classroom, students) {

            var source = document.getElementById("signin-table").innerHTML;
            var template = Handlebars.compile(source);
            var context = {students: students, dates: []};

            var weekOf = $weekOf.val();
            var cur = new Date(weekOf);
            for (var i = 0; i < 5; i++) {
                cur = cur.addDays(1);
                context.dates[i] = months[cur.getMonth()] + ' ' + cur.getDate();
            }

            var html = template(context);
            return $(html);
        }


        return {
            'init' : init,
            'build': build
        };
    })();


    $( function () {
        console.log( "Index page ready" );
        AttendanceTab.init( '#attendance-tab' );
        SigninTab.init( '#signin-tab' );
        Classrooms.load();
        Students.load();
        Schedules.load();
        $( '#tabs' ).tabs().show();
    } );


})( this, jQuery );
