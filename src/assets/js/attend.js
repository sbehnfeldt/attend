;(function () {
    'use strict';

    var Records = (function () {
        function init() {
            this.records   = [];
            this.callbacks = {
                'load-records' : $.Callbacks(),
                'insert-record': $.Callbacks(),
                'remove-record': $.Callbacks(),
                'update-record': $.Callbacks()
            }
        }

        function subscribe( event, fn ) {
            this.callbacks[ event ].add( fn );
        }

        function load( records ) {
            this.records = [];
            for ( var i = 0; i < records.length; i++ ) {
                this.records[ parseInt( records[ i ].id ) ] = records[ i ];
            }
            this.callbacks[ 'load-records' ].fire( records );
        }

        function insert( record ) {
            this.records[ parseInt( record.id ) ] = record;
            this.callbacks[ 'insert-record' ].fire( record );
        }

        function update( id, record ) {
            this.records[ parseInt( id ) ] = record;
            this.callbacks[ 'update-record' ].fire( id, record );
        }

        function remove( id ) {
            this.records[ parseInt( id ) ] = undefined;
            this.callbacks[ 'remove-record' ].fire( id );
        }

        return {
            'init'     : init,
            'subscribe': subscribe,
            'load'     : load,
            'insert'   : insert,
            'update'   : update,
            'remove'   : remove
        }

    })();

    var Classrooms = Object.create( Records );
    var Students   = Object.create( Records );


    var ClassroomPanel = (function () {
        var $panel;
        var $table;
        var table;
        var $newButton;

        function init( selector ) {
            cacheDom( selector );
            bindEvents();

            Classrooms.subscribe( 'load-records', whenClassroomsLoaded );
            Classrooms.subscribe( 'insert-record', whenClassroomAdded );
            Classrooms.subscribe( 'update-record', whenClassroomUpdated );
            Classrooms.subscribe( 'remove-record', whenClassroomRemoved );
        }

        function cacheDom( selector ) {
            $panel     = $( selector );
            $table     = $panel.find( 'table' );
            table      = $table.DataTable( {
                'info'     : false,
                'paging'   : false,
                'searching': false
            } );
            $newButton = $panel.find( 'button.new-record' );
        }

        function bindEvents() {
            $table.on( 'keyup', 'input.edit-classroom', onKeyupEditClassroom );
            $table.on( 'keyup', 'input.new-classroom', onKeyupNewClassroom );
            $table.on( 'click', 'button.update', onClickUpdateClassroom );
            $table.on( 'click', 'button.undo', onClickUndo );
            $table.on( 'click', 'button.delete', onClickDeleteClassroom );
            $table.on( 'click', 'button.submit', onClickSubmitClassroom );
            $table.on( 'click', 'button.discard', onClickDiscardClassroom );
            $newButton.on( 'click', onClickNewClassroom );
        }

        ////////////////////////////////////////////////////////////////////////////////
        // Event Handler Functions
        ////////////////////////////////////////////////////////////////////////////////

        // When the "Classroom" edit box is changed; ie, when the classroom is re-named
        function onKeyupEditClassroom() {
            var $tr     = $( this ).closest( 'tr' );
            var id      = $tr.data( 'classroomId' );
            var current = Classrooms.records[ id ];
            if ( current.name != $( this ).val() ) {
                $( this ).addClass( 'modified' );
                $tr.find( 'button.update' ).removeClass( 'disabled' ).attr( 'disabled', false );
                $tr.find( 'button.delete' ).removeClass( 'delete' ).addClass( 'undo' );
            } else {
                $( this ).removeClass( 'modified' );
                $tr.find( 'button.update' ).addClass( 'disabled' ).attr( 'disabled', true );
                $tr.find( 'button.undo' ).removeClass( 'undo' ).addClass( 'delete' );
            }
        }

        // When a change is made to the name of a new classroom,
        function onKeyupNewClassroom() {
            if ( $( this ).val().length > 0 ) {
                $( this ).addClass( 'modified' );
                $( this ).closest( 'tr' ).find( 'button.submit' ).attr( 'disabled', false ).removeClass( 'disabled' );
            } else {
                $( this ).closest( 'tr' ).find( 'button.submit' ).attr( 'disabled', true ).addClass( 'disabled' );
                $( this ).removeClass( 'modified' );
            }
        }

        // When the 'Update' button for an existing classroom is clicked,
        // update the database (via the controller) accordingly
        function onClickUpdateClassroom() {
            var $tr = $( this ).closest( 'tr' );
            ClassroomController.update( $tr.data( 'classroom-id' ), {
                'name': $tr.find( 'input.edit-classroom' ).val()
            } );
        }

        // When the 'Undo' button for an edited classroom is clicked,
        // discard any edits that have been made
        function onClickUndo() {
            if ( window.confirm( 'Are you sure you want to discard the changes?' ) ) {
                var $tr = $( this ).closest( 'tr' );
                var id  = $tr.data( 'classroomId' );
                $tr.find( 'input.edit-classroom' ).val( Classrooms.records[ id ].name );
                $tr.find( 'input.edit-classroom' ).removeClass( 'modified' );
                $( this ).removeClass( 'undo' ).addClass( 'delete' );
            }
        }

        // When the 'Delete' button for an existing classroom is clicked,
        // delete the data from the database (via the controller)
        function onClickDeleteClassroom() {
            var $tr = $( this ).closest( 'tr' );
            var id  = $tr.data( 'classroomId' );
            if ( window.confirm( 'Are you sure you want to delete the ' + Classrooms.records[ id ].name + ' classroom?' ) ) {
                ClassroomController.remove( id );
            }
        }

        // When the 'Submit' button for a new classroom is clicked,
        // insert the new data to the database (via the controller).
        function onClickSubmitClassroom() {
            ClassroomController.insert( {
                'name': $( this ).closest( 'tr' ).find( 'td input' ).val()
            } );
        }

        // When the 'Discard' button for a new classroom is clicked,
        // discard the "New Classroom" row in the table.
        // Since this classroom has not yet been entered in to the model or database,
        // no call to the model or database (via the controller) is necessary.
        function onClickDiscardClassroom() {
            if ( confirm( 'Are you sure you want to discard this new classroom?' ) ) {
                var $tr = $( this ).closest( 'tr' );
                table.row( $tr ).remove();
                $tr.remove();
                $newButton.show();
            }
        }


        // When the "New Classroom" button is clicked,
        // add an empty row to the end of the Classrooms table
        function onClickNewClassroom() {
            var row = table.row.add( [
                '<input type="text" class="new-classroom"  />',
                '<button class="submit disabled" disabled><span class="glyphicon glyphicon-ok" /></button>',
                '<button class="discard"><span class="glyphicon glyphicon-remove" /></button>'
            ] );
            table.draw();
            $( row.node() ).addClass( 'new-classroom' );
            $( row.node() ).find( 'input' ).focus();
            $newButton.hide();
        }

        ////////////////////////////////////////////////////////////////////////////////
        // Callback Functions
        ////////////////////////////////////////////////////////////////////////////////

        // When classrooms are loaded into the model,
        // populate the Classrooms table
        function whenClassroomsLoaded( classrooms ) {
            for ( var i = 0; i < classrooms.length; i++ ) {
                var row = table.row.add( [
                    '<input type="text" class="edit-classroom" value="' + classrooms[ i ].name + '" />',
                    '<button class="update disabled" disabled><span class="glyphicon glyphicon-ok" /></button>',
                    '<button class="delete"><span class="glyphicon glyphicon-remove" /></button>'
                ] );
                $( row.node() ).data( 'classroomId', classrooms[ i ].id );
            }
            table.draw();
        }

        // When a new classroom is added to the model,
        // add a new classroom to the Classrooms table
        function whenClassroomAdded( classroom ) {
            var row = table.row( '.new-classroom' );
            row.remove();
            $( row ).remove();

            row = table.row.add( [
                '<input type="text" class="edit-classroom"  value="' + classroom.name + '"/>',
                '<button class="update disabled" disabled><span class="glyphicon glyphicon-ok" /></button>',
                '<button class="delete"><span class="glyphicon glyphicon-remove" /></button>'
            ] );
            table.draw();
            $( row.node() ).data( 'classroomId', classroom.id );
            $newButton.show();
        }

        function whenClassroomUpdated( id, classroom ) {
            var rows = table.rows().nodes();
            rows.each( function ( e, i ) {
                var $tr  = $( e );
                var data = $tr.data( 'classroomId' );
                if ( data === id ) {
                    $tr.find( 'input.edit-classroom.modified' ).removeClass( 'modified' );
                    $tr.find( 'button.update' ).attr( 'disabled', true ).addClass( 'disabled' );
                    $tr.find( 'button.undo' ).removeClass( 'undo' ).addClass( 'delete' );
                    return false;
                }
            } );
        }

        // When a classroom is removed from the model,
        // remove the corresponding row from the Classrooms table
        function whenClassroomRemoved( id ) {
            table.rows().nodes().each( function ( e, i ) {
                var $tr  = $( e );
                var data = $tr.data( 'classroomId' );
                if ( data === id ) {
                    $tr.remove();
                    table.row( e ).remove();
                    return false;
                }
            } );
        }

        return {
            'init': init
        }
    })();

    var ClassroomController = {
        'insert': function ( data ) {
            $.ajax( {
                'url'   : 'api/classrooms',
                'method': 'post',
                'data'  : $.param( data ),

                'dataType': 'json',
                'success' : function ( json ) {
                    console.log( json );
                    data.id = json.data;
                    Classrooms.insert( data );
                },
                'error'   : function ( xhr ) {
                    console.log( 'AJAX error inserting new record' );
                    console.log( xhr );
                }
            } );
        },

        'update': function ( classroomId, params ) {
            $.ajax( {
                'url'   : 'api/classrooms/' + classroomId,
                'method': 'put',
                'data'  : params,

                'dataType': 'json',
                'success' : function ( json ) {
                    console.log( json );
                    Classrooms.update( classroomId, params );
                },
                'error'   : function ( xhr ) {
                    console.log( xhr );
                }
            } );
        },

        'load': function () {
            $.ajax( {
                'url'   : 'api/classrooms',
                'method': 'get',

                'dataType': 'json',
                'success' : function onFetchClassroomsSuccess( json ) {
                    console.log( json );
                    Classrooms.load( json.data );
                },
                'error'   : function onFetchClassroomsError( jqXHR, textStatus, errorThrown ) {
                    alert( "AJAX error fetching classes: " + textStatus );
                }
            } );
        },

        'remove': function ( id ) {
            $.ajax( {
                'url'   : 'api/classrooms/' + id,
                'method': 'delete',

                'dataType': 'json',
                'success' : function ( json ) {
                    console.log( json );
                    Classrooms.remove( id );
                },
                'error'   : function ( xhr ) {
                    console.log( 'error' );
                    console.log( xhr );
                }
            } )
        }
    };


    var StudentsPanel = (function () {
        var $panel;
        var $table;
        var table;
        var $newButton;

        function init( selector ) {
            cacheDom( selector );
            bindEvents();

            Students.subscribe( 'load-records', whenStudentsLoaded );
            Students.subscribe( 'remove-record', whenStudentRemoved );
            Classrooms.subscribe( 'load-records', whenClassroomsLoaded );
        }

        function cacheDom( selector ) {
            $panel     = $( selector );
            $table     = $panel.find( 'table.students-table' );
            table      = $table.DataTable( {} );
            $newButton = $panel.find( 'button.new-record' );
        }


        function bindEvents() {
            $table.on( 'click', 'button.edit', onClickEditStudent );
            $table.on( 'click', 'button.delete', onClickDeleteStudent );
            $table.on( 'click', 'button.undo', onClickUndoEdits );
            $table.on( 'click', 'button.discard', onClickDiscardNewStudent );
            $table.on( 'keyup', 'input.family-name', onKeyupEditStudentFamilyName );
            $table.on( 'keyup', 'input.given-name', onKeyupEditStudentGivenName );
            $table.on( 'change', 'select[name=classroom]', onChangeSelectClassroom );

            $newButton.on( 'click', onClickNewStudent );
        }

        function onKeyupEditStudentFamilyName() {
            var $tr     = $( this ).closest( 'tr' );
            var id      = $tr.data( 'studentId' );
            var current = Students.records[ id ];
            if ( current.familyName != $( this ).val() ) {
                $( this ).addClass( 'modified' );
                $tr.find( 'button.update' ).removeClass( 'disabled' ).attr( 'disabled', false );
                $tr.find( 'button.delete' ).removeClass( 'delete' ).addClass( 'undo' );
            } else {
                $( this ).removeClass( 'modified' );
                $tr.find( 'button.update' ).addClass( 'disabled' ).attr( 'disabled', true );
                $tr.find( 'button.undo' ).removeClass( 'undo' ).addClass( 'delete' );
            }
        }

        function onKeyupEditStudentGivenName() {
            var $tr     = $( this ).closest( 'tr' );
            var id      = $tr.data( 'studentId' );
            var current = Students.records[ id ];
            if ( current.firstName != $( this ).val() ) {
                $( this ).addClass( 'modified' );
                $tr.find( 'button.update' ).removeClass( 'disabled' ).attr( 'disabled', false );
                $tr.find( 'button.delete' ).removeClass( 'delete' ).addClass( 'undo' );
            } else {
                $( this ).removeClass( 'modified' );
                $tr.find( 'button.update' ).addClass( 'disabled' ).attr( 'disabled', true );
                $tr.find( 'button.undo' ).removeClass( 'undo' ).addClass( 'delete' );
            }
        }


        function onChangeSelectClassroom() {
            var $tr     = $( this ).closest( 'tr' );
            var id      = $tr.data( 'studentId' );
            var current = Students.records[ id ];
            if ( current.classroomId != $( this ).val() ) {
                $( this ).addClass( 'modified' );
                $tr.find( 'button.update' ).removeClass( 'disabled' ).attr( 'disabled', false );
                $tr.find( 'button.delete' ).removeClass( 'delete' ).addClass( 'undo' );
            } else {
                $( this ).removeClass( 'modified' );
                $tr.find( 'button.update' ).addClass( 'disabled' ).attr( 'disabled', true );
                $tr.find( 'button.undo' ).removeClass( 'undo' ).addClass( 'delete' );
            }
        }


        ////////////////////////////////////////////////////////////////////////////////
        // Event Handler Functions
        ////////////////////////////////////////////////////////////////////////////////
        function onClickEditStudent() {
            var $tr       = $( this ).closest( 'tr' );
            var studentId = $tr.data( 'student-id' );
            var student   = Students.records[ studentId ];
            var tr        = table.row( $tr );

            if ( Classrooms.records.length > 0 ) {
                var $select = '<select name="classroom">';
                $select += '<option value=""></option>';
                for ( var p in Classrooms.records ) {
                    var temp = Classrooms.records[ p ];
                    $select += '<option value="' + temp.id + '">' + temp.name + '</option>';
                }
                $select += '</select>';
            }

            tr.data( [
                '<input type="text" class="family-name" value="' + student.familyName + '" />',
                '<input type="text" class="given-name" value="' + student.firstName + '" />',
                $select,
                '<input type="checkbox" name="enrolled" ' + ( student.enrolled ? ' checked' : '') + '/>',
                '<button class="save"><span class="glyphicon glyphicon-ok-circle" style="color: #080"/></button>',
                '<button class="undo"><span class="glyphicon glyphicon-remove-circle" style="color: #f80"/></button>',
            ] );
            $tr.find( 'select' ).val( student.classroomId );
        }

        function onClickUndoEdits() {
            if ( confirm( 'Are you sure you want to discard your changes?' ) ) {
                var $tr       = $( this ).closest( 'tr' );
                var row       = table.row( $tr );
                var studentId = $tr.data( 'student-id' );
                var student   = Students.records[ studentId ];
                row.data( [
                    student.familyName,
                    student.firstName,
                    (Classrooms.records.length == 0
                        ? '<span class="classroom">' + student.classroomId + '</span>'
                        : '<span class="classroom">' + Classrooms.records[ student.classroomId ].name + '</span>'),
                    '<input type="checkbox" name="enrolled" disabled ' + ( student.enrolled ? ' checked' : '') + '/>',
                    '<button class="edit"><span class="glyphicon glyphicon-edit" style="color: #080"/></button>',
                    '<button class="delete"><span class="glyphicon glyphicon-remove" style="color: #800"/></button>',
                ] );
            }
        }

        function onClickDiscardNewStudent() {
            if ( confirm( 'Are you sure you want to discard this new student?' )) {
                var $tr = $( this ).closest( 'tr' );
                table.row( $tr ).remove();
                $tr.remove();
                $newButton.show();
            }
        }

        function onClickDeleteStudent() {
            if ( confirm( 'Are you sure you want to DELETE this student from the database?' ) ) {
                var $tr = $( this ).closest( 'tr' );
                var id  = $tr.data( 'studentId' );
                StudentController.remove( id );
            }
        }

        function onClickNewStudent() {
            if ( Classrooms.records.length > 0 ) {
                var $select = '<select name="edit-classroom">';
                $select += '<option value=""></option>';
                for ( var p in Classrooms.records ) {
                    var temp = Classrooms.records[ p ];
                    $select += '<option value="' + temp.id + '">' + temp.name + '</option>';
                }
                $select += '</select>';
            }
            var row = table.row.add( [
                '<input type="text" class="edit-family-name" />',
                '<input type="text" class="edit-given-name"  />',
                $select,
                '<input type="checkbox" name="enrolled" />',
                '<button class="save"><span class="glyphicon glyphicon-ok" style="color: #080"/></button>',
                '<button class="discard"><span class="glyphicon glyphicon-remove" style="color: #800"/></button>',
            ] );
            table.draw();
            $( row.node() ).addClass( 'new-classroom' );
            $( row.node() ).find( '.edit-family-name' ).focus();
            $newButton.hide();
        }


        ////////////////////////////////////////////////////////////////////////////////
        // Callback Functions
        ////////////////////////////////////////////////////////////////////////////////

        function whenStudentsLoaded( students ) {
            for ( var i = 0; i < students.length; i++ ) {
                var row = table.row.add( [
                    students[ i ].familyName,
                    students[ i ].firstName,
                    (Classrooms.records.length == 0
                        ? '<span class="classroom">' + students[ i ].classroomId + '</span>'
                        : '<span class="classroom">' + Classrooms.records[ students[ i ].classroomId ].name + '</span>'),
                    '<input type="checkbox" name="enrolled" disabled ' + ( students[ i ].enrolled ? ' checked' : '') + '/>',
                    '<button class="edit"><span class="glyphicon glyphicon-edit" style="color: #080"/></button>',
                    '<button class="delete"><span class="glyphicon glyphicon-remove" style="color: #800"/></button>',
                ] );
                $( row.node() ).data( 'studentId', students[ i ].id );
            }
            table.draw();
        }

        function whenStudentRemoved( id ) {
            table.rows().nodes().each( function ( e, i ) {
                var $tr  = $( e );
                var data = $tr.data( 'studentId' );
                if ( data === id ) {
                    $tr.remove();
                    e.remove();
                    return false;
                }
            } );
        }

        // When the classrooms are loaded,
        // replace the classroom ID in the Students table with the corresponding classroom name
        function whenClassroomsLoaded( classrooms ) {
            table.rows().nodes().each( function ( e, i, a ) {
                var studentId   = $( e ).data( 'studentId' );
                var classroomId = Students.records[ studentId ].classroomId;
                $( e ).find( 'span.classroom' ).text(
                    (Classrooms.records.length > 0 ?
                        Classrooms.records[ classroomId ].name : Students.records[ i ].classroomId) );
            } );
        }

        return {
            'init': init
        };
    })();


    var StudentController = {
        'load': function () {

            $.ajax( {
                url   : 'api/students',
                method: 'get',

                dataType: 'json',
                success : function onFetchStudentsSuccess( json ) {
                    console.log( json );
                    Students.load( json.data );
                },
                error   : function onFetchClassroomsError( jqXHR, textStatus, errorThrown ) {
                    alert( "AJAX error fetching classes: " + textStatus );
                }
            } );
        },

        'remove': function ( id ) {
            $.ajax( {
                'url'   : 'api/students/' + id,
                'method': 'delete',

                'dataType': 'json',
                'success' : function ( json ) {
                    console.log( json );
                    Students.remove( id );
                },
                'error'   : function ( xhr ) {
                    console.log( 'error' );
                    console.log( xhr );
                }
            } );
        }
    };

    var CallbackSelect = (function () {
        var $select, publicApi, callback;

        function init( $el, cb ) {
            $select  = $el;
            callback = cb;
            $select.on( 'change', callback );
        }

        function addOption( label, val ) {
            var $option;
            $option = $( '<option>' );
            $option.val( val ).text( label );
            $select.append( $option );
        }

        function empty() {
            $select.empty();
        }

        function val() {
            return $select.val();
        }

        callback = null;

        publicApi = {
            init     : init,
            empty    : empty,
            val      : val,
            addOption: addOption
        };
        return publicApi;
    })();


    // Return a Date object set to Monday of the week of the input date.
    function normalizeDateToMonday( date ) {
        if ( false === (date instanceof Date) ) {
            throw 'Can only normalize a Date object';
        }
        if ( date.getDay() < 6 ) {
            // normalize to Monday of this week
            date = new Date( date.getFullYear(), date.getMonth(), date.getDate() - (date.getDay() - 1) );
        } else {
            // Normalize to Monday of next week
            date = new Date( date.getFullYear(), date.getMonth(), date.getDate() + 2 );
        }
        return date;
    }

    // From all of a student's schedules, build a composite schedule effective the week of startDate
    function getCompositeSchedule( student, startDate ) {
        var composite,   // Return value
            cur,         // Current date within the
            sched,       // Current student's schedule
            index;       // Index into student's schedules for NEXT schedule

        // Find which student's schedule is in effect on "startDate", or null if his first schedule
        // does not take effect until some point in the future.  This way, users can enroll students
        // in advance.
        sched = null;
        index = 0;
        while ( index < student.schedules.length ) {
            if ( student.schedules[ index ].startDate > startDate ) {
                break;
            }
            sched = student.schedules[ index ];
            index++;
        }
        // "sched" is now the schedule in effect on "startDate" (or null), and "index" points to
        // the NEXT schedule.
        composite = {};
        cur       = new Date( startDate.getFullYear(), startDate.getMonth(), startDate.getDate() );
        [ 'mon', 'tue', 'wed', 'thu', 'fri' ].forEach( function ( day, i, arr ) {
            if ( sched == null ) {
                composite[ day ] = null;
            } else if ( cur.getDay() - i >= 2 ) {
                // If some clown passes in a startDate in the middle  of the week, the effective
                // schedule for all days prior to the start date should be null.
                composite[ day ] = null;
            } else {
                composite[ day ] = sched[ day ];
            }

            // Prepare for the next day: see if the student's next schedule goes into effect
            cur = new Date( cur.getFullYear(), cur.getMonth(), cur.getDate() + 1 );
            if ( index < student.schedules.length ) {
                if ( cur >= student.schedules[ index ].startDate ) {
                    sched = student.schedules[ index ];
                    index++;
                }
            }
        } );
        return composite;
    }

    /********************************************************************************
     * Table showing checkin/checkout
     ********************************************************************************/
    var CheckinPage = (function () {
        var $page,
            $clock,
            $calendar,
            today,
            $checkInReport,
            $filterButtons,
            $classFilter,
            $checkinTable,
            $tbody,
            publicApi;
        var source, template;

        function init( selector ) {
            cacheDom( selector );
            bindEvents();
            source   = $( '#attendance-checkin-row-template' ).html();
            template = Handlebars.compile( source );

            today = new Date();
            tick();
        }

        function cacheDom( selector ) {
            $page     = $( selector );
            $clock    = $page.find( 'span.clock' );
            $calendar = $page.find( 'span.calendar' );

            $checkInReport = $page.find( 'div.attendance-checkin' );
            $filterButtons = $page.find( '.btn-group-toggle button' );
            $classFilter   = Object.create( CallbackSelect );
            $classFilter.init( $page.find( 'select.filter-select' ), function filterByClassroom( event ) {
                $tbody.filter();
            } );
            $checkinTable = $page.find( 'table#attendance-checkin-table' );
            $tbody        = $checkinTable.find( 'tbody' );
        }

        function bindEvents() {
            $page.on( 'show', function onCheckinPageShow( event ) {
                event.stopPropagation();
                $classFilter.empty();
                $classFilter.addOption( 'Show All' );
                $classFilter.addOption( 'Unassigned', 0 );
                Classrooms.forEach( function ( classroom ) {
                    $classFilter.addOption( classroom.name, classroom.id );
                } );
                $tbody.filter();

                $tbody.empty();
                Students.forEach( function ( student ) {
                    var att;
                    var date;
                    if ( student.attendance.length > 0 ) {
                        att = student.attendance[ student.attendance.length - 1 ];
                        if ( att.checkIn ) {
                            date = new Date( att.checkIn );
                            if ( date.getDate() === today.getDate() ) {
                                student.checkedIn = date;
                            }
                        }
                        if ( att.checkOut ) {
                            date = new Date( att.checkOut );
                            if ( date.getDate() === today.getDate() ) {
                                student.checkedOut = date;
                            }
                        }
                    }
                    var html = template( student );
                    $tbody.append( $( html ) );
                } );
                var $rows = $tbody.children( 'tr' );
                $rows.sort( function ( row1, row2 ) {
                    var a = Students[ $( row1 ).data( 'student-id' ) ];
                    var b = Students[ $( row2 ).data( 'student-id' ) ];
                    return (a.familyName < b.familyName) ? -1 :
                        (a.familyName > b.familyName) ? 1 :
                            (a.firstName < b.firstName) ? -1 :
                                (a.firstName > b.firstName) ? 1 :
                                    a.id < b.id ? -1 : 0;
                } );
                $rows.detach().appendTo( $tbody );
            } );

            $filterButtons.on( 'click', function () {
                if ( !$( this ).hasClass( 'btn-selected' ) ) {
                    $filterButtons.removeClass( 'btn-selected' );
                    $( this ).addClass( 'btn-selected' );
                    $tbody.filter();
                }
            } );

            $tbody.on( 'show', 'tr', function ( event ) {
                event.stopPropagation();
            } );
            $tbody.on( 'click', 'button.check-in', function () {
                var $button, studentId, checkIn;
                $button   = $( this );
                studentId = $button.closest( 'tr' ).data( 'student-id' );
                $.ajax( {
                    url     : 'api/checkIn',
                    method  : 'post',
                    data    : {
                        'studentId': studentId,
                        'time'     : Date.now() / 1000
                    },
                    dataType: 'json',
                    success : function onCheckInSuccess( json ) {
                        if ( !json.success ) {
                            alert( 'Error checking in student: ' + json.message );
                        } else {
                            Students[ studentId ].attendance.push( {
                                'checkIn' : json.attendance.checkIn * 1000,
                                'checkOut': json.attendance.checkOut * 1000
                            } );
                            checkIn = new Date( 1000 * json.attendance.checkIn );
                            $button.closest( 'tr' ).find( 'td.check-in' ).text( formatTime( checkIn ) );
                            $button.closest( 'tr' ).find( 'td.check-out' ).text( '' );
                        }
                    },
                    error   : function onCheckInError( jqXHR, textStatus, errorThrown ) {
                        alert( 'AJAX error checking in student: ' + textStatus );
                    }
                } );
            } );

            $tbody.on( 'click', 'button.check-out', function () {
                var $button, studentId, checkOut;
                $button   = $( this );
                studentId = $button.closest( 'tr' ).data( 'student-id' );
                $.ajax( {
                    url     : 'api/checkOut',
                    method  : 'post',
                    data    : {
                        'studentId': studentId,
                        'time'     : Date.now() / 1000
                    },
                    dataType: 'json',
                    success : function onCheckOutSuccess( json ) {
                        var att;
                        if ( !json.success ) {
                            alert( 'Error checking out student: ' + json.message );
                        } else if ( 0 === Students[ studentId ].attendance.length ) {
                            Students[ studentId ].attendance.push( {
                                'checkIn' : json.attendance.checkIn * 1000,
                                'checkOut': json.attendance.checkOut * 1000
                            } );
                        } else {
                            att = Students[ studentId ].attendance[ Students[ studentId ].attendance.length - 1 ];
                            if ( null === att.checkOut ) {
                                att.checkOut = json.attendance.checkOut;
                            } else {
                                Students[ studentId ].attendance.push( {
                                    'checkIn' : json.attendance.checkIn * 1000,
                                    'checkOut': json.attendance.checkOut * 1000
                                } );
                            }
                        }
                        checkOut = new Date( 1000 * json.attendance.checkOut );
                        var t    = $button.closest( 'tr' ).find( 'td.check-out' ).text();
                        if ( t != '' ) {
                            $button.closest( 'tr' ).find( 'td.check-in' ).text( '' );
                        }
                        $button.closest( 'tr' ).find( 'td.check-out' ).text( formatTime( checkOut ) );

                    },
                    error   : function onCheckOutError( jqXHR, textStatus, errorThrown ) {
                        alert( 'AJAX error checking out student: ' + textStatus );
                    }
                } );
            } );

            $tbody.filter = function () {
                var now, day;
                var toggle;
                var classroom;

                toggle    = $filterButtons.filter( '.btn-selected' ).data( 'toggle' );
                classroom = $classFilter.val();
                now       = new Date();
                day       = [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ][ now.getDay() ];
                $tbody.find( 'tr.data' ).each( function ( i, e ) {
                    var student, sched;
                    student = Students[ $( e ).data( 'student-id' ) ];
                    sched   = student.schedules[ student.schedules.length - 1 ][ day ];
                    if ( ((toggle == 'enrolled') || (sched && (sched.Am || sched.Noon || sched.Pm )))
                        && (('' == classroom) || ((0 == classroom) && (undefined == student.classroomId)) || (classroom == student.classroomId )) ) {
                        $( e ).show()
                    } else {
                        $( e ).hide();
                    }
                } );
            };
        }

        function tick() {
            var now, hh, mm, ss;
            var months = [ 'Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];

            now = new Date();
            hh  = now.getHours() % 12;
            if ( 0 == hh ) hh = 12;
            mm = now.getMinutes();
            if ( mm < 10 ) {
                mm = '0' + mm;
            }
            ss = now.getSeconds();
            if ( ss < 10 ) {
                ss = '0' + ss;
            }
            $clock.text( hh + ':' + mm + ':' + ss + (now.getHours() < 12 ? 'am' : 'pm') );
            $calendar.text( months[ now.getMonth() ] + ' ' + now.getDate() + ', ' + now.getFullYear() );
            if ( today.getDate() != now.getDate() ) {
                today = new Date();
                clearData();
            }
            setTimeout( tick, 1000 );
        }

        function clearData() {
            $tbody.find( 'td.check-in' ).text( '' );
            $tbody.find( 'td.check-out' ).text( '' );
            $tbody.find( 'span.glyphicon-ok' ).remove();
        }

        publicApi = {
            init: init
        };
        return publicApi;
    })();


    /********************************************************************************
     * Table showing attendance schedules
     ********************************************************************************/
    var AttendancePage = (function () {
        var $page,
            $attendanceSchedules,
            weekOf,   // Monday of the week to display; default to this week
            $weekOf,  // Control to specify weekOf
            publicApi;


        function init( selector ) {
            cacheDom( selector );
            bindEvents();

            weekOf = new Date();
            weekOf = normalizeDateToMonday( weekOf );
            $weekOf.datepicker();
            $weekOf.datepicker( 'option', 'showAnim', 'slideDown' );
            $weekOf.datepicker( 'setDate', weekOf );
            $( '#pdf-attendance' ).attr( 'href', 'pdf/attendance?week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate() );
        }


        function cacheDom( selector ) {
            $page                = $( selector );
            $weekOf              = $page.find( 'input[name=week-of]' );
            $attendanceSchedules = $page.find( 'div.attendance-page-schedules' );
        }


        // Generate the attendance sheets
        function generateAttendanceSheets() {
            var source;     // Source for the Handlebars template
            var template;   // The compiled template

            $attendanceSchedules.empty();
            source   = $( '#attendance-schedule-class-template' ).html();
            template = Handlebars.compile( source );
            Classrooms.classrooms.forEach( function ( classroom ) {
                var context,   // The runtime data to pass to Handlebars
                    html;
                context = {
                    classroom: classroom,
                    weekOf   : weekOf.toDateString(),
                    dates    : [
                        weekOf,
                        new Date( weekOf.getFullYear(), weekOf.getMonth(), weekOf.getDate() + 1 ),
                        new Date( weekOf.getFullYear(), weekOf.getMonth(), weekOf.getDate() + 2 ),
                        new Date( weekOf.getFullYear(), weekOf.getMonth(), weekOf.getDate() + 3 ),
                        new Date( weekOf.getFullYear(), weekOf.getMonth(), weekOf.getDate() + 4 ),
                    ],

                    students: Students.filter( function ( e, i, arr ) {
                        return ((e.classroomId == classroom.id) && (true == e.enrolled));
                    } ).sort( function ( a, b ) {
                        return (a.familyName < b.familyName) ? -1 :
                            (a.familyName > b.familyName) ? 1 :
                                (a.firstName < b.firstName) ? -1 :
                                    (a.firstName > b.firstName) ? 1 :
                                        a.id < b.id ? -1 : 1;
                    } ),
                    totals  : {
                        'mon': 0,
                        'tue': 0,
                        'wed': 0,
                        'thu': 0,
                        'fri': 0
                    }
                };

                context.students.forEach( function ( student, i, arr ) {
                    var composite;
                    var notes;
                    composite        = getCompositeSchedule( student, weekOf );
                    student.schedule = {};
                    notes            = {
                        'HD' : 0,
                        'HDL': 0,
                        'FD' : 0
                    };
                    for ( var day in composite ) {
                        if ( null == composite[ day ] ) {
                            student.schedule[ day ] = false;
                        } else {
                            student.schedule[ day ] = [];
                            if ( composite[ day ][ 'Am' ] ) student.schedule[ day ].push( 'A' );
                            if ( composite[ day ][ 'Noon' ] ) student.schedule[ day ].push( 'L' );
                            if ( composite[ day ][ 'Pm' ] ) student.schedule[ day ].push( 'P' );
                            student.schedule[ day ] = student.schedule[ day ].join( '/' );
                            if ( student.schedule[ day ] ) context.totals[ day ]++;

                            if ( ( composite[ day ][ 'Am' ]) && ( composite[ day ][ 'Pm' ]) ) {
                                notes[ 'FD' ]++;
                            } else if ( ( composite[ day ][ 'Am' ]) || ( composite[ day ][ 'Pm' ]) ) {
                                if ( composite[ day ][ 'Noon' ] ) {
                                    notes[ 'HDL' ]++;
                                } else {
                                    notes[ 'HD' ]++;
                                }
                            }
                        }
                    }
                    student.notes = [];
                    if ( notes[ 'FD' ] ) student.notes.push( notes[ 'FD' ] + 'FD' );
                    if ( notes[ 'HD' ] ) student.notes.push( notes[ 'HD' ] + 'HD' );
                    if ( notes[ 'HDL' ] ) student.notes.push( notes[ 'HDL' ] + 'HDL' );
                    student.notes = student.notes.join( ',' );
                } );

                // Append 3 blank entries to end of each class list
                [ 1, 2, 3 ].forEach( function () {
                    context.students.push( {
                        firstName : '',
                        familyName: '',
                        schedule  : { 'mon': {}, 'tue': {}, 'wed': {}, 'thu': {}, 'fri': {} },
                        notes     : ''
                    } );
                } );
                html = template( context );
                $attendanceSchedules.append( $( html ) );
            } );

        }


        function bindEvents() {
            $page.on( 'show', generateAttendanceSheets );
            $weekOf.on( 'change', function onWeekOfChange() {
                weekOf = $weekOf.datepicker( 'getDate' );
                weekOf = normalizeDateToMonday( weekOf );
                $weekOf.datepicker( 'setDate', weekOf ).blur();
                generateAttendanceSheets();
                $( '#pdf-attendance' ).attr( 'href', 'pdf/attendance?week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate() );
            } );
        }


        function sort( key ) {
            var $rows = $tbody.children( 'tr[data-student-id]' );
            $rows.sort( function ( a, b ) {
                var id1, id2, key1, key2;
                id1  = $( a ).attr( 'data-student-id' );
                id2  = $( b ).attr( 'data-student-id' );
                key1 = Students[ id1 ][ key ];
                key2 = Students[ id2 ][ key ];
                return (key1 > key2) ? 1 : (key2 > key1) ? -1 : 0;
            } );
            $rows.detach().prependTo( $tbody );
        }


        publicApi = {
            init: init
        };
        return publicApi;
    })();


    /********************************************************************************
     * Table showing signin schedules
     ********************************************************************************/
    var SigninPage = (function () {
        var $page,
            $contents,
            weekOf,   // Monday of the week to display; default to this week
            $weekOf,  // Control to select weekOf
            publicApi;

        function init( selector ) {
            cacheDom( selector );
            bindEvents();

            weekOf = new Date();
            weekOf = normalizeDateToMonday( weekOf );
            $weekOf.datepicker();
            $weekOf.datepicker( 'option', 'showAnim', 'slideDown' );
            $weekOf.datepicker( 'setDate', weekOf );
            $( '#pdf-signin' ).attr( 'href', 'pdf/signin?week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate() );
        }

        function cacheDom( selector ) {
            $page     = $( selector );
            $weekOf   = $page.find( 'input[name=week-of]' );
            $contents = $page.find( '.signin-page-contents' );
        }


        function generateSigninSheets() {
            var source;     // Source for the Handlebars template
            var template;   // The compiled template
            $contents.empty();
            source   = $( '#attendance-signin-class-template' ).html();
            template = Handlebars.compile( source );
            Classrooms.classrooms.forEach( function ( classroom ) {
                var context,
                    html;
                context = {
                    classroom: classroom.name,
                    weekOf   : weekOf.toDateString(),
                    students : Students.filter( function ( e, i, arr ) {
                        return ((e.classroomId == classroom.id) && (true == e.enrolled));
                    } ).sort( function ( a, b ) {
                        return (a.familyName < b.familyName) ? -1 :
                            (a.familyName > b.familyName) ? 1 :
                                (a.firstName < b.firstName) ? -1 :
                                    (a.firstName > b.firstName) ? 1 :
                                        a.id < b.id ? -1 : 1;
                    } )
                };
                context.students.forEach( function ( student, i, arr ) {
                    var composite;
                    var notes;
                    composite        = getCompositeSchedule( student, weekOf );
                    student.schedule = {};
                    for ( var day in composite ) {
                        if ( null == composite[ day ] ) {
                            student.schedule[ day ] = false;
                        } else {
                            student.schedule[ day ] = [];
                            if ( composite[ day ][ 'Am' ] ) student.schedule[ day ].push( 'A' );
                            if ( composite[ day ][ 'Noon' ] ) student.schedule[ day ].push( 'L' );
                            if ( composite[ day ][ 'Pm' ] ) student.schedule[ day ].push( 'P' );
                            student.schedule[ day ] = student.schedule[ day ].join( '/' );
                        }
                    }
                } );

                // Append 3 blank entries to end of each class list
                [ 1, 2, 3 ].forEach( function () {
                    context.students.push( {
                        firstName : '',
                        familyName: '',
                        schedule  : { 'mon': {}, 'tue': {}, 'wed': {}, 'thu': {}, 'fri': {} },
                        notes     : ''
                    } );
                } );

                html = template( context );
                $contents.append( $( html ) );
            } );

        }

        function bindEvents() {
            $page.on( 'show', generateSigninSheets );

            $weekOf.on( 'change', function onWeekOfChange() {
                weekOf = $weekOf.datepicker( 'getDate' );
                weekOf = normalizeDateToMonday( weekOf );
                $weekOf.datepicker( 'setDate', weekOf ).blur();
                generateSigninSheets();
                $( '#pdf-signin' ).attr( 'href', 'pdf/signin?week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate() );
            } );
        }

        publicApi = {
            init: init
        };
        return publicApi;
    })();


    /********************************************************************************
     * Page for displaying reports
     ********************************************************************************/
    var ReportsPage = (function () {
        var $page, $pills, targets, $panels, publicApi;

        function init( selector ) {
            cacheDom( selector );
            bindEvents();
        }

        function cacheDom( selector ) {
            $page   = $( selector );
            $pills  = $page.find( 'li.report-pill' );
            targets = $pills.map( function () {
                return $( this ).data( 'target' );
            } ).get();
            $panels = $page.find( 'div.report' );
        }

        function bindEvents() {
            $page.on( 'show', '.panel', function ( event ) {
                event.stopPropagation();
            } );

            $page.on( 'show', function () {
                showReport();
            } );

            $pills.on( 'click', function () {
                showReport( $( this ).data( 'target' ) );
            } );

        }

        function showReport( id ) {
            id = id || targets[ 0 ];
            $pills.removeClass( 'active' ).filter( function () {
                return ($( this ).data( 'target' ) === id);
            } ).addClass( 'active' );
            $panels.hide().filter( id ).show();
        }

        publicApi = {
            init      : init,
            showReport: showReport
        };
        return publicApi;
    })();


    /********************************************************************************
     * Table showing all students with in-place editing
     ********************************************************************************/
    var EnrollmentPage = (function () {
        var $page,
            $classFilter,
            $activeFilter,
            $studentList,
            $newStudent,
            $deleteStudent,

            $studentDetails,
            $familyName,
            $firstName,
            $isEnrolled,
            $classrooms,
            $inputs,

            $whichSchedule,
            $checkAll,
            $scheduleTable,
            $checkboxes,
            $startDate,
            $endDate,
            $thCheckers,
            $tdCheckers,
            $checkButtons,
            $saveButtons,
            $saveAndAnother,
            $saveAndClose,
            $cancelStudent,
            publicApi;

        function init( selector ) {
            cacheDom( selector );
            bindMethods();
            bindEvents();
        }

        function cacheDom( selector ) {
            $page         = $( selector );
            $classFilter  = $page.find( 'select[name=classroom-filter]' );
            $activeFilter = $page.find( 'select[name=active-filter]' );
            $studentList  = $page.find( 'select[name=student-list]' );

            $newStudent    = $page.find( 'button[name=new-student-button]' );
            $deleteStudent = $page.find( 'button[name=delete-student-button]' );

            $studentDetails = $page.find( '.student-detail-panel' );

            $familyName = $page.find( 'input[name=familyName]' );
            $firstName  = $page.find( 'input[name=firstName]' );
            $isEnrolled = $page.find( 'input[name=enrolled]' );
            $classrooms = $page.find( 'select[name=classrooms]' );
            $inputs     = $page.find( 'input[type=text]' );
            $startDate  = $page.find( 'input[name=startDate]' );
            $startDate.datepicker();
            $startDate.datepicker( "option", "showAnim", 'slideDown' );
            $endDate = $page.find( 'input[name=endDate]' );
            $endDate.datepicker();
            $endDate.datepicker( "option", "showAnim", 'slideDown' );

            $whichSchedule  = $page.find( 'select[name=whichSchedule]' );
            $scheduleTable  = $page.find( 'table#student-schedule-table' );
            $checkAll       = $page.find( 'button.checkAll' );
            $checkboxes     = $scheduleTable.find( 'input[type=checkbox]' );
            $thCheckers     = $scheduleTable.find( 'th.checkcontrol' );
            $tdCheckers     = $scheduleTable.find( 'td.checkcontrol' );
            $checkButtons   = $scheduleTable.find( '.checkcontrol button' );
            $saveButtons    = $page.find( 'button.btn-save' );
            $saveAndAnother = $page.find( 'button[name=save-and-another]' );
            $saveAndClose   = $page.find( 'button[name=save-and-close]' );
            $cancelStudent  = $page.find( 'button[name=cancel-student-button]' );
        }

        function bindMethods() {

            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Class Filter" drop-down list
            ////////////////////////////////////////////////////////////////////////////////
            $classFilter.addClassroom = function ( classroom ) {
                var $option = $( '<option>' ).text( classroom.name ).val( classroom.id );
                $classFilter.append( $option );
                return $classFilter;
            };

            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Students" list
            ////////////////////////////////////////////////////////////////////////////////
            // Filter the students in the student list according to classroom
            $studentList.filter = function () {
                // IE doesn't support $.show() and $.hide() of <option> elements, so instead
                // we hide an <option> by wrapping it in a <span>, and show it by unwrapping
                // it. Seems to work in IE, Chrome and Firefox
                var classId, active;
                classId = $classFilter.val();

                $( this ).find( 'span > option' ).unwrap();   // Show all
                if ( '' == classId ) {
                    // Show all
                } else if ( classId == 0 ) {
                    // Show unassigned
                    $( this ).find( 'option' ).each( function ( i, e ) {
                        var student = Students[ $( e ).val() ];
                        if ( null != student.classroomId ) {
                            $( e ).wrap( '<span>' );
                        }
                    } );
                } else {
                    $( this ).find( 'option' ).each( function ( i, e ) {
                        var student = Students[ $( e ).val() ];
                        if ( null == student.classroomId || classId !== student.classroomId ) {
                            $( e ).wrap( '<span>' );
                        }
                    } );
                }

                active = $activeFilter.val();
                if ( '' == active ) {
                    // Show all
                } else {
                    $( this ).find( 'option' ).each( function ( i, e ) {
                        var student = Students[ $( e ).val() ];
                        if ( $( e ).parent().is( 'span' ) ) {
                            // No option; don't double-wrap
                        } else if ( 'true' == active && !student.enrolled ) {
                            $( e ).wrap( '<span>' );
                        } else if ( 'false' == active && student.enrolled ) {
                            $( e ).wrap( '<span>' );
                        }
                    } );
                }
                return $studentList;
            };

            // Add a student to the Student List
            $studentList.addStudent = function ( student ) {
                var $option;
                $option = $( '<option>' )
                    .text( student.familyName + ', ' + student.firstName ).val( student.id );
                $studentList.append( $option );
                return $studentList;
            };

            // Update one of the students in the student list
            $studentList.updateStudent = function ( student ) {
                var $opt, filter;
                $opt = $( this ).find( 'option[value=' + student.id + ']' );
                $opt.text( student.familyName + ', ' + student.firstName );
                filter = $classFilter.val();
                if ( ( '' == filter )
                    || (( 0 == filter ) && ( undefined == student.classroomId ))
                    || ((undefined != student.classroomId) && (filter == student.classroomId )) ) {
                    $opt.show();
                } else {
                    $opt.hide();
                }
                $studentList.filter();
                $studentList.sort();
                return $studentList;
            };

            // Remove the student identified by the input parameter
            $studentList.deleteStudent = function ( studentId ) {
                $studentList.find( 'option[value=' + studentId + ']' ).remove();
                return $studentList;
            };

            // Sort the student list according to last name
            $studentList.sort = function () {
                var $options = $( this ).children( 'option' );
                $options.sort( function ( a, b ) {
                    return (Students[ $( a ).val() ].familyName > Students[ $( b ).val() ].familyName) ? 1 :
                        (Students[ $( a ).val() ].familyName < Students[ $( b ).val() ].familyName) ? -1 :
                            (Students[ $( a ).val() ].firstName > Students[ $( b ).val() ].firstName) ? 1 :
                                (Students[ $( a ).val() ].firstName < Students[ $( b ).val() ].firstName) ? -1 : 0;
                } );
                $options.detach().prependTo( $( this ) );
                return $studentList;
            };

            // How to update the GUI after a student has been selected in the Student List
            $studentList.updateStudentDetails = function ( studentId ) {
                var student;

                student = Students[ studentId ];
                $deleteStudent.prop( 'disabled', student.enrolled );
                $familyName.val( student.familyName ).data( 'dbval', student.familyName ).removeClass( 'modified' ).prop( 'disabled', false );
                $firstName.val( student.firstName ).data( 'dbval', student.firstName ).removeClass( 'modified' ).prop( 'disabled', false );
                $isEnrolled.prop( 'checked', student.enrolled ).prop( 'disabled', false );
                $classrooms.val( student.classroomId ? student.classroomId : 0 ).data( 'dbval', student.classroomId ).removeClass( 'modified' ).prop( 'disabled', false );

                $whichSchedule.prop( 'disabled', false ).empty();
                student.schedules.forEach( function ( e, i, arr ) {
                    $whichSchedule.addSchedule( e, i );
                } );

                $whichSchedule.selectSchedule( getToday() );
                $checkAll.prop( 'disabled', false );
                $checkButtons.prop( 'disabled', false );
                $startDate.enable( false );
                $endDate.enable( false );

                $saveButtons.prop( 'disabled', false );
                $cancelStudent.prop( 'disabled', false );

                return $studentList;
            };

            // Unselect all students and reset page state.
            $studentList.resetStudentDetails = function () {
                $newStudent.prop( 'disabled', false );
                $deleteStudent.prop( 'disabled', true );
                $inputs.val( '' ).data( 'dbval', '' ).removeClass( 'modified' ).prop( 'disabled', true );
                $isEnrolled.prop( 'checked', false ).prop( 'disabled', true );
                $classrooms.val( 0 ).data( 'dbval', 0 ).removeClass( 'modified' ).prop( 'disabled', true );
                $whichSchedule.empty().removeClass( 'modified' ).prop( 'disabled', true );
                $checkAll.prop( 'disabled', true );
                $checkButtons.prop( 'disabled', true );
                $checkboxes.prop( 'checked', false )
                    .data( 'dbval', false )
                    .removeClass( 'modified' )
                    .prop( 'disabled', true )
                    .closest( 'td' ).removeClass( 'modified' );
                $saveButtons.prop( 'disabled', true );
                $cancelStudent.prop( 'disabled', true );
                return $studentList;
            };


            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Class" drop-down list
            ////////////////////////////////////////////////////////////////////////////////
            $classrooms.addClassroom = function ( classroom ) {
                var $option = $( '<option>' ).text( classroom.name ).val( classroom.id );
                $classrooms.append( $option );
                return $classrooms;
            };


            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Select Schedule" drop-down list
            ////////////////////////////////////////////////////////////////////////////////

            // Add a new schedule to the list of available schedules
            $whichSchedule.addSchedule = function ( schedule, index ) {
                var startDate,
                    $option,
                    months;
                months    = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
                startDate = new Date( schedule.startDate );
                $option   = $( '<option>' )
                    .text( months[ startDate.getMonth() ] + ' ' + startDate.getDate() + ', ' + startDate.getFullYear() )
                    .val( index )
                    .data( 'startDate', startDate );
                $whichSchedule.append( $option );
            };

            // Selects the schedule which will be effective on the given date
            $whichSchedule.selectSchedule = function ( targetDate ) {
                var $opts;
                var startDate;
                var i;

                $opts = $whichSchedule.find( 'option' );
                if ( 0 == $opts.length ) {
                    $checkboxes.reset();
                } else {
                    if ( targetDate <= $opts.eq( 0 ).data( 'startDate' ) ) {
                        $whichSchedule.val( 0 );
                    } else {
                        for ( i = 1; i < $opts.length; i++ ) {
                            startDate = $opts.eq( i ).data( 'startDate' );
                            if ( startDate > targetDate ) {
                                $whichSchedule.val( i - 1 );
                                break;
                            }
                        }
                        if ( i == $opts.length ) {
                            $whichSchedule.val( $opts.length - 1 );
                        }
                    }
                    $whichSchedule.updateSchedule();
                }
            };

            // Update the GUI when user selects a new schedule
            $whichSchedule.updateSchedule = function () {
                var student;
                var schedule;
                student  = Students[ $studentList.val() ];
                schedule = student.schedules[ $whichSchedule.val() ];
                $checkboxes.initialize( schedule );
            };


            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Student Schedule" checkboxes
            ////////////////////////////////////////////////////////////////////////////////

            // Return true if any of the checkboxes have been modified - that is, have a value different from the one
            // stored in the database.
            $checkboxes.areModified = function () {
                return ($checkboxes.filter( function ( index ) {
                    return $( this ).data( 'dbval' ) !== $( this ).prop( 'checked' );
                } ).length > 0);
            };

            // Convert the checkboxes into something that can be passed in an HTTP request
            $checkboxes.serializeSchedule = function () {
                var temp  = {};
                var part;
                var day;
                var sched = [];
                $scheduleTable.find( 'tbody tr[data-day-part]' ).each( function ( i, e ) {
                    temp[ $( e ).data( 'day-part' ) ] = ($( e ).find( 'input:checked' ).map( function ( i, e ) {
                        return $( e ).attr( 'name' );
                    } ).get());
                } );

                for ( part in temp ) {
                    for ( day in temp[ part ] ) {
                        sched.push( temp[ part ][ day ] + part + '=On' );
                    }
                }
                return sched.join( '&' );
            };

            // Set/clear the schedule checkbox
            $checkboxes.initialize = function ( sched ) {
                $checkboxes.each( function initializeScheduleCheckbox( i, e ) {
                    $( e ).prop( 'checked', sched[ $( e ).attr( 'name' ) ][ $( e ).closest( 'tr' ).data( 'day-part' ) ] );
                    $( e ).data( 'dbval', sched[ $( e ).attr( 'name' ) ][ $( e ).closest( 'tr' ).data( 'day-part' ) ] );
                    $( e ).removeClass( 'modified' ).prop( 'disabled', false ).closest( 'td' ).removeClass( 'modified' );
                } );
            };

            // Clear all checkboxes
            $checkboxes.reset = function () {
                $checkboxes.each( function resetCheckbox( i, e ) {
                    $( e ).prop( 'checked', false );
                    $( e ).data( 'dbval', false );
                    $( e ).removeClass( 'modified' ).prop( 'disabled', false ).closest( 'td' ).removeClass( 'modified' );
                } );
            };


            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Schedule Start Date" control
            ////////////////////////////////////////////////////////////////////////////////

            // Enable or disable the Start Date widget.  If enabling, initialize with the current date.  Otherwise,
            // clear the widget and remove the 'modified' class.
            $startDate.enable = function ( b ) {
                $startDate.prop( 'disabled', !b );
                if ( true === b ) {
                    if ( !$startDate.val() ) {
                        $startDate.datepicker( 'setDate', new Date() );
                    }
                } else {
                    $startDate.val( '' ).removeClass( 'modified' );
                }
            };

            $endDate.enable = function ( b ) {
                $endDate.prop( 'disabled', !b );
                if ( false === b ) {
                    $endDate.val( '' ).removeClass( 'modified' );
                }
            };


            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Delete Student" control
            ////////////////////////////////////////////////////////////////////////////////

            // Enable or disable the Delete Student control.
            $deleteStudent.enable = function ( b ) {
                $deleteStudent.prop( 'disabled', !b );
            };

            $deleteStudent.deleteStudent = function ( studentId ) {
                if ( confirm( 'Are you sure you want to delete ' + Students[ studentId ].firstName + ' ' + Students[ studentId ].familyName + '?' ) ) {
                    $.ajax( {
                        url     : 'api/deleteStudent',
                        method  : 'post',
                        data    : { 'id': studentId },
                        dataType: 'json',
                        success : function onDeleteStudentSuccess( json ) {
                            if ( true != json.success ) {
                                alert( 'Unable to delete student: ' + json.message );
                            } else {
                                delete Students[ studentId ];
                                $studentList.deleteStudent( studentId );
                                if ( !$studentList.val() ) {
                                    $studentList.resetStudentDetails();
                                }
                            }
                        },
                        error   : function onDeleteStudentError( jqXHR, textStatus, errorThrown ) {
                            alert( 'AJAX error deleting student: ' + textStatus );
                        }
                    } );
                }
            }
        }


        function bindEvents() {
            // We can do this because we extended the jQuery 'show' function
            $page.on( 'show', function ( event ) {
                event.stopPropagation();

                $classFilter.empty();
                $classFilter.append( $( '<option>' ).text( 'Show All' ).val( '' ) );
                $classFilter.append( $( '<option>' ).text( 'Unassigned' ).val( 0 ) );

                $classrooms.empty();
                $classrooms.append( $( '<option>' ).text( 'Unassigned' ).val( 0 ) );

                Classrooms.classrooms.forEach( function ( classroom ) {
                    $classrooms.addClassroom( classroom );
                    $classFilter.addClassroom( classroom );
                } );

                $studentList.empty();
                Students.forEach( function ( student ) {
                    $studentList.addStudent( student );
                } );
                $studentList.sort().resetStudentDetails();
            } );


            $classFilter.on( 'change', function () {
                $studentList.filter();
            } );

            $activeFilter.on( 'change', function () {
                $studentList.filter();
            } );

            $studentList.on( 'change', function () {
                if ( $( this ).val() ) {
                    $studentList.updateStudentDetails( $( this ).val() );
                } else {
                    $studentList.resetStudentDetails();
                }
            } );


            $studentList.on( 'show', 'option', function ( event ) {
                event.stopPropagation();
            } );


            $newStudent.on( 'click', function () {
                $studentList.val( '' );
                $studentList.resetStudentDetails();
                $deleteStudent.prop( 'disabled', true );
                $inputs.val( '' ).removeClass( 'modified' ).prop( 'disabled', false );

                $isEnrolled.prop( 'checked', false ).prop( 'disabled', false );
                $classrooms.val( 0 ).removeClass( 'modified' ).prop( 'disabled', false );

                $whichSchedule.empty().prop( 'disbaled', true );
                $checkAll.prop( 'disabled', false );
                $checkButtons.prop( 'disabled', false );
                $checkboxes.prop( 'checked', false ).removeClass( 'modified' ).prop( 'disabled', false )
                    .closest( 'td' ).removeClass( 'modified' );
                $startDate.datepicker( 'setDate', new Date() );

                $saveButtons.prop( 'disabled', false );
                $cancelStudent.prop( 'disabled', false );

                $familyName.focus();
            } );

            $deleteStudent.on( 'click', function () {
                $deleteStudent.deleteStudent( $studentList.val() );

            } );

            // When the user changes the value in an input control, set the 'modified' class on that control if the new
            // value is different from the value in the database; clear the 'modified' class if the the new value is the
            // same as the value in the database.
            $inputs.on( 'change', function () {
                if ( $( this ).val() != $( this ).data( 'dbval' ) ) {
                    $( this ).addClass( 'modified' );
                } else {
                    $( this ).removeClass( 'modified' );
                }
            } );

            $classrooms.on( 'change', function () {
                $( this ).addClass( 'modified' );
            } );

            $whichSchedule.on( 'change', function () {
                $whichSchedule.updateSchedule();
            } );

            // When a checkbox in the 'Schedule' table changes value, add the 'modified' class if the new value is
            // different from the value in the database.
            $checkboxes.on( 'change', function () {
                if ( $( this ).is( ':checked' ) != $( this ).data( 'dbval' ) ) {
                    $( this ).addClass( 'modified' );
                    $( this ).closest( 'td' ).addClass( 'modified' );
                } else {
                    $( this ).removeClass( 'modified' );
                    $( this ).closest( 'td' ).removeClass( 'modified' );
                }

                // If any of the checkboxes in the Schedule table are modified, enable the start- and end-date widgets
                $startDate.enable( $checkboxes.areModified() );
                $endDate.enable( $checkboxes.areModified() );
            } );

            // When the user clicks on the 'Check All' button above the 'Schedule' table, set all of the checkboxes
            // in the table, unless they ARE already set, in which case, clear them.
            $checkAll.on( 'click', function () {
                var $unchecked;
                $unchecked = $checkboxes.filter( ':not(:checked)' );
                if ( $unchecked.length ) {
                    $unchecked.trigger( 'click' );
                } else {
                    $checkboxes.trigger( 'click' );
                }
            } );

            // When the user clicks on a column header in the 'Schedule' table, set all of the checkboxes in that
            // column, unless they ARE all already set, in which case, clear them.
            $thCheckers.on( 'click', 'button', function () {
                var index, $boxes, $unchecked;
                index      = ($( this ).closest( 'th' ).index()) + 1;
                $boxes     = $scheduleTable.find( 'tr > td:nth-child(' + index + ') input[type=checkbox]' );
                $unchecked = $boxes.filter( ':not(:checked)' );
                if ( $unchecked.length ) {
                    $unchecked.trigger( 'click' );
                } else {
                    $boxes.trigger( 'click' );
                }
            } );

            // When the user clicks on a row header in the 'Schedule' table, set all of the checkboxes in that
            // row, unless the ARE all already set, in which case, clear them.
            $tdCheckers.on( 'click', 'button', function () {
                var $siblings, $boxes, $unchecked;
                $siblings  = $( this ).closest( 'td' ).siblings();
                $boxes     = $siblings.find( 'input[type=checkbox]' );
                $unchecked = $boxes.filter( ':not(:checked)' );
                if ( $unchecked.length ) {
                    $unchecked.trigger( 'click' );
                } else {
                    $boxes.trigger( 'click' );
                }
            } );

            // Submit new student, or update selected student
            $saveButtons.on( 'click', function onSubmitStudentClick() {
                var $button,
                    studentId,
                    schedule;

                $button   = $( this );
                studentId = $studentList.val();
                if ( $checkboxes.areModified() ) {
                    schedule = $checkboxes.serializeSchedule();
                }
                if ( !studentId ) {
                    $.ajax( {
                        url   : 'api/enrollStudent',
                        method: 'post',
                        data  : {
                            'familyName' : $familyName.val(),
                            'firstName'  : $firstName.val(),
                            'enrolled'   : $isEnrolled.is( ':checked' ),
                            'classroomId': $classrooms.val(),
                            'schedule'   : schedule,
                            'startDate'  : $startDate.val(),
                            'endDate'    : $endDate.val()
                        },

                        dataType: 'json',
                        success : function onEnrollStudentSuccess( json ) {
                            if ( !json.success ) {
                                alert( 'Unable to enroll new student: ' + json.message );
                            } else {
                                json.student.schedules.forEach( function ( schedule, i, schedules ) {
                                    // ref: http://stackoverflow.com/questions/3075577/convert-mysql-datetime-stamp-into-javascripts-date-format
                                    var t                    = schedule.startDate.date.split( /[- :]/ );
                                    schedules[ i ].startDate = new Date( t[ 0 ], t[ 1 ] - 1, t[ 2 ], t[ 3 ], t[ 4 ], t[ 5 ] );
                                } );

                                Students[ json.student.id ] = json.student;
                                $studentList.addStudent( json.student )
                                    .sort()
                                    .filter( $classFilter.val() )
                                    .val( json.student.id )
                                    .updateStudentDetails( json.student.id );
                                if ( $button.attr( 'name' ) == $saveAndAnother.attr( 'name' ) ) {
                                    $newStudent.trigger( 'click' );
                                } else if ( $button.attr( 'name' ) === $saveAndClose.attr( 'name' ) ) {
                                    $cancelStudent.trigger( 'click' );
                                }
                            }
                        },
                        error   : function onEnrollStudentError( jqXHR, textStatus, errorThrown ) {
                            alert( 'AJAX error enrolling student: ' + textStatus );
                        }
                    } );

                } else {
                    $.ajax( {
                        url     : 'api/updateStudent',
                        method  : 'post',
                        data    : {
                            'id'         : studentId,
                            'familyName' : $familyName.val(),
                            'firstName'  : $firstName.val(),
                            'enrolled'   : $isEnrolled.is( ':checked' ),
                            'classroomId': $classrooms.val(),
                            'schedule'   : schedule,
                            'startDate'  : $startDate.val(),
                            'endDate'    : $endDate.val()
                        },
                        dataType: 'json',
                        success : function updateStudentSuccess( json ) {
                            if ( !json.success ) {
                                alert( 'Unable to update student: ' + json.message );
                            } else {
                                json.student.schedules.forEach( function ( schedule, i, schedules ) {
                                    // ref: http://stackoverflow.com/questions/3075577/convert-mysql-datetime-stamp-into-javascripts-date-format
                                    var t                    = schedule.startDate.date.split( /[- :]/ );
                                    schedules[ i ].startDate = new Date( t[ 0 ], t[ 1 ] - 1, t[ 2 ], t[ 3 ], t[ 4 ], t[ 5 ] );
                                } );
                                Students[ json.student.id ] = json.student;
                                $studentList.updateStudent( json.student )
                                    .val( json.student.id )
                                    .updateStudentDetails( json.student.id );
                                if ( $button.attr( 'name' ) == $saveAndAnother.attr( 'name' ) ) {
                                    $newStudent.trigger( 'click' );
                                } else if ( $button.attr( 'name' ) === $saveAndClose.attr( 'name' ) ) {
                                    $cancelStudent.trigger( 'click' );
                                }
                            }
                        },
                        error   : function updateStudentError( jqXHR, textStatus, errorThrown ) {
                            alert( 'AJAX error updating student: ' + textStatus );
                        }
                    } );
                }
            } );

            // Cancel all modifications made so far
            $cancelStudent.on( 'click', function () {
                $studentList.val( '' );
                $studentList.resetStudentDetails();
            } )
        }


        // Empty all data from the page
        function empty() {
            $studentList.resetStudentDetails();
            $studentList.empty();
        }

        function addStudent( student ) {
            var source;
            var template;
            var html;

            source   = $( '#enrollment-list-option-template' ).html();
            template = Handlebars.compile( source );
            html     = template( student );
            $studentList.append( $( html ) );
        }

        // Removes a student from the table (does not affect the database)
        function removeStudent( studentId ) {
            $table.find( 'tr[data-student-id=' + studentId + ']' ).remove();
        }

        function addClassroom( classroom ) {
            if ( undefined == classroom ) throw 'Classroom definition required.';

            [ $classrooms, $classFilter ].forEach( function ( e, i, arr ) {
                var $opt;
                $opt = $( '<option>' );
                $opt.val( classroom.id ).text( classroom.name );
                $( e ).append( $opt );
            } );
        }

        publicApi = {
            init         : init,
            clear        : empty,
            addStudent   : addStudent,
            removeStudent: removeStudent,
            addClassroom : addClassroom
        };

        return publicApi;
    })();


    /********************************************************************************
     * Application-wide Functions
     ********************************************************************************/

    // Return a new Date object for midnight of the current day
    function getToday() {
        var now = new Date();
        return new Date( now.getFullYear(), now.getMonth(), now.getDate() );
    }

    function formatTime( date ) {
        var hh, mm, ss;
        if ( date == undefined ) {
            return '';
        }
        if ( typeof date === 'number' ) {
            date = new Date( date );
        }
        hh = date.getHours();
        mm = date.getMinutes();
        if ( mm < 10 ) {
            mm = '0' + mm;
        }
        ss = date.getSeconds();
        if ( ss < 10 ) {
            ss = '0' + ss;
        }
        return (hh + ':' + mm + ':' + ss );
    }

    function formatDate( date ) {
        if ( date == undefined ) {
            return '';
        }
        if ( typeof date === 'number' ) {
            date = new Date( date );
        }
        return (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear();
    }

    function attendanceSheetDate( date ) {
        return 'spb';
    }


    /********************************************************************************
     * Document on-ready handler
     ********************************************************************************/
    $( function () {
        // All of the tabs in the top-level menu, <a href="#target">
        var $tabs = $( '.tab' );

        // DOM elements identified by the href attributes in the $tabs
        var targets = $tabs.map( function () {
            return this.hash;   // Return the anchor part of the URL
        } ).get();

        //
        var $panels = $( targets.join( ',' ) );

        function showPage( id ) {
            // If no value was given, let's take the first panel
            if ( !id ) id = targets[ 0 ];
            else id = id.split( '/' )[ 0 ];
            $tabs.removeClass( 'active' ).filter( function () {
                return (this.hash === id);
            } ).addClass( 'active' );
            $panels.hide();
            var $panel = $panels.filter( id );
            $panel.show();
        }

        $( window ).on( 'hashchange', function () {
            showPage( location.hash );
        } );


        Handlebars.registerHelper( 'formatTime', formatTime );
        Handlebars.registerHelper( 'formatDate', formatDate );
        Handlebars.registerHelper( 'attendanceSheetDate', function ( date ) {
            var days   = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ];
            var months = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
            return new Handlebars.SafeString( days[ date.getDay() ] + '<br />' + date.getDate() + '-' + months[ date.getMonth() ] );
        } );


        CheckinPage.init( '#checkin-page' );
        AttendancePage.init( '#attendance-page' );
        SigninPage.init( '#signin-page' );
        ReportsPage.init( '#reports' );
        EnrollmentPage.init( '#enrollment-page' );
        //ClassroomPage.init('#classes-page');

        var wait = 2;

        Classrooms.init();
        Students.init();

        ClassroomPanel.init( '#classes-page' );
        StudentsPanel.init( '#enrollment-page' );

        ClassroomController.load();
        StudentController.load();

        showPage( targets.indexOf( location.hash ) !== -1 ? location.hash : '' );
    } );


})( this, jQuery );
