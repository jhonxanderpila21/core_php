
<?php
    require_once("data/db.php");
    session_start();
    session_regenerate_id();

    $departmentID = $_GET['deptid'];

    $dbStatement = $db->prepare("SELECT * FROM departments WHERE deptid = :departmentID");
    $dbStatement->execute(['departmentID' => $departmentID]);
    $school = $dbStatement->fetch();
?>
<h1>Department Update</h1>
<span>
    <?php echo $_SESSION['messages']['updateSuccess'] ?? null; ?>
    <?php echo $_SESSION['messages']['updateError'] ?? null; ?>
</span>
<form action="index.php?section=department&page=processDeptDataChanges" method="post">
    <table>
        <tr>
            <td style="width: 10em;">Department ID:</td>
            <td style="width: 30em;"><input type="text" id="departmentID" name="departmentID" value="<?php echo $school['deptid']; ?>" readonly class="data-input"></td>
        </tr>
        <tr>
            <td>Department Full Name:</td>
            <td><input type="text" id="departmentFullName" name="departmentFullName" value="<?php echo $school['deptfullname']; ?>" class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['departmentFullName'] ?? null; ?>
                </span>
            </td>                
        </tr>
        <tr>
            <td>Department Short Name:</td>
            <td><input type="text" id="departmentShortName" name="departmentShortName" value="<?php echo $school['deptshortname']; ?>" class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['departmentShortName'] ?? null; ?>
                </span>
            </td>                
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" name="saveChanges" class="btn">
                    Update Department Entry
                </button>
                <button type="submit" name="clearChanges" class="btn">
                    Reset Form
                </button>
                <a href="index.php?section=department&page=departmentList" class="btn btn-danger">
                    Exit
                </a>
            </td>
        </tr>
    </table>
</form>    
