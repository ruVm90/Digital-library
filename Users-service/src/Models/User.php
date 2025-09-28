<?php

require_once __DIR__ . '/../Database/Database.php';


function consultUsers()
{
   try {
      $db = new Database();
      $connection = $db->getConnection();
      $sql = "SELECT  * FROM users";
      $result = $connection->query($sql);

      if ($result) {
         while ($fila = $result->fetch_assoc()){
            $users[] = $fila;
         }
         return $users;
      }
   } catch (Exception $e) {
   }
}
