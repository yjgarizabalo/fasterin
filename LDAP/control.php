<?php
		require("ldap.php");
		header("Content-Type: text/html; charset=utf-8");
		$usr = $_POST["usuario"];		
		$usuario = mailboxpowerloginrd($usr,$_POST["clave"]);
		if($usuario == "0" || $usuario == ''){
			$_SERVER = array();
			$_SESSION = array();
			echo"<script> alert('Usuario o clave incorrecta. Vuelva a digitarlos por favor.'); window.location.href='index.php'; </script>";
		}else{
			session_start();
			$_SESSION["user"] = $usuario;
			$_SESSION["autentica"] = "SIP";
			echo"<script>window.location.href='app.php'; </script>";
		}
?>
