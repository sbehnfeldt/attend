<?php

class Api
{
    /** @var \PDO */
    private $pdo;

    /**
     * Api constructor.
     *
     * @param \PDO|NULL $pdo
     */
    public function __construct(\PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * @return PDO
     */
    private function getPdo() {
        if ( null === $this->pdo ) {
            // @TODO: get these values from a config file
            $host = 'localhost';
            $dbname = 'attend';
            $uname = 'attend';
            $pword = 'attend';
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $uname, $pword);
        }
        return $this->pdo;
    }

    /**
     * @param PDO $pdo
     */
    public function setPdo( $pdo ) {
        $this->pdo = $pdo;
    }


    ////////////////////////////////////////////////////////////////////////////////
    // Classrooms
    ////////////////////////////////////////////////////////////////////////////////
    public function fetchClassrooms()
    {
        $classrooms = [];
        $rows = $this->getPdo()->query('select * from classrooms');
        foreach ($rows as $row) {
            $classrooms[] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }
        return $classrooms;
    }

    function submitClassroom($params)
    {
        $sql = 'insert into classrooms (name) values (:name)';
        $stmt = $this->getPdo()->prepare($sql);
        $ex = $stmt->execute([
            ':name' => $params['name']
        ]);
        return [
            'id' => $this->getPdo()->lastInsertId(),
            'name' => $params['name']
        ];
    }


    ////////////////////////////////////////////////////////////////////////////////
    // Students
    ////////////////////////////////////////////////////////////////////////////////
    public function fetchStudents()
    {
        $students = [];
        $rows = $this->getPdo()->query('select id, family_name, first_name, enrolled, classroom_id from students');
        foreach ($rows as $row) {
            $students[] = [
                'id' => $row['id'],
                'familyName' => $row['family_name'],
                'firstName' => $row['first_name'],
                'enrolled' => $row['enrolled'] ? true : false,
                'classroomId' => $row['classroom_id']
            ];
        }
        return $students;
    }

    public function fetchStudent($studentId)
    {
        $sql = 'select id, family_name, first_name, enrolled, classroom_id from students where id = :id';
        $stmt = $this->getPdo()->prepare($sql);
        $ex = $stmt->execute([
            ':id' => $studentId
        ]);
        foreach ($stmt->fetch() as $row) {
            $student = [
                'id' => $row['id'],
                'familyName' => $row['family_name'],
                'firstName' => $row['first_name'],
                'enrolled' => $row['enrolled'] ? true : false,
                'classroomId' => $row['classroom_id']
            ];
        }
        return $student;
    }

    function submitStudent($params)
    {
        $sql = 'insert into students (family_name, first_name, enrolled, classroom_id) values (:family_name, :first_name, :enrolled, :classroom_id)';
        $stmt = $this->getPdo()->prepare($sql);
        $ex = $stmt->execute([
            ':family_name' => $params['familyName'],
            ':first_name' => $params['firstName'],
            ':enrolled' => $params['enrolled'] ? 1 : 0,
            ':classroom_id' => $params['classroomId'] ? intval($params['classroomId']) : null
        ]);

        $student = [
            'id' => $this->getPdo()->lastInsertId(),
            'familyName' => $params['familyName'],
            'firstName' => $params['firstName'],
            'enrolled' => $params['enrolled'],
            'classroomId' => (string)$params['classroomId']
        ];
        return $student;
    }

    function updateStudent($params)
    {
        $sql = 'UPDATE students SET family_name = :family_name, first_name = :first_name, enrolled = :enrolled, classroom_id = :classroom_id WHERE id = :id';
        $stmt = $this->getPdo()->prepare($sql);
        $ex = $stmt->execute([
            ':family_name' => $params['familyName'],
            ':first_name' => $params['firstName'],
            ':enrolled' => $params['enrolled'],
            ':classroom_id' => $params['classroomId'] ? intval($params['classroomId']) : null,
            ':id' => intval( $_POST[ 'id' ])
        ]);

        $student = [
            'id' => $params['id'],
            'familyName' => $params['familyName'],
            'firstName' => $params['firstName'],
            'enrolled' => $params['enrolled'],
            'classroomId' => $params['classroomId']
        ];
        return $student;
    }

    function deleteStudent($studentId)
    {
        $sql = 'delete from students where id = :id and enrolled = 0';
        $stmt = $this->getPdo()->prepare($sql);
        $ex = $stmt->execute([
            ':id' => $studentId
        ]);
    }


    ////////////////////////////////////////////////////////////////////////////////
    // Schedules
    ////////////////////////////////////////////////////////////////////////////////
    function fetchSchedules($studentId)
    {
        $schedules = [];
        $sql = 'select * from schedules where student_id = :student_id';
        $stmt = $this->getPdo()->prepare($sql);
        $ex = $stmt->execute([':student_id' => $studentId]);
        while ( false !== ($row = $stmt->fetch(PDO::FETCH_ASSOC))) {
            $schedules[] = [
                'id' => $row['id'],
                'mon' => [
                    'Am' => $row['mon_am'] ? true : false,
                    'Noon' => $row['mon_noon'] ? true : false,
                    'Pm' => $row['mon_pm'] ? true : false,
                ],
                'tue' => [
                    'Am' => $row['tue_am'] ? true : false,
                    'Noon' => $row['tue_noon'] ? true : false,
                    'Pm' => $row['tue_pm'] ? true : false,
                ],
                'wed' => [
                    'Am' => $row['wed_am'] ? true : false,
                    'Noon' => $row['wed_noon'] ? true : false,
                    'Pm' => $row['wed_pm'] ? true : false,
                ],
                'thu' => [
                    'Am' => $row['thu_am'] ? true : false,
                    'Noon' => $row['thu_noon'] ? true : false,
                    'Pm' => $row['thu_pm'] ? true : false,
                ],
                'fri' => [
                    'Am' => $row['fri_am'] ? true : false,
                    'Noon' => $row['fri_noon'] ? true : false,
                    'Pm' => $row['fri_pm'] ? true : false,
                ],
                'startDate' => new DateTime($row['start_date']),
                'enteredAt' => $row['entered_at']
            ];
        }
        return $schedules;
    }


    function submitSchedule($studentId, $params)
    {
        parse_str($params['schedule'], $sched);
        $startDate = explode('/', $params['startDate']);
        $startDate = implode('-', [$startDate[2], $startDate[0], $startDate[1]]);
        $now = time();

        $sql = 'INSERT INTO schedules (student_id, mon_am, mon_noon, mon_pm, tue_am, tue_noon, tue_pm, wed_am, wed_noon, wed_pm, thu_am, thu_noon, thu_pm, fri_am, fri_noon, fri_pm, start_date, entered_at)
      VALUES (:student_id, :mon_am, :mon_noon, :mon_pm, :tue_am, :tue_noon, :tue_pm, :wed_am, :wed_noon, :wed_pm, :thu_am, :thu_noon, :thu_pm,
        :fri_am, :fri_noon, :fri_pm, :start_date, :entered_at)
        ON DUPLICATE KEY UPDATE
          mon_am = :mon_am, mon_noon = :mon_noon, mon_pm = :mon_pm,
          tue_am = :tue_am, tue_noon = :tue_noon, tue_pm = :tue_pm,
          wed_am = :wed_am, wed_noon = :wed_noon, wed_pm = :wed_pm,
          thu_am = :thu_am, thu_noon = :thu_noon, thu_pm = :thu_pm,
          fri_am = :fri_am, fri_noon = :fri_noon, fri_pm = :fri_pm
          ';

        $stmt = $this->getPdo()->prepare($sql);
        $ex = $stmt->execute([
            ':student_id' => intval($studentId),
            ':mon_am' => array_key_exists( 'monAm', $sched) ? 1 : 0,
            ':mon_noon' => array_key_exists( 'monNoon', $sched) ? 1 : 0,
            ':mon_pm' => array_key_exists( 'monPm', $sched) ? 1 : 0,
            ':tue_am' => array_key_exists( 'tueAm', $sched) ? 1 : 0,
            ':tue_noon' => array_key_exists( 'tueNoon', $sched) ? 1 : 0,
            ':tue_pm' => array_key_exists( 'tuePm', $sched) ? 1 : 0,
            ':wed_am' => array_key_exists( 'wedAm', $sched) ? 1 : 0,
            ':wed_noon' => array_key_exists( 'wedNoon', $sched) ? 1 : 0,
            ':wed_pm' => array_key_exists( 'wedPm', $sched) ? 1 : 0,
            ':thu_am' => array_key_exists( 'thuAm', $sched) ? 1 : 0,
            ':thu_noon' => array_key_exists( 'thuNoon', $sched) ? 1 : 0,
            ':thu_pm' => array_key_exists( 'thuPm', $sched) ? 1 : 0,
            ':fri_am' => array_key_exists( 'friAm', $sched) ? 1 : 0,
            ':fri_noon' => array_key_exists( 'friNoon', $sched) ? 1 : 0,
            ':fri_pm' => array_key_exists( 'friPm', $sched) ? 1 : 0,
            ':start_date' => $startDate,
            ':entered_at' => $now
        ]);

        return [
            'id' => $this->getPdo()->lastInsertId(),
            'mon' => [
                'Am' => array_key_exists( 'monAm', $sched) ? true : false,
                'Noon' => array_key_exists( 'monNoon', $sched) ? true : false,
                'Pm' => array_key_exists( 'monPm', $sched) ? true : false,
            ],
            'tue' => [
                'Am' => array_key_exists( 'tueAm', $sched) ? true : false,
                'Noon' => array_key_exists( 'tueNoon', $sched) ? true : false,
                'Pm' => array_key_exists( 'tuePm', $sched) ? true : false,
            ],
            'wed' => [
                'Am' => array_key_exists( 'wedAm', $sched) ? true : false,
                'Noon' => array_key_exists( 'wedNoon', $sched) ? true : false,
                'Pm' => array_key_exists( 'wedPm', $sched) ? true : false,
            ],
            'thu' => [
                'Am' => array_key_exists( 'thuAm', $sched) ? true : false,
                'Noon' => array_key_exists( 'thuNoon', $sched) ? true : false,
                'Pm' => array_key_exists( 'thuPm', $sched) ? true : false,
            ],
            'fri' => [
                'Am' => array_key_exists( 'friAm', $sched) ? true : false,
                'Noon' => array_key_exists( 'friNoon', $sched) ? true : false,
                'Pm' => array_key_exists( 'friPm', $sched) ? true : false,
            ],
            'startDate' => new DateTime($startDate),
            'enteredAt' => $now
        ];
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Attendance
    ////////////////////////////////////////////////////////////////////////////////
    function fetchAttendance($studentId)
    {
        $attendances = [];
        $sql = 'select * from attendance where student_id = :student_id';
        $stmt = $this->getPdo()->prepare($sql);
        $ex = $stmt->execute([
            ':student_id' => $studentId
        ]);
        while ( false !== ($row = $stmt->fetch(PDO::FETCH_ASSOC))) {
            $attendances[] = [
                'id' => $row['id'],
                'studentId' => $row['student_id'],
                'checkIn' => $row['check_in'],
                'checkOut' =>$row['check_out']
            ];
        }
        return $attendances;
    }

    function checkIn($studentId, $checkin)
    {
        $sql = 'insert into attendance (student_id, check_in) values (:student_id, :check_in)';
        $stmt = $this->getPdo()->prepare($sql);
        if ( false  === $stmt->execute([
                ':student_id' => $studentId,
                ':check_in' => $checkin
            ])) {
            throw new Exception('Database error');
        };

        return [
            'id' => $this->getPdo()->lastInsertId(),
            'studentId' => $studentId,
            'checkIn' => $checkin,
            'checkOut' => null
        ];
    }

    function checkOut($studentId, $checkout)
    {
        $sql = 'select * from attendance where student_id = :student_id';
        $stmt = $this->getPdo()->prepare($sql);
        if ( false === $stmt->execute([':student_id' =>$studentId])) {
            throw new Exception('Database error');
        }

        $attendances = $stmt->fetchAll();
        $latest = array_pop( $attendances );
        if (( null == $latest ) ||  (null !==  $latest['check_out'])) {
            $sql = 'insert into attendance (student_id, check_out) values (:student_id, :check_out)';
            $stmt = $this->getPdo()->prepare($sql);
            $stmt->execute([
                ':student_id' => $studentId,
                ':check_out' => $checkout
            ]);
            $attendance = [
                'id' => $this->getPdo()->lastInsertId(),
                'studentId' => $studentId,
                'checkIn' => null,
                'checkOut' => $checkout
            ];
        } else {
            $sql = 'update attendance set check_out = :check_out where id = :id';
            $stmt = $this->getPdo()->prepare($sql);
            $stmt->execute([
                ':check_out' => $checkout,
                ':id' => $latest['id']
            ]);
            $attendance = [
                'id' => $latest['id'],
                'studentId' => $studentId,
                'checkIn' => $latest['check_in'],
                'checkOut' => $checkout
            ];
        }
        return $attendance;
    }
}
