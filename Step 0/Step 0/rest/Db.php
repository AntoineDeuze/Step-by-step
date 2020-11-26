<?php
class Db
{

    private static $db = null;
    
    //connection à bdd
    private static function connect() 
    {
        if (self::$db === null) {

            // Paramètres de configuration DB (phpmyadmin ?)
            $host = "mysql:host=localhost;port=3306;dbname=stepbystep";
            $user = "AntoineD";
            $password = "UGPb37zwLgmbTgHa";

            try {
                self::$db = new PDO(
                    $host,
                    $user,
                    $password,
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        PDO::ATTR_PERSISTENT => true
                    )
                );
            } catch (PDOException $e) {
                var_dump($e);
                exit();
            }
        }
        return self::$db;
    }

    private static $stmt = null; // ??1
    public static function query($sql, $params = null) //envoie de requete
    {
        $result = false;
        try {
            $stmt = Db::connect()->prepare($sql); //prépare requete
            DB::$stmt = $stmt; //??1
            $resultat = $stmt->execute($params); //execute la requete

        } catch (PDOException $e) {
            var_dump($e);
            exit();
        }
        return $result;
    }

    public static function select($table, $id, $where, $orderby)
    {
        //variable pour stocker paramètre et ses valeurs
        $params = array();
        //if where n'est pas non null alors where prend ? (sécurité)
        if(!isset($where)){
            $where = "active = ?";
            $params[] = true;
        }
        //if id non null alors where prends where. ? (sécurité)
        if (isset($id)) {
            $where .= " AND id=?";
            $params[] = $id;
        }
        //if orderby n'est pas non null alors id ASC / permet de classer par id ascendant
        if (!isset($orderby)) {
            $orderby = "id ASC";
        }
        //requête select
        $sql = "SELECT * FROM $table WHERE $where ORDER BY $orderby";
        $resp = Db::query($sql, $params);
        $rows = Db::$stmt->fetchAll(PDO::FETCH_ASSOC);
        //retourne 
        return $rows;
    }

    public static function insert($table, $fields)
    {
        //variable pour stocker les clés
        $cles = "";
        //variable pour stocker ?,
        $values = "";
        //if fields n'est pas non-null alors fields devient un tableau
        if(!isset($fields)){
            $fields = array();
        }
        //id pas set manuellement
        $fields['id'] = null;
        //variable pour stocker valeurs
        $valuesArray = array();
        //boucle clés valeurs
        foreach ($fields as $k => $v) {
            $cles .= $k . ",";
            $values .= "?,";
            array_push($valuesArray, $v);
        }
        //retire la dernière virgule
        $cles = trim($cles, ',');
        $values = trim($values, ',');
        //requete insérant valeurs à chaque clés
        $sql = "INSERT INTO $table ($cles) VALUES ($values)";
        $resp = Db::query($sql, $valuesArray);
        //vérif ajout une seule ligne
        $resp = $resp && Db::$stmt->rowCount() == 1;
        //recup le last ID
        if ($resp) {
            $resp = Db::$db->lastInsertId();
        }
        //retourne 
        return $resp;
    }

    public static function update($table, $id, $fields)
    {
        //crée variable pour stocker les modifs
        $modif = "";
        //variable pour stocker id
        $idArray = array();        
        //permet de ne pas modif l'id ??
        if(isset($fields) && isset($fields['id'])){
            unset($fields['id']);
        }
        //boucle clé/valeur, pour chaque clé on push la clé et un ?,
        foreach ($fields as $k => $v) {
            $modif .= $k . "=?,";
            array_push($idArray, $v);
        }
        //retire la dernière virgule de $modif
        $modif = trim($modif, ",");
        //on stock l'id de l'objet à update
        array_push($idArray, $id);
        //prépare l'update
        $sql = "UPDATE $table SET $modif WHERE id = $id";
        $resp = Db::query($sql, $idArray);
        //vérif qu'on a modif qu'une ligne
        $resp = $resp && Db::$stmt->rowCount() == 1;
        //retourne 
        return $resp;
         
    }

    public static function delete($table, $id)
    {   //crée une variable dans laquelle on va stocker l'id de l'objet à suppr
        $idArray = array();

        //stocke l'id correspondant dans la variable
        array_push($idArray, $id);
        
        //prépare le delete
        $sql = "DELETE FROM $table WHERE id = $id";
        $resp = DB::query($sql, $idArray);

        //vérifie que l'on a bien supprimé 1 ligne
        $resp = $resp && Db::$stmt->rowCount() == 1;

        //retourne 
        return $resp;
    }
}
