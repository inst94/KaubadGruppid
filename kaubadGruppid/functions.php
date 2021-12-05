<?php
require ('conf.php');


function kaubadData($sort_by = "kaubanimi", $search_term = "") {
    global $connection;
    $sort_list = array("kaubanimi", "hind", "kaubagrupp");
    if(!in_array($sort_by, $sort_list)) {
        return "Seda tulpa ei saa sorteerida";
    }
    $request = $connection->prepare("SELECT kaubad.id, kaubanimi, hind, kaubagrupid.kaubagrupp
    FROM kaubad, kaubagrupid 
    WHERE kaubad.kaubagrupp_id = kaubagrupid.id 
    AND (kaubanimi LIKE '%$search_term%' OR hind LIKE '%$search_term%' OR kaubagrupp LIKE '%$search_term%')
    ORDER BY $sort_by");
    $request->bind_result($id, $kaubanimi, $hind, $kaubagrupp);
    $request->execute();
    $data = array();
    while($request->fetch()) {
        $kaup = new stdClass();
        $kaup->id = $id;
        $kaup->kaubanimi = htmlspecialchars($kaubanimi);
        $kaup->hind = $hind;
        $kaup->kaubagrupp = $kaubagrupp;
        array_push($data, $kaup);
    }
    return $data;
}
function kaubagruppData($sort_by="kaubagrupp") {
    global $connection;
    $sort_list = array("kaubagrupp");
    if(!in_array($sort_by, $sort_list)) {
        return "Seda tulpa ei saa sorteerida";
    }
    $request = $connection->prepare("SELECT id, kaubagrupp FROM kaubagrupid ORDER BY $sort_by");
    $request->bind_result($id, $kaubagrupp);
    $request->execute();
    $gruppdata = array();
    while($request->fetch()) {
        $grupp = new stdClass();
        $grupp->id = $id;
        $grupp->kaubagrupp = $kaubagrupp;
        array_push($gruppdata, $grupp);
    }
    return $gruppdata;
}

function createSelect($query, $name) {
    global $connection;
    $query = $connection->prepare($query);
    $query->bind_result($id, $data);
    $query->execute();
    $result = "<select name='$name'>";
    $result .= "<option value=''></option>";
    while($query->fetch()) {
        $result .= "<option value='$id'>$data</option>";
    }
    $result .= "</select>";
    return $result;
}

function lisaKaubagrupp($kaubagrupp) {
    global $connection;
    $query = $connection->prepare("INSERT INTO kaubagrupid (kaubagrupp)
    VALUES (?)");
    $query->bind_param("s", $kaubagrupp);
    $query->execute();
}

function lisaKaup($kaubanimi, $hind, $kaubagrupp_id) {
    global $connection;
    $query = $connection->prepare("INSERT INTO kaubad (kaubanimi, hind, kaubagrupp_id)
    VALUES (?, ?, ?)");
    $query->bind_param("sid", $kaubanimi, $hind, $kaubagrupp_id);
    $query->execute();
}

function kustutaKaup($kaup_id) {
    global $connection;
    $query = $connection->prepare("DELETE FROM kaubad WHERE id=?");
    $query->bind_param("i", $kaup_id);
    $query->execute();
}
function kustutaKaubagrupp($kaubagrupp_id) {
    global $connection;
    $query = $connection->prepare("DELETE FROM kaubagrupid WHERE id=?");
    $query->bind_param("i", $kaubagrupp_id);
    $query->execute();
}

function muudaKaup($kaup_id, $kaubanimi, $hind, $kaubagrupp_id) {
    global $connection;
    $query = $connection->prepare("UPDATE kaubad
    SET kaubanimi=?, hind=?, kaubagrupp_id=?
    WHERE kaubad.id=?");
    $query->bind_param("siii", $kaubanimi, $hind, $kaubagrupp_id, $kaup_id);
    $query->execute();
}
function kontroll($kaubagrupp){
    global $connection;
    $query = $connection-> prepare("SELECT * FROM kaubagrupid 
WHERE kaubagrupp LIKE ?");
    $query->bind_param("s", $kaubagrupp);
    if($query->execute()){
        $query->store_result();
        $rows = $query->num_rows;
        return $rows;
    }
}
function isAdmin(){
    return $_SESSION["onAdmin"]==1;
}
?>