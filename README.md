<p align="center">
    <h1 align="center">Order Managmenet App</h1>
    <br>
</p>

**How to Setup:**

_Deploy on Prod Env_
1. Clone Repository.
2. cd to root directory
3. run command: composer install --no-dev
4. run command: php ./init
   select production mode on prompt
5. create empty database with name order-management
6. add settings in common/config/main-local.php
7. run command: php yii migrate
   on prompt enter yes.

sample main-local.php file:
```
'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=order-management',
            'username' => '',
            'password' => '',
            'charset' => 'utf8',
        ],
    ],
    'timezone' => 'Europe/Tallinn'
```



_Running dev Env:_
1. run command: composer install --dev
2. run command: php ./init
   select development mode on prompt
3. follow 5 - 7 step from prd env.

_Configuring and runnig test cases:_
1. Create test database
2. Add config file test-local.php in common/config/
3. run command: php yii_test migrate
   hit yes on prompt
4. run command: ./vendor/bin/codecept run

Sample test-local.php file:

```
<?php
return [
            'components' => [
                'db' => [
                    'dsn' => 'mysql:host=localhost;dbname=order-management-test',
                    'username' => '',
                    'password' => '',
                    'charset' => 'utf8',
                ],
            ],
        ];
```