#!/usr/bin/php
<?php
$cmd = getopt('f:v', array('self', 'file:'));
$verbose = (isset($cmd['v']));
$str = NULL;
$str = (isset($argv[1])) ? implode( ' ', array_slice($argv, 1) ) : $str;
$str = isset( $cmd['self'] ) ? file_get_contents(__FILE__) :$str;
$file = (isset($cmd['f'])) ? $cmd['f']: (isset($cmd['file'])) ? $cmd['file']: false;
$str = ($file) ? file_get_contents($file) : $str;
if($str == NULL){
	$stdin = '';
	while (false !== ($line = fgets(STDIN))) {
		$stdin .= $line;
	}
	$str = ($stdin) ?: $str;
}

$str = str_replace( "\r\n", "\n", $str );
$str = str_replace( "\r", "\n", $str );
$l = strlen( $str );
echo $str;
echo "\n========================================";
echo "========================================\n\n";
$special = array();
$hasNL = ( false !== strpos( $str, "\n" ) );
$hasSpace = ( false !== strpos( $str, ' ') );
$prefix = '';
$pValue = 0;
$first_chr = ord( $str[0] );
if( $hasNL && $hasSpace ){
	$factor = 8;
	$first = floor( $first_chr / $factor );
	$prefix = r('+', $factor).'[>+>++++>'.r('+', $first).'<<<-]>++>>';
	$special[32] = '<.>';
	$special[10] = '<<.>>';
	$pValue = $n * $factor;
}
if( !$hasNL && $hasSpace ){
	$factor = 8;
	$first = floor( $first_chr / $factor );
	$prefix = r('+',$factor).'[>++++>'.r('+', $first).'<<-]>>';
	$special[32] = '<.>';
	$special[10] = '';
	$pValue = $first * $factor;
}
if( $hasNL && !$hasSpace ){
	$factor = 5;
	$first = floor( $first_chr / $factor );
	$prefix = r('+',$factor).'[>++>'.r('+', $first).'<<-]>>';
	$special[10] = '<.>';
	$special[32] = '';
	$pValue = $first * $factor;
}


ob_start();
echo $prefix;
for( $i = 0; $i < $l; $i++ ){
	$chr = ord( $str[$i] );
	if( isset( $special[$chr] ) ){
		$specialRepeat = 0;
		$j = $i+1;
		while( isset($str[$j]) && $str[$i] == $str[$j] ){
			$specialRepeat++;
			$j++;
		}
		if( $specialRepeat ){
			$i += $specialRepeat;
			echo str_replace('.', r('.', $specialRepeat ), $special[$chr] );
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
		echo '>'.r( '+', $num ).'[<'.r( $mChar, $div ).'>-]<';
	} elseif( $diff ) {
		echo r( $mChar, $diff );	
	}
	
	echo '.';
	if( $verbose ) echo '# '.$str[$i]." \n";
}
$buff = ob_get_contents();;
ob_end_clean();

echo wordwrap( $buff, 80, "\n", true );
echo "\n\n========================================";
echo "========================================\n\n";

function r($s, $n){
	return str_repeat($s, $n);
}

function getFactor($n){
	$root = sqrt( $n );
	if( $root >= 5 && round($root) == $root ){
		return $root;
	}
	$x = $n/2;
	$candidates = array();  // Numbers that may fit.
	for($i = 5; $i < $x; $i++) {
		if ($n % $i == 0) 
			$candidates[$i] = $i - ($n / $i); // difference
	}
	if( $candidates ){
		asort( $candidates );	
		$keys = array_keys( $candidates );
		return array_shift( $keys );
	}
	return false;
}

echo "\n\n";
?>