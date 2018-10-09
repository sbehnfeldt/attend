;(function ( global, $ ) {
    'use strict';

    var loading  = 0;
    var $loading = $( '#loading' );

    global.Attend = global.Attend || {};

    Attend.loadAnother = function () {
        loading++;
        $loading.show();
    };

    Attend.doneLoading = function () {
        loading--;
        if ( 0 === loading ) {
            $loading.hide();
        }
    };

})(this, jQuery);

