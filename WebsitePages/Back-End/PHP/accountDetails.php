<?php include('dbConnection.php') ?>

<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="../../Front-End/CSS/accountDetails.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
  <title>Account Details</title>
</head>

<body>
  <header class="header">
    <div class="logo">
      <a href="../../Back-End/PHP/userHome.php"><img src="../../images/Logo2.png" alt="Company Logo"></a>
    </div>
  </header>

  <?php
  // Preview profile information
  $CompanyID = 1; // For testing, change to dynamic later
  $query = 'SELECT Email, CompanyName, Logo FROM company WHERE CompanyID=' . $CompanyID;
  $row = mysqli_fetch_assoc(mysqli_query($connection, $query));
  $email = $row['Email'];
  $companyName = $row['CompanyName'];
  $logo = $row['Logo'];

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['logo'])) {

      // Get form data
      $companyName = $_POST['companyName'];
      $email = $_POST['email'];

      // Handle file upload
      if (file_exists($_FILES['logo']['tmp_name']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
        $path_parts = pathinfo($_FILES["logo"]["name"]);
        $extension = $path_parts['extension'];
        $filenewname = $companyName . "_" . uniqid() . "." . $extension; // Unique name with company name
        $folder = "../../images/" . $filenewname;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $folder)) {
          $logo = $filenewname; // Update the logo variable with the new file name
        } else {
          $logo = $row['Logo']; // Keep the old logo if upload fails
        }
      }

      // Update the database
      $stmt = $connection->prepare("UPDATE company SET CompanyName = ?, Email = ?, Logo = ? WHERE CompanyID = ?");
      $stmt->bind_param("sssi", $companyName, $email, $logo, $CompanyID);

      // Execute the statement
      if ($stmt->execute()) {
        echo "<script>alert('Profile edited successfully!');</script>";
      } else {
        echo "<script>alert('Failed to edit: " . $stmt->error . "');</script>";
      }

      // Close the statement
      $stmt->close();
    } else {
      echo '<script>alert("File is NOT set.");</script>';
    }
  }
  ?>

  <div class="main">
    <a id='arrow' href="../../Back-End/PHP/userHome.php"><i class="fa fa-chevron-left" style="color: #003f91; font-size: 30px; justify-self: end;"></i></a>
    <h2 class="title">Profile</h2>
    <div class="profileLogo">
      <div class="profile">
        <img src="../../images/<?php echo $logo ?>" alt="Company Logo" class="logo">
        <div class="overlay">
          <form method="POST" enctype="multipart/form-data">
            <input type="file" name="logo">
            <i class="edit-icon">&#9998;</i>
        </div>
      </div>
      <h2><?php echo $companyName ?></h2>
    </div>

    <form id='addcam' class="form" method="POST" action="../../Back-End/PHP/accountDetails.php" enctype="multipart/form-data">
        <label for="companyName">Company Name</label>
        <input name="companyName" class="form__input" type="text" value="<?php echo $companyName ?>" required>
        <label for="email">Company Email</label>
        <input name="email" class="form__input" type="email" value="<?php echo $email ?>" required>

      <div class="buttonss">
        <div class="button">
          <button name='submit' id="resetPassword" value='0'>Reset Password</button>
        </div>
        <div class="button">
          <button name='submit' id="editEmail" value='1'>Confirm</button>
        </div>
      </div>
    </form>
  </div>
</body>

</html>