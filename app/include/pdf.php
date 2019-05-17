<?php

    /**
     * The pdf creator
     */
    class pdfGenerator  extends TCPDF
    {
        private $pdf;
        private $pageWidth;
        private $pageHeight;
        private $calendar;
        
        const DAY_WIDTH = 15;
        const DAY_HEIGHT = 15;
        
        function __construct() 
        {
            parent::__construct();
            $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            $this->calendar = new calendar();
            
            $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            
            $this->pdf->SetMargins(1, 1, 1);
            $this->pdf->setPrintHeader(false);
            $this->pdf->setPrintFooter(false);
            
            //Portrait mode
            $this->pdf->setPageOrientation('P', true, 0) ;

            //Using poit as unit for clarity
            $this->pdf->setPageUnit('pt');            

            $this->pdf->setCellPaddings(1, 1, 1, 1);

            $this->pdf->setCellMargins(1, 1, 1, 1);
            
            //To be sure that the border of my tables isn't clipped out
            $this->pageWidth = $this->pdf->getPageWidth()-2;
            $this->pageHeight = $this->pdf->getPageHeight()-2;
        }

        /**
         * Luncher function: add all the pages
         */
        public function draw($year)
        {
            $yearSchema = $this->calendar->getCalendarTable($year);
            
            for($monthNum=1; $monthNum<=12; $monthNum++)
            {
                $this->addCalendarMonth($yearSchema, $monthNum);
            }
            
            //Close and output PDF document
            $this->pdf->Output('calendar.pdf', 'I');
        }
        
        /**
         * Add the small calendar at given coordinates
         * 
         * @param \stdClass $yearSchema
         * @param int $monthNum
         * @param int $calendarX
         * @param int $calendarY
         */
        private function addSmallCalendar($yearSchema, $monthNum, $calendarX, $calendarY)
        {
            $monthSchema = $yearSchema->schema[$monthNum];
            
            $this->pdf->SetFont('helvetica', '', 10);
            $this->pdf->SetFillColor(240, 242, 252);
            $this->pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(127, 127, 127)));
            $this->pdf->SetTextColor(90, 97, 127);
                        
            //Month label
            $this->pdf->MultiCell(pdfGenerator::DAY_WIDTH*8, pdfGenerator::DAY_HEIGHT, $monthSchema->name. ' '.$monthSchema->year, 1, 'C', true, 1, $calendarX, $calendarY, true);
            
            //Day header
            $this->pdf->MultiCell(pdfGenerator::DAY_WIDTH, pdfGenerator::DAY_HEIGHT, '', 1, 'C', true, 1, $calendarX, $calendarY+(1*pdfGenerator::DAY_HEIGHT), true);
            foreach($yearSchema->header as $weekDay=>$dayLabel)
            {
                //Change color for weekend
                if($weekDay==6 || $weekDay==7)
                {
                    $this->pdf->SetTextColor(224, 84, 61);
                }
                else
                {
                    $this->pdf->SetTextColor(90, 97, 127);
                }
                
                $this->pdf->MultiCell(pdfGenerator::DAY_WIDTH, pdfGenerator::DAY_HEIGHT, $dayLabel, 1, 'C', true, 1, $calendarX+(($weekDay)*pdfGenerator::DAY_WIDTH), $calendarY+(1*pdfGenerator::DAY_HEIGHT), true);
            }
            
            //table body
            foreach($yearSchema->header as $weekDay)
            {
                $weekCounter = 1;
                foreach($monthSchema->weeks as $weeksNum=>$weekSchema)
                {
                    $weekCounter++;
                    
                    $this->pdf->SetTextColor(160, 160, 160);
                    $this->pdf->MultiCell(pdfGenerator::DAY_WIDTH, pdfGenerator::DAY_HEIGHT, $weeksNum, 1, 'C', true, 1, $calendarX, $calendarY+($weekCounter*pdfGenerator::DAY_HEIGHT), true);
                    
                    foreach($yearSchema->header as $currentWeekDay=>$currentDayLabel)
                    {
                        //Change color for weekend
                        if($currentWeekDay==6 || $currentWeekDay==7)
                        {
                            $this->pdf->SetTextColor(224, 84, 61);
                        }
                        else
                        {
                            $this->pdf->SetTextColor(90, 97, 127);
                        }
                        
                        $dayNumber = '';
                        if(isset($weekSchema[$currentWeekDay])) $dayNumber = $weekSchema[$currentWeekDay];
                        $this->pdf->MultiCell(pdfGenerator::DAY_WIDTH, pdfGenerator::DAY_HEIGHT, $dayNumber, 1, 'C', true, 1, $calendarX+(($currentWeekDay)*pdfGenerator::DAY_WIDTH), $calendarY+($weekCounter*pdfGenerator::DAY_HEIGHT), true);
                    }
                }
            }
        }
        
        /**
         * Add the big calendar as a page. At the end, draw also the two little calendar
         * with the before and after month
         * @param \stdClass $yearSchema
         * @param int $monthNum
         */
        private function addCalendarMonth($yearSchema, $monthNum)
        {
            //$current
            
            $this->pdf->AddPage();
            
            $this->pdf->SetFont('helvetica', 'bi', 20);
            $this->pdf->SetFillColor(255, 255, 215);
            $this->pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(127, 127, 127)));
            $this->pdf->SetTextColor(90, 97, 127);
            $this->pdf->MultiCell($this->pageWidth, 30, $yearSchema->schema[$monthNum]->name, 1, 'C', true, 1, 0, 0, true);
            $this->pdf->MultiCell(200, 30, $yearSchema->schema[$monthNum]->year, 0, 'C', false, 1, 0, 0, true);
            
            $currentMonthPlainSchema = $yearSchema->plain[$monthNum];
            $this->pdf->SetFont('helvetica', 'bi', 8);
            foreach($currentMonthPlainSchema as $dayNumber=>$currentWeekDay)
            {
                $label = $yearSchema->headerLong[$currentWeekDay];
                
                if($currentWeekDay==6 || $currentWeekDay==7)
                {
                    $this->pdf->SetTextColor(224, 84, 61);
                }
                else
                {
                    $this->pdf->SetTextColor(90, 97, 127);
                }
                
                /**
                 * Content
                 * I used two multicell becouse using a single cell I have to deal with the interline
                 * 
                 * ToDo: refactor, if possible, using a smaller interline and using a single cell
                 */
                $this->pdf->SetFillColor(255, 255, 255);
                $this->pdf->MultiCell(50, 20, $dayNumber, 0, 'C', true, 1, 0, 20+20*$dayNumber, true);
                $this->pdf->MultiCell(50, 20, $label, 0, 'C', true, 1, 0, 28+20*$dayNumber, true);
                $this->pdf->SetFillColor(255, 255, 215);
                $this->pdf->MultiCell(50, 20, '', 1, 'L', true, 1, 0, 20+20*$dayNumber, true);
                //Border
                $this->pdf->SetFillColor(255, 255, 245);
                $this->pdf->MultiCell($this->pageWidth-50, 20, '', 1, 'L', true, 1, 50, 20+20*$dayNumber, true);
            }
            
            $this->addSmallCalendar($yearSchema, $monthNum-1, 0, $this->pageHeight-(pdfGenerator::DAY_HEIGHT*10));
            $this->addSmallCalendar($yearSchema, $monthNum+1, $this->pageWidth-(pdfGenerator::DAY_WIDTH*8), $this->pageHeight-(pdfGenerator::DAY_HEIGHT*10));
        }
    }