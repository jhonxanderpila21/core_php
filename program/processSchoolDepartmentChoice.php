<?php
    require_once("data/db.php");
    session_start();
    session_destroy();

    $errors = [];
    $schoolID = $_POST['schoolID'] ?? null;
    $departmentID = $_POST['departmentID'] ?? null;

    // Handle school selection
    if (isset($_POST['selectSchool'])) {
        if (!isset($_POST['schoolID']) || $_POST['schoolID'] === null || $_POST['schoolID'] === '') {
            header("Location: index.php?section=program&page=chooseSchoolDepartment&schoolError=Please select a school", true, 303);
            exit;
        }

        // School selected successfully, redirect back with schoolID in URL
        header("Location: index.php?section=program&page=chooseSchoolDepartment&schoolID={$schoolID}", true, 303);
        exit;
    }

    // Handle department selection
    if (isset($_POST['selectDepartment'])) {
        if (!isset($_POST['departmentID']) || $_POST['departmentID'] === null || $_POST['departmentID'] === '') {
            header("Location: index.php?section=program&page=chooseSchoolDepartment&schoolID={$schoolID}&deptError=Please select a department", true, 303);
            exit;
        }

        if (!isset($_POST['schoolID']) || $_POST['schoolID'] === null || $_POST['schoolID'] === '') {
            header("Location: index.php?section=program&page=chooseSchoolDepartment&schoolError=Please select a school", true, 303);
            exit;
        }

        // All validations passed, redirect to program list
        header("Location: index.php?section=program&page=programList&schoolID={$schoolID}&departmentID={$departmentID}", true, 303);
        exit;
    }

    // No valid action, redirect back
    header("Location: index.php?section=program&page=chooseSchoolDepartment", true, 303);
    exit;
?>
