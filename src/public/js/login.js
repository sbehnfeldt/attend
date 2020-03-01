;(function (global, $) {
    'use strict';

    // Document ready handler
    $(function () {
        console.log('Document ready');
        let $form = $('form');
        let $username = $('input[name=username]');
        let $password = $('input[name=password]');
        let $invalid = $('div.invalid-credentials');
        let $submit = $('button');

        $form.on('submit', function () {
            console.log('submit');
            $invalid.hide();
            $.ajax({
                url: 'login.php',
                method: 'post',
                data: $('form').serialize(),

                success: function (response) {
                    console.log(response);
                    if (response['invalid']) {
                        if (!response['username']) {
                            $username.addClass('invalid').attr('placeholder', 'Enter your username');
                        } else {
                            $username.removeClass('invalid');
                        }
                        if (!response['password']) {
                            if (!response['password']) {
                                $password.addClass('invalid').attr('placeholder', 'Enter your password');
                            } else {
                                $password.removeClass('invalid');
                            }
                        }
                    } else if (response['unauthorized']) {
                        $username.removeClass('invalid');
                        $password.removeClass('invalid');
                        $invalid.show();
                    } else {
                        window.location.href = response['Location'];
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
            return false;
        });

    });
})(this, jQuery);
