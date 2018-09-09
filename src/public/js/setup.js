;(function ( global, $ ) {

    var ClassroomsTab = (function ( selector ) {
        var $self,
            table;

        $self = $( selector );
        table = $self.find( 'table' ).DataTable( {
            "ajax"     : {
                "url"    : "api/classrooms",
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
                "action": function () {
                    alert( "Delete Classroom" );
                }
            } ]
        } );
        b0.dom.container.eq( 0 ).appendTo( $self.find( '.run-buttons' ) );

        return {};
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
                    ClassroomPropsDlg.close();
                },
                "Cancel": function () {
                    ClassroomPropsDlg.close();
                }
            }
        } );

        function open( classroom ) {
            clear();
            console.log( $form.find( '[name=id]' ).val() );
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
