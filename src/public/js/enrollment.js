;(function ( global, $ ) {
    'use strict';


    // "Classroom" column on the Enrollment tab cannot be filled in until both the Students and Classrooms
    // data has been retrieved from the server.  This function checks to see whether this is true; if so,
    // draw the Enrollment table; it will be drawn with the names (rather than the IDs) of the classrooms
    // filled in
    function checkClassrooms() {
        if ( 0 === Classrooms.records.length ) {
            return;
        }
        if ( EnrollmentTab.isEmpty() ) {
            return;
        }
        EnrollmentTab.drawTable();
    }


    // The classrooms records from the database
    var Classrooms = (function () {
        var records = [];

        function load( classrooms ) {
            for ( var i = 0; i < classrooms.length; i++ ) {
                var c          = classrooms[ i ];
                var idx        = c.id;
                records[ idx ] = c;
            }
        }

        return {
            'records': records,
            'load'   : load
        };
    })();


    // The schedules for each student
    var Schedules = (function () {
        var records = [];

        function load( schedules ) {
            for ( var i = 0; i < schedules.length; i++ ) {
                var s = schedules[ i ];
                if ( undefined === records[ s.student_id ] ) {
                    records[ s.student_id ] = [];
                }
                records[ s.student_id ].push( s );
            }

            for ( var p in records ) {
                records[ p ].sort( function ( a, b ) {
                    if ( a.start_date < b.start_date ) return 1;
                    if ( a.start_date > b.start_date ) return -1;
                    return 0;
                } );
            }
        }

        function insert( s ) {
            if ( undefined === records[ s.student_id ] ) {
                records[ s.student_id ] = [];
            }
            records[ s.student_id ].push( s );
            records[ s.student_id ].sort( function ( a, b ) {
                if ( a.start_date < b.start_date ) return 1;
                if ( a.start_date > b.start_date ) return -1;
                return 0;
            } );
        }

        return {
            'records': records,
            'load'   : load,
            'insert' : insert
        };
    })();


    //
    var EnrollmentTab = (function ( selector ) {
        var $self,
            table;

        $self = $( selector );
        table = $self.find( 'table.enrollment-table' ).DataTable( {
            "ajax"   : function () {
                Attend.loadAnother();
                $.ajax( {
                    'url'   : 'api/students',
                    'method': 'get',

                    'success': function ( json ) {
                        console.log( json );
                        for ( var i = 0; i < json.length; i++ ) {
                            table.row.add( json[ i ] );
                        }
                        table.draw();
                        Attend.doneLoading();
                    },
                    'error'  : function ( xhr ) {
                        console.log( xhr );
                        Attend.doneLoading();
                    }
                } );
            },
            "select" : true,
            "columns": [
                { "data": "id" },
                { "data": "family_name" },
                { "data": "first_name" }, {
                    "data"  : "enrolled",
                    "render": function ( data ) {
                        return '<input type=checkbox ' + (1 == data ? 'checked ' : '') + ' disabled />';
                    }
                }, {
                    "data"  : "classroom_id",
                    "render": function ( data ) {
                        if ( data ) {
                            if ( Classrooms.records[ data ] ) {
                                return Classrooms.records[ data ].label;
                            }
                            return data;
                        }
                        return '';
                    }
                }
            ],
            "initComplete": checkClassrooms
        } );

        var b0 = new $.fn.dataTable.Buttons( table, {
            buttons: [ {
                "text"  : "New",
                "action": function () {
                    StudentPropsDlg.open();
                }
            }, {
                "extend": "selected",
                "text"  : "Edit",
                "action": function ( e, dt, button, config ) {
                    var selected = dt.rows( { selected: true } ).indexes();
                    if ( 1 < selected.length ) {
                        alert( "Can edit only 1 record at a time" );
                    } else {
                        StudentPropsDlg.open( dt.rows( selected[ 0 ] ).data()[ 0 ] );
                    }
                }
            }, {
                "extend": "selected",
                "text"  : "Delete",
                "action": function ( e, dt ) {
                    var selected = dt.rows( { selected: true } ).indexes();
                    var msg      = (1 === selected.length) ? 'Are you sure you want to delete this student record?' : 'Are you sure you want to delete these ' + selected.length + ' student records?';
                    if ( confirm( msg ) ) {
                        for ( var i = 0; i < selected.length; i++ ) {
                            (function ( index ) {
                                Attend.loadAnother();
                                $.ajax( {
                                    "url"   : "api/students/" + dt.rows( selected[ index ] ).data()[ 0 ][ 'id' ],
                                    "method": "delete",

                                    "success": function ( json ) {
                                        dt.rows( selected[ index ] ).remove();
                                        dt.draw();
                                        Attend.doneLoading();
                                    },
                                    "error"  : function () {
                                        alert( "Error" );
                                        Attend.doneLoading();
                                    }
                                } );

                            })( i );
                        }
                    }
                }
            } ]
        } );
        b0.dom.container.eq( 0 ).appendTo( $self.find( '.record-buttons' ) );

        var b1 = new $.fn.dataTable.Buttons( table, {
            "buttons": [ {
                "text"  : "Reload",
                "action": function ( e, dt ) {
                    table.clear();
                    Attend.loadAnother();
                    dt.ajax.reload( Attend.doneLoading );
                }
            } ]
        } );
        b1.dom.container.eq( 0 ).appendTo( $self.find( '.table-buttons span' ) );

        // Check whether there are any rows in the Enrollment table
        function isEmpty() {
            return ( 0 === table.rows().count() );
        }

        // Redraw the Enrollment table
        // This is used to convert classroom IDs in table to classroom names.
        function drawTable() {
            table.rows().every( function ( /* rowIdx, tableLoop, rowLoop */ ) {
                this.data( this.data() );   // Forces row to redraw
            } );
        }

        // Redraw a specific row in the Enrollment table
        function redrawRow( newData ) {
            table.rows().every( function ( /* rowIdx, tableLoop, rowLoop */ ) {
                var data = this.data();
                if ( data.id == newData.id ) {
                    this.data( newData );
                }
            } );
        }

        function insert( data ) {
            table.row.add( data ).draw();
        }


        return {
            "isEmpty"  : isEmpty,
            "drawTable": drawTable,
            "redrawRow": redrawRow,
            "insert"   : insert
        };
    })( '#enrollment-tab' );


    var StudentPropsDlg = (function ( selector ) {
        var $self,
            $form,
            $required,
            $studentId,
            $familyName,
            $firstName,
            $classrooms,
            $enrolled,
            $buttons,
            $boxes,
            $list,
            dialog;

        $self       = $( selector );
        $form       = $self.find( 'form' );
        $studentId  = $form.find( '[name=id]' );
        $familyName = $self.find( '[name=family_name]' );
        $firstName  = $self.find( '[name=first_name]' );
        $classrooms = $self.find( '[name=classroomsList]' );
        $enrolled   = $self.find( '[name=enrolled]' );
        $list       = $form.find( '[name=schedulesList]' );

        $boxes    = $form.find( 'table.schedule-table input[type=checkbox]' );
        $buttons  = $form.find( 'table.schedule-table button' );
        $required = $form.find( '.required' );

        dialog = $self.dialog( {
            "autoOpen": false,
            "modal"   : true,
            "width"   : "450px",
            "buttons" : {
                "Submit": function () {
                    if ( validate() ) {
                        submit();
                        close();
                    }
                },
                "Cancel": function () {
                    StudentPropsDlg.close();
                }
            }
        } );

        $buttons.on( 'click', function () {
            return false;
        } );

        $boxes.on( 'change', function () {
//            console.log( this );
//            console.log( $( this ) );
//            console.log( $( this ).val() );
        } );

        $list.on( 'change', function () {
            var id    = $studentId.val();
            var idx   = $( this )[ 0 ].selectedIndex;
            var sched = Schedules.records[ id ][ idx ].schedule;
            $boxes.each( function ( idx, elem ) {
                if ( $( elem ).val() & sched ) {
                    $( elem ).prop( 'checked', true );
                } else {
                    $( elem ).prop( 'checked', false );
                }
            } );
        } );


        function open( student ) {
            clear();
            if ( student ) {
                populate( student );
            }
            dialog.dialog( 'open' );
        }

        function close() {
            dialog.dialog( 'close' );
        }

        function clear() {
            $form[ 0 ].reset();
            $required.removeClass( 'missing' );
            $list.empty();
            $list.addClass( 'hidden' );
        }

        function populate( student ) {
            var $opt;

            $studentId.val( student.id );
            $familyName.val( student.family_name );
            $firstName.val( student.first_name );
            $classrooms.val( student.classroom_id );
            $enrolled.prop( 'checked', (1 == student.enrolled) );

            $list.removeClass( 'hidden' );
            for ( var i = 0; i < Schedules.records[ student.id ].length; i++ ) {
                var s = Schedules.records[ student.id ][ i ];
                $opt  = $( '<option>' ).text( s.start_date ).val( s.id );
                $list.append( $opt );
            }
            $list.trigger( 'change' );
        }

        function validate() {
            var valid = true;
            $required.each( function ( i, e ) {
                if ( !$( e ).val() ) {
                    $( e ).addClass( 'missing' );
                    valid = false;
                } else {
                    $( e ).removeClass( 'missing' );
                }
            } );
            return valid;
        }

        function submit() {
            var id   = $self.find( '[name=id]' ).val();
            var data = {
                "family_name" : $familyName.val(),
                "first_name"  : $firstName.val(),
                "enrolled"    : ('on' === $enrolled.val()) ? 1 : 0,
                "classroom_id": JSON.stringify( {
                    'data': ($classrooms.val() ? $classrooms.val() : null)
                } )
            };

            if ( !id ) {
                insert( data );
            } else {
                update( id, data );
            }
            StudentPropsDlg.close();
        }

        function insert( data ) {
            var sched = 0;
            $boxes.each( function ( i, e ) {
                if ( $( e ).prop( 'checked' ) ) {
                    sched += parseInt( $( e ).val(), 16 );
                }
            } );
            Attend.loadAnother();
            $.ajax( {
                "url"   : "api/students",
                "method": "post",
                "data"  : data,

                "dataType": "json",
                "success" : function ( json ) {
                    $.ajax( {
                        'url'    : 'api/students/' + json,
                        'method' : 'get',
                        'success': function ( json ) {
                            console.log( json );
                            EnrollmentTab.insert( json );
                            Attend.doneLoading();
                        },
                        'error'  : function ( xhr ) {
                            console.log( xhr );
                            Attend.doneLoading();
                        }
                    } );

                    Attend.doneLoading();
                    $.ajax( {
                        "url"   : "api/schedules",
                        "method": "post",
                        "data"  : {
                            'student_id': json,
                            'schedule'  : sched
                        },

                        "dataType": "json",
                        "success" : function ( json ) {
                            $.ajax( {
                                'url'    : 'api/schedules/' + json,
                                'method' : 'get',
                                'success': function ( json ) {
                                    console.log( json );
                                    Schedules.insert( json );
                                    Attend.doneLoading();
                                },
                                'error'  : function ( xhr ) {
                                    console.log( xhr );
                                    Attend.doneLoading();
                                }
                            } )
                        },
                        "error"   : function ( xhr ) {
                            console.log( xhr );
                            alert( "Error" );
                        }
                    } )
                },
                "error"   : function ( xhr ) {
                    console.log( xhr );
                    alert( "Error" );
                }
            } );
        }

        function update( id, data ) {
            Attend.loadAnother();
            $.ajax( {
                "url"   : "api/students/" + id,
                "method": "put",
                "data"  : data,

                "success": function () {
                    data.id           = id;
                    data.classroom_id = JSON.parse( data.classroom_id ).data;
                    EnrollmentTab.redrawRow( data );
                    Attend.doneLoading();
                },
                "error"  : function ( xhr, estring, e ) {
                    console.log( xhr );
                    console.log( estring );
                    console.log( e );
                    Attend.doneLoading();
                    alert( "Error" );
                }
            } );
        }


        return {
            'open' : open,
            'close': close
        };
    })( '#student-props-dlg' );


    $( function () {
        $( '#tabs' ).tabs();

        Attend.loadAnother();
        $.ajax( {
            'url'   : 'api/classrooms',
            'method': 'get',

            'dataType': 'json',
            'success' : function ( json ) {
                Classrooms.load( json );
                checkClassrooms();
                Attend.doneLoading();
            },
            'error'   : function ( xhr ) {
                console.log( xhr );
                Attend.doneLoading();
            }
        } );

        Attend.loadAnother();
        $.ajax( {
            'url'   : 'api/schedules',
            'method': 'get',

            'dataType': 'json',
            'success' : function ( json ) {
                console.log( json );
                Schedules.load( json );
                Attend.doneLoading();
            },
            'error'   : function ( xhr ) {
                console.log( "ERROR loading schedules" );
                console.log( xhr );
                Attend.doneLoading();
            }
        } )
    } );

})( this, jQuery );
