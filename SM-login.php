<?php

/**
* Ce script gère la connexion, la déconnexion et le changement de mot de passe
* des utilisateurs.
*
* @brief Gestion des connexions des utiisateurs
* @author Pierre-Luc MARY
* @date 2012-10
* @version 1.0
* @copyright LGPL License 3.0 http://www.gnu.org/copyleft/lesser.html
*
* @param[in] $_GET['action'] Action spécifique à faire réaliser par le composant
* @param[in] $_GET['expired'] Information pour faire afficher un message spécifique
* @param[in] $_GET['mandatory'] Information pour faire afficher un message spécifique
* @param[in] $_GET['rp'] Précise la page de retour (pour peut qu'il soit possible de réaliser ce retour
*/

session_start();

// Initialise la langue Française par défaut.
if ( ! isset( $_SESSION[ 'Language' ] ) ) $_SESSION[ 'Language' ] = 'fr';

// Récupère le code langue, quand celui-ci est précisé.
if ( array_key_exists( 'Lang', $_GET ) ) {
   $_SESSION[ 'Language' ] = $_GET[ 'Lang' ];
}   

$Script = $_SERVER[ 'SCRIPT_NAME' ];
$Server = $_SERVER[ 'SERVER_NAME' ];
$URI = $_SERVER[ 'REQUEST_URI' ];
$IP_Source = $_SERVER[ 'REMOTE_ADDR' ];

// Force la connexion en HTTPS.
if ( ! array_key_exists( 'HTTPS', $_SERVER ) )
	header( 'Location: https://' . $Server . $URI );

$Action = '';
$Choose_Language = 1;

include( 'Libraries/Class_HTML.inc.php' );
include( 'Libraries/Labels/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );
include( 'Libraries/Labels/' . $_SESSION[ 'Language' ] . '_labels_generic.php' );
include( 'Libraries/Config_Access_DB.inc.php' );
include( 'Libraries/Config_Hash.inc.php' );
include( 'Libraries/Class_IICA_Authentications_PDO.inc.php' );
include( 'Libraries/Class_IICA_Parameters_PDO.inc.php' );
include( 'Libraries/Class_Security.inc.php' );
include( 'Libraries/Class_IICA_Secrets_PDO.inc.php' );


// Initialise l'objet de gestion des pages HTML.
$PageHTML = new HTML();

// Initialise l'objet de gestion des authentifications.
$Authentication = new IICA_Authentications( 
 $_Host, $_Port, $_Driver, $_Base, $_User, $_Password );

// Initialise l'objet de gestion des paramètres.
$Parameters = new IICA_Parameters( 
 $_Host, $_Port, $_Driver, $_Base, $_User, $_Password );

// Initialise l'objet de gestion des paramètres.
$Secrets = new IICA_Secrets( 
 $_Host, $_Port, $_Driver, $_Base, $_User, $_Password );

// Initialise l'objet de gestion des entrés et sorties.
$Security = new Security();


// Récupère l'action spécifique à réaliser dans ce script.
if ( array_key_exists( 'action', $_GET ) ) {
	$Action = strtoupper( $_GET[ 'action' ] );
}


// Exécute l'action spécifique à réaliser.
switch( $Action ) {
 // Traite la déconnexion d'un utilisateur.
 case 'DCNX':
	if ( array_key_exists( 'expired', $_GET ) ) {
		if ( strpos( $Script, '?' ) === false ) {
			$Signal = '?expired';
		} else {
			$Signal = '&expired';
		}
	} else $Signal = '';
	
	$alert_message = $Secrets->formatHistoryMessage( $L_Disconnect . ' ' .
	 $_SESSION[ 'cvl_first_name' ] . ' ' . $_SESSION[ 'cvl_last_name' ] .
	 '(' . $_SESSION[ 'idn_login' ] . ')' );

	$Secrets->updateHistory( '', $_SESSION[ 'idn_id' ], $alert_message, $IP_Source );

	$Authentication->disconnect();
   
	header( 'Location: https://' . $Server . dirname( $Script ) . '/SM-login.php' .
	 $Signal );

	break;


 // Traite le changement de mot de passe d'un utilisateur.
 case 'CMDP':
	include( 'Libraries/Labels/' . $_SESSION[ 'Language' ] . '_SM-secrets.php' );
	
	if ( array_key_exists( 'rp', $_GET ) ) {
		$Previous_Page = 'SM-' . $_GET[ 'rp' ] . '.php';
	} else {
		$Previous_Page = $Script . '?action=DCNX'; // $_SERVER[ 'HTTP_REFERER' ];
	}
	
	print( $PageHTML->enteteHTML( $L_Title ) .
	 "   <!-- debut : zoneTitre -->\n" .
	 "   <div id=\"zoneTitre\">\n" .
	 "    <div id=\"icon-users\" class=\"icon36\"></div>\n" .
	 "    <span id=\"titre\">". $L_Title_CMDP . "</span>\n" .
	 $PageHTML->afficherActions( $Authentication->is_administrator() ) .
	 "    </div> <!-- Fin : zoneTitre -->\n" .
	 "\n" .
	 "    <div id=\"zoneGauche\" >&nbsp;</div>\n" .
	 "    <!-- debut : zoneMilieuComplet -->\n" .
	 "    <div id=\"zoneMilieuComplet\">\n" .
	 "     <center>\n" );
	 
	if ( array_key_exists( 'mandatory', $_GET ) ) {
    	print( "        <h3 id=\"alert\">" . $L_Change_Password ."</h3>\n" );
    }

		print( "     <script>\n" .
		 "function checkPassword(Password_Field, Result_Field, Complexity, Size) {\n" .
		 " var Ok_Size = 0;\n" .
		 " var Result = '';\n" .
		 " var pwd = document.getElementById(Password_Field).value;\n" .
		 " if ( Complexity < 1 || Complexity > 3 ) Complexity = 3;\n" .
		 " if ( pwd.length < Size ) {\n" .
		 "  Result += '" . $L_No_Good_Size . " ' + Size + '). ';\n" .
		 "  document.getElementById(Result_Field).title = Result;\n" .
		 " }\n" .
		 " switch( Complexity ) {\n" .
		 "  case 1:\n" .
		 "   var regex_lcase = new RegExp('[a-z]', 'g');\n" .
		 "   var regex_ucase = new RegExp('[A-Z]', 'g');\n" .
		 "   if ( ! pwd.match( regex_lcase ) ) {\n" .
		 "    Result += '" . $L_Use_Lowercase . ". ';\n" .
		 "    document.getElementById(Result_Field).title = Result;\n" .
		 "   }\n" .
		 "   if ( ! pwd.match( regex_ucase ) ) {\n" .
		 "    Result += '" . $L_Use_Uppercase . ". ';\n" .
		 "    document.getElementById(Result_Field).title = Result;\n" .
		 "   }\n" .
		 "   break;\n" .
		 "  case 2:\n" .
		 "   var regex_lcase = new RegExp('[a-z]', 'g');\n" .
		 "   var regex_ucase = new RegExp('[A-Z]', 'g');\n" .
		 "   var regex_num = new RegExp('[0-9]', 'g');\n" .
		 "   if ( ! pwd.match( regex_lcase ) ) {\n" .
		 "    Result += '" . $L_Use_Lowercase . ". ';\n" .
		 "    document.getElementById(Result_Field).title = Result;\n" .
		 "   }\n" .
		 "   if ( ! pwd.match( regex_ucase ) ) {\n" .
		 "    Result += '" . $L_Use_Uppercase . ". ';\n" .
		 "    document.getElementById(Result_Field).title = Result;\n" .
		 "   }\n" .
		 "   if ( ! pwd.match( regex_num ) ) {\n" .
		 "    Result += '" . $L_Use_Number . ". ';\n" .
		 "    document.getElementById(Result_Field).title = Result;\n" .
		 "   }\n" .
		 "   break;\n" .
		 "  case 3:\n" .
		 "   var regex_lcase = new RegExp('[a-z]', 'g');\n" .
		 "   var regex_ucase = new RegExp('[A-Z]', 'g');\n" .
		 "   var regex_num = new RegExp('[0-9]', 'g');\n" .
		 "   var regex_sc = new RegExp('[^\\\\w]', 'g');\n" .
		 "   if ( ! pwd.match( regex_lcase ) ) {\n" .
		 "    Result += '" . $L_Use_Lowercase . ". ';\n" .
		 "    document.getElementById(Result_Field).title = Result;\n" .
		 "   }\n" .
		 "   if ( ! pwd.match( regex_ucase ) ) {\n" .
		 "    Result += '" . $L_Use_Uppercase . ". ';\n" .
		 "    document.getElementById(Result_Field).title = Result;\n" .
		 "   }\n" .
		 "	 if ( ! pwd.match( regex_num ) ) {\n" .
		 "    Result += '" . $L_Use_Number . ". ';\n" .
		 "    document.getElementById(Result_Field).title = Result;\n" .
		 "   }\n" .
		 "   if ( ! pwd.match( regex_sc ) ) {\n" .
		 "    Result += '" . $L_Use_Special_Chars . ". ';\n" .
		 "    document.getElementById(Result_Field).title = Result;\n" .
		 "   }\n" .
		 "   break;\n" .
		 "  }\n" .
		 "  if ( Result != '' && pwd != '' ) {\n" .
		 "   document.getElementById(Result_Field).alt = 'Ko';\n" .
		 "   document.getElementById(Result_Field).src = 'Pictures/s_attention.png'\n" .
		 "  }\n" .
		 "  if ( Result == '' && pwd != '' ) {\n" .
		 "   document.getElementById(Result_Field).alt = 'Ok';\n" .
		 "   document.getElementById(Result_Field).title = 'Ok';\n" .
		 "   document.getElementById(Result_Field).src = 'Pictures/s_okay.png'\n" .
		 "  }\n" .
		 "}\n" .
		 "     </script>\n" );
	
	print( "      <form method=\"post\" name=\"connectForm\" action=\"". 
	 $Script . "?action=CMDPX\" style=\"width:50%;\">\n" .
	 "       <center>\n" .
	 "        <table>\n" .
	 "         <tr>\n" .
	 "          <td>" . $L_Password . "</td>\n" .
	 "          <td><input type=\"password\" name=\"O_Password\" /></td>\n" .
	 "         </tr>\n" .
	 "         <tr>\n" .
	 "          <td>" . $L_New_Password . "</td>\n" .
	 "          <td><input id=\"iPassword\" type=\"password\" name=\"N_Password\"  onkeyup=\"checkPassword('iPassword', 'Result', 3, 8);\" onchange=\"checkPassword('iPassword', 'Result', 3, 8);\" /><img id=\"Result\" class=\"no-border\" alt=\"Ok\" src=\"Pictures/blank.gif\" width=\"16\" /></td>\n" .
	 "         </tr>\n" .
	 "         <tr>\n" .
	 "          <td>" . $L_Conf_Password . "</td>\n" .
	 "          <td><input type=\"password\" name=\"C_Password\" /></td>\n" .
	 "         </tr>\n" .
	 "         <tr>\n" .
	 "          <td>&nbsp;</td>\n" .
	 "          <td><input type=\"submit\" class=\"button\" value=\"" . 
	 $L_Modify . "\" /><a href=\"" . $Previous_Page . "\" class=\"button\">" . 
	 $L_Return . "</a></td>\n" .
	 "         </tr>\n" .
	 "        </table>\n" .
	 "       </center>\n" .
	 "       <script>\n" .
	 "        document.connectForm.User.focus();\n" .
	 "       </script>\n" .
	 "      </form>\n" .
	 "     </center>\n" .
	 "    </div> <!-- fin : zoneMilieuComplet -->\n" .
	 "    <script>\n" .
	 "     document.connectForm.O_Password.focus();\n" .
	 "    </script>\n" .
	 $PageHTML->construireFooter() .
	 $PageHTML->piedPageHTML() );

	break;


 // Enregistre le changement de mot de passe.
 case 'CMDPX':
	include( 'Libraries/Config_Hash.inc.php' );
	include( 'Libraries/Config_Authentication.inc.php' );

	$Secrets = new IICA_Secrets( 
	 $_Host, $_Port, $_Driver, $_Base, $_User, $_Password );

	
	$Error = 0;

	if ( $_POST[ 'O_Password' ] == '' or $_POST[ 'N_Password' ] == ''
	 or $_POST[ 'C_Password' ] == '' ) {
		$Error_Message = $L_ERR_Mandatories_Fields;
		$Error = 1;
	}
	
	if (  $_POST[ 'N_Password' ] != $_POST[ 'C_Password' ] ) {
		$Error_Message = $L_ERR_Password_Confirmation;
		$Error = 1;
	}
	
	if ( $_POST[ 'O_Password' ] == $_POST[ 'N_Password' ] ) {
		$Error_Message = $L_ERR_Old_Password_Forbidden;
		$Error = 1;
	}
	
	if ( strlen( $_POST[ 'N_Password' ] ) < $_Min_Size_Password ) {
		$Error_Message = $L_ERR_Min_Size;
		$Error = 1;
	}
	
	
	if ( ! $Security->complexityPasswordControl( $_POST[ 'N_Password' ],
	 $_Password_Complexity ) ) {
		$Error_Message = ${'L_ERR_Complexity_' . $_Password_Complexity} ;
		$Error = 1;
	}

	
	if ( $Error == 1 ) {
		print( $PageHTML->returnPage( $L_Title_CMDP, $Error_Message, $Script .
		 "?action=CMDP" ) );

		exit();
	}
	
	try {
		if ( ! $Authentication->changePassword( $_SESSION[ 'idn_id' ],
		 $_POST[ 'O_Password' ], $_POST[ 'N_Password' ] ) ) {
			print( $PageHTML->returnPage( $L_Title_CMDP, $L_ERR_Modify_Password, $Script .
			 "?action=CMDP" ) );

			exit();
		}
	} catch( Exception $e ) {
		print( $PageHTML->returnPage( $L_Title_CMDP, $e->getMessage(), $Script .
		 "?action=CMDP" ) );

		exit();
	}

	$alert_message = $Secrets->formatHistoryMessage( $_SESSION[ 'cvl_first_name' ] . ' ' .
	 $_SESSION[ 'cvl_last_name' ] . '(' . $_SESSION[ 'idn_login' ] . ') - ' .
	 $L_Password_Modified );

	$Secrets->updateHistory( '', $_SESSION[ 'idn_id' ], $alert_message, $IP_Source );

	print( $PageHTML->returnPage( $L_Title_CMDP, $L_Password_Modified, $Script .
	 "?action=DCNX", 0 ) );

	break;


 // Récueille les informations d'authentification.
 default:
	include( 'Libraries/Config_Hash.inc.php' );
   
	print( $PageHTML->enteteHTML( $L_Title, $Choose_Language ) .
     "    <div id=\"icon-users\" class=\"icon36\" style=\"float: left; margin: 3px 9px 3px 3px;\"></div>\n" .
	 "    <h1 style=\"padding-top: 12px;\">" . $L_Title . "</h1>\n" .
	 "    <div id=\"zoneGauche\" >&nbsp;</div>\n" .
	 "    <!-- debut : zoneMilieuComplet -->\n" .
	 "    <div id=\"zoneMilieuComplet\">\n" .
	 "     <center>\n" .
	 "      <form method=\"post\" name=\"connectForm\" action=\"". 
     $Script . "?action=cnx\" style=\"width:50%;\">\n" .
     "       <center>\n" );

	if ( array_key_exists( 'expired', $_GET ) ) {
    	print( "        <h3 id=\"alert\">" . $L_User_Session_Expired ."</h3>\n" );
    }

	print( "        <table>\n" .
	 "         <tr>\n" .
	 "          <td>" . $L_Username . "</td>\n" .
	 "          <td><input type=\"text\" name=\"User\" /></td>\n" .
	 "         </tr>\n" .
	 "         <tr>\n" .
	 "          <td>" . $L_Password . "</td>\n" .
	 "          <td><input type=\"password\" name=\"Password\" /></td>\n" .
	 "         </tr>\n" .
	 "         <tr>\n" .
	 "          <td>&nbsp;</td>\n" .
	 "          <td><input type=\"submit\" class=\"button\" value=\"" . 
	 $L_Connect . "\" /></td>\n" .
	 "         </tr>\n" .
	 "        </table>\n" .
	 "       </center>\n" .
	 "       <script>\n" .
	 "        document.connectForm.User.focus();\n" .
	 "       </script>\n" .
	 "      </form>\n" .
	 "     </center>\n" .
	 "    </div> <!-- fin : zoneMilieuComplet -->\n" .
	 $PageHTML->construireFooter() .
	 $PageHTML->piedPageHTML() );

	break;


 // Contrôle les éléments d'authentification.
 case 'CNX':
	if ( $_POST[ 'User' ] == '' and $_POST[ 'Password' ] == '' ) {
		print( $PageHTML->returnPage( $L_Title, $L_ERR_Mandatories_Fields, $Script ) );
		exit();;
	}
	
	switch ( strtoupper( $Parameters->get( 'authentication_type' ) ) ) {
	 default:
		$Authentication_Type = 'database';
		break;
		
	 case 'R':
		$Authentication_Type = 'radius';
		break;
	}

	try {
		if ( ! ($Salt = $Authentication->getSalt( $_POST[ 'User' ] )) ) {
			$alert_message = $Secrets->formatHistoryMessage( $L_Err_Auth . ' (' .
			 $_POST[ 'User' ] . ') [' . $Authentication_Type . ']' );

			$Secrets->updateHistory( '', 0, $alert_message, $IP_Source );
			
			print( $PageHTML->returnPage( $L_Title, $L_Err_Auth . ' (1)', $Script ) );

			exit();
		}
	
		if ( ! $Authentication->authentication( $_POST[ 'User' ],
		 $_POST[ 'Password' ], $Authentication_Type, $Salt ) ) {
			$Authentication->addAttempt( $_POST[ 'User' ] );

			$alert_message = $Secrets->formatHistoryMessage( $L_Err_Auth . ' (' .
			 $_POST[ 'User' ] . ') [' . $Authentication_Type . ']' );

			$Secrets->updateHistory( '', 0, $alert_message, $IP_Source );
			
			print( $PageHTML->returnPage( $L_Title, $L_Err_Auth . ' (2)', $Script ) );

			exit();
		}
	} catch( Exception $e ) {
		$Authentication->addAttempt( $_POST[ 'User' ] );
			
		$alert_message = $Secrets->formatHistoryMessage( $e->getMessage() . ' (' .
		 $_POST[ 'User' ] . ')' );

		$Secrets->updateHistory( '', 0, $alert_message, $IP_Source );
			
		print( $PageHTML->returnPage( $L_Title, $e->getMessage(), $Script ) );

		exit();
	}

	if ( $_SESSION[ 'idn_change_authenticator' ] == 1 ) {
		$alert_message = $Secrets->formatHistoryMessage( $L_Change_Password . ' ' .
		 $_SESSION[ 'cvl_first_name' ] . ' ' . $_SESSION[ 'cvl_last_name' ] .
		 ' (' . $_SESSION[ 'idn_login' ] . ')' );

		$Secrets->updateHistory( '', $_SESSION[ 'idn_id' ], $alert_message, $IP_Source );
			
		header( 'Location: https://' . $Server . dirname( $Script ) .
		 '/SM-login.php?action=CMDP&mandatory' );
		
		break;
	}

	$alert_message = $Secrets->formatHistoryMessage( $L_Connection . ' ' .
	 $_SESSION[ 'cvl_first_name' ] . ' ' . $_SESSION[ 'cvl_last_name' ] .
	 ' (' . $_SESSION[ 'idn_login' ] . ')' );

	$Secrets->updateHistory( '', $_SESSION[ 'idn_id' ], $alert_message, $IP_Source );
			
	header( 'Location: https://' . $Server . dirname( $Script ) .
	 '/SM-home.php?last_login' );
   
	break;
}
?>