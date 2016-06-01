Experts minimization task
=========================

Command Line Application with common features built using phalcon framework.
More info could be found [here](https://docs.google.com/document/d/1EDVuFLyVRf0Oss1yuX16bkcVZz7K8IWVEAcM5VCpjNk/edit)

Requirements
---------
PHP 5.4 or greater

Required PHP Modules
- Phalcon (http://phalconphp.com/en/download)
- PDO (MySQL or Postgres or Sqlite driver)

To check if `phalcon` module is installed/enabled for CLI use
```bash
$ php -m | grep -i "phalcon"
phalcon
```

Input file structure
--------------------
```
(number of directions) (number of experts)
(couldSolve matrix)
(costs array)
```

Example:
```
20 6
0 0 1 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 1
0 1 0 1 0 0 0 0 0 1 0 0 1 0 0 0 0 1 0 0
1 0 0 0 1 0 1 1 0 0 1 1 0 0 1 0 1 0 1 1
0 1 0 1 0 1 0 1 0 1 0 1 0 1 0 1 0 1 0 1
0 0 1 1 1 1 0 0 1 1 1 1 0 0 1 1 1 1 0 1
1 0 1 0 1 0 1 0 1 0 1 0 1 0 1 1 0 1 0 1
10 20 30 5 40 11
```

Database Configuration
--------------
Open  `php-cli-app-phalcon/app/config.php` and edit your database connection credentials

```php
$settings = array(
        'database' => array(
                'adapter' => 'Mysql',   /* Possible Values: Mysql, Postgres, Sqlite */
                'host' => 'your_ip_or_hostname',
                'username' => 'your_user',
                'password' => 'your_password',
                'name' => 'your_database_schema',
                'port' => 3306
        ),
);
```

Import the tables into your MySQL Server
```bash
mysql -u root -p your_database_schema < php-cli-app-phalcon/schema/mysql.data.sql
```
Import the tables into your Postgres Server
```bash
psql -U root -W -f postgres.data.sql your_database_schema
```

Command Line Examples
----------------------

General Syntax for running a task/job (Note: only Task is required)

```bash
cd php-cli-app-phalcon/private 
php cli.php [Task] [Action] [Param1] [Param2] [...]
```

Tasks are stored in `php-cli-app-phalcon/app/tasks` directory. The following example task is named `ExampleTask.php`.
Basic example of how to kick off a cli job/task.

```bash
cd php-cli-app-phalcon/private
php cli.php Example test1 
```

Passing parameters to your application

```bash
php cli.php Example test2 bob sanders 
```

Special Flags
---------------------

Enable debug mode to see a more detailed overview of what is going on `--debug`
This also enables a more verbose level of php reporting, displaying all php warnings.

```bash
php cli.php Example cmd --debug
```

Record all output to the database (in the `task` table) `--record` . 

```bash
php cli.php Example test1 --record
```


Only allow 1 instance to run at a time `--single`
```bash
php cli.php Example test1 --single
```

Enable all flags
```bash
php cli.php Example test1 --debug --record --single
```

Adding New Tasks
--------------------

Go to `php-cli-app-phalcon/app/tasks` directory. This is where all the tasks are stored.
Just go ahead and create a new file here (eg. `NewTask.php`)

```php
<?php

namespace Tasks;

use \Cli\Output as Output;

class NewTask extends \Phalcon\Cli\Task {

    public function workAction() {
        Output::stdout("hi");
    }
}
?>
```

Now run it!
```bash
cd php-cli-app-phalcon/private
php cli.php New work
```

Adding New Classes to Autoload
--------------------

Note: All classes must be namespaced if you use the provided autoloader.

Open `php-cli-app-phalcon/app/config/autoload.php` and an element to the existing array.
So, you have to use namespacing to load new classes.

```php
$autoload = [
        'Utilities\Debug' => $dir . '/library/utilities/debug/',
	'Trend' => $dir . '/library/trend/'
];

return $autoload;
```
