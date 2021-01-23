<?php

require_once('./require.php');

use qexyorg\MCServerInfo\MCServerInfo;

$config = [
	'address' => 'mc.hypixel.net',
	'port' => 25565,
	'timeout' => 3,
	'cache' => true,
	'cache_expire' => 20,
	'cache_dir' => null, // default
];

$mt = microtime(true);

$connect = MCServerInfo::Connect($config['address'], $config['port'], $config['timeout'])
	->setCacheStatus($config['cache'])
	->setCacheExpire($config['cache_expire'])
	->setCacheDirectory(dirname(__DIR__).'/tmp');

$status = 'offline';
$online = 0;
$slots = 0;
$error = '';

$json = "[]";

$method = "QUERY";

if(!$connect->request()){
	$error = $connect->getError();
}else{
	$status = 'online';
	$response = $connect->getResponse();

	$method = $response::METHOD_NAME;

	$online = $response->getOnline();
	$slots = $response->getSlots();

	$data = json_encode($response->rawData(),  JSON_PRETTY_PRINT);
}

$speed = microtime(true) - $mt;

?><!DOCTYPE html>
<html lang="en">
<head>
	<title>Cached | Examples | MCServerInfo</title>

	<?php require_once('./header.php'); ?>

</head>
<body>

<?php $active = 'cached'; require_once('./navbar.php'); ?>

<div class="container">
	<div class="pt-100">
		<div class="text-center">
			<h2 class="text-gray">Auto search method with cache</h2>
			<div class="pt-20">(Cache on <?php echo $config['cache_expire']; ?> seconds)</div>
		</div>
	</div>

	<div class="pt-60 text-center">
		<h4>Server <u><?php echo "{$config['address']}:{$config['port']}"; ?></u> is <span class="<?php if($status == 'online'){ echo 'text-light-green'; }else{ echo 'text-red'; } ?>"><?php echo $status; ?></span></h4>
	</div>

	<?php if($status == 'online'){ ?>
		<div class="pt-20">
			Current stats: <?php echo $online; ?> / <?php echo $slots; ?>
		</div>

		<div class="pt-20">
			Resulted method: <?php echo $method; ?> (Auto - <span class="text-red">This method can be slow!!!</span>)
		</div>
	<? }else{ ?>
		<div class="pt-20 text-center">(or invalid server settings)</div>
	<?php } ?>

	<div class="pt-40">Stat's speed: <?php echo round($speed, 6); ?> (update page and you can see different)</div>
</div>

</body>
</html>
