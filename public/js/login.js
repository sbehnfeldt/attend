;(function (global, $) {
    'use strict';

    // Document ready handler
    $(function () {
        console.log('Document ready');
        let $form = $('form');

        let $username = $('input[name=username]').first();
        let $password = $('input[name=password]').first();
        let $formError  = $('div#form-error');


        // Ensure the username and password fields are non-empty.
        // "validate" can have many meanings; for the sake of this function,
        // it means ONLY non-empty.
        function validateCredentials(username, password) {
            let valid = true;
            if ('' === $username.val().trim()) {
                $username.addClass('invalid');
                valid = false;
            }
            if ('' === $password.val().trim()) {
                $password.addClass('invalid');
                valid = false;
            }
            return valid;
        }


        $form.on('submit', function (event) {
            $formError.hide();

            if (!validateCredentials()) {
                $formError.text('Please fill in required fields.').show();
                event.preventDefault();
                return;
            }

            Attend.loadAnother();
            $('.invalid').removeClass('invalid');
            $.ajax({
                url: 'login',
                method: 'post',
                data: $('form').serialize(),

                success: function (response) {
                    console.log(response);

                    // Username and/or password were not provided. (This should never happen,
                    // as the username and password should be validated on the front end,
                    // before the form is ever submitted.
                    if (response['invalid']) {
                        if (!response['username']) {
                            $username.addClass('invalid');
                        } else {
                            $username.removeClass('invalid');
                            $('#username-error').text('This field is required').show();
                        }
                        if (!response['password']) {
                            if (!response['password']) {
                                $password.addClass('invalid').attr('placeholder', 'Password is required');
                            } else {
                                $password.removeClass('invalid');
                            }
                        }
                        Attend.doneLoading();

                    } else if (response['unauthorized']) {
                        $formError.text('Invalid credentials. Please check your username and password and try again.').show();
                        Attend.doneLoading();

                    } else {
                        window.location.href = response['Location'];
                    }
                },

                error: function (xhr) {
                    Attend.doneLoading();
                    console.log(xhr);
                }
            });
            return false;
        });

    });
})(this, jQuery);
