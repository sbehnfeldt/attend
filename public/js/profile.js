;(function (global, $) {
    'use strict';

    let EmailForm = (function () {
        let $form, $email, $button;

        function init(selector) {
            $form = $(selector);
            $email = $form.find('input[name=email]');
            $button = $form.find('button[name=updateEmail]');

            $form.on('submit', function () {
                return false;
            });
            $button.on('click', update);
        }

        function update() {
            let data = {};
            if ($email.val() !== $email.data()['value']) {
                data['email'] = $email.val();
            }

            Attend.loadAnother();
            try {
                $.ajax({
                    url: 'profile/email',
                    method: 'post',
                    data: data,

                    success: function (json) {
                        console.log(json);
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
        }

        return {
            init: init
        }

    })();


    let PasswordForm = (function () {
        let $form;
        let $pwOld;
        let $pwNew;
        let $pwRepeat;
        let $button;

        function init(selector) {
            $form = $(selector);
            $pwOld = $form.find('input[name=oldpassword]');
            $pwNew = $form.find('input[name=newpassword]');
            $pwRepeat = $form.find('input[name=repeatpassword]');
            $button = $form.find('button');

            $form.on('submit', function () {
                return false;
            });

            $button.on('click', function () {
                if (validate($pwOld, $pwNew, $pwRepeat)) {
                    submit($pwOld, $pwNew);
                }
            });
        }

        function validate($oldPw, $newPw, $repeatPw) {
            let valid = true;

            [$oldPw, $newPw, $repeatPw].forEach(function (e, i, a) {
                e.removeClass('invalid');
            });
            if (!$oldPw.val()) {
                $oldPw.addClass('invalid');
                valid = false;
            }


            if (!$newPw.val()) {
                $newPw.addClass('invalid').attr('placeholder', 'Enter new password');
                valid = false;
            }
            if (!$repeatPw.val()) {
                $repeatPw.addClass('invalid').attr('placeholder', 'Repeat new password');
                valid = false;
            }

            if ($newPw.val() && $repeatPw.val()) {
                if ($newPw.val() !== $repeatPw.val()) {
                    $newPw.val('').addClass('invalid').attr('placeholder', 'Passwords do not match');
                    $repeatPw.val('').addClass('invalid').attr('placeholder', 'Passwords do not match');
                    valid = false;
                }
            }
            return valid;
        }

        function submit($oldPw, $newPw) {

            Attend.loadAnother();
            try {
                $.ajax({
                    url: 'profile/password',
                    method: 'post',
                    data: {
                        'pwOld': $oldPw.val(),
                        'pwNew': $newPw.val()
                    },

                    success: function (json) {
                        console.log(json);
                        if (json.msg) {
                            alert(json.msg);
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
        }

        return {
            init: init
        };
    })();


    // Document ready handler
    $(function () {
        console.log('Document ready');
        EmailForm.init('#updateEmail');
        PasswordForm.init('#changePassword');
    });

})(this, jQuery);
