<?php
    require_once("data/db.php");
    session_start();
    session_regenerate_id();

    $departmentID = $_GET['deptid'];

    $dbStatement = $db->prepare("SELECT * FROM departments WHERE deptid = :deptid");
    $dbStatement->execute(['deptid' => $departmentID]);
    $department = $dbStatement->fetch();
?>
<h1>Department Delete</h1>

<span class="success-message">
    <?php echo $_SESSION['messages']['updateSuccess'] ?? null; ?>
</span>
<span class="error-message">
    <?php echo $_SESSION['messages']['updateError'] ?? null; ?>
    <?php echo $_SESSION['errors']['deleteError'] ?? null; ?>
</span>
<form action="index.php?section=department&page=processDeptDataChanges" method="post">
    <input type="hidden" name="deptcollid" value="<?php echo htmlspecialchars($department['deptcollid']); ?>">
    <table>
        <tr>
            <td style="width: 10em;">Department ID:</td>
            <td style="width: 30em;"><input type="text" id="departmentID" name="departmentID" value="<?php echo $department['deptid']; ?>" readonly class="data-input"></td>
        </tr>
        <tr>
            <td>Department Full Name:</td>
            <td><input type="text" id="departmentFullName" name="departmentFullName" value="<?php echo $department['deptfullname']; ?>" readonly class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['departmentFullName'] ?? null; ?>
                </span>
            </td>                
        </tr>
        <tr>
            <td>Department Short Name:</td>
            <td><input type="text" id="departmentShortName" name="departmentShortName" value="<?php echo $department['deptshortname']; ?>" readonly class="data-input"></td>
            <td>
                <span>
                    <?php echo $_SESSION['errors']['departmentShortName'] ?? null; ?>
                </span>
            </td>                
        </tr>
        <?php if(isset($_SESSION['confirmDelete']) && $_SESSION['confirmDelete']): ?>
        <tr>
            <td colspan="3">
                <div class="confirmation-warning">
                    <h3 style="color: #f10a0a;">Are you sure?</h3>
                    <p>You are about to permanently delete the department "<strong><?php echo htmlspecialchars($department['deptfullname']); ?></strong>". This action cannot be undone.</p>
                </div>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="2">
                <a href="index.php?section=department&page=departmentList&deptcollid=<?php echo $department['deptcollid']; ?>" class="btn btn-primary">
                    Cancel Operation
                </a>
                <?php if(!isset($_SESSION['confirmDelete']) || !$_SESSION['confirmDelete']): ?>
                <button type="submit" name="confirmDelete" class="btn btn-danger">
                    Confirm Delete
                </button>

                <?php else: ?>
                <button type="submit" name="executeDelete" class="btn btn-danger">
                    Yes, Delete This Department             
                </button>
                <button type="button" onclick="location.href='index.php?section=school&page=schoolList'" class="btn btn-secondary">
                    Cancel
                </button>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</form>    
