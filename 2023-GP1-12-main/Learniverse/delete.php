<?php
session_start();
 $manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
        
        //$query = new MongoDB\Driver\Query(array('email' =>  $_SESSION['email']));

        $query = new MongoDB\Driver\Query(array('email' =>  $_SESSION['email']));

        $cursor = $manager->executeQuery('Learniverse.users', $query);
        
        // Convert cursor to Array and print result
        $emailCount = $cursor->toArray();
        echo "<pre>";
        $arr = explode("//-//", $emailCount[0]->file_names);
        $str = "";
        for($i=0;$i< $emailCount[0]->files_count;$i++) {
            if($_POST['value'] != $i)
            $str .= ($arr[$i]."//-//");
        }
        // print_r((string)$emailCount[0]->_id);
        
    // if($_FILES['file']['type'] == 'application/pdf') {

    //     $manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
        
    //     $query = new MongoDB\Driver\Query(array('email' =>  'ff@e.c'));

    //     $cursor = $manager->executeQuery('Learniverse.users', $query);
        
        // Convert cursor to Array and print result
        // $emailCount = $cursor->toArray();
        // //print_r($emailCount[0]->files_count);
        // // Output of the executeQuery will be object of MongoDB\Driver\Cursor class
        $bulk = new MongoDB\Driver\BulkWrite;


        $bulk->update(
            ['email' => $_SESSION['email']],
            ['$set' => ['files_count' => $emailCount[0]->files_count-1, 'file_names' => $str]],
            ['multi' => false, 'upsert' => false]
        );



        $result = $manager->executeBulkWrite('Learniverse.users', $bulk);
               

        $data = [ 'message' => false ];
        echo json_encode($data);
    // }

