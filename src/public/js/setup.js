;(function ( global, $ ) {

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
            ],
        } );

        var b0 = new $.fn.dataTable.Buttons( table, {
            buttons: [ {
                "text"  : "New",
                "action": function () {
                    dialog.dialog( 'open' )
                }
            }, {
                "extend": "selected",
                "text"  : "Edit",
                "action": function () {
                    dialog.dialog( 'open' )
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

        var $classroomPropsDlg = $( '#classroom-props-dlg' );
        var $form              = $classroomPropsDlg.find( 'form' );

        var dialog = $classroomPropsDlg.dialog( {
            "autoOpen": false,
            "modal"   : true,
            "buttons" : {
                "Submit": function () {
                    dialog.dialog( 'close' );

                },
                "Cancel": function () {
                    dialog.dialog( 'close' );

                }
            }
        } )

    } );

})( this, jQuery );
