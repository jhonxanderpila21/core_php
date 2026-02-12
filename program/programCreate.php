
<?php
   session_start();
   session_regenerate_id();
   $schoolID = $_GET['schoolID'] ?? null;
   $departmentID = $_GET['departmentID'] ?? null;
?>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Create</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body> -->
    <h1>Program Create</h1>
    <span>
        <?php echo $_SESSION['messages']['createSuccess'] ?? null; ?>
        <?php echo $_SESSION['messages']['createError'] ?? null; ?>
    </span>
    <form action="index.php?section=program&page=processProgramData" method="post">
        <input type="hidden" name="progcollid" value="<?php echo htmlspecialchars($schoolID); ?>">
        <input type="hidden" name="progcolldeptid" value="<?php echo htmlspecialchars($departmentID); ?>">
        <table>
            <tr>
                <td style="width: 10em;">Program ID:</td>
                <td style="width: 30em;"><input type="text" id="programID" name="programID" value="<?= $_SESSION['input']['programID'] ?? null; ?>" class="data-input"></td>
                <td>
                    <span>
                        <?php echo $_SESSION['errors']['programID'] ?? null; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>Program Full Name:</td>
                <td><input type="text" id="programFullName" name="programFullName" value="<?= $_SESSION['input']['programFullName'] ?? null; ?>" class="data-input"></td>
                <td>
                    <span>
                        <?php echo $_SESSION['errors']['programFullName'] ?? null; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>Program Short Name:</td>
                <td><input type="text" id="programShortName" name="programShortName" value="<?= $_SESSION['input']['programShortName'] ?? null; ?>" class="data-input"></td>
                <td>
                    <span>
                        <?php echo $_SESSION['errors']['programShortName'] ?? null; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="saveNewProgramEntry" class="btn">
                        Save New Program Entry
                    </button>
                    <button type="submit" name="clearEntries" class="btn">
                        Reset Form
                    </button>
                    <a href="index.php?section=program&page=programList" class="btn btn-danger">
                        Exit
                    </a>
                </td>
            </tr>
        </table>
    </form>    
<!-- </body>
</html> -->
