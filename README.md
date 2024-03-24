```
   )\.-.       .'(     /`-.      .-./(    )\.--.          )\.-.      /`-.   )\.---.   )\   )\   .')      .'(   )\  )\  
 ,' ,-,_)  ,') \  )  ,' _  \   ,'     )  (   ._.'       ,' ,-,_)   ,' _  \ (   ,-._( (  ',/ /  ( /       \  ) (  \, /  
(  .   _  (  '-' (  (  '-' (  (  .-, (    `-.`.        (  .   __  (  '-' (  \  '-,    )    (    ))       ) (   ) \ (   
 ) '..' )  ) .-.  )  )   _  )  ) '._\ )  ,_ (  \        ) '._\ _)  ) ,_ .'   ) ,-`   (  \(\ \   )'._.-.  \  ) ( ( \ \  
(  ,   (  (  ,  ) \ (  ,' ) \ (  ,   (  (  '.)  )      (  ,   (   (  ' ) \  (  ``-.   `.) /  ) (       )  ) \  `.)/  ) 
 )/'._.'   )/    )/  )/    )/  )/ ._.'   '._,_.'        )/'._.'    )/   )/   )..-.(       '.(   )/,__.'    )/     '.(  
                                                                                                                       
```

# Chaos Gremlin

Chaos Gremlin is a PHP chaos testing tool, it introduces random problems into your application that simulate latency, CPU use, memory use, high disk use etc. This tool is for testing how well your application recovers from unknown events.

**USE WITH CAUTION** some of these Gremlins will not stop until you manually kill the process. Enable only the Gremlins you want to use and review the settings.

**THIS IS INTENDED FOR TESTING YOUR OWN APPLICATIONS OR APPLICATIONS YOU HAVE PERMISSION TO TEST**

Install with: `composer require dvnc0/chaos-gremlin`

## Default Settings
```php
protected array $settings = [
	'probability' => 30,
	'min_latency_seconds' => 2,
	'max_latency_seconds' => 10,
	'exception_message' => 'Chaos Gremlin Exception',
	'dice_roll_over_under' => 3.5,
	'max_memory_percent' => 90,
	'disk_gremlin_directory' => './chaos_gremlin',
	'disk_gremlin_number_files' => 100,
	'disk_gremlin_file_size' => 5 * 1024 * 1024,
	'traffic_requests' => 100,
	'traffic_url' => 'http://localhost:8080',
	'log_directory' => './chaos_gremlin_logs',
	'traffic_gremlin_spawns_gremlins' => false,
];
```

These are the default settings for Chaos Gremlin and can be changed by using the `settings` method. We will cover that later in this README.

|Setting |Description|
|--------|-----------|
|probability| The probability a Gremlin will be released into the system|
|min_latency_seconds| The minimum latency a Latency_Gremlin should add|
|max_latency_seconds| The maximum latency a Latency_Gremlin should add|
|exception_message| The message that will be thrown with the Exception_Gremlin|
|dice_roll_over_under| The number that the dice will roll over or under to release a Gremlin|
|max_memory_percent| The maximum memory percentage that the Memory_Gremlin will use|
|disk_gremlin_directory| The directory that the Disk_Gremlin will create files in|
|disk_gremlin_number_files| The number of files the Disk_Gremlin will create|
|disk_gremlin_file_size| The size of the files the Disk_Gremlin will create|
|traffic_requests| The number of requests the Traffic_Gremlin will make|
|traffic_url| The URL the Traffic_Gremlin will make requests to|
|log_directory| The directory that the logs will be saved to|
|traffic_gremlin_spawns_gremlins| If the Traffic_Gremlin should spawn other Gremlins, this can get out of hand quick|

## Using Chaos Gremlin

To use Chaos Gremlin you will need to create an instance of the Gremlin class, enable any Gremlins you would like to use, and then call the `release` method. This may release the Gremlins into your application.

```php
<?php
declare(strict_types=1);
require_once 'vendor/autoload.php';

use ChaosGremlin\Chaos_Gremlin;

$Gremlin = Chaos_Gremlin::getInstance();
$Gremlin->enableGremlin('Memory_Gremlin');

$Gremlin->release();
```

The above code will create an instance of the Gremlin class, enable the Memory_Gremlin, and then release the Gremlins into the application. The Gremlin will then use 90% of the memory available to PHP or 90% of the system memory. This is using the default settings listed above.

### Available Gremlins

There are a number of Gremlins available to use in Chaos Gremlin. You can enable any of the following Gremlins by using the `enableGremlin` method.

|Gremlin Name |Description|
|-------------|-----------|
|Latency_Gremlin |Adds a random amount of latency to a request |
|Exception_Gremlin |Throws an Exception |
|Memory_Gremlin |Consumes memory until the percent set in the settings array |
|Disk_Gremlin |Writes files to disk |
|Traffic_Gremlin |Makes requests to the defined endpoint |
|Cpu_Gremlin |Consumes CPU |
|Black_Hole_Gremlin |Writes a random amount of data to /dev/null |
|Die_Gremlin |Calls PHPs `die` function |
|Service_Gremlin |Restarts a random service, will not work without sudo privileges |

#### Traffic Gremlin
The Traffic Gremlin will make requests to the URL set in the settings array. This can be used to test how your application handles a large number of requests. By default requests from the Traffic Gremlin will not spawn other Gremlins, this can be changed by setting the `traffic_gremlin_spawns_gremlins` setting to `true`. Again, be cautious with this setting as it can spawn a large number of Gremlins depending on your settings, including more Traffic Gremlins which could create additional Gremlins etc, etc.

## Adding custom settings
```php
<?php
declare(strict_types=1);
require_once 'vendor/autoload.php';

use ChaosGremlin\Chaos_Gremlin;

$Gremlin = Chaos_Gremlin::getInstance();
$Gremlin->settings([
	'probability' => 75,
	'min_latency_seconds' => 5,
	'max_latency_seconds' => 10,
	'max_memory_percent' => 90,
	'disk_gremlin_number_files' => 1000,
	'disk_gremlin_file_size' => 5 * 1024 * 1024,
	'traffic_requests' => 500,
	'traffic_url' => 'http://localhost:9000',
]);
$Gremlin->enableGremlin('Memory_Gremlin');
$Gremlin->enableGremlin('Disk_Gremlin');
$Gremlin->enableGremlin('Traffic_Gremlin');
$Gremlin->enableGremlin('Cpu_Gremlin');
$Gremlin->enableGremlin('Latency_Gremlin');

$Gremlin->release();
```

## Custom Gremlins
You can create your own Gremlins by extending the Gremlin class and implementing the `attack` method. 

```php
abstract public function attack(): void;
```

The settings array will be passed to the custom Gremlin and the following methods are available to the Gremlin.

|Method |Description| Return |
|-------|-----------|--------|
|rollDice |Rolls a dice to see if the Gremlin should be released | bool |
|getDiceRoll |Returns a random number between 1-6 | int |
|probabilityCheck|Decides if a Gremlin should be released based on the probability setting | bool |
|writeToLog|Writes a message to the log file | void |

To enable the custom Gremlin use the `enableCustomGremlin` method which takes a `string` key and an instance of the custom Gremlin.

```php
<?php
declare(strict_types=1);
require_once 'vendor/autoload.php';

use ChaosGremlin\Chaos_Gremlin;
use Custom_Database_Gremlin;

$Gremlin = Chaos_Gremlin::getInstance();
$Gremlin->settings([
	'probability' => 75,
	'min_latency_seconds' => 5,
	'max_latency_seconds' => 10,
	'max_memory_percent' => 90,
	'disk_gremlin_number_files' => 1000,
	'disk_gremlin_file_size' => 5 * 1024 * 1024,
	'traffic_requests' => 500,
	'traffic_url' => 'http://localhost:9000',
]);
$Gremlin->enableGremlin('Memory_Gremlin');
$Gremlin->enableGremlin('Disk_Gremlin');

$Gremlin->enableCustomGremlin('Database_Gremlin', new Custom_Database_Gremlin());

$Gremlin->release();
```

To release the custom Gremlin you will need to call the `callGremlin` method using the key you set when enabling the Gremlin. This allows you to decide when to attempt to release a Gremlin allowing you to add Gremlins into specific parts of your application.

```php
<?php
declare(strict_types=1);
require_once 'vendor/autoload.php';

use ChaosGremlin\Chaos_Gremlin;

$Gremlin = Chaos_Gremlin::getInstance();

$Gremlin->callGremlin('Database_Gremlin');
```