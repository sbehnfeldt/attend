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


    //
    var EnrollmentTab = (function ( selector ) {
        var $self,
            table;

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


        $self = $( selector );
        table = $self.find( 'table' ).DataTable( {
            "ajax"        : {
                "url"    : "api/students",
                "dataSrc": ""
            },
            "select"      : true,
            "columns"     : [
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
                    var msg      = (1 === selected.length) ? 'Are you sure you want to delete this record?' : 'Are you sure you want to delete these ' + selected.length + ' records?';
                    if ( confirm( msg ) ) {
                        for ( var i = 0; i < selected.length; i++ ) {
                            console.log( dt.rows( selected[ i ] ).data()[ 0 ] );
                            $.ajax( {
                                "url"   : "api/students/" + dt.rows( selected[ i ] ).data()[ 0 ][ 'id' ],
                                "method": "delete",

                                "success": function ( json ) {
                                    alert( "Deleted" );
                                },
                                "error"  : function () {
                                    alert( "Error" );
                                }
                            } );
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
                    dt.ajax.reload();
                }
            } ]
        } );
        b1.dom.container.eq( 0 ).appendTo( $self.find( '.table-buttons span' ) );

        return {
            "isEmpty"  : isEmpty,
            "drawTable": drawTable,
            "redrawRow": redrawRow
        };
    })( '#enrollment-tab' );


    var StudentPropsDlg = (function ( selector ) {
        var $self,
            $form,
            dialog;

        $self  = $( selector );
        $form  = $self.find( 'form' );
        dialog = $self.dialog( {
            "autoOpen": false,
            "modal"   : true,
            "width"   : "450px",
            "buttons" : {
                "Submit": function () {
                    var id   = $self.find( '[name=id]' ).val();
                    var data = {
                        "family_name": $self.find( '[name=family_name]' ).val(),
                        "first_name" : $self.find( '[name=first_name]' ).val(),
                        "enrolled"   : ('on' === $self.find( '[name=enrolled]' ).val()) ? 1 : 0,
                    };
                    if ( $self.find( '[name=classroom_id]' ).val() ) {
                        data.classroom_id = $self.find( '[name=classroom_id]' ).val();
                    }
                    if ( !id ) {
                        $.ajax( {
                            "url"   : "api/students",
                            "method": "post",
                            "data"  : data,

                            "dataType": "json",
                            "success" : function ( json ) {
                                console.log( json );
                            },
                            "error"   : function ( xhr ) {
                                console.log( xhr );
                                alert( "Error" );
                            }
                        } );
                    } else {
                        $.ajax( {
                            "url"   : "api/students/" + id,
                            "method": "put",
                            "data"  : data,

                            "success": function ( body, status, xhr ) {
                                console.log( body );
                                console.log( status );
                                console.log( xhr );
                                data.id = id;
                                EnrollmentTab.redrawRow( data );
                            },
                            "error"  : function ( xhr, estring, e ) {
                                console.log( xhr );
                                console.log( estring );
                                console.log( e );
                                alert( "Error" );
                            }
                        } );
                    }
                    StudentPropsDlg.close();
                },
                "Cancel": function () {
                    StudentPropsDlg.close();
                }
            }
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

        function populate( student ) {
            console.log( student );
            $form.find( '[name=id]' ).val( student.id );
            $form.find( '[name=family_name]' ).val( student.family_name );
            $form.find( '[name=first_name]' ).val( student.first_name );
            $form.find( '[name=classroom_id]' ).val( student.classroom_id );
            $form.find( '[name=enrolled]' ).prop( 'checked', (1 == student.enrolled) );
        }

        function clear() {
            $form[ 0 ].reset();
        }

        return {
            'open' : open,
            'close': close
        };
    })( '#student-props-dlg' );

    $( function () {
        $( '#tabs' ).tabs();

        $.ajax( {
            'url'   : 'api/classrooms',
            'method': 'get',

            'dataType': 'json',
            'success' : function ( json ) {
                Classrooms.load( json );
                checkClassrooms();
            },
            'error'   : function ( xhr ) {
                console.log( xhr );
            }
        } );
    } );

})( this, jQuery );
