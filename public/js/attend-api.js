;(function(global, $) {
    'use strict';

    global.AttendApi = (function() {

        let classrooms = {
            // Fetch all classroom data
            select: async function() {
                const response = await fetch('/api/classrooms');
                if (!response.ok) {
                    // throw new Error(`HTTP error status: ${response.status}`)
                    console.log(`HTTP error status: ${response.status}`);
                    return [];
                }
                let json = await response.json();
                return json.data;   // All classroom data
            },

            // Insert a new classroom record
            insert: async function(data) {
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
            },

            // Update an existing classroom record
            update: async function (data) {
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
            },

            // Delete an existing classroom record
            remove: async function (id) {
                const response = await fetch(`/api/classrooms/${id}`, {
                    method: 'DELETE'
                });
                if (!response.ok) {
                    // throw new Error(`HTTP error status: ${response.status}`)
                    console.log(`HTTP error status: ${response.status}`);
                }
                return;
            }
        }

        let students = {
            // Fetch all student data
            select: async function() {
                const response = await fetch('/api/students');
                if (!response.ok) {
                    // throw new Error(`HTTP error status: ${response.status}`)
                    console.log(`HTTP error status: ${response.status}`);
                    return [];
                }
                let json = await response.json();
                return json.data;   // All classroom data
            },

            // Insert a new student record
            insert: async function(data) {
                const response = await fetch('/api/students', {
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
            },

            // Update an existing student record
            update: async function (data) {
                const response = await fetch(`/api/students/${data.Id}`, {
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
            },

            // Delete an existing student record
            remove: async function (id) {
                const response = await fetch(`/api/students/${id}`, {
                    method: 'DELETE'
                });
                if (!response.ok) {
                    // throw new Error(`HTTP error status: ${response.status}`)
                    console.log(`HTTP error status: ${response.status}`);
                }
                return;
            }
        }

        return {classrooms, students};
    })();

})(this, jQuery);
