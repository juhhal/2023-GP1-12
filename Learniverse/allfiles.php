<?php

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
// $collection = (new MongoDB\Driver\Client)->test->users;
$query = new MongoDB\Driver\Query(array('email' => 'ff@e.c'));

// Output of the executeQuery will be object of MongoDB\Driver\Cursor class
$cursor = $manager->executeQuery('Learniverse.users', $query);

// Convert cursor to Array and print result
$data = $cursor->toArray()[0];
//print_r($data);
$filesCount = $data->files_count;
$id = (string)$data->_id;

for($i=1;$i<=$filesCount;$i++) {

?>

    <a class="file" href="<?php echo "FILES/$id-$i.pdf" ?>"><?php echo "File - $i" ?></a>

<?php } ?>


