<?php
/*
$ldap_dn = "cn=proj_eis, ou=serviceusers,dc=tgm,dc=ac, dc=at";
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

if ( ($userName == 'swahl@student.tgm.ac.at' && $password == '1') || ($userName == 'fgavric@student.tgm.ac.at' && $password == '2') || ($userName == 'khoeher@tgm.ac.at' && $password == '3'))
{
    return True;
}
else
{
    return False;
}

?>