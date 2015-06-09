
<?php
return new \Phalcon\Config(array(
  'database' => array(
    'adapter'     => '',
    'host'        => '',
    'username'    => '',
    'password'    => '',
    'dbname'      => '',
    'charset'     => '',
  ),
  'application' => array(
    'tasksDir'    => __DIR__ . '/../../app/messages/',
    'baseUri'        => '/',
  )
));
