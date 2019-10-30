<html>
<head>
<title>Dusty Murray's talking CV prototype</title>
<link rel="stylesheet" href="style.css">
<link rel="icon" 
      type="image/png" 
      href="img/favicon.bmp">
</head>
<body>



<?php 
include "lib.php";
include "feedback.php";
$session = "";
$feedback = array();
if ( array_key_notexists_orisempty(  "session", $_REQUEST ) == true ) {
    $sessionId = getSessionId();
    $initialgreeting = getInitialGreeting();
    capture( "", $initialgreeting, $sessionId, false );
     $feedback[ 0 ] = $initialgreeting;
     $feedback[ 1 ] = "neutral1";
     $feedback[ 2 ] = "";
    $session = loadSession( $sessionId );
}
else {
    $sessionId = $_REQUEST["session"];
    $session = loadSession( $sessionId );
    $feedback = feedback( $_REQUEST["input"] , $session );
    if( $_REQUEST["input"] == "" ) {
        capture( "", $feedback[ 0 ], $sessionId , true);
    }
    else
    {
        capture( $_REQUEST["input"], $feedback[ 0 ], $sessionId , true);
    }
    $session = loadSession( $sessionId );
}

$img = "img/".$feedback[ 1 ].".jpg";

?>
<center>
<div class="bigdiv">

<div class="vcenterdiv" align="left">
<center><img src=logo.png></center>
<?php
$istart = count( $session ) - 10;
if ( $istart < 0 ) {
    $istart = 0; 
}
echo "<div class=\"history\">";
for( $i=$istart; $i < count( $session ) - 2 ; $i++ ) {
    echo htmlspecialchars2 ( $session[$i] )."<br>";
}
echo "</div>";
if( count( $session ) - 2 >= 0 ) {
    echo "<div class=\"lastrowmin1\">";
    echo htmlspecialchars2 ( $session[ count( $session ) - 2 ] )."<br>";
    echo "</div>";
}
echo "<div class=\"lastrow\">";

echo htmlspecialchars2 ( $session[ count( $session ) - 1 ] )."<br>";
echo "</div>";

?>
<form  action="" method="GET">
<input id="input" name="input" type="text" style="width: 100%">
<input name="session" type="hidden" value="<?php echo $sessionId; ?>">
<br>
</form>
</div>
</div>  

<div align="right" class="mediv">
<img id="me" src="<?php echo $img;?>" class="mepic">
</div>

</center>
<script>
input=document.getElementById('input');
input.focus();

document.getElementById('input').onkeypress = function(e) {
    var event = e || window.event;
    var charCode = event.which || event.keyCode;

    if ( charCode == '13' ) {
      document.getElementById('me').src = "img/thinking.jpg";
      return true;
    }
}

var images = new Array()
function preload() {
    for (i = 0; i < preload.arguments.length; i++) {
        images[i] = new Image()
        images[i].src = preload.arguments[i]
    }
}
preload(
    "img/neutral1.jpg",
    "img/neutral2.jpg",
    "img/thinking.jpg",
    "img/notsure.jpg",
    "img/amused1.jpg",
    "img/amused2.jpg",
    "img/confused.jpg",
    "img/blank.jpg"
)

var timeoutCounter = 0;
var timeout2Counter = 0;
var timeoutCounterMax = 10;
var timeout2CounterMax = 333;
window.setTimeout(function () {TimeOutPictureHandler()}, 1000);

function nextMoveInMillis() {
    return Math.floor((Math.random() * 1500) + 500);
}

function TimeOutPictureHandler() {
    imgnr = Math.floor((Math.random() * 2) + 1);
    document.getElementById('me').src = "img/neutral"+imgnr+".jpg";

    window.setTimeout(function () {TimeOutPictureHandler2()}, nextMoveInMillis());

    
}

function TimeOutPictureHandler2() {

    timeoutCounter = timeoutCounter + 1;
    timeout2Counter = timeout2Counter + 1;

    if( timeout2Counter > timeout2CounterMax ) {
            document.getElementById('me').src = "img/blank.jpg";
    }
    else {
        if( timeoutCounter > timeoutCounterMax ) {
            document.getElementById('me').src = "img/thinking.jpg";
            timeoutCounter = 0;
            timeoutCounterMax = Math.floor((Math.random() * 15) + 5);
        }
        else
        {
            imgnr = Math.floor((Math.random() * 2) + 1);
            document.getElementById('me').src = "img/neutral"+imgnr+".jpg";
        }
    }
    window.setTimeout(function () {TimeOutPictureHandler2()}, nextMoveInMillis());
}

</script>
</body>
</html>