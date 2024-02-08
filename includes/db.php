<?php
include dirname(__DIR__) . "/utils/helper.php";
    // Connection credentials for the database
    // You can find the credentials in docker-compose.yml
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPassword = getenv('DB_PASSWORD');
    $dbHost = getenv('DB_HOST');

try {
    // Create a connection to the database
    $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPassword);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Connection Error: " . $e->getMessage()); // display error message
}

// Query functions

function fetchQuestionIdSequence($topic, $questionNum, $dbConnection) {
    $query = "SELECT `id` FROM `questions` WHERE `topic` = '$topic' ORDER BY RAND() LIMIT $questionNum";
    // all data with 
    $sqlStatement = $dbConnection->query($query);
    $rows = $sqlStatement->fetchAll(PDO::FETCH_COLUMN, 0);
    return $rows;
}

function questionRequest($id, $dbConnection) {
    // all data with id
    $sqlStatement = $dbConnection->query("SELECT * FROM `questions` WHERE `id` = '$id'");
    $rows = $sqlStatement->fetch(PDO::FETCH_ASSOC); 
    return $rows;
}

// a function to check if a row is recorded once; it takes data from createROW
function recordOnce($answer, $dbConnection){
    $addRow = "SELECT COUNT(*) FROM answer WHERE answer = :answer";
    $statment = $dbConnection->prepare($addRow);
    $statment->bindParam(':answer', $answer);
    $statment->execute();
    $fetchTest = $statment->fetchColumn();
    if ($fetchTest>0){
        echo ' <br>';
        echo "There is already a title with name {$answer}";
        echo ' <br>';
        return true;
    } else {
        echo ' <br>';
        echo "One line checkin;";
        echo ' <br>';
        return false;
    }

}


// function to take all data from a table as an Array; here from questions
function takeFromTable($dbConnection){
    $querySelect = "SELECT * FROM `questions`";
    // all data with 
    $sqlStatement = $dbConnection->query($querySelect);
    $rows = $sqlStatement->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

// function to insert new data to a table according to the query; here insert into answers
function insertIntoTable($answer, $correctAnswer, $questionId, $dbConnection){

    try {
        // in fall of a duplications, which are not needed, we may need some duplications
        /* if (recordOnce($answer, $dbConnection)){
            return;
        } */
        $queryInsert = "INSERT INTO `answer`(`answer`, `is_correct`, `question_id`) VALUES ('$answer', $correctAnswer, $questionId)";
        // all data with 
        $sqlStatement = $dbConnection->query($queryInsert);
        // ->query is equivalant to both ->prepare and ->execute, therefore use either ->query or the other two
        // $sqlStatement->execute();
        echo "<p>insertIntoTable: $answer successful</p>";
        // echo "The ROW was created successfully!";
        echo ' <br>';
    } catch (PDOException $e) {
        echo $e->getMessage();
     }
}


?>