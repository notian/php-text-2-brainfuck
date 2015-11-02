#!/usr/bin/php
<?php
$cmd = getopt('f:v', array('self', 'file:'));
/*
$stdin = '';
while (false !== ($line = fgets(STDIN))) {
  echo $stdin .= $line;
}
*/
$verbose = (isset($cmd['v']));
$str = 'Hello World';
$str = (isset($argv[1])) ? implode( ' ', array_slice($argv, 1) ) : $str;
$str = isset( $cmd['self'] ) ? file_get_contents(__FILE__) :$str;
$file = (isset($cmd['f'])) ? $cmd['f']: (isset($cmd['file'])) ? $cmd['file']: false;
$str = ($file) ? file_get_contents($file) : $str;
$str = ($stdin) ?: $str;

echo $str;
echo "\n========================================";
echo "========================================\n\n";
$str = str_replace( "\r\n", "\n", $str );
$str = str_replace( "\r", "\n", $str );
$l = strlen( $str );

$special = array();
$hasNL = ( false !== strpos( $str, "\n" ) );
$hasSpace = ( false !== strpos( $str, ' ') );
$prefix = '';
$pValue = 0;
if( $hasNL && $hasSpace ){
	$prefix = '+++++[>++>++++++>+++++++++<<<-]>>++>';
	$special[32] = '<.>';
	$special[10] = '<<.>>';
	$pValue = 45;
}
if( !$hasNL && $hasSpace ){
	$prefix = '+++++[>++++++>+++++++++<<-]>++>';
	$special[32] = '<.>';
	$special[10] = '';
	$pValue = 45;
}
if( $hasNL && !$hasSpace ){
	$prefix = '++[>+++++>+++++++++<<-]>';
	$special[10] = '<.>';
	$special[32] = '';
	$pValue = 45;
}


ob_start();
echo $prefix;
for( $i = 0; $i < $l; $i++ ){
	$chr = ord( $str[$i] );
	if( isset( $special[$chr] ) ){
		$specialRepeat = 0;
		while( $str[$i] == $str[++$i] ){
			$specialRepeat++;
		}
		if( $specialRepeat ){
			$i += $specialRepeat;
			echo str_replace('.', printX('.', $specialRepeat, true ), $special[$chr] );
		}else{
			echo $special[$chr];
		}
		continue;
	}
	if( $chr == $pValue ){
		echo '.';
		continue;
	}
	
	$diff = $chr - $pValue;
	$div = false;
	$pValue = $chr;
	$mChar = '+';
	if($diff < 0){
		$mChar = '-';
		$diff = $diff * -1;
	}

	while($diff > 5){
		$div = getFactor( $diff );
		if( $div ) {
			break;
		}
		echo $mChar;
		--$diff;
	}
	
	if( $div ){
		$num = $diff / $div;
		echo '>'.printx( '+', $num, 1 ).'[<'.printx( $mChar, $div, 1 ).'>-]<';
	} elseif( $diff ) {
		printX( $mChar, $diff );	
	}
	
	echo '.';
	if( $verbose ) echo '# '.$str[$i]." \n";
}
$buff = ob_get_contents();;
ob_end_clean();

echo wordwrap( $buff, 80, "\n", true );
echo "\n\n========================================";
echo "========================================\n\n";

function printX($chr, $n, $return = false ){
	$rVal = implode('', array_fill( 0, $n, $chr ));
	if($return) return $rVal;
	echo $rVal;
}

function getFactor($n){
	$x = 5;
	while( $x++ < 15 ){
		if( $n % $x == 0 && $n/$x > 1) return $x;
	}
	return false;
}

echo "\n\n";
?>