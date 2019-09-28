;(function ( global, $ ) {
    'use strict';

    $( function () {
        console.log( 'Document loaded.' );
        $( 'table.attendance-table' ).DataTable( {
            'order'     : [ [ 0, 'asc' ] ],
            'info'      : false,
            'paging'    : false,
            'search'    : false,
            'columnDefs': [
                { targets: [ 0 ] },
                { targets: '_all', sortable: false }
            ]
        } );

        let $weekOf = $( 'input[name=week-of]' );
        $weekOf.datepicker();
        $( 'td' ).each( function ( idx, td ) {
            if ( !$( this ).text().trim() ) {
                $( this ).addClass( 'dark' );
            }
        } );

        $( '#tabs' ).tabs().show();
    } );
})( this, jQuery );
