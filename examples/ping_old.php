<?php

require_once('./require.php');

use qexyorg\MCServerInfo\MCServerInfo;

$config = [
	'address' => 'mc.hypixel.net',
	'port' => 25565,
	'timeout' => 3
];

$connect = MCServerInfo::Connect($config['address'], $config['port'], $config['timeout'])->setMethod(MCServerInfo::METHOD_OLD_PING);

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

?><!DOCTYPE html>
<html lang="en">
<head>
	<title>Old ping | Examples | MCServerInfo</title>

	<?php require_once('./header.php'); ?>

</head>
<body>

<?php $active = 'old_ping'; require_once('./navbar.php'); ?>

<div class="container">
	<div class="pt-100">
		<div class="text-center">
			<h2 class="text-gray">Old ping method</h2>
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
			Resulted method: <?php echo $method; ?> (Manual)
		</div>

		<div class="pt-20">
			<div class="bb-code">
				<div class="bb-code-language">Json response</div>
				<div class="bb-code-text w-space-pre-wrap">
					<div class="of-auto" style="max-height:300px;">
						<?php echo $data; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="pt-20">
			<div class="bb-code">
				<div class="bb-code-language">PHP</div>
				<div class="bb-code-text w-space-pre-wrap">
					<div class="of-auto" style="max-height:300px;">
						$connect = MCServerInfo::Connect('<?php echo $config['address']; ?>', <?php echo $config['port']; ?>, <?php echo $config['timeout']; ?>)->setMethod(MCServerInfo::METHOD_OLD_PING);

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
					</div>
				</div>
			</div>
		</div>
	<? }else{ ?>
		<div class="pt-20 text-center">(or invalid server settings)</div>
	<?php } ?>

</div>

</body>
</html>
