<?php
require __DIR__ . '/../vendor/autoload.php';
$f3 = Base::instance();
$f3->set('AUTOLOAD', '../app/');
/*** GET ***/

$f3->route('GET /users', 'Controllers\Index->get_all_users');
$f3->route('GET /users_heart', 'Controllers\Index->get_all_users_heart');
$f3->route('GET /@name', 'Controllers\Index->get_user');

$f3->route('POST /name', 'Controllers\Index->get_user_test');

//$f3->route('GET /test/@last_name', 'Controllers\Index->test_func');

/*** POST ***/
$f3->route('POST /heart', 'Controllers\Index->update_heart_post_heart');

//$f3->route('POST /add', 'Controllers\Index->create_heart_table');

$f3->run();