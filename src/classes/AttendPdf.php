<?php


class AttendPdf extends FPDF
{

    /** @var  array */
    protected static $dayAbbrevs = ['mon', 'tue', 'wed', 'thu', 'fri' ];

    /** @var Api */
    protected $api;

    /** @var array */
    protected $theClassroom;

    /** @var  array */
    protected $colWidths;

    /** @var  int */
    protected $rowHeight;

    /** @var  int */
    protected $headerHeight;

    /** @var  DateTime */
    protected $weekOf;

    public function __construct($api, $orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        $this->api = $api;
        $this->theClassroom = null;
        $this->colWidths = [];
        $this->headerHeight = 5;
        $this->rowHeight = 10;
        $this->weekOf = new DateTime();
        parent::__construct($orientation, $unit, $size);
    }

    /**
     * @return Api
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param Api $api
     */
    public function setApi($api)
    {
        $this->api = $api;
    }

    /**
     * @return array
     */
    public function getTheClassroom()
    {
        return $this->theClassroom;
    }

    /**
     * @param array $theClassroom
     */
    public function setTheClassroom($theClassroom)
    {
        $this->theClassroom = $theClassroom;
    }

    /**
     * @return array
     */
    public function getColWidths()
    {
        return $this->colWidths;
    }

    /**
     * @param array $colWidths
     */
    public function setColWidths($colWidths)
    {
        $this->colWidths = $colWidths;
    }

    /**
     * @return int
     */
    public function getRowHeight()
    {
        return $this->rowHeight;
    }

    /**
     * @param int $rowHeight
     */
    public function setRowHeight($rowHeight)
    {
        $this->rowHeight = $rowHeight;
    }

    /**
     * @return int
     */
    public function getHeaderHeight()
    {
        return $this->headerHeight;
    }

    /**
     * @param int $headerHeight
     */
    public function setHeaderHeight($headerHeight)
    {
        $this->headerHeight = $headerHeight;
    }

    /**
     * @return DateTime
     */
    public function getWeekOf()
    {
        return $this->weekOf;
    }

    /**
     * @param string $weekOf
     */
    public function setWeekOf($weekOf)
    {
        $this->weekOf = new DateTime($weekOf);
    }

    /**
     * @return array
     */
    public static function getDayAbbrevs()
    {
        return self::$dayAbbrevs;
    }


    /**
     * @param $student
     *
     * From all of a student's schedules, build a composite schedule effective
     * the week of $this->startDate
     */
    public function getCompositeSchedule($student)
    {
        $composite = null;
        $schedules = $this->getApi()->fetchSchedules($student['id']);

        return $schedules[0];
//        return $composite;
    }
}
