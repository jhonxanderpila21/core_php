<?php
   session_start();
   session_regenerate_id();
?>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Create</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body> -->
    <h1>Department Create</h1>
    <span>
        <?php echo $_SESSION['messages']['createSuccess'] ?? null; ?>
        <?php echo $_SESSION['messages']['createError'] ?? null; ?>
    </span>
    <form action="index.php?section=department&page=processDepartmentData" method="post">
        <table>
            <tr>
                <td style="width: 10em;">Department ID:</td>
                <td style="width: 30em;"><input type="text" id="departmentID" name="departmentID" value="<?= $_SESSION['input']['departmentID'] ?? null; ?>" class="data-input"></td>
                <td>
                    <span>
                        <?php echo $_SESSION['errors']['departmentID'] ?? null; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>Department Full Name:</td>
                <td><input type="text" id="departmentFullName" name="departmentFullName" value="<?= $_SESSION['input']['departmentFullName'] ?? null; ?>" class="data-input"></td>
                <td>
                    <span>
                        <?php echo $_SESSION['errors']['departmentFullName'] ?? null; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>Department Short Name:</td>
                <td><input type="text" id="departmentShortName" name="departmentShortName" value="<?= $_SESSION['input']['departmentShortName'] ?? null; ?>" class="data-input"></td>
                <td>
                    <span>
                        <?php echo $_SESSION['errors']['departmentShortName'] ?? null; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="saveNewDepartmentEntry" class="btn">
                        Save New Department Entry
                    </button>
                    <button type="submit" name="clearEntries" class="btn">
                        Reset Form
                    </button>
                    <a href="index.php?section=department&page=departmentList" class="btn btn-danger">
                        Exit
                    </a>
                </td>
            </tr>
        </table>
    </form>    
<!-- </body>
</html> -->