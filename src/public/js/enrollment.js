;(function ( global, $ ) {

    var EnrollmentTab = (function ( selector ) {
        var $self,
            table;

        $self = $( selector );
        table = $self.find( 'table' ).DataTable( {
            "ajax"     : {
                "url"    : "api/students",
                "dataSrc": "data"
            },
            "paging"   : false,
            "searching": false,
            "select"   : true,
            "columns"  : [
                { "data": "id" },
                { "data": "label" }
            ]
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

        return {};
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

        function open( classroom ) {
            clear();
            if ( classroom ) {
                populate( classroom );
            }
            dialog.dialog( 'open' );
        }

        function close() {
            dialog.dialog( 'close' );
        }

        function populate( classroom ) {
            $form.find( '[name=id]' ).val( classroom.id );
            $form.find( '[name=label]' ).val( classroom.label );
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
    } );

})( this, jQuery );
