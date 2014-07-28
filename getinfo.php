<?php
if (!empty($_GET['id'])) {
    try {
        // open connection to MongoDB server
        $conn = new Mongo('localhost');

        // access database
        $db = $conn->mediadb;

        // access collection
        $collection = $db->items;
        //echo substr_replace($_GET['id'] ,"",-3);
        $item = $collection->findOne(array('_id' => new MongoId(substr_replace($_GET['id'] ,"",-3))));
        header('Content-Type: application/json');
        echo json_encode($item);
        // disconnect from server
        $conn->close();
    } catch (MongoConnectionException $e) {
        die('Error connecting to MongoDB server');
    } catch (MongoException $e) {
        die('Error: ' . $e->getMessage());
    }
} else {
    echo "no object id get";
    exit();
}

?>