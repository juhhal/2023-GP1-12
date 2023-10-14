<?php
session_start();
 $manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
        
        $query = new MongoDB\Driver\Query(array('email' =>  $_SESSION['email']));

        $cursor = $manager->executeQuery('Learniverse.users', $query);
        
        // Convert cursor to Array and print result
        $emailCount = $cursor->toArray();
        echo "<pre>";
        print_r((string)$emailCount[0]->_id);
        
    if($_FILES['file']['type'] == 'application/pdf') {

        $manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
        
        $query = new MongoDB\Driver\Query(array('email' =>  $_SESSION['email']));

        $cursor = $manager->executeQuery('Learniverse.users', $query);
        
        // Convert cursor to Array and print result
        $emailCount = $cursor->toArray();
        //print_r($emailCount[0]->files_count);
        // Output of the executeQuery will be object of MongoDB\Driver\Cursor class
        $bulk = new MongoDB\Driver\BulkWrite;


        $bulk->update(
            ['email' => 'ff@e.c'],
            ['$set' => ['files_count' => $emailCount[0]->files_count+1]],
            ['multi' => false, 'upsert' => false]
        );



        $result = $manager->executeBulkWrite('Learniverse.users', $bulk);
                $val = $emailCount[0]->files_count+1;
                $id = (string)$emailCount[0]->_id;
              move_uploaded_file($_FILES['file']['tmp_name'], "FILES/$id-$val.pdf");

        $data = [ 'message' => false ];
        echo json_encode($data);
    }

