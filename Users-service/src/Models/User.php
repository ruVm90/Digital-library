<?php

require_once __DIR__ . '/../Database/Database.php';

class UserModel
{

   private $db;


   public function __construct()
   {
      $this->db = new Database();
   }


   public function getAllUsers()
   {
      $connection = null;
      try {
         $users = [];
         $connection = $this->db->getConnection();
         $sql = "SELECT  * FROM users";
         $result = $connection->query($sql);

         if ($result) {
            while ($fila = $result->fetch_assoc()) {
               $users[] = $fila;
            }
            return $users;
         }
      } catch (Exception $e) {
         return ["Error" => $e->getMessage()];
      } finally {
         if ($connection) {
            $this->db->closeConnection($connection);
         }
      }
   }

   public function getUser($id)
   {
      $db = new Database();
      $connection = null;
      try {
         $user = null;
         $connection = $this->db->getConnection();
         $sql = "SELECT  * FROM users WHERE id = $id";
         $result = $connection->query($sql);

         if ($result) {
            $user = $result->fetch_object();
            return $user;
         }
      } catch (Exception $e) {
         return ["Error" => $e->getMessage()];
      } finally {
         if ($connection) {
            $this->db->closeConnection($connection);
         }
      }
   }

   public function createUser($name, $role = 'reader')
   {
      $db = new Database();
      $connection = null;
      try {
         $connection = $this->db->getConnection();
         $sql = "INSERT INTO users (name , role) VALUES ('$name', '$role')";
         $insert = $connection->query($sql);

         if ($insert) {
            return $connection->insert_id; // Devuelve el id
         } else {
            return false;
         }
      } catch (Exception $e) {
         return ["Error" => $e->getMessage()];
      } finally {
         if ($connection) {
            $this->db->closeConnection($connection);
         }
      }
   }

   public function updateUser($name, $id , $role)
   {
      $db = new Database();
      $connection = null;
      try {
         $connection = $this->db->getConnection();
         $sql = "UPDATE users SET name = '$name' , role = '$role' WHERE id = $id";
         $update = $connection->query($sql);
         if ($update && $connection->affected_rows > 0) {
            return true;
         } else {
            return false;
         }
      } catch (Exception $e) {
         return ["Error" => $e->getMessage()];
      } finally {
         if ($connection) {
            $this->db->closeConnection($connection);
         }
      }
   }

   public function deleteUser($id)
   {
      $db = new Database();
      $connection = null;
      try {
         $connection = $this->db->getConnection();
         $sql = "DELETE FROM users WHERE id = $id";
         $connection->query($sql);

         if ($connection->affected_rows > 0) {
            return true;
         } else {
            return false;
         }
      } catch (Exception $e) {
         return ["Error" => $e->getMessage()];
      } finally {
         if ($connection) {
            $this->db->closeConnection($connection);
         }
      }
   }
}
