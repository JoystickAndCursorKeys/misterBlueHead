<?php

function feedback(  $input, $session ) {

    $feedback=array();
    $debug = false; 
    $wordArray = preProcessInput( $input );

    $emptyLikelyHood = 0;
    $questionLikelyHood = 0;
    $questionLikelyHood = isQuestion( $wordArray ) ; 

    $duplicates = 0;
    for ( $i = count( $session ) - 20 ; $i < count( $session) ; $i++) {
        if( $i >= 0 ) {
            if( substr( $session[$i], 0,1) == "<" ) {
                if ( substr( $session[$i], 1) == $input ) {
                    $duplicates++;
                 }
            }
        }
    }

    $lastreply="";
    for ( $i = count( $session ) - 5 ; $i < count( $session) ; $i++) {
        if( $i >= 0 ) {
            if( substr( $session[$i], 0,1) == ">" ) {
                $lastreply = $session[$i];
            }
        }
    }

    $returnQuestion = false;
    if( lastchar ( $lastreply ) == "?" ) {
        $returnQuestion = true;
    }

    $debugstring = "";
    if( $debug = true ) {
        $debugstring = "Q=".$questionLikelyHood." E=".$emptyLikelyHood." D=". $duplicates. " RQ=".$returnQuestion;
    }


    $feedback[ 1 ] = "neutral".mt_rand(1,2); 
    $feedback[ 2 ] = $debugstring;

    $wordArray = preProcessInput2 ( $wordArray );

	
    $errorlist=array();
    if( $duplicates > 2 and $returnQuestion == false ) {
            
            
            $errorlist[0]="duplicate_inputs";
            $thought = think( $errorlist );
            $feedback[ 0 ] = $thought[ "presentation" ];
            $feedback[ 1 ] = "notsure"; 
            $debugstring.=" ".$thought[ "debug" ];
            $feedback[ 2 ] = $debugstring;

            return  $feedback;

            #return $reply["string"].$debugstring;
    }
    else if( $returnQuestion and $questionLikelyHood < .4) {
            
            $errorlist[0]="returnquestion_in_output";
            $thought = think( $errorlist );
            
            $feedback[ 0 ] = $thought[ "presentation" ];
            $feedback[ 1 ] = "amused".mt_rand(1,2); 
            $debugstring.=" ".$thought[ "debug" ];
            $feedback[ 2 ] = $debugstring;

            return  $feedback;

            #return $reply["string"].$debugstring;
    }
    else {
        if( count ( $wordArray ) == 0 ) {

            $errorlist[0]="no_word_sentence";
            $thought = think( $errorlist );

            $feedback[ 0 ] = $thought[ "presentation" ];
            $feedback[ 1 ] = "confused"; 
            $debugstring.=" ".$thought[ "debug" ];
            $feedback[ 2 ] = $debugstring;

            return  $feedback;
        }
        else {

            $thought = think( $wordArray );
            $feedback[ 0 ] = $thought[ "presentation" ];
            $debugstring.=" ".$thought[ "debug" ];

            if( $thought["understood"] == false and count( $wordArray ) == 1 ) {
                $errorlist[0]="single_word_sentence";
                $thought = think( $errorlist );

                $feedback[ 0 ] = $thought[ "presentation" ];
                $feedback[ 1 ] = "confused"; 
                $debugstring.=" ".$thought[ "debug" ];
                $feedback[ 2 ] = $debugstring;
                return $feedback;
            }
            else {
                $debugstring.=" ".$thought[ "debug" ];
                $feedback[ 2 ] = $debugstring;
                return $feedback;
            }
        }
    }


    return "OK Google Q=".$questionLikelyHood." E=".$emptyLikelyHood." G=".$greetingLikelyHood." B=".$goodbyeLikelyHood;
}


function isQuestion( $wordArray ) {
    $likelyHood = 0;
    #echo "WA=" . $wordArray[0];

    $questionStarters = fileToArrayNice( "questionstarters.txt" );
	
	
	
    for( $i=0; $i<count( $questionStarters ) ; $i++ ) {
        for( $j=0; $j<count( $wordArray ) ; $j++ ) {
				
            if( wordMatch( $wordArray[ $j ] , $questionStarters[ $i ] ) ) {
                if ( $j == 0 ) {
                    $likelyHood += .60;
                }
                else if ( $j == 1 ) {
                    $likelyHood += .40;
                }
                else  {
                    $likelyHood += .10;
                }
                break;
            }
        }
    }

    if( contains( lastStringItem( $wordArray ) , "?" )) {
        $likelyHood = 1;
    }

    if( $likelyHood > 1 ) {
        $likelyHood = 1;
    }
    return $likelyHood;
}


function MatchSentences2( $wordArray1, $wordArray2, $wordScores ) {
 
    $debug = false; 
    if( $debug ) {
        $debugPrefix = "MatchSentences2";
    }
    if( $debug ) {
        echo "<br>$debugPrefix::MatchSentences2<br>";
        var_dump( $wordArray1 );
        echo "<br>";
        var_dump( $wordArray2 );
        echo "<br>";
        echo "$debugPrefix------------------<br>";
    }
    $matchedWords = 0;
    for( $i2=0; $i2<count( $wordArray2 ) ; $i2++ ) {
        if( $debug ) {
            echo "$debugPrefix::wordArray2=" . $wordArray2[$i2]."<br>";
        }

        $prevWord2="";
        $nextWord2="";
        if($i2>0) {
            $prevWord2 = $wordArray2[ $i2-1 ];
        }
        if($i2< (count( $wordArray2 )-1) ) {
            $nextWord2 = $wordArray2[ $i2+1 ];
        }

        $matched = false;

        for( $j=0; $j<count( $wordArray1 ) ; $j++ ) {

            if( $debug ) {
                echo "$debugPrefix::i2=" . $i2."<br>";
                echo "$debugPrefix::j=" . $j."<br>";
            }
            $prevWord1="";
            $nextWord1="";
            if($j>0) {
                $prevWord1 = $wordArray1[ $j-1 ];
            }
            if($j< (count( $wordArray1 )-1) ) {
                $nextWord1 = $wordArray1[ $j+1 ];
            }

            if( $debug ) {
                echo "$debugPrefix::WA1=" . $wordArray1[$j]."<br>";
            }
            if( wordMatch( $wordArray1[ $j ] , $wordArray2[ $i2 ] ) ) {
                $score = $wordScores[ alphaFilter( strtolower( $wordArray2[ $i2 ]  )) ];
                
                $scorefactor = 1;
                if  ( wordMatch( $prevWord1, $prevWord2 )) {
                    $scorefactor ++;
                }
                if ( wordMatch ( $nextWord1, $nextWord2 )) {
                    $scorefactor ++;
                }

                $matchedWords += $score * $scorefactor;
                if( $debug ) {
                echo "$debugPrefix::word >>'" . $wordArray2[ $i2 ] . "'<< matched, points = " . $score. "*" . $scorefactor."<br>";
                }
                break;
            }
        }
        if( $matched == false ) {
            $score = $wordScores[ alphaFilter( strtolower( $wordArray2[ $i2 ]  )) ];
            $matchedWords-=$score;
        }
        if( $debug ) {
                    echo "$debugPrefix::GmatchedSentenceScore=" . $matchedWords . "<br>";
        }

    }

/*    if( $matchedWords > count( $wordArray1 ) ) {
        $matchedWords = count( $wordArray1 ) * .75;
    }
    $Fraction = $matchedWords / count( $wordArray1 );
    if( $debug ) {
        echo "$debugPrefix::Fraction=" . $Fraction . "<br>";
    }*/
    return $matchedWords;
}


function MatchSentences( $wordArray1, $wordArray2 ) {
    
    $debug = false;
    if( $debug ) {
        $debugPrefix = "MatchSentences";
    }
    if( $debug ) {
        echo "<br>$debugPrefix::MatchSentences<br>";
        var_dump( $wordArray1 );
        var_dump( $wordArray2 );
    }
    $matchedWords = 0;
    for( $i2=0; $i2<count( $wordArray2 ) ; $i2++ ) {
        if( $debug ) {
            echo "$debugPrefix::wordArray2=" . $wordArray2[$i2]."<br>";
        }

        for( $j=0; $j<count( $wordArray1 ) ; $j++ ) {

            if( $debug ) {
                echo "$debugPrefix::WA1=" . $wordArray1[$j]."<br>";
            }
            if( wordMatch( $wordArray1[ $j ] , $wordArray2[ $i2 ] ) ) {
                $matchedWords ++;
                break;
            }
        }
        if( $debug ) {
                    echo "$debugPrefix::GmatchedWords=" . $matchedWords . "<br>";
        }

    }

    if( $matchedWords > count( $wordArray1 ) ) {
        $matchedWords = count( $wordArray1 ) * .75;
    }
    $Fraction = $matchedWords / count( $wordArray1 );
    if( $debug ) {
        echo "$debugPrefix::Fraction=" . $Fraction . "<br>";
    }
    return $Fraction;
}


function contains(  $string, $substring ) {
    $pos = strpos($string, $substring);
    if( $pos === false ) {
        return false;
    }
    else {
        return true;
    }
}





function wordMatch(  $_str1, $_str2 ) {
    $str1 = alphaFilter( $_str1 );
    $str2 = alphaFilter( $_str2 );

    if( strtolower( $str1 ) == strtolower( $str2 ) ) {
        return true;
    }
    return false;
}


function lastStringItem(  $array ) {
    if( count( $array ) == 0 ) {
        return "";
    }
    return $array[ count( $array) - 1 ];
}


function notEmpty(  $string ) {
   if( $string == "" ) {
        return false; 
    }
    return true;
}


function preProcessInput( $input )
{
    $wordArray = array_filter( explode( " ",$input), "notEmpty");
    for( $j=0; $j<count( $wordArray ) ; $j++ ) {
		if( array_key_exists( $j, $wordArray ) === false ) {
			$wordArray[ $j ] = "";
		}
    }
    return $wordArray;
}


function preProcessInput2( $wordArray1 )
{
    $wordArray2 = array();
    for( $j=0; $j<count( $wordArray1 ) ; $j++ ) {
        $wordArray2[ $j ] = alphaFilter ( $wordArray1[ $j ] );
    }
    return $wordArray2;
}

/*

BS Session
Silence - detected
Questions   - detected
    OnTopiuc
    OffTopic
Comments & Remarks

Greetings
    detected
Goodbyes

Insults
Jokes


Internal state
    repeats
    repeat_often
    

*/



function scoreWords( $wordArray, $scoresIn ) {

    $scoresOut = $scoresIn;

    for( $i=0; $i<count( $wordArray ) ; $i++ ) {
        $key = alphaFilter( strtolower( $wordArray[ $i ] ) );
        if (array_key_exists( $key, $scoresOut)) {
            $scoresOut[ $key ]++;
        }
        else {
            $scoresOut[ $key ] = 1;
        }
    }
    return $scoresOut;
}


function prepareAlternatives( $alterNatives, $choices ) {
    $alternatives2=prepareAlternatives0( $alterNatives, $choices );
    for ( $i = 0; $i < count( $alternatives2 ) ; $i++ ) {
            $alternatives2[ $i ] = strtolower( $alternatives2[ $i ] );
    }
    return $alternatives2;
}

function prepareAlternatives0( $alterNatives, $choices ) {

    $debug=false;
    if( $debug) {
        $debugprefix="prepareAlternatives:";
    }
    if( $debug) {
        echo $debugprefix."prepareAlternatives<br>";
    }
    if( $choices == null ) { 
        if( $debug) {
            echo $debugprefix."Passthrough<br>";
        }
        return $alterNatives;
    }
    elseif( count ( $choices ) == 0 ) { 
        if( $debug) {
            echo $debugprefix."Passthrough<br>";
        }
        return $alterNatives;
    }
    
    if( $debug) {
        for ( $i = 0; $i < count( $alterNatives ) ; $i++ ) {
            echo $debugprefix."old:". $alterNatives[ $i] ."<br>";
        }
    }

    $alternatives2 = array();
    $i2=0;
    for ( $i = 0; $i < count( $alterNatives ) ; $i++ ) {
        for ( $j = 0; $j < count( $choices ) ; $j++ ) {
            $alternatives2[ $i2 ] = $str2 = str_replace('%C', $choices[ $j ] , $alterNatives[ $i ]);
            $i2++;
        }
    }

    if( $debug) {
        for ( $i = 0; $i < count( $alternatives2 ) ; $i++ ) {
            echo $debugprefix."new:". $alternatives2[ $i] ."<br>";
        }
    }

   return $alternatives2;
       
}


function getReferenceText(  )
{
    $array = file(  "referencetext.txt" , FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
    $array3=array();
    $k=0;

    for( $i=0; $i <count( $array ) ; $i++ ) {
        $array2=preProcessInput2( preProcessInput( $array[ $i ] ) );

        for( $j=0; $j <count( $array2 ) ; $j++ ) 
        {
            $array3[ $k ] = $array2[  $j ]; 
            $k++;
        }

    }

    return $array3;
}



function think( $wordArray ) {
    $matchedWordsScoreMax = 0.2; /*question must at least match 20% */
    $matchedWordsScoreMaxIndex = 0; /*Otherwise it defaults to the I don't know answer */
    $matchedWordsScoreMaxQuestionDebug = "";
    $matchedWordsScoreWinnerQuestionDebug = "";
    $complexAnswerArray0 = JSONRead( "data.txt" );

    $complexAnswerArray1 = $complexAnswerArray0[ "connections" ];

    $debug="";
    /* score words by usage */
    
    $wordScores=array();
    $refWords = getReferenceText();
    $wordScores = scoreWords(  $refWords, $wordScores );

    for( $i=0; $i<count( $complexAnswerArray1 ) ; $i++ ) {

        $complexAnswer=$complexAnswerArray1[ $i ];
        $input_choices=null;
        if( array_key_exists ( "input_choices", $complexAnswer ) ) {
             $input_choices = $complexAnswer["input_choices"];
        }

        $currentQuestionAlternatives=prepareAlternatives( $complexAnswer["input_frases"], $input_choices );
        for( $j=0; $j<count( $currentQuestionAlternatives ) ; $j++ ) {
            $questionWA = array_filter( explode( " ",$currentQuestionAlternatives[ $j ]), "notEmpty");
            $wordScores = scoreWords(  $questionWA, $wordScores );
        }
    }


    $debugarray2=array();
    $maxvalue=0;
    foreach ( $wordScores as $k => $v) {
        ###$debugarray2[ $v.".." ] = $k;
        if( $v > $maxvalue ) {
            $maxvalue = $v;
        }
    }

    asort ( $wordScores, SORT_NUMERIC  );
    foreach ( $wordScores as $k => $v) {
        #echo "wordScores[ $k ]=" . $v ."<br>";
    }

    #echo "maxvalue=" .$maxvalue."<br>";

    foreach ( $wordScores as $k => $v) {
        $wordScores2[ $k ] = 1 + ($maxvalue - $v);
        #echo "wordScores2[ $k ]=" . $wordScores2[ $k ] ."<br>";
    }


/*choose most matching question */
    for( $i=0; $i<count( $complexAnswerArray1 ) ; $i++ ) {

        $complexAnswer=$complexAnswerArray1[ $i ];

        $input_choices=null;
        if( array_key_exists ( "input_choices", $complexAnswer ) ) {
             $input_choices = $complexAnswer["input_choices"];
        }
        $currentQuestionAlternatives=prepareAlternatives( $complexAnswer["input_frases"], $input_choices );

        for( $j=0; $j<count( $currentQuestionAlternatives ) ; $j++ ) {

            $questionWA = array_filter( explode( " ",$currentQuestionAlternatives[ $j ]), "notEmpty");
            $matchedWordsScore = MatchSentences2( $wordArray, $questionWA, $wordScores2 );

             $matchedWordsScoreMaxQuestionDebug = $currentQuestionAlternatives[ $j ] . ":" .$matchedWordsScore ;
             ##echo "matchedWordsScoreMaxQuestionDebug:".$matchedWordsScoreMaxQuestionDebug."<br>";

            if( $matchedWordsScoreMax < $matchedWordsScore ) {
                $matchedWordsScoreMax = $matchedWordsScore;
                $matchedWordsScoreMaxIndex = $i;

                $matchedWordsScoreWinnerQuestionDebug = $currentQuestionAlternatives[ $j ] . ":" .$matchedWordsScore ;
                #echo "Max is now " . $matchedWordsScoreMax . "<br>";

            }
        }
    }

    #echo "Matched sentence " . $matchedWordsScoreWinnerQuestionDebug . "<br>";
    
    $answer = array();
    $answer["understood"] = true;
    if( $matchedWordsScoreMax < ( $maxvalue * .5 ) ) {
        $matchedWordsScoreMaxIndex = 0;
        $answer["understood"] = false;
        #echo "score too low, default to answer 0<br>";
    }
    $debug.="score=".$matchedWordsScoreMaxIndex." ";

    $complexAnswer = $complexAnswerArray1[ $matchedWordsScoreMaxIndex ];
    $answers = $complexAnswer[ "output_frases" ];
    $output_appendixes = $complexAnswer[ "output_appendixes"];
    ###echo "count=".count( $answers );
    $string = $answers[mt_rand(0,count( $answers )-1)];
    $chance = mt_rand(0,5 ) ;
    $append = "";
    $appendix = -1;
    
    if( $chance != 0 and count( $output_appendixes )>0) {
        $appendix = mt_rand(0,count( $output_appendixes )-1);
    }
    if( $appendix > -1 ) {    
        $debug.="APPEND ";
        $append =  $output_appendixes[ $appendix ] ;
    }

    $string = append_with_punctiation( $string, $append );

    $cv = "{{a target=\"_blank\" href=\"CV2015-DustyMurray.pdf\"}}Here{{/a}}";
    $string = str_replace('%CV', $cv, $string);
    $br = "{{br}}";
    $string = str_replace('%BR', $br, $string);

    $answer[ "presentation" ] = utf8_decode ( $string );
    $answer[ "debug" ] = $debug;
    return $answer;
}


?>