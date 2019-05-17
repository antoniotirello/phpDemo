<?php

    require_once('tcpdf/tcpdf.php');
    require_once('./include/pdf.php');
    require_once('./include/calendar.php');
    
    $year = filter_input(INPUT_GET, 'year');
    
    $pdf = new pdfGenerator();
    $pdf->draw($year);