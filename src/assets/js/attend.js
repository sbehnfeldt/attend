;(function () {
    'use strict';

    // Model, container of individual records
    var Records = (function () {
        function init() {
            this.records   = {};
            this.callbacks = {
                'empty-records': $.Callbacks(),
                'load-records' : $.Callbacks(),
                'insert-record': $.Callbacks(),
                'remove-record': $.Callbacks(),
                'update-record': $.Callbacks()
            }
        }

        function subscribe(event, fn) {
            this.callbacks[event].add(fn);
            return this;
        }

        function empty() {
            this.records = {};
            this.callbacks['empty-records'].fire();
        }

        function load(records) {
            for (var i = 0; i < records.length; i++) {
                var r                        = records[i];
                this.records[parseInt(r.id)] = r;
            }
            this.callbacks['load-records'].fire(records);
            return this;
        }

        function insert(record) {
            this.records[parseInt(record.id)] = record;
            this.callbacks['insert-record'].fire(record.id);
            return this;
        }

        function update(id, updates) {
            for (var p in updates) {
                this.records[parseInt(id)][p] = updates[p];
            }
            this.callbacks['update-record'].fire(id, updates);
            return this;
        }

        function remove(id) {
            this.records[parseInt(id)] = undefined;
            this.callbacks['remove-record'].fire(id);
            return this;
        }

        return {
            'init'     : init,
            'subscribe': subscribe,
            'empty'    : empty,
            'load'     : load,
            'insert'   : insert,
            'update'   : update,
            'remove'   : remove
        };

    })();

    var Classrooms = Object.create(Records);
    var Students   = Object.create(Records);
    var Schedules  = Object.create(Records);

    // Communicator between front and back ends
    function Uhura(url, model) {
        this.url   = url;
        this.model = model;

        this.load   = function () {
            var self = this;
            $.ajax({
                'url'   : this.url,
                'method': 'get',

                'dataType': 'json',
                'success' : function (json) {
                    console.log(json);
                    self.model.load(json.data)
                },
                'error'   : function (xhr) {
                    console.log(xhr);
                    if (xhr.responseJSON) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert("Unhandled error");
                    }
                }
            });
        };
        this.insert = function (data) {
            var self = this;
            $.ajax({
                'url'   : this.url,
                'method': 'post',
                'data'  : $.param(data),

                'dataType': 'json',
                'success' : function (json) {
                    console.log(json);
                    data.id = json.data;
                    self.model.insert(json.data)
                },
                'error'   : function (xhr) {
                    console.log(xhr);
                    if (xhr.responseJSON) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert("Unhandled error");
                    }
                }
            });
        };
        this.update = function (id, params) {
            var self = this;
            $.ajax({
                'url'   : this.url + '/' + id,
                'method': 'put',
                'data'  : $.param(params),

                'dataType': 'json',
                'success' : function (json) {
                    console.log(json);
                    self.model.update(id, params)
                },
                'error'   : function (xhr) {
                    console.log(xhr);
                    if (xhr.responseJSON) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert("Unhandled error");
                    }
                }
            });
        };
        this.remove = function (id) {
            var self = this;
            $.ajax({
                'url'   : this.url + '/' + id,
                'method': 'delete',

                'dataType': 'json',
                'success' : function (json) {
                    console.log(json);
                    self.model.remove(id);
                },
                'error'   : function (xhr) {
                    console.log(xhr);
                    if (xhr.responseJSON) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert("Unhandled error");
                    }
                }
            });
        };
    }

    var ClassroomController = new Uhura('/attend-api/classrooms', Classrooms);
    var StudentController   = new Uhura('/attend-api/students', Students);
    var SchedulesController = new Uhura('/attend-api/schedules', Schedules);


    /****************************************************************************************************
     * Classrooms Table
     ****************************************************************************************************/
    var ClassroomsTable = (function () {
        var $table;
        var table;

        function init(selector) {
            $table = $(selector);
            table  = $table.DataTable({
                'info'     : false,
                'paging'   : false,
                'searching': false,
                'ordering' : false
            });
            $table.on('click', 'button.edit', onClickUpdateClassroom);
            $table.on('click', 'button.delete', onClickDeleteClassroom);

            Classrooms.subscribe('empty-records', whenClassroomsEmptied);
            Classrooms.subscribe('load-records', whenClassroomsLoaded);
            Classrooms.subscribe('insert-record', whenClassroomAdded);
            Classrooms.subscribe('update-record', whenClassroomUpdated);
            Classrooms.subscribe('remove-record', whenClassroomRemoved);
        }

        function toArray(classroom) {
            return [
                classroom && classroom.label ? classroom.label : '',
                '<button class="edit"><span class="glyphicon glyphicon-edit" /> </button>&nbsp;',
                '<button class="delete"><span class="glyphicon glyphicon-remove" /> </button>'
            ];
        }

        // Add a new row to the classroom table
        function addClassroom(classroom) {
            var row = table.row.add(toArray(classroom));
            $(row.node()).data('classroomId', classroom ? classroom.id : '');
            return row;
        }

        ////////////////////////////////////////////////////////////////////////////////
        // Internal Event Handlers
        ////////////////////////////////////////////////////////////////////////////////

        // When the 'Update' button for an existing classroom is clicked,
        // open the Classroom Properties dialog
        function onClickUpdateClassroom() {
            var $tr         = $(this).closest('tr');
            var classroomId = $tr.data('classroomId');
            ClassroomPropsDlg.open(Classrooms.records[classroomId]);
        }

        // When the 'Delete' button for an existing classroom is clicked,
        // delete the data from the database (via the controller)
        function onClickDeleteClassroom() {
            var $tr = $(this).closest('tr');
            var id  = $tr.data('classroomId');
            if (window.confirm('Are you sure you want to delete the ' + Classrooms.records[id].name + ' classroom?')) {
                ClassroomController.remove(id);
            }
        }

        ////////////////////////////////////////////////////////////////////////////////
        // External Event Callbacks
        ////////////////////////////////////////////////////////////////////////////////

        // WHen classroom model is cleared of all records,
        // empty the Classrooms table
        function whenClassroomsEmptied() {
            table.clear().draw();
        }

        // When classrooms are loaded into the model,
        // populate the Classrooms table
        function whenClassroomsLoaded(records) {
            for (var id in records) {
                addClassroom(records[id]);
            }
            table.draw();
        }

        // When a new classroom is added to the model,
        // add a new classroom to the Classrooms table
        function whenClassroomAdded(classroom) {
            var row = table.row('.new-classroom');
            row.remove();
            $(row).remove();

            table.addClassroom(classroom);
            table.draw();
            $newButton.show();
        }

        // When a classroom is updated in the model,
        // update the corresponding row in the table acordingly
        function whenClassroomUpdated(id, updates) {
            var rows = table.rows().nodes();
            rows.each(function (e, i) {
                var $tr  = $(e);
                var data = $tr.data('classroomId');
                if (data === id) {
                    var row = table.row($tr);
                    row.data(toArray(Classrooms.records[id]));
                    $(row.node()).data('classroomId', id);
                    row.draw();
                    return false;
                }
            });
        }

        // When a classroom is removed from the model,
        // remove the corresponding row from the Classrooms table
        function whenClassroomRemoved(id) {
            table.rows().nodes().each(function (e, i) {
                var $tr  = $(e);
                var data = $tr.data('classroomId');
                if (data === id) {
                    $tr.remove();
                    table.row(e).remove();
                    return false;
                }
            });
        }


        return {
            'init': init
        };
    })();

    /****************************************************************************************************
     * Classroom Properties Dialog
     ****************************************************************************************************/
    var ClassroomPropsDlg = (function () {
        var $dialog;
        var dialog;
        var $id;
        var $label;
        var $tips;
        var tipsTimer;

        function init(selector) {
            $dialog   = $(selector);
            dialog    = $dialog.dialog({
                autoOpen: false,
                modal   : true,
                buttons : {
                    "Submit": onClickSubmitClassroomForm,
                    "Cancel": function () {
                        dialog.dialog("close");
                    }
                },
                "close" : clear
            });
            $id       = $dialog.find('input[name=id]');
            $label    = $dialog.find('input[name=label]');
            $tips     = $dialog.find('p.update-tips');
            tipsTimer = null;
        }

        function clear() {
            $dialog.find('form')[0].reset();
            $tips
                .text('')
                .removeClass("ui-state-highlight");
            if (tipsTimer) {
                clearTimeout(tipsTimer);
                tipsTimer = null;
            }
            $label
                .text('')
                .removeClass('ui-state-error');
        }

        function open(classroom) {
            if (classroom) {
                $id.val(classroom.id);
                $label.val(classroom.label);
            }

            dialog.dialog('open');
        }

        function checkLength(o, n, min, max) {
            if (o.val().length > max || o.val().length < min) {
                o.addClass("ui-state-error");
                updateTips("Length of " + n + " must be between " +
                    min + " and " + max + ".");
                return false;
            } else {
                return true;
            }
        }

        function updateTips(t) {
            $tips
                .text(t)
                .addClass("ui-state-highlight");
            tipsTimer = setTimeout(function () {
                $tips.removeClass("ui-state-highlight", 1500);
            }, 2500);
        }


        ////////////////////////////////////////////////////////////////////////////////
        // Internal Event Handlers
        ////////////////////////////////////////////////////////////////////////////////

        // When the 'Submit' button on the classroom dialog form is clicked,
        // enter the new classroom data into the database (via the controller)
        function onClickSubmitClassroomForm() {
            $tips.text('').removeClass('ui-state-highlight');
            var valid = true;
            valid     = valid && checkLength($label, "label", 1, 55);

            if (valid) {
                if ($id.val()) {
                    ClassroomController.update($id.val(), {
                        'label': $label.val()
                    });
                } else {
                    ClassroomController.insert({
                        'label': $label.val()
                    });
                }
                dialog.dialog("close");
            }
            return valid;
        }


        return {
            'init': init,
            'open': open
        };
    })();


    /****************************************************************************************************
     * Classrooms Panel
     ****************************************************************************************************/
    var ClassroomsPanel = (function () {
        var $panel;
        var $newButton;
        var $refreshButton;

        function init(selector) {
            $panel         = $(selector);
            $newButton     = $panel.find('button.new-record');
            $refreshButton = $panel.find('button.refresh-records');
            $newButton.on('click', onClickNewClassroom);
            $refreshButton.on('click', onRefreshClassrooms);
        }

        ////////////////////////////////////////////////////////////////////////////////
        // Event Handler Functions
        ////////////////////////////////////////////////////////////////////////////////


        // When the "New Classroom" button is clicked,
        // add an empty row to the end of the Classrooms table
        function onClickNewClassroom() {
            ClassroomPropsDlg.open();
        }

        function onRefreshClassrooms() {
            Classrooms.empty();
            ClassroomController.load();
        }

        return {
            'init': init
        }
    })();


    /****************************************************************************************************
     * Students Table
     ****************************************************************************************************/
    var StudentsTable = (function () {

        var $table;
        var table;

        function init(selector) {
            $table = $(selector);
            table  = $table.DataTable({});
            $table.on('click', 'button.edit', onClickEditStudent);
            $table.on('click', 'button.schedules', onClickEditSchedules);
            $table.on('click', 'button.delete', onClickDeleteStudent);

            Classrooms.subscribe('load-records', whenClassroomsLoaded);

            Students.subscribe('empty-records', whenStudentsEmptied);
            Students.subscribe('load-records', whenStudentsLoaded);
            Students.subscribe('remove-record', whenStudentRemoved);
            Students.subscribe('insert-record', whenStudentAdded);
            Students.subscribe('update-record', whenStudentUpdated);
        }

        function toArray(student) {
            return [
                student.family_name,
                student.first_name,
                (student.classroom_id in Classrooms.records)
                    ? '<span class="classroom">' + Classrooms.records[student.classroom_id].label + '</span>'
                    : '<span class="classroom">' + student.classroom_id + '</span>',

                '<input type="checkbox" name="enrolled" disabled ' + (( "1" === student.enrolled ) ? ' checked' : '') + '/>',
                '<button class="edit"><span class="glyphicon glyphicon-edit" /> </button>',
                '<button class="schedules"><span class="glyphicon glyphicon-time" /> </button>',
                '<button class="delete"><span class="glyphicon glyphicon-remove" /> </button>'
            ];
        }


        ////////////////////////////////////////////////////////////////////////////////
        // Internal Event Handler Functions
        ////////////////////////////////////////////////////////////////////////////////

        function onClickEditStudent() {
            var $tr = $(this).closest('tr');
            var id  = $tr.data('studentId');
            StudentPropsDlg.open(Students.records[id]);
        }

        function onClickEditSchedules() {
            var $tr = $(this).closest('tr');
            var id  = $tr.data('studentId');
            ScheduleDlg.open(id);
        }


        function onClickDeleteStudent() {
            if (confirm('Are you sure you want to DELETE this student from the database?')) {
                var $tr = $(this).closest('tr');
                var id  = $tr.data('studentId');
                StudentController.remove(id);
            }
        }


        ////////////////////////////////////////////////////////////////////////////////
        // External Event Callback Functions
        ////////////////////////////////////////////////////////////////////////////////

        function whenStudentsEmptied() {
            table.clear().draw();
        }

        function whenStudentsLoaded(records) {
            for (var id in records) {
                var row = table.row.add(toArray(records[id]));
                $(row.node()).data('studentId', records[id].id);
            }
            table.draw();
        }

        function whenStudentAdded(studentId) {
            var row = table.row.add(toArray(Students.records[studentId]));
            $(row.node()).data('studentId', Students.records[studentId].id);
            row.draw();
        }

        function whenStudentUpdated(id, updates) {
            var rows = table.rows().nodes();
            rows.each(function (e, i) {
                var $tr  = $(e);
                var data = $tr.data('studentId');
                if (data === id) {
                    var row = table.row($tr);
                    row.data(toArray(Students.records[id]));
                    $(row.node()).data('studentId', id);
                    row.draw();
                    return false;
                }
            });

        }


        function whenStudentRemoved(id) {
            table.rows().nodes().each(function (e, i) {
                var $tr  = $(e);
                var data = $tr.data('studentId');
                if (data === id) {
                    table.row($tr).remove();
                    return false;
                }
            });
            table.draw();
        }

        // When the classrooms are loaded,
        // replace the classroom ID in the Students table with the corresponding classroom name
        function whenClassroomsLoaded() {
            table.rows().nodes().each(function (tr, i, a) {
                var studentId   = $(tr).data('studentId');
                var classroomId = Students.records[studentId].classroom_id;
                $(tr).find('span.classroom').text(
                    (classroomId in Classrooms.records) ?
                        Classrooms.records[classroomId].label :
                        Students.records[studentId].classroom_id);
            });
        }

        return {
            'init': init
        };
    })();


    /****************************************************************************************************
     * Student Properties Dialog
     ****************************************************************************************************/
    var StudentPropsDlg = (function () {
        var $dialog;
        var dialog;

        var form;
        var $id;
        var $tips;
        var $familyName;
        var $firstName;
        var $classrooms;
        var $active;

        var tipsTimer;

        function init(selector) {
            $dialog     = $(selector);
            dialog      = $dialog.dialog({
                autoOpen: false,
                modal   : true,
                width   : '50%',
                buttons : {
                    "Submit": onClickSubmiStudentForm,
                    "Close" : function () {
                        dialog.dialog("close");
                    }
                },
                "close" : clear
            });
            form        = $dialog.find('form[name=studentData]');
            $id         = $dialog.find('input[name=id]');
            $tips       = $dialog.find('p.update-tips');
            $familyName = form.find('input[name=family_name]');
            $firstName  = form.find('input[name=first_name]');
            $classrooms = form.find('select[name=classroom_id]');
            for (var p in Classrooms.records) {
                console.log(p);
                var $opt = $('option').text(p).val(p);
                $classrooms.append($opt);
            }
            $active = $dialog.find('input[name=enrolled]');

            tipsTimer = null;

            Classrooms.subscribe('load-records', whenClassroomsLoaded);
            Classrooms.subscribe('insert-record', whenClassroomAdded);
            Classrooms.subscribe('update-record', whenClassroomUpdated);
        }

        function clear() {
            $dialog.find('form')[0].reset();
            $id.val('');
            $tips
                .text('')
                .removeClass("ui-state-highlight");
            if (tipsTimer) {
                clearTimeout(tipsTimer);
                tipsTimer = null;
            }
        }

        function open(student) {
            if (student) {
                $id.val(student.id);
                $familyName.val(student.family_name);
                $firstName.val(student.first_name);
                $classrooms.val(student.classroom_id);
                $active.prop('checked', (student.enrolled === "1"));
            }

            dialog.dialog('open');
        }

        function checkLength(o, n, min, max) {
            if (o.val().length > max || o.val().length < min) {
                o.addClass("ui-state-error");
                updateTips("Length of " + n + " must be between " +
                    min + " and " + max + ".");
                return false;
            }
            o.removeClass("ui-state-error");
            return true;
        }

        function checkSelected(o, n) {
            if (!parseInt(o.val())) {
                o.addClass('ui-state-error');
                updateTips("Selection is required from " + n);
                return false;
            }
            o.removeClass('ui-state-error');
            return true;
        }

        function updateTips(t) {
            $tips
                .text(t)
                .addClass("ui-state-highlight");
            tipsTimer = setTimeout(function () {
                $tips.removeClass("ui-state-highlight", 1500);
            }, 2500);
        }

        ////////////////////////////////////////////////////////////////////////////////
        // Internal Event Handler Functions
        ////////////////////////////////////////////////////////////////////////////////

        function onClickSubmiStudentForm() {
            $tips.text('').removeClass('ui-state-highlight');
            if (tipsTimer) {
                clearTimeout(tipsTimer);
                tipsTimer = null;
            }
            var valid = true;
            valid     = valid && checkLength($familyName, "family name", 1, 55);
            valid     = valid && checkLength($firstName, "first name", 1, 55);
            valid     = valid && checkSelected($classrooms, "classroom");
            if (valid) {
                if ($id.val()) {
                    //StudentController.update($id.val(), form.serialize());
                    StudentController.update($id.val(), {
                        'family_name' : $familyName.val(),
                        'first_name'  : $firstName.val(),
                        'enrolled'    : $active.is(':checked') ? 1 : 0,
                        'classroom_id': $classrooms.val()
                    });
                } else {
                    //StudentController.submit(form.serialize());
                    StudentController.submit({
                        'family_name' : $familyName.val(),
                        'first_name'  : $firstName.val(),
                        'enrolled'    : $active.is(':checked') ? 1 : 0,
                        'classroom_id': $classrooms.val()
                    });
                }
                dialog.dialog("close");
            }
            //$scheds.filter( ':checked' ).each( function(i, e ) {
            //    console.log( $(e ).val());
            //});
        }


        function onKeyupEditStudentFamilyName() {
            var $tr     = $(this).closest('tr');
            var id      = $tr.data('studentId');
            var current = Students.records[id];
            if (current.familyName != $(this).val()) {
                $(this).addClass('modified');
                $tr.find('button.update').removeClass('disabled').attr('disabled', false);
                $tr.find('button.delete').removeClass('delete').addClass('undo');
            } else {
                $(this).removeClass('modified');
                $tr.find('button.update').addClass('disabled').attr('disabled', true);
                $tr.find('button.undo').removeClass('undo').addClass('delete');
            }
        }

        function onKeyupEditStudentGivenName() {
            var $tr     = $(this).closest('tr');
            var id      = $tr.data('studentId');
            var current = Students.records[id];
            if (current.firstName != $(this).val()) {
                $(this).addClass('modified');
                $tr.find('button.update').removeClass('disabled').attr('disabled', false);
                $tr.find('button.delete').removeClass('delete').addClass('undo');
            } else {
                $(this).removeClass('modified');
                $tr.find('button.update').addClass('disabled').attr('disabled', true);
                $tr.find('button.undo').removeClass('undo').addClass('delete');
            }
        }

        function onChangeSelectClassroom() {
            var $tr     = $(this).closest('tr');
            var id      = $tr.data('studentId');
            var current = Students.records[id];
            if (current.classroomId != $(this).val()) {
                $(this).addClass('modified');
                $tr.find('button.update').removeClass('disabled').attr('disabled', false);
                $tr.find('button.delete').removeClass('delete').addClass('undo');
            } else {
                $(this).removeClass('modified');
                $tr.find('button.update').addClass('disabled').attr('disabled', true);
                $tr.find('button.undo').removeClass('undo').addClass('delete');
            }
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////
        // External Event Callbacks
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        function whenClassroomsLoaded(classrooms) {
            var $opt;
            for (var i in classrooms) {
                var classroom = classrooms[i];
                $opt          = $('<option>').text(classroom.label).val(classroom.id);
                $classrooms.append($opt);
            }
        }

        function whenClassroomAdded(classroom) {

        }

        function whenClassroomUpdated(classroomId, updates) {

        }


        return {
            'init': init,
            'open': open
        };
    })();


    /****************************************************************************************************
     * Student Schedule Dialog
     ****************************************************************************************************/
    var ScheduleDlg = (function () {
        var $dialog;
        var dialog;
        var $studentName;
        var $tips;

        var form;
        var $studentId;
        var $schedList;
        var $scheds;
        var $schedGroups;
        var $startDate;

        function init(selector) {
            $dialog      = $(selector);
            dialog       = $dialog.dialog({
                autoOpen: false,
                modal   : true,
                width   : '50%',
                buttons : {
                    "Submit": onClickSubmitSchedule,
                    "Close" : function () {
                        dialog.dialog("close");
                    },
                    "Reset" : reset
                },
                "close" : clear
            });
            $studentName = $dialog.find('p.student-name');
            $tips        = $dialog.find('p.update-tips');
            form         = $dialog.find('form[name=schedules]');
            $studentId   = form.find('input[name=student_id]');
            $schedList   = form.find('select[name=id]');
            $scheds      = form.find('input.scheds');
            $schedGroups = form.find('button.sched-group');
            $startDate   = form.find('input[name=start_date]');
            $startDate.datepicker();


            $schedList.on('change', onChangeSchedList);
            $scheds.on('click', onClickScheds);
            $schedGroups.on('click', onClickSchedGroup);
        }

        function clear() {
            //$dialog.find( 'form' )[ 0 ].reset();
            form[0].reset();
            form.find('.modified').removeClass('modified');
            $tips
                .text('')
                .removeClass("ui-state-highlight");
        }

        function reset() {
            var $modified = $scheds.closest('td').filter('.modified');
            if ($modified.length > 0) {
                if (confirm('Are you sure you want to discard your changes?')) {
                    $modified.each(function (i, e) {
                        var $input = $(e).find('input');
                        $input.prop('checked', $input.data('data'));
                        $(e).removeClass('modified');
                    });
                }
            }
        }

        function open(studentId) {
            if (studentId) {
                $studentName.text(Students.records[studentId].first_name + ' ' + Students.records[studentId].family_name);
                $studentId.val(studentId);
                $schedList.empty();

                var temp = [];
                for (var p in Schedules.records) {
                    if (studentId === Schedules.records[p].student_id) {
                        temp.push(p);
                    }
                }

                if (temp.length) {
                    temp.sort(function (a, b) {
                        if (Schedules.records[a].id < Schedules.records[b].id) return 1;
                        if (Schedules.records[a].id > Schedules.records[b].id) return -1;
                        return 0;
                    });

                    for (var i = 0; i < temp.length; i++) {
                        var $opt = $('<option>').val(temp[i]).text(Schedules.records[temp[i]].start_date);
                        $schedList.append($opt);
                    }
                } else {
                    var now = new Date();
                    $startDate.datepicker('setDate', now);

                    var $opt = $('<option>').val('').text(( 1 + now.getMonth()) + '/' + now.getDate() + '/' + (1900 + now.getYear()));
                    $schedList.append($opt);
                }

                $schedList.trigger('change');
                dialog.dialog('open');
            }
        }


        ////////////////////////////////////////////////////////////////////////////////////////////////////
        // Internal Event Handlers
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        function onChangeSchedList() {
            var $list = $(this);
            if ($list.val()) {
                $scheds.each(function (i, e) {
                    $(e).prop('checked', ($(e).val() & Schedules.records[$list.val()].schedule));
                    $(e).data('data', ($(e).val() & Schedules.records[$list.val()].schedule));
                });
                $startDate.val(Schedules.records[$list.val()].start_date);
            } else {
                $scheds.each(function (i, e) {
                    $(e).prop('checked', false);
                    $(e).data('data', 0);
                });
            }

        }


        // When one of the shceduling check boxes in the scheduling dialog is clicked,
        // reverse its state.  If no longer in its original state, add "modified" class to parent.
        function onClickScheds() {
            if ($(this).is(':checked')) {
                if ($(this).data('data')) {
                    $(this).parent().removeClass('modified');
                } else {
                    $(this).parent().addClass('modified');
                }
            } else {
                if ($(this).data('data')) {
                    $(this).parent().addClass('modified');
                } else {
                    $(this).parent().removeClass('modified');
                }
            }
        }

        // Programmatically 'click' all the scheduling checkboxes in the corresponding row or column
        function onClickSchedGroup() {
            var self = this;
            $scheds.each(function () {
                if ($(self).val() & $(this).val()) {
                    $(this).trigger('click');
                }
            });
        }


        function onClickSubmitSchedule() {
            alert("Submit schedule");
        }


        return {
            'init': init,
            'open': open
        }

    })();


    /****************************************************************************************************
     * Students Panel
     ****************************************************************************************************/
    var StudentsPanel = (function () {
        var $panel;
        var $newButton;
        var $refreshButton;

        function init(selector) {
            $panel         = $(selector);
            $newButton     = $panel.find('button.new-record');
            $refreshButton = $panel.find('button.refresh-records');

            $newButton.on('click', onClickNewStudent);
            $refreshButton.on('click', onClickRefreshStudents);
        }


        ////////////////////////////////////////////////////////////////////////////////
        // Event Handler Functions
        ////////////////////////////////////////////////////////////////////////////////
        function onClickNewStudent() {
            StudentPropsDlg.open();
        }

        function onClickRefreshStudents() {
            Students.empty();
            StudentController.load();
        }


        return {
            'init': init
        };
    })();


    //var CallbackSelect = (function () {
    //    var $select, publicApi, callback;
    //
    //    function init( $el, cb ) {
    //        $select  = $el;
    //        callback = cb;
    //        $select.on( 'change', callback );
    //    }
    //
    //    function addOption( label, val ) {
    //        var $option;
    //        $option = $( '<option>' );
    //        $option.val( val ).text( label );
    //        $select.append( $option );
    //    }
    //
    //    function empty() {
    //        $select.empty();
    //    }
    //
    //    function val() {
    //        return $select.val();
    //    }
    //
    //    callback = null;
    //
    //    publicApi = {
    //        init     : init,
    //        empty    : empty,
    //        val      : val,
    //        addOption: addOption
    //    };
    //    return publicApi;
    //})();


    // Return a Date object set to Monday of the week of the input date.
    function normalizeDateToMonday(date) {
        if (false === (date instanceof Date)) {
            throw 'Can only normalize a Date object';
        }
        if (date.getDay() < 6) {
            // normalize to Monday of this week
            date = new Date(date.getFullYear(), date.getMonth(), date.getDate() - (date.getDay() - 1));
        } else {
            // Normalize to Monday of next week
            date = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 2);
        }
        return date;
    }

    // From all of a student's schedules, build a composite schedule effective the week of startDate
    function getCompositeSchedule(student, startDate) {
        var composite,   // Return value
            cur,         // Current date within the
            sched,       // Current student's schedule
            index;       // Index into student's schedules for NEXT schedule

        // Find which student's schedule is in effect on "startDate", or null if his first schedule
        // does not take effect until some point in the future.  This way, users can enroll students
        // in advance.
        sched = null;
        index = 0;
        while (index < student.schedules.length) {
            if (student.schedules[index].startDate > startDate) {
                break;
            }
            sched = student.schedules[index];
            index++;
        }
        // "sched" is now the schedule in effect on "startDate" (or null), and "index" points to
        // the NEXT schedule.
        composite = {};
        cur       = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
        ['mon', 'tue', 'wed', 'thu', 'fri'].forEach(function (day, i, arr) {
            if (sched == null) {
                composite[day] = null;
            } else if (cur.getDay() - i >= 2) {
                // If some clown passes in a startDate in the middle  of the week, the effective
                // schedule for all days prior to the start date should be null.
                composite[day] = null;
            } else {
                composite[day] = sched[day];
            }

            // Prepare for the next day: see if the student's next schedule goes into effect
            cur = new Date(cur.getFullYear(), cur.getMonth(), cur.getDate() + 1);
            if (index < student.schedules.length) {
                if (cur >= student.schedules[index].startDate) {
                    sched = student.schedules[index];
                    index++;
                }
            }
        });
        return composite;
    }

    /********************************************************************************
     * Table showing checkin/checkout
     ********************************************************************************/
    var CheckinPage = (function () {
        var $page,
            $clock,
            $calendar,
            today,
            $checkInReport,
            $filterButtons,
            $classFilter,
            $checkinTable,
            $tbody,
            publicApi;
        var source, template;

        function init(selector) {
            cacheDom(selector);
            bindEvents();
            source   = $('#attendance-checkin-row-template').html();
            template = Handlebars.compile(source);

            today = new Date();
            tick();
        }

        function cacheDom(selector) {
            $page     = $(selector);
            $clock    = $page.find('span.clock');
            $calendar = $page.find('span.calendar');

            $checkInReport = $page.find('div.attendance-checkin');
            $filterButtons = $page.find('.btn-group-toggle button');
            //$classFilter   = Object.create( CallbackSelect );
            //$classFilter.init( $page.find( 'select.filter-select' ), function filterByClassroom( event ) {
            //    $tbody.filter();
            //} );
            $checkinTable = $page.find('table#attendance-checkin-table');
            $tbody        = $checkinTable.find('tbody');
        }

        function bindEvents() {
            $page.on('show', function onCheckinPageShow(event) {
                event.stopPropagation();
                $classFilter.empty();
                $classFilter.addOption('Show All');
                $classFilter.addOption('Unassigned', 0);
                Classrooms.forEach(function (classroom) {
                    $classFilter.addOption(classroom.name, classroom.id);
                });
                $tbody.filter();

                $tbody.empty();
                Students.forEach(function (student) {
                    var att;
                    var date;
                    if (student.attendance.length > 0) {
                        att = student.attendance[student.attendance.length - 1];
                        if (att.checkIn) {
                            date = new Date(att.checkIn);
                            if (date.getDate() === today.getDate()) {
                                student.checkedIn = date;
                            }
                        }
                        if (att.checkOut) {
                            date = new Date(att.checkOut);
                            if (date.getDate() === today.getDate()) {
                                student.checkedOut = date;
                            }
                        }
                    }
                    var html = template(student);
                    $tbody.append($(html));
                });
                var $rows = $tbody.children('tr');
                $rows.sort(function (row1, row2) {
                    var a = Students[$(row1).data('student-id')];
                    var b = Students[$(row2).data('student-id')];
                    return (a.familyName < b.familyName) ? -1 :
                        (a.familyName > b.familyName) ? 1 :
                            (a.firstName < b.firstName) ? -1 :
                                (a.firstName > b.firstName) ? 1 :
                                    a.id < b.id ? -1 : 0;
                });
                $rows.detach().appendTo($tbody);
            });

            $filterButtons.on('click', function () {
                if (!$(this).hasClass('btn-selected')) {
                    $filterButtons.removeClass('btn-selected');
                    $(this).addClass('btn-selected');
                    $tbody.filter();
                }
            });

            $tbody.on('show', 'tr', function (event) {
                event.stopPropagation();
            });
            $tbody.on('click', 'button.check-in', function () {
                var $button, studentId, checkIn;
                $button   = $(this);
                studentId = $button.closest('tr').data('student-id');
                $.ajax({
                    url     : 'api/checkIn',
                    method  : 'post',
                    data    : {
                        'studentId': studentId,
                        'time'     : Date.now() / 1000
                    },
                    dataType: 'json',
                    success : function onCheckInSuccess(json) {
                        if (!json.success) {
                            alert('Error checking in student: ' + json.message);
                        } else {
                            Students[studentId].attendance.push({
                                'checkIn' : json.attendance.checkIn * 1000,
                                'checkOut': json.attendance.checkOut * 1000
                            });
                            checkIn = new Date(1000 * json.attendance.checkIn);
                            $button.closest('tr').find('td.check-in').text(formatTime(checkIn));
                            $button.closest('tr').find('td.check-out').text('');
                        }
                    },
                    'error' : function (xhr) {
                        console.log(xhr);
                        if (xhr.responseJSON) {
                            alert(xhr.responseJSON.message);
                        } else {
                            alert("Unhandled error");
                        }
                    }
                });
            });

            $tbody.on('click', 'button.check-out', function () {
                var $button, studentId, checkOut;
                $button   = $(this);
                studentId = $button.closest('tr').data('student-id');
                $.ajax({
                    'url'     : 'api/checkOut',
                    'method'  : 'post',
                    'data'    : {
                        'studentId': studentId,
                        'time'     : Date.now() / 1000
                    },
                    'dataType': 'json',
                    'success' : function onCheckOutSuccess(json) {
                        var att;
                        if (!json.success) {
                            alert('Error checking out student: ' + json.message);
                        } else if (0 === Students[studentId].attendance.length) {
                            Students[studentId].attendance.push({
                                'checkIn' : json.attendance.checkIn * 1000,
                                'checkOut': json.attendance.checkOut * 1000
                            });
                        } else {
                            att = Students[studentId].attendance[Students[studentId].attendance.length - 1];
                            if (null === att.checkOut) {
                                att.checkOut = json.attendance.checkOut;
                            } else {
                                Students[studentId].attendance.push({
                                    'checkIn' : json.attendance.checkIn * 1000,
                                    'checkOut': json.attendance.checkOut * 1000
                                });
                            }
                        }
                        checkOut = new Date(1000 * json.attendance.checkOut);
                        var t    = $button.closest('tr').find('td.check-out').text();
                        if (t != '') {
                            $button.closest('tr').find('td.check-in').text('');
                        }
                        $button.closest('tr').find('td.check-out').text(formatTime(checkOut));

                    },
                    'error'   : function (xhr) {
                        console.log(xhr);
                        if (xhr.responseJSON) {
                            alert(xhr.responseJSON.message);
                        } else {
                            alert("Unhandled error");
                        }
                    }
                });
            });

            $tbody.filter = function () {
                var now, day;
                var toggle;
                var classroom;

                toggle    = $filterButtons.filter('.btn-selected').data('toggle');
                classroom = $classFilter.val();
                now       = new Date();
                day       = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'][now.getDay()];
                $tbody.find('tr.data').each(function (i, e) {
                    var student, sched;
                    student = Students[$(e).data('student-id')];
                    sched   = student.schedules[student.schedules.length - 1][day];
                    if (((toggle == 'enrolled') || (sched && (sched.Am || sched.Noon || sched.Pm )))
                        && (('' == classroom) || ((0 == classroom) && (undefined == student.classroomId)) || (classroom == student.classroomId ))) {
                        $(e).show()
                    } else {
                        $(e).hide();
                    }
                });
            };
        }

        function tick() {
            var now, hh, mm, ss;
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            now = new Date();
            hh  = now.getHours() % 12;
            if (0 == hh) hh = 12;
            mm = now.getMinutes();
            if (mm < 10) {
                mm = '0' + mm;
            }
            ss = now.getSeconds();
            if (ss < 10) {
                ss = '0' + ss;
            }
            $clock.text(hh + ':' + mm + ':' + ss + (now.getHours() < 12 ? 'am' : 'pm'));
            $calendar.text(months[now.getMonth()] + ' ' + now.getDate() + ', ' + now.getFullYear());
            if (today.getDate() != now.getDate()) {
                today = new Date();
                clearData();
            }
            setTimeout(tick, 1000);
        }

        function clearData() {
            $tbody.find('td.check-in').text('');
            $tbody.find('td.check-out').text('');
            $tbody.find('span.glyphicon-ok').remove();
        }

        publicApi = {
            init: init
        };
        return publicApi;
    })();


    /********************************************************************************
     * Table showing attendance schedules
     ********************************************************************************/
    var AttendancePage = (function () {
        var $page,
            $attendanceSchedules,
            weekOf,   // Monday of the week to display; default to this week
            $weekOf,  // Control to specify weekOf
            publicApi;


        function init(selector) {
            cacheDom(selector);
            bindEvents();

            weekOf = new Date();
            weekOf = normalizeDateToMonday(weekOf);
            $weekOf.datepicker();
            $weekOf.datepicker('option', 'showAnim', 'slideDown');
            $weekOf.datepicker('setDate', weekOf);
            $('#pdf-attendance').attr('href', 'pdf/attendance?week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate());
        }


        function cacheDom(selector) {
            $page                = $(selector);
            $weekOf              = $page.find('input[name=week-of]');
            $attendanceSchedules = $page.find('div.attendance-page-schedules');
        }


        // Generate the attendance sheets
        function generateAttendanceSheets() {
            var source;     // Source for the Handlebars template
            var template;   // The compiled template

            $attendanceSchedules.empty();
            source   = $('#attendance-schedule-class-template').html();
            template = Handlebars.compile(source);
            Classrooms.classrooms.forEach(function (classroom) {
                var context,   // The runtime data to pass to Handlebars
                    html;
                context = {
                    classroom: classroom,
                    weekOf   : weekOf.toDateString(),
                    dates    : [
                        weekOf,
                        new Date(weekOf.getFullYear(), weekOf.getMonth(), weekOf.getDate() + 1),
                        new Date(weekOf.getFullYear(), weekOf.getMonth(), weekOf.getDate() + 2),
                        new Date(weekOf.getFullYear(), weekOf.getMonth(), weekOf.getDate() + 3),
                        new Date(weekOf.getFullYear(), weekOf.getMonth(), weekOf.getDate() + 4),
                    ],

                    students: Students.filter(function (e, i, arr) {
                        return ((e.classroomId == classroom.id) && (true == e.enrolled));
                    }).sort(function (a, b) {
                        return (a.familyName < b.familyName) ? -1 :
                            (a.familyName > b.familyName) ? 1 :
                                (a.firstName < b.firstName) ? -1 :
                                    (a.firstName > b.firstName) ? 1 :
                                        a.id < b.id ? -1 : 1;
                    }),
                    totals  : {
                        'mon': 0,
                        'tue': 0,
                        'wed': 0,
                        'thu': 0,
                        'fri': 0
                    }
                };

                context.students.forEach(function (student, i, arr) {
                    var composite;
                    var notes;
                    composite        = getCompositeSchedule(student, weekOf);
                    student.schedule = {};
                    notes            = {
                        'HD' : 0,
                        'HDL': 0,
                        'FD' : 0
                    };
                    for (var day in composite) {
                        if (null == composite[day]) {
                            student.schedule[day] = false;
                        } else {
                            student.schedule[day] = [];
                            if (composite[day]['Am']) student.schedule[day].push('A');
                            if (composite[day]['Noon']) student.schedule[day].push('L');
                            if (composite[day]['Pm']) student.schedule[day].push('P');
                            student.schedule[day] = student.schedule[day].join('/');
                            if (student.schedule[day]) context.totals[day]++;

                            if (( composite[day]['Am']) && ( composite[day]['Pm'])) {
                                notes['FD']++;
                            } else if (( composite[day]['Am']) || ( composite[day]['Pm'])) {
                                if (composite[day]['Noon']) {
                                    notes['HDL']++;
                                } else {
                                    notes['HD']++;
                                }
                            }
                        }
                    }
                    student.notes = [];
                    if (notes['FD']) student.notes.push(notes['FD'] + 'FD');
                    if (notes['HD']) student.notes.push(notes['HD'] + 'HD');
                    if (notes['HDL']) student.notes.push(notes['HDL'] + 'HDL');
                    student.notes = student.notes.join(',');
                });

                // Append 3 blank entries to end of each class list
                [1, 2, 3].forEach(function () {
                    context.students.push({
                        firstName : '',
                        familyName: '',
                        schedule  : { 'mon': {}, 'tue': {}, 'wed': {}, 'thu': {}, 'fri': {} },
                        notes     : ''
                    });
                });
                html = template(context);
                $attendanceSchedules.append($(html));
            });

        }


        function bindEvents() {
            $page.on('show', generateAttendanceSheets);
            $weekOf.on('change', function onWeekOfChange() {
                weekOf = $weekOf.datepicker('getDate');
                weekOf = normalizeDateToMonday(weekOf);
                $weekOf.datepicker('setDate', weekOf).blur();
                generateAttendanceSheets();
                $('#pdf-attendance').attr('href', 'pdf/attendance?week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate());
            });
        }


        function sort(key) {
            var $rows = $tbody.children('tr[data-student-id]');
            $rows.sort(function (a, b) {
                var id1, id2, key1, key2;
                id1  = $(a).attr('data-student-id');
                id2  = $(b).attr('data-student-id');
                key1 = Students[id1][key];
                key2 = Students[id2][key];
                return (key1 > key2) ? 1 : (key2 > key1) ? -1 : 0;
            });
            $rows.detach().prependTo($tbody);
        }


        publicApi = {
            init: init
        };
        return publicApi;
    })();


    /********************************************************************************
     * Table showing signin schedules
     ********************************************************************************/
    var SigninPage = (function () {
        var $page,
            $contents,
            weekOf,   // Monday of the week to display; default to this week
            $weekOf,  // Control to select weekOf
            publicApi;

        function init(selector) {
            cacheDom(selector);
            bindEvents();

            weekOf = new Date();
            weekOf = normalizeDateToMonday(weekOf);
            $weekOf.datepicker();
            $weekOf.datepicker('option', 'showAnim', 'slideDown');
            $weekOf.datepicker('setDate', weekOf);
            $('#pdf-signin').attr('href', 'pdf/signin?week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate());
        }

        function cacheDom(selector) {
            $page     = $(selector);
            $weekOf   = $page.find('input[name=week-of]');
            $contents = $page.find('.signin-page-contents');
        }


        function generateSigninSheets() {
            var source;     // Source for the Handlebars template
            var template;   // The compiled template
            $contents.empty();
            source   = $('#attendance-signin-class-template').html();
            template = Handlebars.compile(source);
            Classrooms.classrooms.forEach(function (classroom) {
                var context,
                    html;
                context = {
                    classroom: classroom.name,
                    weekOf   : weekOf.toDateString(),
                    students : Students.filter(function (e, i, arr) {
                        return ((e.classroomId == classroom.id) && (true == e.enrolled));
                    }).sort(function (a, b) {
                        return (a.familyName < b.familyName) ? -1 :
                            (a.familyName > b.familyName) ? 1 :
                                (a.firstName < b.firstName) ? -1 :
                                    (a.firstName > b.firstName) ? 1 :
                                        a.id < b.id ? -1 : 1;
                    })
                };
                context.students.forEach(function (student, i, arr) {
                    var composite;
                    var notes;
                    composite        = getCompositeSchedule(student, weekOf);
                    student.schedule = {};
                    for (var day in composite) {
                        if (null == composite[day]) {
                            student.schedule[day] = false;
                        } else {
                            student.schedule[day] = [];
                            if (composite[day]['Am']) student.schedule[day].push('A');
                            if (composite[day]['Noon']) student.schedule[day].push('L');
                            if (composite[day]['Pm']) student.schedule[day].push('P');
                            student.schedule[day] = student.schedule[day].join('/');
                        }
                    }
                });

                // Append 3 blank entries to end of each class list
                [1, 2, 3].forEach(function () {
                    context.students.push({
                        firstName : '',
                        familyName: '',
                        schedule  : { 'mon': {}, 'tue': {}, 'wed': {}, 'thu': {}, 'fri': {} },
                        notes     : ''
                    });
                });

                html = template(context);
                $contents.append($(html));
            });

        }

        function bindEvents() {
            $page.on('show', generateSigninSheets);

            $weekOf.on('change', function onWeekOfChange() {
                weekOf = $weekOf.datepicker('getDate');
                weekOf = normalizeDateToMonday(weekOf);
                $weekOf.datepicker('setDate', weekOf).blur();
                generateSigninSheets();
                $('#pdf-signin').attr('href', 'pdf/signin?week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate());
            });
        }

        publicApi = {
            init: init
        };
        return publicApi;
    })();


    /********************************************************************************
     * Page for displaying reports
     ********************************************************************************/
    var ReportsPage = (function () {
        var $page, $pills, targets, $panels, publicApi;

        function init(selector) {
            cacheDom(selector);
            bindEvents();
        }

        function cacheDom(selector) {
            $page   = $(selector);
            $pills  = $page.find('li.report-pill');
            targets = $pills.map(function () {
                return $(this).data('target');
            }).get();
            $panels = $page.find('div.report');
        }

        function bindEvents() {
            $page.on('show', '.panel', function (event) {
                event.stopPropagation();
            });

            $page.on('show', function () {
                showReport();
            });

            $pills.on('click', function () {
                showReport($(this).data('target'));
            });

        }

        function showReport(id) {
            id = id || targets[0];
            $pills.removeClass('active').filter(function () {
                return ($(this).data('target') === id);
            }).addClass('active');
            $panels.hide().filter(id).show();
        }

        publicApi = {
            init      : init,
            showReport: showReport
        };
        return publicApi;
    })();


    /********************************************************************************
     * Table showing all students with in-place editing
     ********************************************************************************/
    var EnrollmentPage = (function () {
        var $page,
            $classFilter,
            $activeFilter,
            $studentList,
            $newStudent,
            $deleteStudent,

            $studentDetails,
            $familyName,
            $firstName,
            $isEnrolled,
            $classrooms,
            $inputs,

            $whichSchedule,
            $checkAll,
            $scheduleTable,
            $checkboxes,
            $startDate,
            $endDate,
            $thCheckers,
            $tdCheckers,
            $checkButtons,
            $saveButtons,
            $saveAndAnother,
            $saveAndClose,
            $cancelStudent,
            publicApi;

        function init(selector) {
            cacheDom(selector);
            bindMethods();
            bindEvents();
        }

        function cacheDom(selector) {
            $page         = $(selector);
            $classFilter  = $page.find('select[name=classroom-filter]');
            $activeFilter = $page.find('select[name=active-filter]');
            $studentList  = $page.find('select[name=student-list]');

            $newStudent    = $page.find('button[name=new-student-button]');
            $deleteStudent = $page.find('button[name=delete-student-button]');

            $studentDetails = $page.find('.student-detail-panel');

            $familyName = $page.find('input[name=familyName]');
            $firstName  = $page.find('input[name=firstName]');
            $isEnrolled = $page.find('input[name=enrolled]');
            $classrooms = $page.find('select[name=classrooms]');
            $inputs     = $page.find('input[type=text]');
            $startDate  = $page.find('input[name=startDate]');
            $startDate.datepicker();
            $startDate.datepicker("option", "showAnim", 'slideDown');
            $endDate = $page.find('input[name=endDate]');
            $endDate.datepicker();
            $endDate.datepicker("option", "showAnim", 'slideDown');

            $whichSchedule  = $page.find('select[name=whichSchedule]');
            $scheduleTable  = $page.find('table#student-schedule-table');
            $checkAll       = $page.find('button.checkAll');
            $checkboxes     = $scheduleTable.find('input[type=checkbox]');
            $thCheckers     = $scheduleTable.find('th.checkcontrol');
            $tdCheckers     = $scheduleTable.find('td.checkcontrol');
            $checkButtons   = $scheduleTable.find('.checkcontrol button');
            $saveButtons    = $page.find('button.btn-save');
            $saveAndAnother = $page.find('button[name=save-and-another]');
            $saveAndClose   = $page.find('button[name=save-and-close]');
            $cancelStudent  = $page.find('button[name=cancel-student-button]');
        }

        function bindMethods() {

            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Class Filter" drop-down list
            ////////////////////////////////////////////////////////////////////////////////
            $classFilter.addClassroom = function (classroom) {
                var $option = $('<option>').text(classroom.name).val(classroom.id);
                $classFilter.append($option);
                return $classFilter;
            };

            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Students" list
            ////////////////////////////////////////////////////////////////////////////////
            // Filter the students in the student list according to classroom
            $studentList.filter = function () {
                // IE doesn't support $.show() and $.hide() of <option> elements, so instead
                // we hide an <option> by wrapping it in a <span>, and show it by unwrapping
                // it. Seems to work in IE, Chrome and Firefox
                var classId, active;
                classId = $classFilter.val();

                $(this).find('span > option').unwrap();   // Show all
                if ('' == classId) {
                    // Show all
                } else if (classId == 0) {
                    // Show unassigned
                    $(this).find('option').each(function (i, e) {
                        var student = Students[$(e).val()];
                        if (null != student.classroomId) {
                            $(e).wrap('<span>');
                        }
                    });
                } else {
                    $(this).find('option').each(function (i, e) {
                        var student = Students[$(e).val()];
                        if (null == student.classroomId || classId !== student.classroomId) {
                            $(e).wrap('<span>');
                        }
                    });
                }

                active = $activeFilter.val();
                if ('' == active) {
                    // Show all
                } else {
                    $(this).find('option').each(function (i, e) {
                        var student = Students[$(e).val()];
                        if ($(e).parent().is('span')) {
                            // No option; don't double-wrap
                        } else if ('true' == active && !student.enrolled) {
                            $(e).wrap('<span>');
                        } else if ('false' == active && student.enrolled) {
                            $(e).wrap('<span>');
                        }
                    });
                }
                return $studentList;
            };

            // Add a student to the Student List
            $studentList.addStudent = function (student) {
                var $option;
                $option = $('<option>')
                    .text(student.familyName + ', ' + student.firstName).val(student.id);
                $studentList.append($option);
                return $studentList;
            };

            // Update one of the students in the student list
            $studentList.updateStudent = function (student) {
                var $opt, filter;
                $opt = $(this).find('option[value=' + student.id + ']');
                $opt.text(student.familyName + ', ' + student.firstName);
                filter = $classFilter.val();
                if (( '' == filter )
                    || (( 0 == filter ) && ( undefined == student.classroomId ))
                    || ((undefined != student.classroomId) && (filter == student.classroomId ))) {
                    $opt.show();
                } else {
                    $opt.hide();
                }
                $studentList.filter();
                $studentList.sort();
                return $studentList;
            };

            // Remove the student identified by the input parameter
            $studentList.deleteStudent = function (studentId) {
                $studentList.find('option[value=' + studentId + ']').remove();
                return $studentList;
            };

            // Sort the student list according to last name
            $studentList.sort = function () {
                var $options = $(this).children('option');
                $options.sort(function (a, b) {
                    return (Students[$(a).val()].familyName > Students[$(b).val()].familyName) ? 1 :
                        (Students[$(a).val()].familyName < Students[$(b).val()].familyName) ? -1 :
                            (Students[$(a).val()].firstName > Students[$(b).val()].firstName) ? 1 :
                                (Students[$(a).val()].firstName < Students[$(b).val()].firstName) ? -1 : 0;
                });
                $options.detach().prependTo($(this));
                return $studentList;
            };

            // How to update the GUI after a student has been selected in the Student List
            $studentList.updateStudentDetails = function (studentId) {
                var student;

                student = Students[studentId];
                $deleteStudent.prop('disabled', student.enrolled);
                $familyName.val(student.familyName).data('dbval', student.familyName).removeClass('modified').prop('disabled', false);
                $firstName.val(student.firstName).data('dbval', student.firstName).removeClass('modified').prop('disabled', false);
                $isEnrolled.prop('checked', student.enrolled).prop('disabled', false);
                $classrooms.val(student.classroomId ? student.classroomId : 0).data('dbval', student.classroomId).removeClass('modified').prop('disabled', false);

                $whichSchedule.prop('disabled', false).empty();
                student.schedules.forEach(function (e, i, arr) {
                    $whichSchedule.addSchedule(e, i);
                });

                $whichSchedule.selectSchedule(getToday());
                $checkAll.prop('disabled', false);
                $checkButtons.prop('disabled', false);
                $startDate.enable(false);
                $endDate.enable(false);

                $saveButtons.prop('disabled', false);
                $cancelStudent.prop('disabled', false);

                return $studentList;
            };

            // Unselect all students and reset page state.
            $studentList.resetStudentDetails = function () {
                $newStudent.prop('disabled', false);
                $deleteStudent.prop('disabled', true);
                $inputs.val('').data('dbval', '').removeClass('modified').prop('disabled', true);
                $isEnrolled.prop('checked', false).prop('disabled', true);
                $classrooms.val(0).data('dbval', 0).removeClass('modified').prop('disabled', true);
                $whichSchedule.empty().removeClass('modified').prop('disabled', true);
                $checkAll.prop('disabled', true);
                $checkButtons.prop('disabled', true);
                $checkboxes.prop('checked', false)
                    .data('dbval', false)
                    .removeClass('modified')
                    .prop('disabled', true)
                    .closest('td').removeClass('modified');
                $saveButtons.prop('disabled', true);
                $cancelStudent.prop('disabled', true);
                return $studentList;
            };


            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Class" drop-down list
            ////////////////////////////////////////////////////////////////////////////////
            $classrooms.addClassroom = function (classroom) {
                var $option = $('<option>').text(classroom.name).val(classroom.id);
                $classrooms.append($option);
                return $classrooms;
            };


            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Select Schedule" drop-down list
            ////////////////////////////////////////////////////////////////////////////////

            // Add a new schedule to the list of available schedules
            $whichSchedule.addSchedule = function (schedule, index) {
                var startDate,
                    $option,
                    months;
                months    = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                startDate = new Date(schedule.startDate);
                $option   = $('<option>')
                    .text(months[startDate.getMonth()] + ' ' + startDate.getDate() + ', ' + startDate.getFullYear())
                    .val(index)
                    .data('startDate', startDate);
                $whichSchedule.append($option);
            };

            // Selects the schedule which will be effective on the given date
            $whichSchedule.selectSchedule = function (targetDate) {
                var $opts;
                var startDate;
                var i;

                $opts = $whichSchedule.find('option');
                if (0 == $opts.length) {
                    $checkboxes.reset();
                } else {
                    if (targetDate <= $opts.eq(0).data('startDate')) {
                        $whichSchedule.val(0);
                    } else {
                        for (i = 1; i < $opts.length; i++) {
                            startDate = $opts.eq(i).data('startDate');
                            if (startDate > targetDate) {
                                $whichSchedule.val(i - 1);
                                break;
                            }
                        }
                        if (i == $opts.length) {
                            $whichSchedule.val($opts.length - 1);
                        }
                    }
                    $whichSchedule.updateSchedule();
                }
            };

            // Update the GUI when user selects a new schedule
            $whichSchedule.updateSchedule = function () {
                var student;
                var schedule;
                student  = Students[$studentList.val()];
                schedule = student.schedules[$whichSchedule.val()];
                $checkboxes.initialize(schedule);
            };


            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Student Schedule" checkboxes
            ////////////////////////////////////////////////////////////////////////////////

            // Return true if any of the checkboxes have been modified - that is, have a value different from the one
            // stored in the database.
            $checkboxes.areModified = function () {
                return ($checkboxes.filter(function (index) {
                    return $(this).data('dbval') !== $(this).prop('checked');
                }).length > 0);
            };

            // Convert the checkboxes into something that can be passed in an HTTP request
            $checkboxes.serializeSchedule = function () {
                var temp  = {};
                var part;
                var day;
                var sched = [];
                $scheduleTable.find('tbody tr[data-day-part]').each(function (i, e) {
                    temp[$(e).data('day-part')] = ($(e).find('input:checked').map(function (i, e) {
                        return $(e).attr('name');
                    }).get());
                });

                for (part in temp) {
                    for (day in temp[part]) {
                        sched.push(temp[part][day] + part + '=On');
                    }
                }
                return sched.join('&');
            };

            // Set/clear the schedule checkbox
            $checkboxes.initialize = function (sched) {
                $checkboxes.each(function initializeScheduleCheckbox(i, e) {
                    $(e).prop('checked', sched[$(e).attr('name')][$(e).closest('tr').data('day-part')]);
                    $(e).data('dbval', sched[$(e).attr('name')][$(e).closest('tr').data('day-part')]);
                    $(e).removeClass('modified').prop('disabled', false).closest('td').removeClass('modified');
                });
            };

            // Clear all checkboxes
            $checkboxes.reset = function () {
                $checkboxes.each(function resetCheckbox(i, e) {
                    $(e).prop('checked', false);
                    $(e).data('dbval', false);
                    $(e).removeClass('modified').prop('disabled', false).closest('td').removeClass('modified');
                });
            };


            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Schedule Start Date" control
            ////////////////////////////////////////////////////////////////////////////////

            // Enable or disable the Start Date widget.  If enabling, initialize with the current date.  Otherwise,
            // clear the widget and remove the 'modified' class.
            $startDate.enable = function (b) {
                $startDate.prop('disabled', !b);
                if (true === b) {
                    if (!$startDate.val()) {
                        $startDate.datepicker('setDate', new Date());
                    }
                } else {
                    $startDate.val('').removeClass('modified');
                }
            };

            $endDate.enable = function (b) {
                $endDate.prop('disabled', !b);
                if (false === b) {
                    $endDate.val('').removeClass('modified');
                }
            };


            ////////////////////////////////////////////////////////////////////////////////
            // Methods bound to the "Delete Student" control
            ////////////////////////////////////////////////////////////////////////////////

            // Enable or disable the Delete Student control.
            $deleteStudent.enable = function (b) {
                $deleteStudent.prop('disabled', !b);
            };

            $deleteStudent.deleteStudent = function (studentId) {
                if (confirm('Are you sure you want to delete ' + Students[studentId].firstName + ' ' + Students[studentId].familyName + '?')) {
                    $.ajax({
                        url     : 'api/deleteStudent',
                        method  : 'post',
                        data    : { 'id': studentId },
                        dataType: 'json',
                        success : function onDeleteStudentSuccess(json) {
                            if (true != json.success) {
                                alert('Unable to delete student: ' + json.message);
                            } else {
                                delete Students[studentId];
                                $studentList.deleteStudent(studentId);
                                if (!$studentList.val()) {
                                    $studentList.resetStudentDetails();
                                }
                            }
                        },
                        'error' : function (xhr) {
                            console.log(xhr);
                            if (xhr.responseJSON) {
                                alert(xhr.responseJSON.message);
                            } else {
                                alert("Unhandled error");
                            }
                        }
                    });
                }
            }
        }


        function bindEvents() {
            // We can do this because we extended the jQuery 'show' function
            $page.on('show', function (event) {
                event.stopPropagation();

                $classFilter.empty();
                $classFilter.append($('<option>').text('Show All').val(''));
                $classFilter.append($('<option>').text('Unassigned').val(0));

                $classrooms.empty();
                $classrooms.append($('<option>').text('Unassigned').val(0));

                Classrooms.classrooms.forEach(function (classroom) {
                    $classrooms.addClassroom(classroom);
                    $classFilter.addClassroom(classroom);
                });

                $studentList.empty();
                Students.forEach(function (student) {
                    $studentList.addStudent(student);
                });
                $studentList.sort().resetStudentDetails();
            });


            $classFilter.on('change', function () {
                $studentList.filter();
            });

            $activeFilter.on('change', function () {
                $studentList.filter();
            });

            $studentList.on('change', function () {
                if ($(this).val()) {
                    $studentList.updateStudentDetails($(this).val());
                } else {
                    $studentList.resetStudentDetails();
                }
            });


            $studentList.on('show', 'option', function (event) {
                event.stopPropagation();
            });


            $newStudent.on('click', function () {
                $studentList.val('');
                $studentList.resetStudentDetails();
                $deleteStudent.prop('disabled', true);
                $inputs.val('').removeClass('modified').prop('disabled', false);

                $isEnrolled.prop('checked', false).prop('disabled', false);
                $classrooms.val(0).removeClass('modified').prop('disabled', false);

                $whichSchedule.empty().prop('disbaled', true);
                $checkAll.prop('disabled', false);
                $checkButtons.prop('disabled', false);
                $checkboxes.prop('checked', false).removeClass('modified').prop('disabled', false)
                    .closest('td').removeClass('modified');
                $startDate.datepicker('setDate', new Date());

                $saveButtons.prop('disabled', false);
                $cancelStudent.prop('disabled', false);

                $familyName.focus();
            });

            $deleteStudent.on('click', function () {
                $deleteStudent.deleteStudent($studentList.val());

            });

            // When the user changes the value in an input control, set the 'modified' class on that control if the new
            // value is different from the value in the database; clear the 'modified' class if the the new value is the
            // same as the value in the database.
            $inputs.on('change', function () {
                if ($(this).val() != $(this).data('dbval')) {
                    $(this).addClass('modified');
                } else {
                    $(this).removeClass('modified');
                }
            });

            $classrooms.on('change', function () {
                $(this).addClass('modified');
            });

            $whichSchedule.on('change', function () {
                $whichSchedule.updateSchedule();
            });

            // When a checkbox in the 'Schedule' table changes value, add the 'modified' class if the new value is
            // different from the value in the database.
            $checkboxes.on('change', function () {
                if ($(this).is(':checked') != $(this).data('dbval')) {
                    $(this).addClass('modified');
                    $(this).closest('td').addClass('modified');
                } else {
                    $(this).removeClass('modified');
                    $(this).closest('td').removeClass('modified');
                }

                // If any of the checkboxes in the Schedule table are modified, enable the start- and end-date widgets
                $startDate.enable($checkboxes.areModified());
                $endDate.enable($checkboxes.areModified());
            });

            // When the user clicks on the 'Check All' button above the 'Schedule' table, set all of the checkboxes
            // in the table, unless they ARE already set, in which case, clear them.
            $checkAll.on('click', function () {
                var $unchecked;
                $unchecked = $checkboxes.filter(':not(:checked)');
                if ($unchecked.length) {
                    $unchecked.trigger('click');
                } else {
                    $checkboxes.trigger('click');
                }
            });

            // When the user clicks on a column header in the 'Schedule' table, set all of the checkboxes in that
            // column, unless they ARE all already set, in which case, clear them.
            $thCheckers.on('click', 'button', function () {
                var index, $boxes, $unchecked;
                index      = ($(this).closest('th').index()) + 1;
                $boxes     = $scheduleTable.find('tr > td:nth-child(' + index + ') input[type=checkbox]');
                $unchecked = $boxes.filter(':not(:checked)');
                if ($unchecked.length) {
                    $unchecked.trigger('click');
                } else {
                    $boxes.trigger('click');
                }
            });

            // When the user clicks on a row header in the 'Schedule' table, set all of the checkboxes in that
            // row, unless the ARE all already set, in which case, clear them.
            $tdCheckers.on('click', 'button', function () {
                var $siblings, $boxes, $unchecked;
                $siblings  = $(this).closest('td').siblings();
                $boxes     = $siblings.find('input[type=checkbox]');
                $unchecked = $boxes.filter(':not(:checked)');
                if ($unchecked.length) {
                    $unchecked.trigger('click');
                } else {
                    $boxes.trigger('click');
                }
            });

            // Submit new student, or update selected student
            $saveButtons.on('click', function onSubmitStudentClick() {
                var $button,
                    studentId,
                    schedule;

                $button   = $(this);
                studentId = $studentList.val();
                if ($checkboxes.areModified()) {
                    schedule = $checkboxes.serializeSchedule();
                }
                if (!studentId) {
                    $.ajax({
                        url   : 'api/enrollStudent',
                        method: 'post',
                        data  : {
                            'familyName' : $familyName.val(),
                            'firstName'  : $firstName.val(),
                            'enrolled'   : $isEnrolled.is(':checked'),
                            'classroomId': $classrooms.val(),
                            'schedule'   : schedule,
                            'startDate'  : $startDate.val(),
                            'endDate'    : $endDate.val()
                        },

                        dataType: 'json',
                        success : function onEnrollStudentSuccess(json) {
                            if (!json.success) {
                                alert('Unable to enroll new student: ' + json.message);
                            } else {
                                json.student.schedules.forEach(function (schedule, i, schedules) {
                                    // ref: http://stackoverflow.com/questions/3075577/convert-mysql-datetime-stamp-into-javascripts-date-format
                                    var t                  = schedule.startDate.date.split(/[- :]/);
                                    schedules[i].startDate = new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5]);
                                });

                                Students[json.student.id] = json.student;
                                $studentList.addStudent(json.student)
                                    .sort()
                                    .filter($classFilter.val())
                                    .val(json.student.id)
                                    .updateStudentDetails(json.student.id);
                                if ($button.attr('name') == $saveAndAnother.attr('name')) {
                                    $newStudent.trigger('click');
                                } else if ($button.attr('name') === $saveAndClose.attr('name')) {
                                    $cancelStudent.trigger('click');
                                }
                            }
                        },
                        'error' : function (xhr) {
                            console.log(xhr);
                            if (xhr.responseJSON) {
                                alert(xhr.responseJSON.message);
                            } else {
                                alert("Unhandled error");
                            }
                        }
                    });

                } else {
                    $.ajax({
                        url     : 'api/updateStudent',
                        method  : 'post',
                        data    : {
                            'id'         : studentId,
                            'familyName' : $familyName.val(),
                            'firstName'  : $firstName.val(),
                            'enrolled'   : $isEnrolled.is(':checked'),
                            'classroomId': $classrooms.val(),
                            'schedule'   : schedule,
                            'startDate'  : $startDate.val(),
                            'endDate'    : $endDate.val()
                        },
                        dataType: 'json',
                        success : function updateStudentSuccess(json) {
                            if (!json.success) {
                                alert('Unable to update student: ' + json.message);
                            } else {
                                json.student.schedules.forEach(function (schedule, i, schedules) {
                                    // ref: http://stackoverflow.com/questions/3075577/convert-mysql-datetime-stamp-into-javascripts-date-format
                                    var t                  = schedule.startDate.date.split(/[- :]/);
                                    schedules[i].startDate = new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5]);
                                });
                                Students[json.student.id] = json.student;
                                $studentList.updateStudent(json.student)
                                    .val(json.student.id)
                                    .updateStudentDetails(json.student.id);
                                if ($button.attr('name') == $saveAndAnother.attr('name')) {
                                    $newStudent.trigger('click');
                                } else if ($button.attr('name') === $saveAndClose.attr('name')) {
                                    $cancelStudent.trigger('click');
                                }
                            }
                        },
                        'error' : function (xhr) {
                            console.log(xhr);
                            if (xhr.responseJSON) {
                                alert(xhr.responseJSON.message);
                            } else {
                                alert("Unhandled error");
                            }
                        }
                    });
                }
            });

            // Cancel all modifications made so far
            $cancelStudent.on('click', function () {
                $studentList.val('');
                $studentList.resetStudentDetails();
            })
        }


        // Empty all data from the page
        function empty() {
            $studentList.resetStudentDetails();
            $studentList.empty();
        }

        function addStudent(student) {
            var source;
            var template;
            var html;

            source   = $('#enrollment-list-option-template').html();
            template = Handlebars.compile(source);
            html     = template(student);
            $studentList.append($(html));
        }

        // Removes a student from the table (does not affect the database)
        function removeStudent(studentId) {
            $table.find('tr[data-student-id=' + studentId + ']').remove();
        }

        function addClassroom(classroom) {
            if (undefined == classroom) throw 'Classroom definition required.';

            [$classrooms, $classFilter].forEach(function (e, i, arr) {
                var $opt;
                $opt = $('<option>');
                $opt.val(classroom.id).text(classroom.name);
                $(e).append($opt);
            });
        }

        publicApi = {
            init         : init,
            clear        : empty,
            addStudent   : addStudent,
            removeStudent: removeStudent,
            addClassroom : addClassroom
        };

        return publicApi;
    })();


    /********************************************************************************
     * Application-wide Functions
     ********************************************************************************/

    // Return a new Date object for midnight of the current day
    function getToday() {
        var now = new Date();
        return new Date(now.getFullYear(), now.getMonth(), now.getDate());
    }

    function formatTime(date) {
        var hh, mm, ss;
        if (date == undefined) {
            return '';
        }
        if (typeof date === 'number') {
            date = new Date(date);
        }
        hh = date.getHours();
        mm = date.getMinutes();
        if (mm < 10) {
            mm = '0' + mm;
        }
        ss = date.getSeconds();
        if (ss < 10) {
            ss = '0' + ss;
        }
        return (hh + ':' + mm + ':' + ss );
    }

    function formatDate(date) {
        if (date == undefined) {
            return '';
        }
        if (typeof date === 'number') {
            date = new Date(date);
        }
        return (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear();
    }

    function attendanceSheetDate(date) {
        return 'spb';
    }


    /********************************************************************************
     * Document on-ready handler
     ********************************************************************************/
    $(function () {
        var $tabs   = $('.tab');  // All of the tabs in the top-level menu, <a href="#target">
        var oldhash = '';           // Previous hash fragment in URL


        // DOM elements identified by the href attributes in the $tabs
        var targets = $tabs.map(function () {
            return this.hash;   // Return the anchor part of the URL
        }).get();

        //
        var $panels = $(targets.join(','));

        function showPage(id) {
            // If no value was given, let's take the first panel
            if (!id) id = targets[0];
            else id = id.split('/')[0];
            $tabs.removeClass('active').filter(function () {
                return (this.hash === id);
            }).addClass('active');
            $panels.hide();
            var $panel = $panels.filter(id);
            $panel.show();
        }

        // Prevent user from leaving a panel with unsaved changes
        $(window).on('hashchange', function () {
            if (( $panels.filter(':visible').find('.modified').length ) && ( location.hash !== oldhash )) {
                alert("You have unsaved changes on this page");
                location.hash = oldhash;
                return false;
            } else {
                oldhash = location.hash;
                showPage(location.hash);
            }
        });

        // Warn user  if they try to leave the page with unsaved changes
        $(window).on('beforeunload', function () {
            if ($panels.filter(':visible').find('.modified').length) {
                return 'Are you sure you want to leave?';
            }
        });


        Handlebars.registerHelper('formatTime', formatTime);
        Handlebars.registerHelper('formatDate', formatDate);
        Handlebars.registerHelper('attendanceSheetDate', function (date) {
            var days   = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return new Handlebars.SafeString(days[date.getDay()] + '<br />' + date.getDate() + '-' + months[date.getMonth()]);
        });


        CheckinPage.init('#checkin-page');
        AttendancePage.init('#attendance-page');
        SigninPage.init('#signin-page');
        ReportsPage.init('#reports');
        //EnrollmentPage.init( '#enrollment-page' );
        //ClassroomPage.init('#classes-page');

        var wait = 2;

        Classrooms.init();
        Students.init();
        Schedules.init();

        ClassroomsPanel.init('#classrooms-page');
        ClassroomsTable.init('#classrooms-table');
        ClassroomPropsDlg.init('#classroom-dlg');

        StudentsPanel.init('#enrollment-page');
        StudentsTable.init('#students-table');
        StudentPropsDlg.init('#student-dlg');

        ScheduleDlg.init('#schedule-dlg');

        ClassroomController.load();
        StudentController.load();
        SchedulesController.load();

        if (targets.indexOf(location.hash) !== 1) {
            oldhash = location.hash;
            showPage(location.hash);
        } else {
            oldhash = '';
            showPage('');
        }
    });


})(this, jQuery);
