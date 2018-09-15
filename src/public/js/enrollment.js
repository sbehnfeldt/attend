;(function ( global, $ ) {

    function checkClassrooms() {
        if ( 0 === Classrooms.records.length ) {
            return;
        }
        if ( EnrollmentTab.isEmpty() ) {
            return;
        }
        EnrollmentTab.drawTable();
    }


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


    var EnrollmentTab = (function ( selector ) {
        var $self,
            table;

        function isEmpty() {
            return ( 0 === table.rows().count() );
        }

        // Convert classroom IDs in table to classroom names
        function draw() {
            table.rows().every( function ( /* rowIdx, tableLoop, rowLoop */ ) {
                this.data( this.data() );   // Forces row to redraw
            } );
        }

        $self = $( selector );
        table = $self.find( 'table' ).DataTable( {
            "ajax"        : {
                "url"    : "api/students",
                "dataSrc": "data"
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
            "drawTable": draw
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
            "buttons" : {
                "Submit": function () {
                    id    = $self.find( '[name=id]' ).val();
                    label = $self.find( '[name=label]' ).val();
                    if ( !id ) {
                        $.ajax( {
                            "url"   : "api/students",
                            "method": "post",
                            "data"  : {
                                "label": label
                            },

                            "dataType": "json",
                            "success" : function ( json ) {
                                console.log( json );
                                alert( "Success" );
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
                            "data"  : {
                                "label": label
                            },

                            "dataType": "json",
                            "success" : function ( json ) {
                                console.log( json );
                                alert( "Success" );
                            },
                            "error"   : function ( xhr ) {
                                console.log( xhr );
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
                Classrooms.load( json.data );
                checkClassrooms();
            },
            'error'   : function ( xhr ) {
                console.log( xhr );
            }
        } );
    } );

})( this, jQuery );
