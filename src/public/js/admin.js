;(function (global, $) {
    'use strict';

    // Document ready handler
    $(function () {
        let $dlg;
        console.log('Document ready');
        $('table').DataTable({
            dom: 'rtB',
            buttons: [{
                text: 'New',
                action: function (e, dt, node, config) {
                    $dlg.dialog('open');
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

        $dlg = $('section#accountPropsDlg').dialog({
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
    });
})(this, jQuery);
