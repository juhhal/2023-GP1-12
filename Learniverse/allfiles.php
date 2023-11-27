<?php
error_reporting(0);
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
// $collection = (new MongoDB\Driver\Client)->test->users;
$query = new MongoDB\Driver\Query(array('email' => $_SESSION['email']));

// Output of the executeQuery will be object of MongoDB\Driver\Cursor class
$cursor = $manager->executeQuery('Learniverse.users', $query);

// Convert cursor to Array and print result
$data = $cursor->toArray()[0];
//print_r($data);
$filesCount = $data->files_count;
$id = (string)$data->_id;
$aee = explode("//-//", $data->file_names);
for ($i = 1; $i <= $filesCount; $i++) {

?>
    <span class="box" id="box_<?php echo $i; ?>">
        <iframe scrolling="no" src="<?php echo "FILES/$id-$i.pdf" ?>"></iframe>
        <span class="collection">
            <a target='_blank' class="file" href="<?php echo "FILES/$id-$i.pdf" ?>"><?php echo $aee[$i - 1]; ?></a>
            <img class="icon deleteic" data-p="<?php echo $i - 1; ?>" data-value="box_<?php echo $i; ?>" src="images/delete.jpeg" id="delete">

            <img class="icon three" src="images/three.jpeg" data-value="box_<?php echo $i; ?>">
            <span class="queries">
                <button><img src="images/summary.png">
                    <p>Summarize</p>
                </button>

                <button><img src="images/question.png">
                    <p>Generate Questions</p>
                </button>

                <button><img src="images/flash.png">
                    <p>Create Flashcards</p>
                </button>
            </span>

        </span>

    </span>

<?php } ?>