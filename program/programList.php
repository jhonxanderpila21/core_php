<?php
   require_once("data/db.php");
   session_start();
   session_destroy();

   $schoolID = $_GET['schoolID'] ?? null;
   $departmentID = $_GET['departmentID'] ?? null;

   // Redirect if school or department not provided
   if ($schoolID === null || $departmentID === null) {
       header("Location: index.php?section=program&page=chooseSchoolDepartment", true, 301);
       exit;
   }

   // Fetch school and department details
   $dbStatement = $db->prepare("SELECT collfullname FROM colleges WHERE collid = :schoolID");
   $dbStatement->execute([':schoolID' => $schoolID]);
   $school = $dbStatement->fetch();

   $dbStatement = $db->prepare("SELECT deptfullname FROM departments WHERE deptid = :departmentID");
   $dbStatement->execute([':departmentID' => $departmentID]);
   $department = $dbStatement->fetch();

   $limit = 6;
   // Get total programs for the selected school and department
   $dbStatement = $db->prepare("SELECT * FROM programs WHERE progcollid = :schoolID AND progcolldeptid = :departmentID");
   $dbStatement->execute([':schoolID' => $schoolID, ':departmentID' => $departmentID]);
   $totalPrograms = $dbStatement->rowCount();

   $totalPages = ceil($totalPrograms / $limit);

   if(!isset($_GET['pgSection']) || !is_numeric($_GET['pgSection'])) {
       $currentPage = 1;
   } else {
       $currentPage = intval($_GET['pgSection']);
   }

   $offset = ($currentPage - 1) * $limit;

   // Fetch programs for the selected school and department with pagination
   $dbStatement = $db->prepare("SELECT * FROM programs WHERE progcollid = :schoolID AND progcolldeptid = :departmentID ORDER BY progid LIMIT :offset, :limit;");
   $dbStatement->bindParam('schoolID', $schoolID, PDO::PARAM_INT);
   $dbStatement->bindParam('departmentID', $departmentID, PDO::PARAM_INT);
   $dbStatement->bindParam('offset', $offset, PDO::PARAM_INT);
   $dbStatement->bindParam('limit', $limit, PDO::PARAM_INT);
   $dbStatement->execute();
   
   $programs = $dbStatement->fetchAll();
?>

<h1>Program List</h1>
<h2><?php echo htmlspecialchars($school['collfullname']); ?> - <?php echo htmlspecialchars($department['deptfullname']); ?></h2>

<div>
    <h2><a href="index.php?section=program&page=chooseSchoolDepartment" class="btn btn-primary">Back</a></h2>
    <h2><a href="index.php?section=program&page=programCreate&schoolID=<?php echo $schoolID; ?>&departmentID=<?php echo $departmentID; ?>" class="btn btn-primary">Create Program</a></h2>
</div>

<table>
    <tr>
        <th>Program ID</th>
        <th>Program Full Name</th>
        <th>Program Short Name</th>
        
    </tr>
    <?php if (count($programs) > 0): ?>
        <?php foreach ($programs as $program): ?>
        <tr>
            <td><?php echo $program['progid']; ?></td>
            <td><?php echo $program['progfullname']; ?></td>
            <td><?php echo $program['progshortname']; ?></td>
            <td>
                <a href="index.php?section=program&page=programUpdate&progid=<?php echo $program['progid']; ?>&schoolID=<?php echo $schoolID; ?>&departmentID=<?php echo $departmentID; ?>" class="btn btn-info">Update</a>
                <a href="index.php?section=program&page=programDelete&progid=<?php echo $program['progid']; ?>&schoolID=<?php echo $schoolID; ?>&departmentID=<?php echo $departmentID; ?>" class="btn btn-danger">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">No programs found for this school and department.</td>
        </tr>
    <?php endif; ?>
    <tr>
        <td colspan="4">
            <span>
                Total of: <?= $totalPrograms ?> programs
            </span>
        </td>
        <td colspan="4">
          <?php if($totalPages > 1): ?>  
            <?php if ($currentPage > 1): ?>
                <a href="index.php?section=program&page=programList&schoolID=<?php echo $schoolID; ?>&departmentID=<?php echo $departmentID; ?>&pgSection=<?= $currentPage - 1 ?>" class="btn btn-primary">Previous</a>
            <?php else: ?>
                <span>Previous</span>
            <?php endif; ?>
            <?php if ($currentPage < $totalPages): ?>
                <a href="index.php?section=program&page=programList&schoolID=<?php echo $schoolID; ?>&departmentID=<?php echo $departmentID; ?>&pgSection=<?= $currentPage + 1 ?>" class="btn btn-primary">Next</a>
            <?php else: ?>
                <span>Next</span>
            <?php endif; ?>
          <?php endif; ?>  
        </td>
    </tr>
</table>
