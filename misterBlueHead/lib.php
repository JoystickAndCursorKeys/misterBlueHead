<?php

function sessionFile ( $sessionId ) {
    return "sessions/".$sessionId.".php" ;
}


function capture( $input, $feedback, $sessionId, $keepemptyinput ) {
    $string="";
    if( $input != "" or $keepemptyinput) {
        $string.="\n<".$input;
    }
    if( $feedback != "" ) {
        $string.="\n>".$feedback;
    }

    file_put_contents ( sessionFile( $sessionId) , 
            $string , LOCK_EX | FILE_APPEND );
}

function lastchar ( $str ) {
    if ( strlen ( $str ) == 0 ) { return ""; }
    return substr( $str, -1);
} 

function stripPunctuation( $_str ) {
   return rtrim( rtrim($_str, '.') , ',' ) ;
}

function append_with_punctiation( $_str1, $_str2 ) {
    $str1 = $_str1;
    $str2 = $_str2;

    if( $str2 == "" ) {
        $str1 = stripPunctuation( $str1 );
        if( alphaFilter( lastchar( $str1 ) ) == "" ) {
        }
        else {
            $str1.=".";
        }
    }
    else
    {
        $str1 = stripPunctuation( $str1 );
        if( alphaFilter( lastchar( $str1 ) ) == "" ) {
            $str1.=" ";
        }
        else {
            $str1.=", ";
        }
        $str1 .= stripPunctuation( $str2 );

        if( alphaFilter( lastchar( $str1 ) ) == "" ) {
        }
        else {
            $str1.=".";
        }

    }  
    return $str1;
}


function alphaFilter(  $str ) {
    $str2="";
     
    for( $i=0; $i<strlen ( $str ) ; $i++ ) {
        if( ctype_alpha( $str[ $i ] ) ) {
            $str2.=$str[ $i ];
        }
    }
    return $str2;
}

function fileToArrayNice( $fileName )
{
    $array = file(  $fileName , FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
    return $array;
}




function JSONRead( $fileName )
{
    $string = file_get_contents(  $fileName );
    $string = utf8_encode($string); 
    $object = json_decode($string , true);
    return $object;
}


function loadSession( $sessionId )
{
    $session = fileToArrayNice( sessionFile( $sessionId) );
    return $session;
}


function getSessionId(  ) {
    return mt_rand();
}

function htmlspecialchars2( $str ) {
    $str2 = $str;
    $str2 = str_replace('<', "&lt;", $str2);
    $str2 = str_replace('>', "&gt;", $str2);
    $str2 = str_replace('{{', "<", $str2);
    $str2 = str_replace('}}', ">", $str2);
    return $str2;
}

function array_key_notexists_orisempty( $key, $array )
{
    if( array_key_exists( $key, $array ) === false ) {
        return true;
    }

    if ( $array[ $key ] == "" ) {
        return true;
    }

    return false;
}


function getInitialGreeting()
{
    $allGreetings = file( 'greeting0.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
    $index = 0;
    return $allGreetings[ $index ];
}

?>