<?php
namespace Controllers;

use Base;
use DB\SQL;

require __DIR__ . '/../../vendor/autoload.php';
class Index
{

    private SQL $db;
    private Base $f3;

    public function __construct($f3)
    {
        $this->db = new SQL(
            'mysql:host=us-cdbr-east-06.cleardb.net;port=3306;dbname=heroku_22968bb32d3b037',
            'b7070d63af0c2e',
            '3cd4a5e2'
            //mysql://b7070d63af0c2e:3cd4a5e2@us-cdbr-east-06.cleardb.net/heroku_22968bb32d3b037?reconnect=true
        );
        $this->f3 = $f3;
        $this->f3->set('db', $this->db);
    }

    public function get_all_articles_array(): false|int|array
    {
        return $this->db->exec('SELECT * FROM `articles`');
    }

    public function get_all_articles(Base $f3): void
    {
        header('Access-Control-Allow-Origin: *');
        $result = $f3->get('db')->exec('SELECT `article` FROM `articles`');
        if (count($result) > 0) {
            echo json_encode($result);
        } else {
            $f3->error(404);
        }
    }
    public function get_all_users(Base $f3): void
    {
        header('Access-Control-Allow-Origin: *');
        $first_name = $f3->get('db')->exec('SELECT `first_name` FROM `articles`');
//        $last_name = $f3->get('db')->exec('SELECT `last_name` FROM `articles`');
        if (count($first_name) > 0) {
            echo json_encode($first_name);
        } else {
            $f3->error(404);
        }
    }

    public function get_article(Base $f3, array $params): void
    {
        header('Access-Control-Allow-Origin: *');
        $json_data = $f3->get('BODY');
        $data = json_decode($json_data, true);

        $email = $data['email'];

        $result = $f3->get('db')->exec('SELECT `article` FROM `articles` WHERE `email` = ?', $email);

        if (count($result) > 0) {
            echo json_encode($result);
        } else {
            $f3->error(404);
        }

    }
//    public function get_user_test(Base $f3, array $params): void
//    {
//        header('Access-Control-Allow-Origin: *');
//        $json_data = $f3->get('BODY');
//        $data = json_decode($json_data, true);
//        $name = $data['first_name'];
//
//        $result = $f3->get('db')->exec('SELECT * FROM `user_heart` WHERE `first_name` = ?', $name);
//
//        if (count($result) > 0) {
//            echo json_encode($result);
//        } else {
//            $f3->error(404);
//        }
//    }
    public function get_article_info(Base $f3, array $params): void
    {
        header('Access-Control-Allow-Origin: *');
        $json_data = $f3->get('BODY');
        $data = json_decode($json_data, true);

        $email = $data['email'];

        $result = $f3->get('db')->exec('SELECT * FROM `articles` WHERE `email` = ?', $email);

        if (count($result) > 0) {
            echo json_encode($result);
        } else {
            $f3->error(404);
        }
    }
//    public function get_article_info_test(Base $f3, array $params): void
//    {
//        header('Access-Control-Allow-Origin: *');
//        $first_name = $params['name'];
//        $data_set = new SQL\Mapper($this->db, 'articles');
//
//        if (!empty($data_set->load(array('first_name=?', $first_name)))) {
//            $first_name = $data_set->first_name;
//            $email = $data_set->email;
//            $last_name = $data_set->last_name;
//            $text = $data_set->article;
//            echo json_encode($first_name) . '<br>';
//            echo json_encode($last_name) . '<br>';
//            echo json_encode($email) . '<br>';
//            echo json_encode($text);
//        } else {
//            $f3->error(404);
//        }
//    }

    public function post_article(Base $f3, array $params): void
    {
        $response = $f3->response();
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        $json_data = $f3->get('BODY');
        $data = json_decode($json_data, true);

        $email = $data['email'];
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $text = $data['article'];

        $data_set = new SQL\Mapper($this->db, 'articles');

        $data_set->first_name = $first_name;
        $data_set->last_name = $last_name;
        $data_set->email = $email;
        $data_set->article = $text;
        $data_set->save();
        $data_set->reset();

        $f3->set('response', array(
                'success' => 'true',
            )
        );

        echo json_encode($f3->get('response'));
    }
    public function email(Base $f3, array $params): void {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        $to = 'cutesytee@gmail.com';
        $subject = 'Hello from PHP';
        $message = 'This is a test email';

        $headers = 'From: testing.the.email.function@gmail.com' . "\r\n" .
            'Reply-To: testing.the.email.function@gmail.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

// Send the email
        $mailSent = mail($to, $subject, $message, $headers);

// Check if the email was sent successfully
        if ($mailSent) {
            echo 'Email sent successfully';
        } else {
            echo 'Failed to send email';
        }

    }

//    public function is_json($string): bool
//    {
//        json_decode($string);
//        return (json_last_error() == JSON_ERROR_NONE);
//    }

//    public function helloWorldAction(\Base $f3, array $args = []): void {
//        if ($args['name'] === 'Henry') {
//            $f3->reroute('/redirected', false);
//        }

//        echo 'This is hello world. Method is a '. $f3->VERB. ' request'. 'my name is '. $args['name'];
//    }
}