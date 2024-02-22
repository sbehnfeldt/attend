;(function (global, $) {
    'use strict';


    /*================================================================================
     * Tab for display information about user accounts for the application
     ================================================================================*/
    let AccountsTab = (function (selector) {
        let $tab        = $(selector);
        let $acctsTable = $('#accounts-table');

        let table = $acctsTable.DataTable({
            dom: 'rtB',
            select: {
                style: 'single'
            },
            autoWidth: false,
            columnDefs: [
                {targets: [0], visible: false}
            ],
            buttons: [{
                text: 'New',
                action: function (e, dt, node, config) {
                    AcctDlg.clear().open();
                }
            }, {
                text: 'Update',
                extend: 'selected',
                action: function (e, dt, node, config) {
                    let selected = dt.rows({selected: true}).indexes();
                    if (1 < selected.length) {
                        alert("Can edit only 1 record at a time");
                    } else {
                        AcctDlg.clear().populate($(dt.rows(selected[0]).nodes()[0]).data('account')).open();
                    }
                }
            }, {
                text: 'Delete',
                extend: 'selected',
                action: function (event, table, node, config) {
                    try {
                        let selected = table.rows({selected: true});
                        let acctId   = $(selected.nodes()[0]).attr('data-id');
                        try {
                            Attend.loadAnother();
                            $.ajax({
                                url: '/api/accounts/' + acctId,
                                method: 'delete',

                                success: function (success) {
                                    Attend.doneLoading();
                                    selected.remove().draw(false);
                                },
                                error: function (xhr) {
                                    Attend.doneLoading();
                                    console.log(xhr);
                                    alert("Error!");
                                }
                            });
                        } catch (e) {
                            Attend.doneLoading();
                            console.log(e);
                            alert("Exception!");
                        }
                    } catch (e) {
                        console.log(e);
                    }
                }
            }]
        });


        function insert(acct) {
            let data = [
                acct.Id,
                `<a data-user-id="${acct.Id}" href="javascript:void(0)">${acct.Username}</a>`,
                acct.Email
            ];

            let row = table.row.add(data);
            row.nodes().to$().attr('data-id', acct.Id);
            row.nodes().to$().attr('data-account', JSON.stringify(acct));
            table.draw();
        }

        return {insert};
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
            $dlg      = $(selector);
            $form     = $dlg.find('form');
            $id       = $dlg.find('input[name=id]');
            $username = $form.find('input[name=username]');
            $password = $form.find('input[name=password]');
            $email    = $form.find('input[name=email]');
            // $role = $form.find('select[name=role]');

            $dlg.dialog({
                modal: true,
                autoOpen: false,
                width: "750px",
                buttons: {
                    Submit: function () {
                        if ($id.val()) {
                            // Update existing account
                            Attend.loadAnother();
                            $.ajax({
                                url: 'api/accounts/' + $id.val(),
                                method: 'put',
                                data: $dlg.find('form').serialize(),

                                success: function (acct) {
                                    console.log('success');
                                    console.log(acct);
                                    Attend.doneLoading();
                                },
                                error: function (xhr) {
                                    console.log('error');
                                    console.log(xhr);
                                    Attend.doneLoading();
                                }
                            });

                        } else {
                            Attend.loadAnother();
                            $.ajax({
                                url: 'api/accounts',
                                method: 'post',
                                data: $dlg.find('form').serialize(),

                                success: function (acct) {
                                    AccountsTab.insert(acct);
                                    Attend.doneLoading();
                                },
                                error: function (xhr) {
                                    console.log('error');
                                    console.log(xhr);
                                    Attend.doneLoading();
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
            // $role.val(undefined);
            return this;
        }

        function populate(acct) {
            $id.val(acct.Id);
            $username.val(acct.Username);

            $password.val('');
            $email.val(acct.Email);
            // $role.val(acct.Role);
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
            $tab         = $(selector);
            $backupForm  = $tab.find('form[name=backup-db]');
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
            $tab   = $(selector);
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
