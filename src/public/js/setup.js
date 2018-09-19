;(function ( global, $ ) {
    'use strict';

    var ClassroomsTab = (function ( selector ) {
        var $self,
            table;

        $self = $( selector );
        table = $self.find( 'table' ).DataTable( {
            "ajax"     : {
                "url"    : "api/classrooms",
                "dataSrc": ""
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
                    ClassroomPropsDlg.open();
                }
            }, {
                "extend": "selected",
                "text"  : "Edit",
                "action": function ( e, dt, button, config ) {
                    var selected = dt.rows( { selected: true } ).indexes();
                    if ( 1 < selected.length ) {
                        alert( "Can edit only 1 record at a time" );
                    } else {
                        ClassroomPropsDlg.open( dt.rows( selected[ 0 ] ).data()[ 0 ] );
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
                                "url"   : "api/classrooms/" + dt.rows( selected[ i ] ).data()[ 0 ][ 'id' ],
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


        function insert( data ) {
            alert( "insert" );
        }

        function redrawRow( newData ) {
            table.rows().every( function ( /* rowIdx, tableLoop, rowLoop */ ) {
                var data = this.data();
                if ( data.id == newData.id ) {
                    this.data( newData );
                }
            } );
        }

        return {
            "insert"   : insert,
            "redrawRow": redrawRow
        };
    })( '#classrooms-tab' );

    var ClassroomPropsDlg = (function ( selector ) {
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
                    var id    = $self.find( '[name=id]' ).val();
                    var label = $self.find( '[name=label]' ).val();
                    var data  = {
                        "label": label
                    };
                    if ( !id ) {
                        $.ajax( {
                            "url"   : "api/classrooms",
                            "method": "post",
                            "data"  : data,

                            "dataType": "json",
                            "success" : function ( json ) {
                                console.log( json );
                                ClassroomsTab.insert( data );
                            },
                            "error"   : function ( xhr ) {
                                console.log( xhr );
                            }
                        } );
                    } else {
                        $.ajax( {
                            "url"   : "api/classrooms/" + id,
                            "method": "put",
                            "data"  : {
                                "label": label
                            },

                            "dataType": "json",
                            "success" : function ( json ) {
                                console.log( json );
                                data.id = id;
                                ClassroomsTab.redrawRow( data );
                            },
                            "error"   : function ( xhr ) {
                                console.log( xhr );
                            }
                        } );
                    }
                    ClassroomPropsDlg.close();
                },
                "Cancel": function () {
                    ClassroomPropsDlg.close();
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
    })( '#classroom-props-dlg' );

    $( function () {
        $( '#tabs' ).tabs();
    } );

})( this, jQuery );
