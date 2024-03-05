;(function (global, $) {
    'use strict';

    let ClassroomsTab = (function (selector) {
        let $self,
            table;

        $self = $(selector);
        table = $self.find('table').DataTable({
            "ajax": function () {
                Attend.loadAnother();
                $.ajax({
                    'url': 'api/classrooms',
                    'method': 'get',

                    'success': function (json) {
                        console.log(json);
                        for (let i = 0; i < json.length; i++) {
                            table.row.add(json[i]);
                        }
                        table.draw();
                        Attend.doneLoading();
                    },
                    'error': function (xhr) {
                        console.log(xhr);
                        Attend.doneLoading();
                    }
                });
            },
            "paging": false,
            "searching": false,
            "select": "single",
            "order": [[2, "asc"]],
            "columns": [
                {
                    'data': "Id",
                    'visible': false
                },
                {'data': "Label"},
                {'data': "Ordering"},
                {
                    'data': "CreatedAt",
                    'render': (x) => {
                        return moment(x).format('YYYY-MM-D');
                    }
                },
                {
                    'data': "UpdatedAt",
                    'render': (x) => {
                        return moment(x).format('YYYY-MM-D');
                    }
                }
            ]
        });

        let b0 = new $.fn.dataTable.Buttons(table, {
            buttons: [{
                "text": "New",
                "action": function () {
                    ClassroomPropsDlg.open();
                }
            }, {
                "extend": "selected",
                "text": "Edit",
                "action": function (e, dt, button, config) {
                    let selected = dt.rows({selected: true}).indexes();
                    ClassroomPropsDlg.open(dt.rows(selected[0]).data()[0]);
                }
            }, {
                "extend": "selected",
                "text": "Delete",
                "action": function (e, dt) {
                    let selected = dt.rows({selected: true});
                    if (confirm('Are you sure you want to delete this record?')) {

                        let data = dt.row(selected[0]).data();
                        Attend.loadAnother();
                        $.ajax({
                            "url": "api/classrooms/" + data.Id,
                            "method": "delete",

                            "success": function (json) {
                                reload();
                                Attend.doneLoading();
                            },
                            "error": function (xhr) {
                                console.log(xhr);
                                Attend.doneLoading();
                            }
                        });
                    }
                }
            }]
        });
        b0.dom.container.eq(0).appendTo($self.find('.record-buttons'));


        let b1 = new $.fn.dataTable.Buttons(table, {
            "buttons": [{
                "text": "Reload",
                "action": function (e, dt) {
                    Attend.loadAnother();
                    table.clear();
                    dt.ajax.reload(Attend.doneLoading);
                }
            }]
        });
        b1.dom.container.eq(0).appendTo($self.find('.table-buttons'));


        function insert(data) {
            table.row.add(data).draw();
        }

        function reload() {
            table.clear();
            table.ajax.reload();
        }

        function redrawRow(newData) {
            table.rows().every(function ( /* rowIdx, tableLoop, rowLoop */) {
                let data = this.data();
                if (data.Id === newData.Id) {
                    let oldData = this.data();
                    for (let p in newData) {
                        oldData[p] = newData[p];
                    }
                    this.data(oldData);
                }
            });
        }

        function deleteRow(classroom_id) {
            table.rows().every(function (rowIdx, tableLoop, rowLoop) {
                let data = this.data();
                console.log(rowIdx);
                console.log(tableLoop);
                console.log(rowLoop);

                console.log(data);
                if (classroom_id == data.id) {
                    this.remove();
                }
            });
        }

        return {
            "insert": insert,
            "reload": reload,
            "redrawRow": redrawRow,
            "deleteRow": deleteRow
        };
    })('#classrooms-tab');


    let ClassroomPropsDlg = (function (selector) {
        let $self,
            $form,
            $classroomId,
            $label,
            $order,
            $inputs,
            $required,
            dialog;

        $self        = $(selector);
        $form        = $self.find('form');
        $classroomId = $form.find('[name=Id]');
        $label       = $form.find('[name=Label]');
        $order       = $form.find('[name=Ordering]');

        $inputs = $form.find('input');
        $inputs.on('change', function () {
            if ($(this).val() !== $(this).data('db-val')) {
                $(this).addClass('modified');
            } else {
                $(this).removeClass('modified');
            }
        });

        $required = $form.find('.required input');

        dialog = $self.dialog({
            "autoOpen": false,
            "modal": true,
            "width": "600px",
            "buttons": {
                "Submit": function () {
                    if (validate()) {
                        submit();
                    }
                },
                "Cancel": function () {
                    ClassroomPropsDlg.close();
                }
            }
        });

        function open(classroom) {
            clear();
            if (classroom) {
                populate(classroom);
            }
            dialog.dialog('open');
        }

        function close() {
            dialog.dialog('close');
        }

        function clear() {
            $form[0].reset();
            $required.removeClass('missing');
            $('.error').text('').hide();
            $inputs.data('db-val', '').removeClass('modified');
        }

        function populate(classroom) {
            $classroomId.val(classroom.Id);
            $label.val(classroom.Label).data('db-val', classroom.Label);
            $order.val(classroom.Ordering).data('db-val', classroom.Ordering);
        }


        function validate() {
            let valid = true;
            $required.each(function (i, e) {
                if (!$(e).val()) {
                    $(e).addClass('missing');
                    $(e).next().text("This field cannot be blank").show();
                    valid = false;
                } else {
                    $(e).removeClass('missing');
                    $(e).next().text("").hide();
                }
            });
            return valid;
        }

        function submit() {
            let data = {
                "Id": '' === $classroomId.val() ? null : $classroomId.val(),
                "Label": $label.val(),
                "Ordering": '' === $order.val() ? null : $order.val()
            };
            if ($classroomId.val()) {
                update(data);
            } else {
                insert(data);
            }
        }

        function insert(data) {
            Attend.loadAnother();
            $.ajax({
                "url": "api/classrooms",
                "method": "post",
                'data': data,

                "dataType": "json",
                "success": function (json) {
                    ClassroomsTab.reload(json);
                    ClassroomPropsDlg.close();
                    Attend.doneLoading();
                },
                "error": function (xhr) {
                    console.log(xhr);
                    if ('_' in xhr.responseJSON) {
                        $form.find(".form-error").text(xhr.responseJSON['_']).show();
                    }
                    if ('Label' in xhr.responseJSON) {
                        $label.next().text(temp['Label']).show();
                    }
                    Attend.doneLoading();
                }
            });

        }

        function update(data) {
            Attend.loadAnother();
            $.ajax({
                "url": "api/classrooms/" + data.Id,
                "method": "put",
                'data': data,

                "dataType": "json",
                "success": function (json) {
                    console.log(json);
                    ClassroomsTab.reload(json);
                    ClassroomPropsDlg.close();
                    Attend.doneLoading();
                },
                "error": function (xhr) {
                    console.log(xhr);
                    console.log(xhr);
                    if ('_' in xhr.responseJSON) {
                        $form.find(".form-error").text(xhr.responseJSON['_']).show();
                    }
                    if ('Label' in xhr.responseJSON) {
                        $label.next().text(temp['Label']).show();
                    }
                    Attend.doneLoading();
                }
            });
        }


        return {
            'open': open,
            'close': close
        };
    })('#classroom-props-dlg');

    // $(function () {
    //     $('#tabs').tabs().show();
    // });

})(this, jQuery);
