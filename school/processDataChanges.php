<?php
require_once("data/db.php");

session_start();
session_regenerate_id();

$entryURL = $_SERVER['HTTP_REFERER'];

if($_POST && isset($_POST['clearChanges'])){
    $_SESSION['errors']['schoolFullName'] = "";
    $_SESSION['errors']['schoolShortName'] = "";
    $_SESSION['messages']['updateSuccess'] = "";
    $_SESSION['messages']['updateError'] = "";

    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveChanges'])){
    $schoolID = $_POST['schoolID'];
    $schoolFullName = $_POST['schoolFullName'];
    $schoolShortName = $_POST['schoolShortName'];

    $_SESSION['input']['schoolFullName'] = $schoolFullName;
    $_SESSION['input']['schoolShortName'] = $schoolShortName;

    if(isset($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    if(filter_input(INPUT_POST,'schoolFullName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['schoolFullName'] = "Invalid Full Name entry. Reverting to original value";
    } else {
        $_SESSION['errors']['schoolFullName'] = "";
    }

    if(filter_input(INPUT_POST,'schoolShortName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['schoolShortName'] = "Invalid Short Name entry. Reverting to original value";
    } else {
        $_SESSION['errors']['schoolShortName'] = "";
    }

    if(empty($_SESSION['errors']['schoolFullName']) && empty($_SESSION['errors']['schoolShortName'])){
        
        $dbStatement = $db->prepare('UPDATE colleges SET collfullname = ?, collshortname = ? WHERE collid = ?');
        $dbResult = $dbStatement->execute([
            $schoolFullName,
            $schoolShortName,
            $schoolID
        ]);

        if($dbResult){
            $_SESSION['messages']['updateSuccess'] = "School entry updated successfully";
            $_SESSION['messages']['updateError'] = "";
        } else {
            $_SESSION['messages']['updateError'] = "Failed to update school entry";
            $_SESSION['messages']['updateSuccess'] = "";
        }

        header("Location: $entryURL", true, 301);

    } else {
        header("Location: $entryURL", true, 301);
    }
}

if($_POST && isset($_POST['confirmDelete'])){
    $schoolID = $_POST['schoolID'];

    // Validate schoolID is numeric
    if(!is_numeric($schoolID) || $schoolID <= 0){
        $_SESSION['errors']['deleteError'] = "Invalid School ID provided";
        header("Location: $entryURL", true, 301);
        exit;
    }

    // Set confirmation flag in session to show confirmation page
    $_SESSION['confirmDelete'] = true;
    header("Location: $entryURL", true, 301);
    exit;
}

if($_POST && isset($_POST['executeDelete'])){
    $schoolID = $_POST['schoolID'];

    if(isset($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    // Validate schoolID is numeric
    if(!is_numeric($schoolID) || $schoolID <= 0){
        $_SESSION['errors']['deleteError'] = "Invalid School ID provided";
        $_SESSION['confirmDelete'] = false;
        header("Location: $entryURL", true, 301);
        exit;
    }

    // Check if school exists before deletion
    $dbCheckStatement = $db->prepare('SELECT collid FROM colleges WHERE collid = ?');
    $dbCheckStatement->execute([$schoolID]);
    $schoolExists = $dbCheckStatement->fetch();

    if(!$schoolExists){
        $_SESSION['errors']['deleteError'] = "School record not found";
        $_SESSION['confirmDelete'] = false;
        header("Location: $entryURL", true, 301);
        exit;
    }

    // Check if school has associated students before deletion
    $dbStudentCheckStatement = $db->prepare('SELECT COUNT(*) as studentCount FROM students WHERE studcollid = ?');
    $dbStudentCheckStatement->execute([$schoolID]);
    $studentCheck = $dbStudentCheckStatement->fetch();

    if($studentCheck['studentCount'] > 0){
        $_SESSION['errors']['deleteError'] = "Cannot delete school with existing students. Please delete all students first.";
        $_SESSION['confirmDelete'] = false;
        header("Location: $entryURL", true, 301);
        exit;
    }

    // Proceed with deletion
    $dbStatement = $db->prepare('DELETE FROM colleges WHERE collid = ?');
    $dbResult = $dbStatement->execute([$schoolID]);

    // Clear confirmation flag
    $_SESSION['confirmDelete'] = false;

    if($dbResult){
        $_SESSION['messages']['updateSuccess'] = "School entry deleted successfully";
        $_SESSION['messages']['updateError'] = "";
        header("Location: index.php?section=school&page=schoolList", true, 301);
    } else {
        $_SESSION['errors']['deleteError'] = "Failed to delete school entry";
        header("Location: $entryURL", true, 301);
    }
}

?>