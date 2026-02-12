<?php
require_once("data/db.php");

session_start();
session_regenerate_id();




$entryURL = $_SERVER['HTTP_REFERER'];


if($_POST && isset($_POST['clearEntries'])){
    $_SESSION['input']['departmentID'] = null;
    $_SESSION['input']['departmentFullName'] = null;
    $_SESSION['input']['departmentShortName'] = null;
    $_SESSION['messages']['createSuccess'] = "";
    $_SESSION['messages']['createError'] = "";    

    $_SESSION['errors']['departmentID'] = "";
    $_SESSION['errors']['departmentFullName'] = "";
    $_SESSION['errors']['departmentShortName'] = "";

    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveNewDepartmentEntry'])){
    $departmentID = $_POST['departmentID'];
    $departmentFullName = $_POST['departmentFullName'];
    $departmentShortName = $_POST['departmentShortName'];
    $deptcollid = $_POST['deptcollid'] ?? null;

    $_SESSION['input']['departmentID'] = $departmentID;
    $_SESSION['input']['departmentFullName'] = $departmentFullName;
    $_SESSION['input']['departmentShortName'] = $departmentShortName;

    if(!$_SESSION['errors']){
        $_SESSION['errors'] = [];
    }

    if(filter_input(INPUT_POST,'departmentID', FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['departmentID'] = "Invalid ID entry or format";
    } else {
        $_SESSION['errors']['departmentID'] = "";
    } 

    if(filter_input(INPUT_POST,'departmentFullName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['departmentFullName'] = "Invalid Full Name entry or format";
    } else {
        $_SESSION['errors']['departmentFullName'] = "";
    }

    if(filter_input(INPUT_POST,'departmentShortName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['departmentShortName'] = "Invalid Short Name entry or format";
    } else {
        $_SESSION['errors']['departmentShortName'] = "";
    }

    if(empty($_SESSION['errors']['departmentID']) && empty($_SESSION['errors']['departmentFullName']) && empty($_SESSION['errors']['departmentShortName'])){
        $dbStatement = $db->prepare("INSERT INTO departments (deptid, deptfullname, deptshortname, deptcollid) VALUES (:deptid, :deptfullname, :deptshortname, :deptcollid);");
        $dbResult = $dbStatement->execute([
            'deptid' => $departmentID,
            'deptfullname' => $departmentFullName,
            'deptshortname' => $departmentShortName,   
            'deptcollid' => $deptcollid
        ]);

        if($dbResult){
            $_SESSION['messages']['createSuccess'] = "Department entry created successfully";
            $_SESSION['messages']['createError'] = "";
        } else {
            $_SESSION['messages']['createError'] = "Failed to create department entry";
            $_SESSION['messages']['createSuccess'] = "";
        }        

        header("Location: $entryURL", true, 301);
    } else {
        header("Location: $entryURL", true, 301);
    }
}