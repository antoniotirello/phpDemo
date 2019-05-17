<?php

/**
 * This class build the schema for the calendar calculating the day-of-week of every days.
 * 
 * ToDo:
 * - Internationalization (headers and name)
 * - Add the fixed holidays
 * - Add the variable holidays (Easter)
 * - Change the stdClass in a regular class with method like isHolyday() and so on
 */
class calendar
{
    /**
     * return the schema of the given year:
     * ->array header
     * ->array headerLong
     * ->array plain 
     * ->array schema   ->int number
     *                  ->string name
     *                  ->string year
     *                  ->int numberOfDays
     *                  ->array weeks [][int]
     * 
     * @param int $year
     * @return \stdClass
     */
    public function getCalendarTable($year)
    {
        $return = new stdClass();
        
        //Build the week header
        $return->header = array(1=>"M", 2=>"T", 3=>"W", 4=>"T", 5=>"F", 6=>"S", 7=>"S");
        $return->headerLong = array(1=>"Monday", 2=>"Tuesday", 3=>"Wednesday", 4=>"Thursday", 5=>"Friday", 6=>"Saturday", 7=>"Sunday");
        
        /**
         * Note that the loop use 14 month and not 12. 
         * This is because I want the before and after month also for december and jenuary. 
         */
        for($month=0; $month<=13; $month++)
        {
            $monthSchema = new stdClass();
            $monthSchema->number = $month;
            $monthSchema->name = date('F', mktime(1, 0, 0, $month, 1, $year));
            $monthSchema->year = date('Y', mktime(1, 0, 0, $month, 1, $year));
            
            //Retrive the number of days of the current month
            $monthSchema->numberOfDays = date('t', mktime(1, 0, 0, $month, 1, $year))*1;
            
            $monthSchema->weeks = array();
            
            for($day = 1; $day <= $monthSchema->numberOfDays; $day++)
            {
                $currentDay =  mktime(1, 0, 0, $month, $day, $year);
                $currentWeek = date('W',$currentDay);
                
                //Used for little calendar
                $monthSchema->weeks[$currentWeek][date('N',$currentDay)] = $day;                                
                //Used for big calendar
                $return->plain[$month][$day] = date('N',$currentDay);
            }
            $return->schema[$month] = $monthSchema;
        }
        
        //debug
        //print('<pre>'); var_dump($return); exit();
        
        return $return;
    }
}