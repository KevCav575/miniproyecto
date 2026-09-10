<?php
    try{
        $conn = new PDO("mysql:host=localhost; dbname=mercado; charset=utf8","root","");
    }

    catch(PDOException $e){
        echo "No funciona" .$e->getMessage();
    }
?>