<?php

function setLanguage($f3) {
    $lang_array = $f3->get('languages'); // languages is defined in config.ini
    if (preg_match('/^\/[A-Za-z]{2}\//',$f3->get('PATH'),$lang)) {
        $langString = trim($lang[0],'/');
        $valid = false;
        foreach ($lang_array as $key => $value){
            if ($key === $langString) {
                $valid = true;
                break;
            }
        }
        if (!$valid) {
            reset($lang_array);
            $langString = key($lang_array);
        }
    } else {
        $langString = \Preflang::instance()->langDirectory($f3);
    }
    $f3->set('LANGUAGE',$lang_array[$langString][0]);
    $f3->set('langSubdirectory',$langString);
}