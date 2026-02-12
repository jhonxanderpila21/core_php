<?php
require_once("data/db.php");

session_start();
session_regenerate_id();

$entryURL = $_SERVER['HTTP_REFERER'];

if($_POST && isset($_POST['clearChanges'])){
    $_SESSION['errors']['departmentFullName'] = "";
    $_SESSION['errors']['departmentShortName'] = "";
    $_SESSION['messages']['updateSuccess'] = "";
    $_SESSION['messages']['updateError'] = "";

    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveChanges'])){
    $departmentID = $_POST['departmentID'];
    $departmentFullName = $_POST['departmentFullName'];
    $departmentShortName = $_POST['departmentShortName'];

    $_SESSION['input']['departmentFullName'] = $departmentFullName;
    $_SESSION['input']['departmentShortName'] = $departmentShortName;

    if(isset($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    if(filter_input(INPUT_POST,'departmentFullName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['departmentFullName'] = "Invalid Full Name entry. Reverting to original value";
    } else {
        $_SESSION['errors']['departmentFullName'] = "";
    }

    if(filter_input(INPUT_POST,'departmentShortName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false){
        $_SESSION['errors']['departmentShortName'] = "Invalid Short Name entry. Reverting to original value";
    } else {
        $_SESSION['errors']['departmentShortName'] = "";
    }

    if(empty($_SESSION['errors']['departmentFullName']) && empty($_SESSION['errors']['departmentShortName'])){
        
        $dbStatement = $db->prepare('UPDATE departments SET deptfullname = ?, deptshortname = ? WHERE deptid = ?');
        $dbResult = $dbStatement->execute([
            $departmentFullName,
            $departmentShortName,
            $departmentID
        ]);

        if($dbResult){
            $_SESSION['messages']['updateSuccess'] = "Department entry updated successfully";
            $_SESSION['messages']['updateError'] = "";
        } else {
            $_SESSION['messages']['updateError'] = "Failed to update department entry";
            $_SESSION['messages']['updateSuccess'] = "";
        }

        header("Location: $entryURL", true, 301);

    } else {
        header("Location: $entryURL", true, 301);
    }
}

if($_POST && isset($_POST['confirmDelete'])){
    $departmentID = $_POST['departmentID'];

    // Validate departmentID is numeric
    if(!is_numeric($departmentID) || $departmentID <= 0){
        $_SESSION['errors']['deleteError'] = "Invalid Department ID provided";
        header("Location: $entryURL", true, 301);
        exit;
    }

    // Set confirmation flag in session to show confirmation page
    $_SESSION['confirmDelete'] = true;
    header("Location: $entryURL", true, 301);
    exit;
}

if($_POST && isset($_POST['executeDelete'])){
    $departmentID = $_POST['departmentID'];
    $deptcollid = $_POST['deptcollid'] ?? null;

    if(isset($_SESSION['errors'])){
        $_SESSION['errors'] = [];
    }

    // Validate departmentID is numeric
    if(!is_numeric($departmentID) || $departmentID <= 0){
        $_SESSION['errors']['deleteError'] = "Invalid Department ID provided";
        $_SESSION['confirmDelete'] = false;
        header("Location: $entryURL", true, 301);
        exit;
    }

    // Check if department exists before deletion
    $dbCheckStatement = $db->prepare('SELECT deptid FROM departments WHERE deptid = ?');
    $dbCheckStatement->execute([$departmentID]);
    $departmentExists = $dbCheckStatement->fetch();
    if(!$departmentExists){
        $_SESSION['errors']['deleteError'] = "Department record not found";
        $_SESSION['confirmDelete'] = false;
        header("Location: $entryURL", true, 301);
        exit;
    }

    // Check if department has associated students before deletion
    $dbStudentCheckStatement = $db->prepare('SELECT COUNT(*) as studentCount FROM students WHERE studcolldeptid = ?');
    $dbStudentCheckStatement->execute([$departmentID]);
    $studentCheck = $dbStudentCheckStatement->fetch();

    if($studentCheck['studentCount'] > 0){
        $_SESSION['errors']['deleteError'] = "Cannot delete department with existing students. Please delete all students first.";
        $_SESSION['confirmDelete'] = false;
        header("Location: $entryURL", true, 301);
        exit;
    }

    // Proceed with deletion
    $dbStatement = $db->prepare('DELETE FROM departments WHERE deptid = ?');
    $dbResult = $dbStatement->execute([$departmentID]);

    // Clear confirmation flag
    $_SESSION['confirmDelete'] = false;

    if($dbResult){
        $_SESSION['messages']['updateSuccess'] = "Department entry deleted successfully";
        $_SESSION['messages']['updateError'] = "";
        header("Location: index.php?section=department&page=departmentList&deptcollid={$deptcollid}", true, 301);
    } else {
        $_SESSION['errors']['deleteError'] = "Failed to delete department entry";
        header("Location: $entryURL", true, 301);
    }
}

?>