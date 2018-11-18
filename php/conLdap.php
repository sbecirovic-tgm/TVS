<?php
	function checkUserPass( $userName, $password )
	{
	/*
		$ldap_dn = "cn=read-only-admin,dc=example,dc=com";
		$ldap_password = "password";
	
		$ldap_con = ldap_connect("ldap.forumsys.com");
	
		ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
	
		if(ldap_bind($ldap_con, $ldap_dn, $ldap_password)) 
		{
			return True;
		}
		else
		{
			return False;
		}
	*/
		if ( $userName == 'swahl@student.tgm.ac.at' && $password == '1' )
		{
			return True;
		}
		else
		{
			return False;
		}

	}
	

?>