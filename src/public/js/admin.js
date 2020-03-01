;(function (global, $) {
    'use strict';


    let AcctsTable = (function () {
        let $table;

        function init(selector) {
            $table = $(selector);
            $table.DataTable({
                dom: 'rtB',
                select: {
                    style: 'single'
                },
                buttons: [{
                    text: 'New',
                    action: function (e, dt, node, config) {
                        AcctDlg.open();
                    }
                }, {
                    text: 'Update',
                    action: function (e, dt, node, config) {
                        alert("Update")
                    }
                }, {
                    text: 'Delete',
                    action: function (e, dt, node, config) {
                        alert("Delete");
                    }
                }]
            });
        }

        return {
            init: init
        };
    })();


    let AcctDlg = (function () {
        let $dlg;

        function init(selector) {
            $dlg = $(selector).dialog({
                modal: true,
                autoOpen: false,
                buttons: {
                    Submit: function () {
                        console.log($dlg.find('form').serialize());
                        console.log($dlg.find('form').serializeArray());
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
                        $dlg.dialog('close');
                    },
                    Cancel: function () {
                        alert("Cancel");
                        $dlg.dialog('close');
                    }
                }
            });
        }

        function open() {
            $dlg.dialog('open');
        }

        return {
            init: init,
            open: open
        };
    })();


    // Document ready handler
    $(function () {
        console.log('Document ready');
    });

    AcctsTable.init('#acctsTable');
    AcctDlg.init('#accountPropsDlg');
})(this, jQuery);
