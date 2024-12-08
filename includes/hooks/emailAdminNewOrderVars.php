<?php

// Version 1.0

//use WHMCS\Database\Capsule;

// Ticket subject text that triggers this hook to proceed
define('SUBJECT', 'Service Provisioned');

// Regular expression to search for
define('BODY_REGEX', '/(Service|Addon) ID # (\d+) was just auto provisioned/');

// https://developers.whmcs.com/hooks-reference/everything-else/#emailpresend
add_hook('EmailPreSend', 1, function($vars) {

    //logActivity('[Hook emailAdminNewOrderVars] EmailPreSend Triggered with vars: ' . print_r($vars));

    if ($vars['messagename'] != 'WHMCS New Order Notification') return; //only proceed if it's the new order notification

    $messagename    = $vars['messagename'];
    $orderid        = $vars['relid']; //assuming order ID
    $clientid       = $vars['mergefields']['client_id'];

    // https://developers.whmcs.com/api-reference/getorders/
    $results = localAPI('GetOrders', array('userid' => $clientid));
    
    $firstorder = ($results['numreturned'] == 1)? true:false;
	
    $merge_fields = [];
    if (!array_key_exists('client_firstorder', $vars['mergefields'])) {
        $merge_fields['client_firstorder'] = $firstorder;
    }
    return $merge_fields;

});

//Output additional merge fields in the list when editing an email template
add_hook('EmailTplMergeFields', 1, function($vars) {
    $merge_fields = [];
    $merge_fields['client_firstorder'] = "New Client?";
    return $merge_fields;
});