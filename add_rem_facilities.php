<?php 
  session_start(); 
  $Add=0;
  $db = new mysqli('localhost', 'root', '', 'project');
  if (!isset($_SESSION['roll_no'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: student_login.php');
  }
  else
  {
    if(isset($_GET['form_facility_add']))
    {
        $id=$_SESSION['roll_no'];
        $req_facility=$_GET['form_facility_add'];
        $doit=0;
        $Add=0;
        $hostel_id_query="SELECT hostel_id from student_details where roll_no='$id'";
        $hostel_id=$db->query($hostel_id_query);
        $hostel_id=$hostel_id->fetch_assoc()['hostel_id'];
        $hostel_available_query="SELECT * FROM facilities_available where hostel_id='$hostel_id';";
        $hostel_available_set=$db->query($hostel_available_query);
        if($hostel_available_set->num_rows>0)
        {
            while($row=$hostel_available_set->fetch_assoc())
            {
                if ($row['facility_id']==$req_facility)
                {
                    $doit=1;
                    break;
                }
            }
        }
        if($doit)
        {
            $checking_query="SELECT * FROM facilities_availed where roll_no='$id';";
            $output=$db->query($checking_query);
                    $Add_facility_query="Insert into facilities_availed values ('$id','$hostel_id','$req_facility');";
                    $db->query($Add_facility_query);
                    echo "Facility Added Succesfully";
        }
        else{echo "This facility is not available in this hostel!";}

            
    }
    if(isset($_GET['form_facility_remove']))
    {
        $id=$_SESSION['roll_no'];
        $req_facility=$_GET['form_facility_remove'];

        $hostel_id_query="SELECT hostel_id from student_details where roll_no='$id'";
        $hostel_id=$db->query($hostel_id_query);
        $hostel_id=$hostel_id->fetch_assoc()['hostel_id'];
        $checking_query="SELECT * FROM facilities_availed where roll_no='$id';";
        $output=$db->query($checking_query);
        if($output->num_rows>0)
            {
                while($row=$output->fetch_assoc())
                {
                    if($row['facility_id']==$req_facility)
                    {
                        $Add=0;
                        break;
                    }
                }
            }
        if($Add)
        {
            $rem_facility="DELETE FROM facilities_availed where roll_no='$id' AND facility_id='$req_facility';";
            $db->query($rem_facility);
            echo "Facility Removed Succesfully";
        }
        else
        {
            echo "this facility is not availed by you";
        }

        }
    }
  
?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="header">
	<h2>Add Or Remove Facilities</h2>
</div>
<div class="content">
<table>
    <h4>All Available Facilities in your Hostel</h4><br><br>
    <tr>
      <th>Facility ID</th>
      <th>Facility Name</th>
      <th>Facility Cost</th>
    </tr>
    <?php
        $id=$_SESSION['roll_no'];
        $hostel_id_query="SELECT hostel_id from student_details where roll_no='$id'";
        $hostel_id=$db->query($hostel_id_query);
        $hostel_id=$hostel_id->fetch_assoc()['hostel_id'];
        $facility_available="SELECT * FROM facilities_available inner join facilities on facilities_available.facility_id=facilities.facility_id where hostel_id='$hostel_id';";
        $results=$db->query($facility_available);
        if($results->num_rows > 0)
        {
            while($row = $results->fetch_assoc())
            {
                echo "<tr>";
                echo "<td>".$row['facility_id']."</td>";
                echo "<td>".$row['facility_name']."</td>";
                echo "<td>".$row['facility_cost']."</td>";
                echo "</tr>";
            }
        }
        else
        {
            echo "No Facilities are available in your hostel";
        }
        
    ?>
</table>
<br>
<br>
    <form action="add_rem_facilities.php" method="get">
        <label>Enter the facility ID you want to Add</label><br>
        <input type="number" name="form_facility_add">
        <input type="submit" value="submit">
    </form>

    <form action="add_rem_facilities.php" method="get">
        <label>Enter the facility ID you want to Remove</label><br>
        <input type="number" name="form_facility_remove">
        <input type="submit" value="submit">
    </form>
</div>
</body>
</html>