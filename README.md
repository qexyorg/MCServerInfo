# MCServerInfo
Minecraft server stats

### Install
`composer require qexyorg/mcserverinfo`

### Example
```php
<?php

use qexyorg\MCServerInfo\MCServerInfo;

require_once('vendor/autoload.php');

$query = new MCServerInfo();

$req = $query->connect('mc.server.com', 25565);

if(!$req->execute()){
	exit('Error: '.$req->getError().' | Errno: '.$req->getErrno());
}

var_dump($req->getResponse());
```
In this example, the logic will be found automatically


If you know what is a logic used you can set it manually
Available logics: `query`; `ping`(Default); `ping_old`
### Example with manually logic
```php
<?php

use qexyorg\MCServerInfo\MCServerInfo;

require_once('vendor/autoload.php');

$query = new MCServerInfo();

$req = $query->connect('mc.server.com', 25565)->setLogic('ping');

if(!$req->execute()){
	exit('Error: '.$req->getError().' | Errno: '.$req->getErrno());
}

var_dump($req->getResponse());
```

You can change default search logic via `$req->setDefaultSearchLogic('query')` before call `$req->execute()`