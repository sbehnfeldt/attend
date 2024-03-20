;(function (global, $) {
    'use strict';

    // Classrooms API, for interacting with backend for classroom data
    let api = (function () {

        // Fetch all classroom data
        async function select() {
            const response = await fetch('/api/classrooms');
            if (!response.ok) {
                // throw new Error(`HTTP error status: ${response.status}`)
                console.log(`HTTP error status: ${response.status}`);
                return [];
            }
            let json = await response.json();
            return json.data;   // All classroom data
        }

        // Insert a new classroom record
        async function insert(data) {
            const response = await fetch('/api/classrooms', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data)
            });
            if (!response.ok) {
                // throw new Error(`HTTP error status: ${response.status}`)
                console.log(`HTTP error status: ${response.status}`);
                return [];
            }
            let json = await response.json();
            return json.data;   // The new classroom
        }

        // Update an existing classroom record
        async function update(data) {
            const response = await fetch(`/api/classrooms/${data.Id}`, {
                method: 'PUT',
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data)
            });
            if (!response.ok) {
                // throw new Error(`HTTP error status: ${response.status}`)
                console.log(`HTTP error status: ${response.status}`);
                return [];
            }
            let json = await response.json();
            return json.data;   // The updated classroom
        }

        // Delete an existing classroom record
        async function remove(id) {
            const response = await fetch(`/api/classrooms/${id}`, {
                method: 'DELETE'
            });
            if (!response.ok) {
                // throw new Error(`HTTP error status: ${response.status}`)
                console.log(`HTTP error status: ${response.status}`);
            }
            return;
        }

        return {select, insert, update, remove}
    })();


    let ClassroomsTab = (function (selector) {
        let $self,
            table;

        $self = $(selector);
        table = $self.find('table').DataTable({
            ajax: load,
            paging: false,
            searching: false,
            select: "single",
            order: [[2, "asc"]],
            columns: [{
                data: "Id",
                visible: false
            }, {
                data: "Label"
            }, {
                data: "Ordering"
            }, {
                data: "CreatedAt",
                render: (x) => {
                    return moment(x).format('YYYY-MM-D');
                }
            }, {
                data: "CreatedBy.Username",
            }, {
                data: "UpdatedAt",
                render: (x) => {
                    return x ? moment(x).format('YYYY-MM-D') : '';
                }
            }, {
                data: "UpdatedBy",
                render: (x) => {
                    return x ? x.Username : '';
                }
            }]
        });

        let b0 = new $.fn.dataTable.Buttons(table, {
            buttons: [{
                text: "New",
                action: function () {
                    ClassroomPropsDlg.open();
                }
            }, {
                extend: "selected",
                text: "Edit",
                action: function (e, dt, button, config) {
                    let selected = dt.rows({selected: true});
                    ClassroomPropsDlg.open(dt.rows(selected[0]).data()[0]);
                }
            }, {
                extend: "selected",
                text: "Delete",
                action: async function (e, dt) {
                    let selected = dt.rows({selected: true});
                    if (confirm('Are you sure you want to delete this record?')) {
                        Attend.loadAnother();
                        let data = dt.row(selected[0]).data();
                        await api.remove(data.Id);
                        await ClassroomsTab.reload();
                        Attend.doneLoading();
                    }
                }
            }]
        });
        b0.dom.container.eq(0).appendTo($self.find('.record-buttons'));

        let b1 = new $.fn.dataTable.Buttons(table, {
            buttons: [{
                text: "Reload",
                action: load
            }]
        });
        b1.dom.container.eq(0).appendTo($self.find('.table-buttons'));

        // Fetch the classroom data from the server and populate the table
        async function load() {
            Attend.loadAnother();
            let classrooms = await api.select();
            table.clear();
            for (let i = 0; i < classrooms.length; i++) {
                table.row.add(classrooms[i]);
            }
            table.draw();
            Attend.doneLoading();
        }

        return {reload: load};
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
            autoOpen: false,
            modal: true,
            width: "600px",
            buttons: {
                Submit: async function () {
                    if (validate()) {
                        await submit();
                        await ClassroomsTab.reload();
                    }
                },
                Cancel: function () {
                    close();
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

        async function submit() {
            let data = {
                Id: '' === $classroomId.val() ? null : $classroomId.val(),
                Label: $label.val(),
                Ordering: '' === $order.val() ? null : $order.val()
            };
            if ($classroomId.val()) {
                await api.update(data);
            } else {
                await api.insert(data);
            }
            close();
        }

        return {open, close};
    })('#classroom-props-dlg');

    // $(function () {
    //     $('#tabs').tabs().show();
    // });

})(this, jQuery);
