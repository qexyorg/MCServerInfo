# MCServerInfo
Minecraft server stats

### Install
`composer require qexyorg/mcserverinfo`

### Example
```php
<?php

use qexyorg\MCServerInfo\MCServerInfo;

require_once('vendor/autoload.php');

$connect = MCServerInfo::Connect('mc.my-super-server.net', 25565);

if(!$connect->request()){
	exit($connect->getError());
}

var_dump($connect->getResponse()->rawData());
```
In this example, method will be found automatically (**Can be slow!!!**).


If you know what is a method used you can set it manually

### Example with manually method
```php
<?php

use qexyorg\MCServerInfo\MCServerInfo;

require_once('vendor/autoload.php');

$connect = MCServerInfo::Connect('mc.my-super-server.net', 25565)->setMethod(MCServerInfo::METHOD_PING);

if(!$connect->request()){
	exit($connect->getError());
}

var_dump($connect->getResponse()->rawData());
```

More examples you can find in "examples" folder
