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
            "order"    : [ [ 2, "asc" ] ],
            "columns"  : [
                { "data": "id" },
                { "data": "label" },
                { "data": "ordering" },
                { "data": "created_at" },
                { "data": "updated_at" }
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
                    var selected = dt.rows( { selected: true } );
                    var msg      = (1 === selected[ 0 ].length) ? 'Are you sure you want to delete this record?' : 'Are you sure you want to delete these ' + selected[ 0 ].length + ' records?';
                    if ( confirm( msg ) ) {
                        var length = selected[ 0 ].length;
                        selected.every( function () {
                            var row  = this;
                            var data = row.data();
                            $.ajax( {
                                "url"   : "api/classrooms/" + data.id,
                                "method": "delete",

                                "success": function ( json ) {
                                    length--;
                                    if ( !length ) {
                                        selected.remove().draw( false );
                                    }
                                },
                                "error"  : function ( xhr ) {
                                    console.log( xhr );
                                    length--;
                                    row.deselect();
                                    selected = dt.rows( { selected: true } );
                                    if ( !length ) {
                                        selected.remove().draw( false );
                                    }
                                }
                            } );
                        } );
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
            table.row.add( data ).draw();
        }

        function reload() {
            table.ajax.reload();
        }

        function redrawRow( newData ) {
            table.rows().every( function ( /* rowIdx, tableLoop, rowLoop */ ) {
                var data = this.data();
                if ( data.id == newData.id ) {
                    var oldData = this.data();
                    for ( var p in newData ) {
                        oldData[ p ] = newData[ p ];
                    }
                    this.data( oldData );
                }
            } );
        }

        function deleteRow( classroom_id ) {
            table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                var data = this.data();
                console.log( rowIdx );
                console.log( tableLoop );
                console.log( rowLoop );

                console.log( data );
                if ( classroom_id == data.id ) {
                    this.remove();
                }
            } );
        }

        return {
            "insert"   : insert,
            "reload"   : reload,
            "redrawRow": redrawRow,
            "deleteRow": deleteRow
        };
    })( '#classrooms-tab' );

    var ClassroomPropsDlg = (function ( selector ) {
        var $self,
            $form,
            $classroomId,
            $label,
            $order,
            $required,
            dialog;

        $self        = $( selector );
        $form        = $self.find( 'form' );
        $classroomId = $form.find( '[name=id]' );
        $label       = $form.find( '[name=label]' );
        $order       = $form.find( '[name=ordering]' );


        $required = $form.find( '.required' );
        dialog    = $self.dialog( {
            "autoOpen": false,
            "modal"   : true,
            "width"   : "300px",
            "buttons" : {
                "Submit": onSubmit,
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

        function clear() {
            $form[ 0 ].reset();
            $required.removeClass( 'missing' );
        }

        function onSubmit() {
            if ( validate() ) {
                submit();
                close();
            }
        }

        function populate( classroom ) {
            $classroomId.val( classroom.id );
            $label.val( classroom.label );
            $order.val( classroom.ordering );
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
            var id       = $classroomId.val();
            var label    = $label.val();
            var ordering = $order.val();
            if ( ordering === '' ) {
                ordering = null;
            }
            var data = {
                "label"   : label,
                "ordering": ordering
            };
            if ( !id ) {
                insert( data );
            } else {
                update( id, data );
            }
            ClassroomPropsDlg.close();
        }

        function insert( data ) {
            $.ajax( {
                "url"   : "api/classrooms",
                "method": "post",
                "data"  : data,

                "dataType": "json",
                "success" : function ( json ) {
                    $.ajax( {
                        'url'   : "api/classrooms/" + json,
                        "method": "get",

                        "success": function ( json ) {
                            console.log( json );
                            if ( !ordering ) {
                                ClassroomsTab.insert( json );
                            } else {
                                ClassroomsTab.reload( json );
                            }
                        },
                        "error"  : function ( xhr ) {
                            console.log( xhr );
                        }
                    } );
                },
                "error"   : function ( xhr ) {
                    console.log( xhr );
                }
            } );

        }

        function update( id, data ) {
            $.ajax( {
                "url"   : "api/classrooms/" + id,
                "method": "put",
                "data"  : data,

                "dataType": "json",
                "success" : function ( json ) {
                    ClassroomsTab.redrawRow( json );
                },
                "error"   : function ( xhr ) {
                    console.log( xhr );
                }
            } );
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
