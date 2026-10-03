<?php

/**
 * ApexPulse SMM Panel - Root Proxy Front Controller
 * 
 * Safely delegates requests to public/index.php when the web hosting environment
 * points the domain directly to the project root instead of /public.
 */

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

chdir(FCPATH);

require FCPATH . 'index.php';
