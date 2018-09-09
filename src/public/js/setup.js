;(function ( global, $ ) {

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

        function open() {
            dialog.dialog( 'open' );
        }

        function close() {
            dialog.dialog( 'close' );
        }

        return {
            'open' : open,
            'close': close
        };
    })( '#classroom-props-dlg' );


    $( function () {
        $( '#tabs' ).tabs();

        var $tab  = $( '#classrooms' );
        var table = $tab.find( 'table' ).DataTable( {
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
                "action": function () {
                    ClassroomPropsDlg.open();
                }
            }, {
                "extend": "selected",
                "text"  : "Delete",
                "action": function () {
                    alert( "Delete Classroom" );
                }
            } ]
        } );
        b0.dom.container.eq( 0 ).appendTo( $tab.find( '.run-buttons' ) );

    } );

})( this, jQuery );
