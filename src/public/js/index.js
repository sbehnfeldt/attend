;(function ( global, $ ) {
    'use strict';


    $( function () {
        console.log( "Index page ready" );
        $( '#tabs' ).tabs();

        $( '.attendance-table' ).DataTable( {
            'searching': false,
            'paging'   : false,
            'ordering' : false
        } );
    } );


})( this, jQuery );
