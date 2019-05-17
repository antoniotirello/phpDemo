<!DOCTYPE html>
<html lang="en-US">
    <head itemscope="itemscope" itemtype="http://schema.org/WebSite">
        <meta charset="UTF-8" />
        <title>Demo page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <style>
            body
            {
                background-color: rgb(200,200,200);
            }
        </style>
    </head>
    <body>
        <p>
            This is a simple calendar generator.<br />
            It require only TCPDF (in the <i>tcpdf</i> folder).<br />
            It's designed to be printed and as a PHP's demo.<br />
            <br />
            In a future, with more time, could be customized with some<br />
            user's photos
        </p>
        
        <form name="year_data" action="pdf_view.php">
            Year: <input name="year" alt="Year" maxlength="4" size="4" style="text-align: center;" value="<?php echo date('Y'); ?>" />
            <input type="submit" value="Ok" />
        </form>
    </body>
</html>