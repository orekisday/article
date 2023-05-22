<?php
require __DIR__ . '/../vendor/autoload.php';
$f3 = Base::instance();
$f3->set('AUTOLOAD', '../app/');
/*** GET ***/

$f3->route('GET /show', 'Controllers\Index->get_all_articles');
$f3->route('GET /users', 'Controllers\Index->get_all_users');

$f3->route('GET /email', 'Controllers\Index->email');

$f3->route('GET /user/@name', 'Controllers\Index->get_user');

//$f3->route('POST /name', 'Controllers\Index->get_user_test');

$f3->route('GET /article/@name', 'Controllers\Index->get_article_info');

/*** POST ***/
$f3->route('POST /post/article', 'Controllers\Index->post_article');

//$f3->route('POST /add', 'Controllers\Index->create_heart_table');

$f3->run();