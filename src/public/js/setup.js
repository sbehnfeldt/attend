;(function ( global, $ ) {

    var ClassroomPropsDlg = {
        "init": function ( selector ) {
            this.$dialog = $( selector );
            this.$form   = this.$dialog.find( 'form' );
            this.dialog  = this.$dialog.dialog( {
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
        },

        "open": function () {
            this.dialog.dialog( 'open' );
        },

        "close": function () {
            this.dialog.dialog( 'close' );
        }
    };


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

        ClassroomPropsDlg.init( '#classroom-props-dlg' );
    } );

})( this, jQuery );
