<?php
session_start();
require 		"utils/intuit_helper.php";
if (isset($_REQUEST['code']) && isset($_REQUEST['realmId']))
{
    $realmId                    = $_REQUEST['realmId'];
    $code                       = $_REQUEST['code'];
    $_SESSION['quick_realmId']  = $realmId;
    getAccessTokens($code);
    create_intuit_payment();
}

?>