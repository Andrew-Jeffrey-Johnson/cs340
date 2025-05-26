<?php
session_start();
// Include config file
require_once "config.php";
ob_start();

$Ssn = $_SESSION["Ssn"];
 
// Define variables and initialize with empty values
$Dependent_name = $Relationship = $Bdate = $Sex = "";
$Dependent_name_err = $Relationship_err = $Bdate_err = $Sex_err = "";


if(isset($_GET["Dependent_name"]) && !empty(trim($_GET["Dependent_name"]))){
		$_SESSION["Dependent_name"] = $_GET["Dependent_name"];

    // Prepare a select statement
    $sql1 = "SELECT * FROM DEPENDENT WHERE Essn = ? AND Dependent_name = ?";
  
    if($stmt1 = mysqli_prepare($link, $sql1)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt1, "is", $param_Essn, $param_Dependent_name);      
        // Set parameters
       $param_Essn = $_SESSION["Ssn"];
       $param_Dependent_name = $_SESSION["Dependent_name"];

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt1)){
            $result1 = mysqli_stmt_get_result($stmt1);
			if(mysqli_num_rows($result1) > 0){

				$row = mysqli_fetch_array($result1);

				$Dependent_name = $row['Dependent_name'];
				$Essn = $row['Essn'];
				$Bdate = $row['Bdate'];
				$Relationship = $row['Relationship'];
				$Sex = $row['Sex'];
			}
		}
	}
}
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	// Validate Dependent_name
    $Dependent_name = trim($_POST["Dependent_name"]);
    if(empty($Dependent_name)){
        $Dependent_name_err = "Please enter a Dependent Name.";
    } elseif(!filter_var($Dependent_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Dependent_name_err = "Please enter a valid Dependent Name.";
    } 
	// Validate Sex
    $Sex = trim($_POST["Sex"]);
    if(empty($Sex)){
        $Sex_err = "Please enter Sex.";     
    } elseif(!filter_var($Sex, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Sex_err = "Please enter only characters.";
    } 
    // Validate Relationship
    $Relationship = trim($_POST["Relationship"]);
    if(empty($Relationship)){
        $Relationship_err = "Please enter a Relationship.";
    } elseif(!filter_var($Relationship, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Relationship_err = "Please enter only characters.";
    }
	// Validate Birthdate
    $Bdate = trim($_POST["Bdate"]);
    if(empty($Bdate)){
        $SBdate_err = "Please enter birthdate.";     
    }	
    // Check input errors before inserting in database
    if(empty($Dependent_name_err) && empty($Relationship_err) && empty($Bdate_err) && empty($Sex_err)){
        // Prepare an insert statement
        $sql = "UPDATE DEPENDENT SET Essn=?, Dependent_name=?, Sex=?, Bdate=?, Relationship=? WHERE Dependent_name=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isssss", $param_Ssn, $param_Dependent_name, 
				$param_Sex, $param_Bdate, $param_Relationship, $_SESSION["Dependent_name"]);
            
            // Set parameters
			$param_Ssn = $Ssn;
            $param_Dependent_name = $Dependent_name;
			$param_Sex = $Sex;
			$param_Bdate = $Bdate;
            $param_Relationship = $Relationship;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
				    header("location: index.php");
					exit();
            } else{
                echo "<center><h4>Error while updating dependent: ", mysqli_error($link), "</h4></center>";
				$Dependent_name_err = "Enter a unique Dependent Name";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Create a Dependent</h2>
                    </div>
                    <p>Update a Dependent for Employee with SSN = <?php echo $Ssn;?></p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						<div class="form-group <?php echo (!empty($Dependent_name_err)) ? 'has-error' : ''; ?>">
                            <label>Dependent Name</label>
                            <input type="text" name="Dependent_name" class="form-control" value="<?php echo $Dependent_name; ?>">
                            <span class="help-block"><?php echo $Dependent_name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Sex_err)) ? 'has-error' : ''; ?>">
                            <label>Sex</label>
                            <input type="text" name="Sex" class="form-control" value="<?php echo $Sex; ?>">
                            <span class="help-block"><?php echo $Sex_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Relationship_err)) ? 'has-error' : ''; ?>">
                            <label>Relationship</label>
                            <input type="text" name="Relationship" class="form-control" value="<?php echo $Relationship; ?>">
                            <span class="help-block"><?php echo $Relationship_err;?></span>
                        </div>            
						<div class="form-group <?php echo (!empty($Bdate_err)) ? 'has-error' : ''; ?>">
                            <label>Birth date</label>
                            <input type="date" name="Bdate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            <span class="help-block"><?php echo $Bdate_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
