<?php

require_once 'SetLanguage.php';

//! Front-end processor
class CommonPageController extends Controller {

    //const

    // Note: F3 uses all uppercase names for Hive variables, so no risk with using 
    
    function contactNew($f3) {
        /*
            For contact, I can use route names, so can have alternate languages
        */
        //setLanguage is imported from SetLanguage.php
        setLanguage($f3);
        $f3->set('googleNoIndex',true);
        $f3->set('pageTitle',$f3->get('dictContactPageTitle'));
        $f3->set('pageDescription',$f3->get('dictContactPageDescription'));
        $f3->set('isDetailPage',true);
        $f3->set('isContactPage',true);
        $f3->set('contactName','');
        $f3->set('contactEmail','');
        $f3->set('contactSubject','');
        $f3->set('contactMessage','');
        $f3->set('contactNameError','');
        $f3->set('contactEmailError','');
        $f3->set('contactSubjectError','');
        $f3->set('contactSuccess',false);
        // Set the content source
        $f3->set('pageContent','contact.html');
        $f3->set('pageJavaScript','contact_script.html');
    }


    function renderTerms($f3) {
        /*
            The Terms of Use / Privacy page is in just one language (the site's primary language), but the header/menus and the footer are provided in the user's selected language
        */
        setLanguage($f3);
        $f3->set('googleNoIndex',true);
        $f3->set('pageTitle',$f3->get('dictTermsPageTitle'));
        $f3->set('pageDescription',$f3->get('dictTermsPageDescription'));
        $f3->set('isDetailPage',false);
        $f3->set('isTermsPage',true);
        $f3->set('pageContent','terms.html');
    }

    function error($f3) {
        /*
            For error, there is no route name, so the language menu should point to the home pages
        */
        setLanguage($f3);
        $f3->set('googleNoIndex',true);
        $f3->set('isErrorPage',true);
        
        if ($f3->get('ERROR.code') === 404) {
            $f3->set('pageTitle',$f3->get('dict404PageTitle'));
            $f3->set('pageDescription',$f3->get('dict404PageDescription'));
            $f3->set('pageCSS','404_css.html');
            // Set the content source
            $f3->set('pageContent','404.html');
            $f3->set('pageJavaScript','404_script.html');
        } else { // 5XX error
            // while (ob_get_level())
            //     ob_end_clean();
            $f3->set('pageTitle',$f3->get('dict500PageTitle'));
            $f3->set('pageDescription',$f3->get('dict500PageDescription'));
            $f3->set('pageCSS','error_css.html');
            // Set the content source
            $f3->set('pageContent','error.html');
            $f3->set('pageJavaScript','error_script.html');
        }
    }




}
