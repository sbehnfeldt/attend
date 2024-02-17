;(function (global, $) {
    'use strict';


    /*================================================================================
     * Tab for display information about user accounts for the application
     ================================================================================*/
    let AccountsTab = (function (selector) {
        let $tab = $(selector);
        let $acctsTable = $('#accounts-table');

        let table = $acctsTable.DataTable({
            dom: 'rtB',
            select: {
                style: 'single'
            },
            buttons: [{
                text: 'New',
                action: function (e, dt, node, config) {
                    AcctDlg.clear().open();
                }
            }, {
                text: 'Update',
                extend: 'selected',
                action: function (e, dt, node, config) {
                    console.log(dt);
                    var selected = dt.rows({selected: true}).indexes();
                    if (1 < selected.length) {
                        alert("Can edit only 1 record at a time");
                    } else {
                        AcctDlg.clear().populate($(dt.rows(selected[0]).nodes()[0]).data('account')).open();
                    }
                }
            }, {
                text: 'Delete',
                action: function (e, dt, node, config) {
                    alert("Delete");
                }
            }]

        });
        return {};
    })('#accounts-tab');


    let AcctDlg = (function () {
        let $dlg;
        let $form,
            $id,
            $username,
            $password,
            $email,
            $role;

        function init(selector) {
            $dlg = $(selector);
            $form = $dlg.find('form');
            $id = $dlg.find('input[name=id]');
            $username = $form.find('input[name=username]');
            $password = $form.find('input[name=password]');
            $email = $form.find('input[name=email]');
            $role = $form.find('select[name=role]');

            $dlg.dialog({
                modal: true,
                autoOpen: false,
                buttons: {
                    Submit: function () {
                        console.log($dlg.find('form').serialize());
                        console.log($dlg.find('form').serializeArray());
                        if ($id.val()) {
                            $.ajax({
                                url: 'api/accounts/' + $id.val(),
                                method: 'put',
                                data: $dlg.find('form').serialize(),

                                success: function (success) {
                                    console.log('success');
                                    console.log(success);
                                },
                                error: function (xhr) {
                                    console.log('error');
                                    console.log(xhr);
                                }
                            });

                        } else {
                            $.ajax({
                                url: 'api/accounts',
                                method: 'post',
                                data: $dlg.find('form').serialize(),

                                success: function (success) {
                                    console.log('success');
                                    console.log(success);
                                },
                                error: function (xhr) {
                                    console.log('error');
                                    console.log(xhr);
                                }
                            });
                        }
                        $dlg.dialog('close');
                    },
                    Cancel: function () {
                        $dlg.dialog('close');
                    }
                }
            });
        }

        function clear() {
            $id.val(undefined);
            $username.val(undefined);
            $password.val(undefined);
            $email.val(undefined);
            $role.val(undefined);
            return this;
        }

        function populate(acct) {
            console.log(acct);
            $id.val(acct.Id);
            console.log($id.val());
            $username.val(acct.Username);

            $password.val('');
            $email.val(acct.Email);
            $role.val(acct.Role);
            return this;
        }

        function open() {
            $dlg.dialog('open');
        }

        return {
            init: init,
            clear: clear,
            populate: populate,
            open: open
        };
    })();

    let DatabaseTab = (function () {
        let $tab,
            $backupForm,
            $restoreForm;

        function init(selector) {
            $tab = $(selector);
            $backupForm = $tab.find('form[name=backup-db]');
            $restoreForm = $tab.find('form[name=restore-db]');

            $backupForm.find('button').on('click', function (e) {
                e.preventDefault();
                $backupForm.submit();
            });

            $restoreForm.on('submit', function () {
                Attend.loadAnother();
                try {
                    // let formData = new FormData($restoreForm[0]);
                    $.ajax({
                        url: 'restore-db',
                        method: 'post',
                        data: new FormData($restoreForm[0]),
                        processData: false,
                        contentType: false,

                        success: function (data) {
                            console.log("Success");
                            console.log(data);
                            Attend.doneLoading();
                        },
                        error: function (xhr) {
                            console.log(xhr);
                            alert("Error");
                            Attend.doneLoading();
                        }
                    });
                } catch (e) {
                    console.log(e);
                    alert('Exception');
                    Attend.doneLoading();
                }
                return false;
            });
        }

        return {
            init: init
        };
    })();

    let SecurityTab = (function () {
        let $tab,
            $table;

        function init(selector) {
            $tab = $(selector);
            $table = $tab.find('table');
            $table.DataTable({
                order: [[1, 'desc']]
            });
        }

        return {
            init: init
        };
    })();


    // Document ready handler
    $(function () {
        console.log('Document ready');

        AcctDlg.init('#accountPropsDlg');
        DatabaseTab.init('#database-tab');
        SecurityTab.init('#security-tab');
        $('#tabs').tabs().show();
    });

})(this, jQuery);
