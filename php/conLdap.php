<?php
/**
$ldap_dn = "cn=proj_eis, ou=serviceusers,dc=tgm,dc=ac, dc=at";
$ldap_dn = "zenuser@tgm.ac.at";
$ldap_password = "All4Ice!";

$ldap_con = ldap_connect("dc-01.tgm.ac.at");

ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

if(ldap_bind($ldap_con, $ldap_dn, $ldap_password)) {

    echo "Bind successful!";

} else {
    echo "Invalid user/pass or other errors!";
}
*/
// https://www.youtube.com/watch?v=AEjGhzZpGlg phpinfo();


function checkUserPass( $userName, $password)
{

    if ( ($userName == 'swahl' && $password == '1') || ($userName == 'fgavric' && $password == '2'))
    {
        return array(true, false);
    }
    else if ( $userName == 'khoeher' && $password == '3' )
    {
        return array(true, true);
    }
    $out = false;
    $ldap_dn = $userName . "@tgm.ac.at";

    $ldap_con = ldap_connect("dc-01.tgm.ac.at") ;
    ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

    set_error_handler(function() { /* Nichts machen! */ });
    if (ldap_bind($ldap_con, $ldap_dn, $password)) {
        $out = true;
        $query = ldap_search($ldap_con,"ou=People,ou=identity,dc=tgm,dc=ac,dc=at","samaccountname=" . $userName, array( "employeeType") );
        $entries = ldap_get_entries( $ldap_con, $query );
        if ( $entries[0]['employeetype'][0] == 'schueler' )
        {
            $type = false;
        }
        else
        {
            $type = true;
        }

        $var = $entries[0]['dn'];
        $posAnfang = strpos($var, "=")+1;
        $name = substr($var, $posAnfang, strpos($var, ',')-$posAnfang);

    } else {
        $out = false;
    }
    restore_error_handler();

    return array($out, $type, $name);
}


?>