;(function (global, $) {
    'use strict';

    // Document ready handler
    let $email = $('input[name=email]');
    let $button = $('button');

    $(function () {
        console.log('Document ready');

        $button.on('click', function () {
            let data = {};
            console.log($email.data());
            if ($email.val() !== $email.data()['value']) {
                data['email'] = $email.val();
            }
            Attend.loadAnother();
            try {
                $.ajax({
                    url: 'profile',
                    method: 'post',
                    data: data,

                    success: function (json) {
                        console.log(json);
                        // alert( "Success");
                        if (json.email) {
                            $email.data()['value'] = json.email;
                        }
                        Attend.doneLoading();
                    },
                    error: function (xhr) {
                        console.log(xhr);
                        alert("Error");
                        Attend.doneLoading();
                    }
                });
            } catch (e) {
                alert("Exception");
                console.log(e);
                Attend.doneLoading();
            }
        });
    });
})(this, jQuery);
