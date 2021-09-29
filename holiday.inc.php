<?php

class Date2 {
    function __construct($d,$m) {
        $this->day = $d;
        $this->month = $m;
    }

    public $day;
    public $month;
}

class Holiday {
    private $year;
    public $table;
    private $is_leap;
    private $day_offset // Each month's offset from March
    = [-999999,                      // Offset 0 - not used
      -31-28,                        // Jan
      -28,                           // Feb
       0,                            // Mar
       31,                           // Apr
       31+30,                        // May
       31+30+31,                     // Jun
       31+30+31+30,                  // Jul
       31+30+31+30+31,               // Aug
       31+30+31+30+31+31,            // Sep
       31+30+31+30+31+31+30,         // Oct
       31+30+31+30+31+31+30+31,      // Nov
       31+30+31+30+31+31+30+31+30,   // Dec
       31+30+31+30+31+31+30+31+30+31 // End-of-year
    ];
    public $sundays_after_epiphany; // Not counting last Sunday after Epiphany
    public $sundays_after_trinity;  // Not counting last Sunday after Trinity
    private $first_dominical_letter;  // Jan, Feb  (0=C, 1=D, 2=E, 3=F, 4=G, 5=A, 6=B)
    private $second_dominical_letter; // Mar,...  (0=C, 1=D, 2=E, 3=F, 4=G, 5=A, 6=B)

    
    function __construct($yr) {
        $this->year = $yr;
        $this->is_leap = $this->year%400==0 || $this->year%100!=0 && $this->year%4==0;
        if ($this->is_leap) {
            --$this->day_offset[1];  // Jan
            --$this->day_offset[2];  // Feb
        }
        $this->set_table();

        $this->second_dominical_letter = $this->table % 7;
        $this->first_dominical_letter = ($this->table + $this->is_leap) % 7;

        $this->sundays_after_epiphany = intdiv($this->table + $this->is_leap + 3,7);
        $this->sundays_after_trinity = intdiv(187-$this->table,7);
    }

    function get_year() {
        return $this->year;
    }

    function get_table() {
        return $this->table;
    }

    private function set_table() {
        $G = $this->year % 19;

        $C = intdiv($this->year,100);
        $H = ($C - intdiv($C,4) - intdiv(8*$C+13,25) + 19*$G + 15) % 30;
        $I = $H - intdiv($H,28)*(1 - intdiv(29,$H + 1)*intdiv(21 - $G,11));
        $J = ($this->year + intdiv($this->year,4) + $I + 2 - $C + intdiv($C,4)) % 7;

        $this->table = $I - $J + 7;
    }

    function day2daymonth($day) {
        $found = false;
        for ($month=1; $day>$this->day_offset[$month]; ++$month)
            /* Nothing */;
        
        --$month; // for loop went one too far
        return new Date2($day - $this->day_offset[$month], $month);
    }
        
    // Return date number for Easter + the indicated number of days
    // Use to retrieve dates for Last sunday after Epiphany ($days==-70)
    // to Nth sunday after Trinity ($weeks==56+N*7)
    private function get_easter_with_offset_number($offset) {
        // $day is days relative to 0th March
        return $this->table + 21 + $offset;
    }
    
    // Return date for Easter + the indicated number of days
    // Use to retrieve dates for Last sunday after Epiphany ($days==-70)
    // to Nth sunday after Trinity ($weeks==56+N*7)
    function get_easter_with_offset($offset) {
        return $this->day2daymonth($this->get_easter_with_offset_number($offset));
    }

    function get_newyear() {
        return new Date2(1,1);
    }

    function get_epiphany_sunday() {
        switch ($this->first_dominical_letter) {
            case 0:
            case 1:
            case 2:
            case 3:
                return new Date2($this->first_dominical_letter+3,1);
            case 4:
            case 5:
                return null;
            case 6:
                return new Date2($this->first_dominical_letter-4,1);
        }
    }

    // Return n'th Sunday after Epiphany
    function get_sunday_after_epiphany($n) {
        if ($n>$this->sundays_after_epiphany)
            return null;
        
        if ($this->first_dominical_letter<=3)
            return $this->day2daymonth($this->first_dominical_letter+3 + $this->day_offset[1] + $n*7);
        else
            return $this->day2daymonth($this->first_dominical_letter-4 + $this->day_offset[1] + $n*7);
    }

    private function get_all_saints_sunday_number() {
        if ($this->second_dominical_letter==0)
            return 7 + $this->day_offset[11];
        else
            return $this->second_dominical_letter + $this->day_offset[11];
    }

    function get_all_saints_sunday() {
        return $this->day2daymonth($this->get_all_saints_sunday_number());
    }
    

    // Returns true if the n'th Sunday after Trinity is really All Saints' Sunday
    function sunday_after_trinity_is_all_saints($n) {
        return $n>=20 &&
               $this->get_all_saints_sunday_number()==$this->get_easter_with_offset_number(56 + $n*7);
    }

    // Returns true if the n'th holiday number is really All Saints' Sunday
    function holiday_is_all_saints($n) {
        return $this->sunday_after_trinity_is_all_saints($n-31);
    }

    // Returns n'th Sunday after Trinity
    function get_sunday_after_trinity($n) {
        if ($n>$this->sundays_after_trinity)
            return null;

        return $this->day2daymonth($this->get_easter_with_offset_number(56 + $n*7));
    }

    function format_date(Date2 $d2, bool $with_year=false) {
        if ($with_year)
            return "$d2->day.$d2->month.$this->year";
        else
            return "$d2->day.$d2->month";
    }

    // Returns n'th Sunday in Advent.
    // For $n==0, returns last Sunday in liturgical year
    function get_advent($n) {
        if ($this->second_dominical_letter==6)
            return $this->day2daymonth(20 + $n*7 + $this->day_offset[11]);
        else
            return $this->day2daymonth(21 + $this->second_dominical_letter + $n*7 + $this->day_offset[11]);
    }

    function get_christmas_eve() {
        return new Date2(24,12);
    }

    function get_christmas_day() {
        return new Date2(25,12);
    }

    function get_st_stephens_day() {
        return new Date2(26,12);
    }

    function get_christmas_sunday() {
        if ($this->first_dominical_letter==0 || $this->first_dominical_letter==6)
            return null;
        else
            return new Date2($this->second_dominical_letter+26,12);
    }

    function get_holiday_from_number($hn) {
        switch ($hn) {
            case 0:
                return $this->get_newyear();
            case 1:
                return $this->get_epiphany_sunday();
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
                return $this->get_sunday_after_epiphany($hn-1);
           case 7: case 8: case 9: case 10: case 11: case 12: case 13: case 14: case 15: case 16:
                return $this->get_easter_with_offset($hn*7 - 119);
            case 17:
                return $this->get_easter_with_offset(-3);
            case 18:
                return $this->get_easter_with_offset(-2);
            case 19:
                return $this->get_easter_with_offset(0);
            case 20:
                return $this->get_easter_with_offset(1);
            case 21: case 22: case 23:
                return $this->get_easter_with_offset($hn*7 - 140);
            case 24:
                return $this->get_easter_with_offset(26);
            case 25: case 26:
                return $this->get_easter_with_offset($hn*7 - 147);
            case 27:
                return $this->get_easter_with_offset(39);
            case 28: case 29:
                return $this->get_easter_with_offset($hn*7 - 154);
            case 30:
                return $this->get_easter_with_offset(50);
            case 31:
                return $this->get_easter_with_offset(56);
            case 32: case 33: case 34: case 35: case 36: case 37: case 38: case 39: case 40: case 41:
            case 42: case 43: case 44: case 45: case 46: case 47: case 48: case 49: case 50: case 51:
            case 52: case 53: case 54: case 55: case 56: case 57: case 58:
                return $this->get_sunday_after_trinity($hn-31);
            case 59: case 60: case 61: case 62: case 63:
                return $this->get_advent($hn-59);
            case 64:
                return $this->get_christmas_eve();
            case 65:
                return $this->get_christmas_day();
            case 66:
                return $this->get_st_stephens_day();
            case 67:
                return $this->get_christmas_sunday();
            default:
                return null;
        }
    }

    function get_holiday_name_from_number($hn) {
        switch ($hn) {
            case 0:
                return "Nytårsdag";
            case 1:
                return "Hellig tre kongers søndag";
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
                $n = $hn-1;
                return "$n. søndag efter helligtrekonger";
            case 7:
                return "Sidste søndag efter helligtrekonger";
            case 8:
                return "Søndag septuagesima";
            case 9:
                return "Søndag seksagesima";
            case 10:
                return "Fastelavns søndag";
            case 11:
            case 12:
            case 13:
                $n = $hn-10;
                return "$n. søndag i fasten";
            case 14:
                return "Midfaste søndag";
            case 15:
                return "Mariæ bebudelses dag";
            case 16:
                return "Palmesøndag";
            case 17:
                return "Skærtorsdag";
            case 18:
                return "Langfredag";
            case 19:
                return "Påskedag";
            case 20:
                return "Anden påskedag";
            case 21:
            case 22:
            case 23:
                $n = $hn-20;
                return "$n. søndag efter påske";
            case 24:
                return "Bededag";
            case 25:
            case 26:
                $n = $hn-21;
                return "$n. søndag efter påske";
            case 27:
                return "Kristi himmelfarts dag";
            case 28:
                return "6. søndag efter påske";
            case 29:
                return "Pinsedag";
            case 30:
                return "Anden pinsedag";
            case 31:
                return "Trinitatis søndag";
            case 32: case 33: case 34: case 35: case 36: case 37: case 38: case 39: case 40: case 41:
            case 42: case 43: case 44: case 45: case 46: case 47: case 48: case 49: case 50: case 51:
            case 52: case 53: case 54: case 55: case 56: case 57: case 58:
                $n = $hn-31;
                if ($this->sunday_after_trinity_is_all_saints($n))
                    return "Alle helgens søndag";
                else
                    return "$n. søndag efter trinitatis";
            case 59:
                return "Sidste søndag i kirkeåret";
            case 60:
            case 61:
            case 62:
            case 63:
                $n = $hn-59;
                return "$n. søndag i advent";
            case 64:
                return "Juleaften";
            case 65:
                return "Juledag";
            case 66:
                return "Anden juledag / Sankt Stefans dag";
            case 67:
                return "Julesøndag";
            default:
                return null;
        }
    }
}
