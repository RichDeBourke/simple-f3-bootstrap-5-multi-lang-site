<?php

require_once 'SetLanguage.php';

// Base controller
class UtilitiesController {

    // For request for the root directory – reroute to a language subdirectory
	function getLanguage($f3) {
        $f3->reroute('@'.\Preflang::instance()->langDirectory($f3).'_home');
    }

    // Redirect requests for index.htm, index.html, and index.php to the root directory.
    function indexOverride($f3) {
        $f3->reroute('/');
    }

    // Generates an error (for testing the error page)
    function errorTest($f3) {
        user_error('Error',E_USER_ERROR);
    }

    function contactPost($f3) {
        function invalidInput($data) {
            $suspiciousData = false;
            $bad = array('content-type','bcc:','to:','cc:','href', 'src=', '<');

            foreach ($bad as $badString) {
                if (preg_match('/('.$badString.')/i',$data)) {
                    // It looks like someone is trying to hack
                    // into the site via the contact page.
                    $suspiciousData = true;
                }
            }
            return $suspiciousData;
        }

        // set SMTP email values
        $contactSMTPAddress = 'smtp-contact.mywebsite.com'; // SMTP address for sending emails
        $contactSendingAddress = 'contact@mywebsite.com'; // email address for sending emails
        $contactSendingPassword = 'pw=12345!'; // email password for sending emails
        
        // define local variables and set to false or empty values
        $contactSuccess = false;
        $inputFieldsValid = false;
        $validEmailAddress = false;
        $goodInputData = false;
        $name = $email = $subject = $message = '';
        $json = array();

        $audit = \Audit::instance(); // used to check if email address is valid
        
    
        //setLanguage is imported from SetLanguage.php
        setLanguage($f3);
    
        // Verify all of the required fields are posted
        if ($f3->exists('POST.name',$name) && $f3->exists('POST.email',$email) && $f3->exists('POST.subject',$subject) && $f3->exists('POST.message',$message)) {
            // Verify none of the required fields are empty
            if ($name !== '' && $email !== '' && $subject !== '' && $message !== '') {
                $inputFieldsValid = true;
            }
        }

        // If inputs are valid, check the email address
        if ($inputFieldsValid) {
            $validEmailAddress = $audit->email($email, TRUE);
        }

        // Check the name and subject for issues
        if ($inputFieldsValid && $validEmailAddress) {
            if (!invalidInput($name) && !invalidInput($subject)) {
                $goodInputData = true;
            } else {
                $goodInputData = false;
                $name = htmlspecialchars($name, ENT_QUOTES);
                $subject = htmlspecialchars($subject, ENT_QUOTES);
            }
        }

        // Trim and sanitize the message body
        $message = htmlspecialchars(mb_strimwidth($message, 0, $f3->get('contactMessageLimit')), ENT_QUOTES);

        // Send the message
        If ($inputFieldsValid && $validEmailAddress) {
            $smtp = new SMTP ($contactSMTPAddress, 587, 'tls', $contactSendingAddress, $contactSendingPassword);
            $smtp->set('From', $contactSendingAddress);
            $smtp->set('To', $f3->get('contactAddress'));
            if ($goodInputData) {
                $smtp->set('Subject', 'Message from contact form');
            } else {
                $smtp->set('Subject', 'Message from contact form - possible issues');
            }
            $outgoingMessage = "Contact name: $name \n";
            $outgoingMessage .= "Contact email: $email \n";
            $outgoingMessage .= "Contact subject: $subject \n";
            $outgoingMessage .= "$message \n";
            $outgoingMessage = wordwrap($outgoingMessage,70);
            $contactSuccess = $smtp->send($outgoingMessage);
        }

        //setup and echo the json response
        header('Content-Type: application/json');

        if ($contactSuccess === true) { // message was sent
            $json['status'] = 'success';
            $json['alert'] = 'alert-success';
            $json['label'] = 'Success';
            $json['text'] = $f3->get('dictContact13');
        } elseif (!$validEmailAddress) { // email address doesn't work
            $json['status'] = 'email address not valid';
            $json['alert'] = 'alert-warning';
            $json['label'] = 'Warning';
            $json['text'] = $f3->get('dictContact14');
        } elseif (!$inputFieldsValid) { // missing fields, so message not sent
            $json['status'] = 'a field was missing or empty';
            $json['alert'] = 'alert-warning';
            $json['label'] = 'Warning';
            $json['text'] = $f3->get('dictContact15');
        } else { // something didn't work
            $json['status'] = 'not able to send the message - maybe server error';
            $json['alert'] = 'alert-warning';
            $json['label'] = 'Warning';
            $json['text'] = $f3->get('dictContact15');
        }

        // English language fields from the dict file for reference
        // 'dictContact13'=>'Thank you for contacting us. We will respond as soon as we can.',
        // 'dictContact14'=>'Our server is not able to process your email address as entered.',
        // 'dictContact15'=>'We experienced an unknown problem with submitting your message. Please send your request using our email address below.',

        echo json_encode($json);

        // log questionable contacts
        if (!$inputFieldsValid || !$validEmailAddress || !$goodInputData) {
            $logger = new Log('contacts.log');
            $logger->write('IP: '.$f3->get('IP').' email: '.$email);
        }
    }

    function getSitemap($f3) {
        $f3->set('fdate',
            function($fileName) {
                if (file_exists('app/ui/'.$fileName.'.html')) {
                    return date ('Y-m-d', filemtime('app/ui/'.$fileName.'.html'));
                 } else {
                     return 'Error';
                 }
            }
        );

        $aliases = ($f3->get('ALIASES'));
        $sitemapAliases = [];

        // route names ending with x are left off the sitemap
        foreach ($aliases as $routeName => $path) {
            if (substr($routeName,-1) !== 'x') {
                $sitemapAliases[$routeName] = $path;
            }
        }

        $f3->set('sitemapAliases',$sitemapAliases);

		echo \Template::instance()->render('sitemap.xml','text/xml');
    }


	// Instantiate class
	function __construct() {
		$f3=\Base::instance();
    }
    
}
