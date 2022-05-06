<?php

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Promise\Utils;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Psr\Http\Message\ResponseInterface;

/**
 * @param string $key
 * @return string
 */
function getPostOrRedirect(string $key):string {
	if(isset($_POST[$key])){
		return $_POST[$key];
	}

	header('Location: /');
	die();
}


$promises = [];
$start = getPostOrRedirect('start');
$end = getPostOrRedirect('end');
$url = getPostOrRedirect('url');

$jar = CookieJar::fromArray([
	'JSESSIONID' => getPostOrRedirect('JSESSIONID'),
	'remember-me' => getPostOrRedirect('remember-me')
], 'www.europathek.de');

$options = ['cookies' => $jar];

$pdf = new Mpdf();
$client = new Client();

for ($i = $start;$i < $end;$i++){
	$promises[] = $client
		->getAsync("$url$i.jpg", $options)
		->then(function(ResponseInterface $response) use ($i){
			global $pdf;
			$pdf->imageVars[$i] = $response->getBody()->getContents();
		});
}

try {
	ob_start();
	include __DIR__ . '/template.php';
	Utils::unwrap($promises);
	$pdf->WriteHTML(ob_get_clean());
	$pdf->Output('doc.pdf', Destination::INLINE);
} catch (Throwable $e) {
	echo $e->getMessage();
}