<?php

//============= Закрываем индексацию страниц ===========//
    
//======================================================//
//                 for BITRIX CMS only!                 //
//======================================================//
// AddEventHandler("main", "OnEndBufferContent", "GoogleBotDirective"); // put it in init.php

//======================================================//
//                    for other CMS                     //
//======================================================//
// GoogleBotDirective($sContent); // if you have content in variable
// GoogleBotDirective(false); // if you want to output tag right there

function GoogleBotDirective(&$content)
{
    $path = $_SERVER["DOCUMENT_ROOT"].'robots.txt';
    $noindex = '<meta name="googlebot" content="noindex">';
    $buffered = false;
    
    if(@file_exists($path)) $sR = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        else $sR = file('http://'.$_SERVER['SERVER_NAME'].'/robots.txt');
    $sR = array_unique(array_filter($sR));

    foreach($sR as $sRule) 
    {
        if(strpos($sRule, '#')) $sRule = preg_replace('~^([^#]*)#~i', "$1", $sRule); // comment removing
        if(!preg_match('~Disallow.*~', $sRule)) continue; // нам нужны только запрещающие правила
        
        if($content !== false && strlen($content) > 100)
        {
          if(!preg_match('~<meta[^>]+name\s*=\s*["|\']googlebot["|\'][^>]+noindex[^>]+>~siU', $content)) $buffered = true;
            else continue;
        }

        $sRule = preg_replace('~^\s*Disallow\s*:\s*~i', '', $sRule);
        $sRule = trim(str_replace(array( '*',  '?' ), array( '.*', '\?'), $sRule));
        
        if(preg_match('~'.$sRule.'~i', $_SERVER['REQUEST_URI']))
        {
            if($buffered) $content = str_ireplace('</title>', '</title>'.$noindex, $content);
              else print $noindex;
            return true;
        }
    }
    return false;
}
?>
