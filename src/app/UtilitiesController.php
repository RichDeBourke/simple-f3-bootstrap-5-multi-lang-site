<?php

require_once 'EnvLoader.php';

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

    // Function to check against blacklist
    private function is_blacklisted($email) {
        $blacklisted_domains = ['example.com', 'example.org', 'example.net']; // Example domains
        $blacklisted_emails = ['spam@example.com', 'another_spam@example.net']; // Example email addresses
        // Check against blacklisted domains
        foreach ($blacklisted_domains as $domain) {
            if (preg_match('/@' . preg_quote($domain) . '$/', $email)) {
                return true; // Blacklisted domain
            }
        }
    
        // Check against blacklisted email addresses
        foreach ($blacklisted_emails as $blacklisted_email) {
            if ($email === $blacklisted_email) {
                return true; // Blacklisted email
            }
        }
        return false; // Not blacklisted
    }

    // Rate limiting
    function checkRateLimit($f3, $ip) {
        $cache_key = "contact_rate_limit_$ip";
        $cache = \Cache::instance();
    
        $attempts = $cache->get($cache_key) ?: 0;
    
        if ($attempts >= 5) { // Max 5 attempts per hour
            return false;
        }
    
        $new_attempts = $attempts + 1;
        $cache->set($cache_key, $new_attempts, 3600); // 1 hour
        
        return true;
    }

    // Enhanced input sanitization
    private function sanitizeInput($input) {
        // Remove null bytes and control characters
        $input = str_replace("\0", '', $input);
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        // Remove potential PHP filter chains
        if (stripos($input, 'php://') !== false || 
            stripos($input, 'convert.') !== false || 
            stripos($input, 'filter/') !== false) {
            return '';
        }
        
        // Basic trim and length limit
        return trim(substr($input, 0, 1000));
    }

    // Strict email validation
    private function isValidEmail($email) {
        // Basic format check
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Check for suspicious patterns
        $suspicious_patterns = [
            '/php:\/\//',
            '/convert\./',
            '/filter\//',
            '/base64/',
            '/iconv/',
            '/&#\d+;/',
            '/<script/i',
            '/javascript:/i'
        ];
        
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $email)) {
                return false;
            }
        }
        
        // Length and character restrictions
        return strlen($email) <= 254 && preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
    }

    // Enhanced name validation
    private function isValidName($name) {
        // Check length
        if (strlen($name) > 100 || strlen($name) < 1) {
            return false;
        }
        
        // Check for suspicious patterns
        $suspicious_patterns = [
            '/php:\/\//',
            '/convert\./',
            '/filter\//',
            '/base64/',
            '/<script/i',
            '/javascript:/i',
            '/&#\d+;/'
        ];
        
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $name)) {
                return false;
            }
        }
        
        // Allow only letters, spaces, hyphens, and apostrophes
        return preg_match('/^[a-zA-Z\s\'-]+$/', $name);
    }

    // Enhanced message validation
    private function isValidMessage($message, $f3) {
        // Check length
        if (strlen($message) > $f3->get('contactMessageLimit') || strlen($message) < 10) {
            return false;
        }
        
        // Check for suspicious patterns
        $suspicious_patterns = [
            '/php:\/\//',
            '/convert\./',
            '/filter\//',
            '/base64-/',
            '/<script/i',
            '/javascript:/i',
            '/&#\d+;/',
            '/data:/',
            '/file:/'
        ];
        
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return false;
            }
        }
        
        return true;
    }

    // CSRF token validation
    private function validateCSRFToken($token) {
        session_start();
        return $token && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Detect additional suspicious input patterns
    private function detectSuspiciousInput($f3) {
        $all_post_data = json_encode($f3->get('POST'));
        
        $suspicious_indicators = [
            'katana&#039;uq', // From your example
            'convert.iconv',
            'base64-encode',
            'php://filter',
            'resource=php://',
            'data:text/plain',
            '/etc/passwd',
            '../../../',
            'union select',
            '<script',
            'javascript:',
            'onload=',
            'onerror='
        ];
        
        foreach ($suspicious_indicators as $indicator) {
            if (stripos($all_post_data, $indicator) !== false) {
                return true;
            }
        }
        
        return false;
    }

    // Enhanced security logging
    private function logSecurityIncident($f3, $incident_type = 'GENERAL') {
        $logger = new Log('security_incidents.log');
        $incident_data = [
            'timestamp' => date('c'),
            'type' => $incident_type,
            'ip' => $f3->get('IP'),
            'user_agent' => $f3->get('HEADERS.User-Agent'),
            'referer' => $f3->get('HEADERS.Referer'),
            'post_data' => $f3->get('POST')
        ];
        
        $logger->write('SECURITY_INCIDENT: ' . json_encode($incident_data));
    }

    // Log for troubleshooting
    private function logTroubleshooting($f3, $incident_type = 'GENERAL', $data = null) {
        $logger = new Log('trouble.log');
        
        // Handle different data types
        if (is_array($data) || is_object($data)) {
            // If it's already an array/object, use it as additional data
            $additional_data = $data;
        } else {
            // If it's a simple value, treat it as count for backwards compatibility
            $additional_data = ['count' => $data ?: 1];
        }
        
        $incident_data = array_merge([
            'timestamp' => date('c'),
            'type' => $incident_type,
            'post_data' => $f3->get('POST')
        ], $additional_data);
    
        $logger->write('TROUBLE_INCIDENT: ' . json_encode($incident_data));
    }

    function contactPost($f3) {
        // Rate limiting check first
        if (!$this->checkRateLimit($f3, $f3->get('IP'))) {
            http_response_code(429);
            echo json_encode(['status' => 'valid']); // Don't reveal rate limiting
            return;
        }

        // CSRF token validation
        if (!$this->validateCSRFToken($f3->get('POST.csrf_token'))) {
            $this->logSecurityIncident($f3, 'CSRF_TOKEN_INVALID');
            echo json_encode(['status' => 'valid']); // Don't reveal CSRF failure
            return;
        }

        // define local variables and set to false or empty values
        $inputFieldsPresent = false;
        $contactSuccess = false;
        $validEmailAddress = false;
        $goodInputData = false;
        $name = $email = $subject = $message = '';
        $json = [];
        $audit = \Audit::instance(); // used to check if email address is valid
    
        // Verify all of the required fields are posted
        if ($f3->exists('POST.name',$name) && $f3->exists('POST.email',$email) && $f3->exists('POST.subject',$subject) && $f3->exists('POST.message',$message)) {
            // Enhanced input sanitization and validation
            $name = $this->sanitizeInput($name);
            $email = $this->sanitizeInput($email);
            $subject = $this->sanitizeInput($subject); // Fixed typo: was $subect
            $message = $this->sanitizeInput($message);

            // Verify none of the required fields are empty after sanitization
            if ($name !== '' && $email !== '' && $subject !== '' && $message !== '') {
                $inputFieldsPresent = true;
            }
        }

        // If inputs are present, check the email address
        if ($inputFieldsPresent) {
            // Strict email validation
            if ($this->isValidEmail($email)) {
                $validEmailAddress = $audit->email($email, TRUE);
                
                if ($validEmailAddress && !$this->is_blacklisted($email)) {
                    // Enhanced input validation for all fields
                    if ($this->isValidName($name) && $this->isValidMessage($message, $f3)) {
                        $goodInputData = true;
                    }
                }
            }
        }

        // $json['address'] = $email;
        // $json['valid-audit'] = $validEmailAddress;

        // Send the message if validation passes and .env is loaded
        if ($goodInputData && function_exists('envLoader') && envLoader()) {
            // Additional sanitization before sending
            $name = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $subject = htmlspecialchars($subject, ENT_QUOTES | ENT_HTML5, 'UTF-8'); // Fixed typo: was $subect
            $message = htmlspecialchars(
                mb_strimwidth($message, 0, $f3->get('contactMessageLimit')), 
                ENT_QUOTES | ENT_HTML5, 
                'UTF-8'
            );

            $smtp = new SMTP($_ENV['SMTP_HOST'], $_ENV['SMTP_PORT'], $_ENV['SMTP_SCHEME'], $_ENV['SMTP_USER'], $_ENV['SMTP_PASSWORD']);
            $smtp->set('From', $_ENV['SMTP_USER']);
            $smtp->set('To', $f3->get('contactAddress'));
            $smtp->set('Subject', 'Message from contact form');

            $outgoingMessage = "Contact name: $name\r\n";
            $outgoingMessage .= "Contact email: $email\r\n";
            $outgoingMessage .= "$message\r\n";
            $outgoingMessage = wordwrap($outgoingMessage, 70, "\r\n");

             try {
                // Comment / uncomment below for testing and operation
                $contactSuccess = $smtp->send($outgoingMessage);
                // $contactSuccess = false;
                // $goodInputData = false;
            } catch (Exception $e) {
                error_log("Contact form SMTP error: " . $e->getMessage());
                $contactSuccess = false;
            }
        }

        // Send response - only two possible outcomes related to email sending
        // If message sent, send success
        // If there was a problem sending the message, report a technical problem
        // For all other issues, which could be a hacking attempt, say it was okay, but don't send
        header('Content-Type: application/json');
        if ($contactSuccess === true) {
            // Email was successfully sent
            $json['status'] = 'success';
            $json['alert'] = 'alert-success';
            $json['label'] = 'Success';
            $json['text'] = $f3->get('dictContact13');
        } elseif ($contactSuccess === false && $goodInputData === true) {
            // Valid data but email failed to send
            $json['status'] = 'problem sending email';
            $json['alert'] = 'alert-warning';
            $json['label'] = 'Warning';
            $json['text'] = $f3->get('dictContact14');
        } else {
            // Invalid data or other issues - silently appear successful but log incident
            $json['status'] = 'success';
            $json['alert'] = 'alert-success';
            $json['label'] = 'Success';
            $json['text'] = $f3->get('dictContact15');
            
            // Log security incidents for invalid data
            if (!$validEmailAddress || !$goodInputData || $this->detectSuspiciousInput($f3)) {
                $this->logSecurityIncident($f3, 'INVALID_INPUT_DATA');
            }
        }

        // English language fields from the dict file for reference
        // 'dictContact13'=>'Thank you for contacting us. We will respond as soon as we can.',
        // 'dictContact14'=>'We experienced an unknown problem with submitting your message. Please send your request using our email address below.',
        // 'dictContact15'=>'Thank you for contacting us.',

        echo json_encode($json);
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
    private $f3;
}