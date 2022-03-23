<?php
require_once("config.php");
function mailboxpowerloginrd($user,$pass){
	$ldaprdn = trim($user).'@'.DOMINIO; 
  $ldappass = trim($pass); 
  $ds = 'ldap.cuc.edu.co';
  $dn = DN;  
  $puertoldap = 389; 
  $ldapconn = ldap_connect($ds,$puertoldap);
  ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION,3); 
  ldap_set_option($ldapconn, LDAP_OPT_REFERRALS,0); 
  $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass); 
  $resp = ($ldapbind) ?  1 : $resp = 0;
  ldap_close($ldapconn); 
	return $resp;
} 
?>
