<?php
    require_once("data/db.php");
    session_start();
    session_destroy();

    $dbStatement = $db->prepare("SELECT * FROM colleges");
    $dbStatement->execute();
    $schools = $dbStatement->fetchAll();

    $departments = [];
    $selectedSchoolID = $_GET['schoolID'] ?? null;
    $schoolError = $_GET['schoolError'] ?? null;
    $deptError = $_GET['deptError'] ?? null;

    // Load departments if school is selected
    if ($selectedSchoolID) {
        $dbStatement = $db->prepare("SELECT * FROM departments WHERE deptcollid = :schoolID");
        $dbStatement->execute([':schoolID' => $selectedSchoolID]);
        $departments = $dbStatement->fetchAll();
    }

?>

<h1>Select School</h1>
<form action="index.php?section=program&page=processSchoolDepartmentChoice" method="post">
    <table>
        <tr>
            <td>
                <select name="schoolID" id="schoolID" class="school-select">
                    <option value=null selected hidden disabled>Select School</option>
                    <?php foreach ($schools as $school): ?>
                    <option value="<?php echo $school['collid']; ?>" <?php if ($selectedSchoolID == $school['collid']) echo 'selected'; ?>><?php echo $school['collfullname']; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="selectSchool" class="btn btn-info">Select School</button>
                <?php if ($schoolError): ?>
                    <span style="color: black; margin-left: 10px;"><?php echo $schoolError; ?></span>
                <?php elseif (empty($departments) && $selectedSchoolID !== null): ?>
                    <span style="color: black; margin-left: 10px;">No departments available.</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>
                
            </td>
        </tr>
    </table>
</form>

<form action="index.php?section=program&page=processSchoolDepartmentChoice" method="post">
    <input type="hidden" name="schoolID" value="<?php echo htmlspecialchars($selectedSchoolID); ?>">
    <table>
        <tr>
            <td>                
                <select name="departmentID" id="departmentID" class="school-select" <?php if (empty($departments)) echo 'disabled'; ?>>
                    <option value=null selected hidden disabled>Select Department</option>
                    <?php foreach ($departments as $department): ?>
                    <option value="<?php echo $department['deptid']; ?>"><?php echo $department['deptfullname']; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="selectDepartment" class="btn btn-info" <?php if (empty($departments)) echo 'disabled'; ?>>Select Department</button>
                <?php if ($deptError): ?>
                    <span style="color: black; margin-left: 10px;"><?php echo $deptError; ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>
                
            </td>
        </tr>
    </table>
</form>