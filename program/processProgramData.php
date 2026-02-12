
<?php
require_once("data/db.php");

session_start();
session_regenerate_id();

$entryURL = $_SERVER['HTTP_REFERER'];

if($_POST && isset($_POST['clearEntries'])){
    $_SESSION['input']['programID'] = null;
    $_SESSION['input']['programFullName'] = null;
    $_SESSION['input']['programShortName'] = null;
    $_SESSION['messages']['createSuccess'] = "";
    $_SESSION['messages']['createError'] = "";    

    $_SESSION['errors']['programID'] = "";
    $_SESSION['errors']['programFullName'] = "";
    $_SESSION['errors']['programShortName'] = "";

    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveNewProgramEntry'])){
    $programID = $_POST['programID'];
    $programFullName = $_POST['programFullName'];
    $programShortName = $_POST['programShortName'];
    $progcollid = $_POST['progcollid'] ?? null;
    $progcolldeptid = $_POST['progcolldeptid'] ?? null;

    $_SESSION['input']['programID'] = $programID;
    $_SESSION['input']['programFullName'] = $programFullName;
    $_SESSION['input']['programShortName'] = $programShortName;

    if(!isset($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    if(filter_input(INPUT_POST,'programID', FILTER_VALIDATE_INT) === false){
        $_SESSION['errors']['programID'] = "Invalid ID entry or format";
    } else {
        $_SESSION['errors']['programID'] = "";
    } 

    if(filter_input(INPUT_POST,'programFullName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['programFullName'] = "Invalid Full Name entry or format";
    } else {
        $_SESSION['errors']['programFullName'] = "";
    }

    if(filter_input(INPUT_POST,'programShortName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['programShortName'] = "Invalid Short Name entry or format";
    } else {
        $_SESSION['errors']['programShortName'] = "";
    }

    if(empty($_SESSION['errors']['programID']) && empty($_SESSION['errors']['programFullName']) && empty($_SESSION['errors']['programShortName'])){
        // Check for duplicate ID, full name or short name
        $dbCheck = $db->prepare("SELECT progid, progfullname, progshortname FROM programs WHERE progid = :progid OR progfullname = :progfullname OR progshortname = :progshortname");
        $dbCheck->execute([
            'progid' => $programID,
            'progfullname' => $programFullName,
            'progshortname' => $programShortName
        ]);
        $existing = $dbCheck->fetch(PDO::FETCH_ASSOC);

        if($existing){
            if(isset($existing['progid']) && $existing['progid'] == $programID){
                $_SESSION['errors']['programID'] = "ID already exists";
            }
            if(isset($existing['progfullname']) && strcasecmp($existing['progfullname'], $programFullName) === 0){
                $_SESSION['errors']['programFullName'] = "Full name already exists";
            }
            if(isset($existing['progshortname']) && strcasecmp($existing['progshortname'], $programShortName) === 0){
                $_SESSION['errors']['programShortName'] = "Short name already exists";
            }
            $_SESSION['messages']['createError'] = "Duplicate program entry exists";
            $_SESSION['messages']['createSuccess'] = "";
            header("Location: $entryURL", true, 301);
            exit;
        }

        $dbStatement = $db->prepare("INSERT INTO programs (progid, progfullname, progshortname, progcollid, progcolldeptid) VALUES (:progid, :progfullname, :progshortname, :progcollid, :progcolldeptid)");
        $dbResult = $dbStatement->execute([
            'progid' => $programID,
            'progfullname' => $programFullName,
            'progshortname' => $programShortName,
            'progcollid' => $progcollid,
            'progcolldeptid' => $progcolldeptid
        ]);

        if($dbResult){
            $_SESSION['messages']['createSuccess'] = "Program entry created successfully";
            $_SESSION['messages']['createError'] = "";
        } else {
            $_SESSION['messages']['createError'] = "Failed to create program entry";
            $_SESSION['messages']['createSuccess'] = "";
        }        

        header("Location: $entryURL", true, 301);
    } else {
        header("Location: $entryURL", true, 301);
    }
}
