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

    public function get_all_users_array(): false|int|array
    {
        return $this->db->exec('SELECT * FROM `user`');
    }

    public function create_heart_table(Base $f3): void
    {
        $data_set2 = new SQL\Mapper($this->db, 'user_heart');
        $data_set = new SQL\Mapper($this->db, 'user');

        $red = 0;
        $beige = 0;
        $pink = 0;

        for ($i = 1; $i < 497; $i++) {
            $data_set->load(array('id=?', $i));

            $first_name = $data_set->first_name;
            $last_name = $data_set->last_name;
            $department = $data_set->department;
            $email = $data_set->email;
            $edu_level = $data_set->edu_level;
            $class = $data_set->class;

            if ($edu_level === 'M' || $edu_level === 'B') {
                if ($class === 0 || $class === 1) {
                    $class = 22;
                } elseif ($class === 2) {
                    $class = 21;
                } elseif ($class === 3) {
                    $class = 20;
                } elseif ($class === 4) {
                    $class = 19;
                }

                if ($department === 'Applied Mathematics and Informatics') {
                    $department = 'MAT';
                } elseif ($department === 'Computer Science') {
                    $department = 'COM';
                }

            } elseif ($edu_level === 'DR') {
                $department = 'teacher';
                $class = '';
            }

            $group_name = $department . $class;

            $data_set2->first_name = $first_name;
            $data_set2->last_name = $last_name;
            $data_set2->email = $email;
            $data_set2->group_name = $group_name;
            $data_set2->red = $red;
            $data_set2->beige = $beige;
            $data_set2->pink = $pink;
            $data_set2->save();
            $data_set2->reset();
        }
    }
    public function get_all_users(Base $f3): void
    {
        header('Access-Control-Allow-Origin: *');
        $result = $f3->get('db')->exec('SELECT * FROM `user`');
        if (count($result) > 0) {
            echo json_encode($result);
        } else {
            $f3->error(404);
        }
    }
    public function get_all_users_heart(Base $f3): void
    {
        header('Access-Control-Allow-Origin: *');
        $result = $f3->get('db')->exec('SELECT * FROM `user_heart`');
        if (count($result) > 0) {
            echo json_encode($result);
        } else {
            $f3->error(404);
        }
    }

    public function get_user(Base $f3, array $params): void
    {
        header('Access-Control-Allow-Origin: *');
        $name = $params['name'];
        $result = $f3->get('db')->exec('SELECT * FROM `user_heart` WHERE `first_name` = ?', $name);

        if (count($result) > 0) {
            echo json_encode($result);
        } else {
            $f3->error(404);
        }

    }
    public function get_user_test(Base $f3, array $params): void
    {
        header('Access-Control-Allow-Origin: *');
        $json_data = $f3->get('BODY');
        $data = json_decode($json_data, true);
        $name = $data['first_name'];

        $result = $f3->get('db')->exec('SELECT * FROM `user_heart` WHERE `first_name` = ?', $name);

        if (count($result) > 0) {
            echo json_encode($result);
        } else {
            $f3->error(404);
        }
    }

    public function test_func(Base $f3, array $params): void
    {
        header('Access-Control-Allow-Origin: *');
        $first_name = $params['first_name'];
        $data_set = new SQL\Mapper($this->db, 'user_heart');

        if (!empty($data_set->load(array('first_name=?', $first_name)))) {
            $first_name = $data_set->first_name;
            $email = $data_set->email;
            $group_name = $data_set->group_name;
            $last_name = $data_set->last_name;
            echo json_encode($first_name) . '<br>';
            echo json_encode($last_name) . '<br>';
            echo json_encode($email) . '<br>';
            echo json_encode($group_name);
        } else {
            $f3->error(404);
        }
    }

    public function update_heart_post_heart(Base $f3, array $params): void
    {
        header('Access-Control-Allow-Origin: *');
        $json_data = $f3->get('BODY');
        $data = json_decode($json_data, true);

        $email_sender = $data['email_sender'];
        $email_receiver = $data['email_receiver'];
        $text = $data['text'];
        $heart_type = $data['heart_type'];

        $data_set = new SQL\Mapper($this->db, 'user_heart');

        if (!empty($data_set->load(array('email=?', $email_sender)))) {

            if (!empty($data_set->load(array('email=?', $email_receiver)))) {
                $f3->get('db')->exec('UPDATE `user_heart` SET ' . $heart_type . ' = ' . $heart_type . '  + 1 
                    WHERE `email` = ?', $email_receiver);

                $letter_mapper = new SQL\Mapper($this->db, 'user_letter');
                $existing_letter = $letter_mapper->findone(
                    array(
                        'sender_email = ? AND receiver_email = ?',
                        $email_sender,
                        $email_receiver
                    )
                );

                if (!empty($existing_letter)) {
                    // The sender has already sent a letter to the receiver
                    $f3->error(400, 'Sender has already sent a letter to the receiver');
                }
                $letter_mapper->sender_email = $email_sender;
                $letter_mapper->receiver_email = $email_receiver;
                $letter_mapper->letter = $text;
                $letter_mapper->save();
                $letter_mapper->reset();

                $f3->set('response', array(
                    'success' => 'true',
                )
                );

                echo json_encode($f3->get('response'));
            }
        } else {
            $f3->error(404);
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