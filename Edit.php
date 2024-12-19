<?php
  session_start();//initiantes session
  $seshrole = $_SESSION["role"];//get the value from session role
  ob_start(); //turn output buffering on

  include("database.php");
  include("logout.php");
  include("sessionCheck.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
  <a class="navbar-brand" href="#"><?php 
        // shows the link depending of the sehsrole{
          if ($seshrole == 'admin') {
            echo '<a class="navbar-brand" href="adm.php">Admin Page</a>';
          } elseif ($seshrole == 'editor') {
            echo '<a class="navbar-brand" href="editors.php">Editor Page</a>';
          } elseif ($seshrole == 'viewer') {
            echo '<a class="navbar-brand" href="viewer.php">Viewer Page</a>';
          } else {
            echo '<a class="navbar-brand" href="#">Home</a>';
          }
        //shows the link depending of the sehsrole}
      ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#"></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
      <form class="d-flex"  action="index.php" method="post">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
        <button class="btn btn-outline-success ms-5  " name="logout" type="submit">Logout</button>
      </form>
    </div> 
  </div>
</nav>  
<?php




if (isset($_POST["id"])) //if the id is set
{
    $id = intval($_POST["id"]); //saves the value of id
    echo "<h1>Edit Item ID: $id</h1>";

    $sql = "SELECT * FROM Post WHERE id = $id";//selects the post with the id = id
    $result = $conn->query( $sql);

    if ($result && $result->num_rows > 0) //if any result
    {
        $item = mysqli_fetch_assoc($result);
        $text = $item["text"];
        $fileName = $item["fileName"];
        $imageUrl = "upload/" . $fileName;
        $price = $item["Price"];
        $year = $item["year"];
        $kilometer = $item["kilometers"];

        //form to edit the post
        echo "
        <form action='Edit.php' method='post' enctype='multipart/form-data'>
            <label for='text'>car model:</label><br>
            <textarea name='text' id='text'>$text</textarea><br>
            <label for='year'>date of fabrication: </label><br>
            <input type='date' name='year' id='year' value='$year'><br>
            <label for='kilo'>Kilometers: </label><br>
            <input type='number' name='kilo' id='kilo' value='$kilometer'><br>
            <label for='price'>Price:</label><br>   
            <input type='number' name='price' id='price'>
            <input  class='form-control mt-4' type='file' name='image'>
            <img src='$imageUrl' alt='Current Image' style='max-width: 100px;'>
            <input type='hidden' name='id' value='$id'>
            <input type='submit' name='submit' value='Save Changes'>
        </form>";
    } else {
        echo "Nenhum item encontrado com o ID: $id";
    }



    if (isset($_POST["submit"])) //if button of the form to edit the post is clicked
    {

      $texto = filter_input(INPUT_POST, "text", FILTER_SANITIZE_SPECIAL_CHARS);
      $price = filter_input(INPUT_POST, "price", FILTER_SANITIZE_SPECIAL_CHARS);
      $fileName = $_FILES["image"]["name"];
      $kilometer = filter_input(INPUT_POST,"kilo", FILTER_SANITIZE_SPECIAL_CHARS);
      $year = filter_input(INPUT_POST,"year", FILTER_SANITIZE_SPECIAL_CHARS);


      $updates = [];  //creates an array to know what was changes
      if(!empty($year))//if the year was changed adds to updates
      {
        $updates[] = "year = '$year'";
      }
      if(!empty($kilometer))//if the text was changed adds to updates
      {
        $updates[] = "kilometers = '$kilometer'";
      }
      if(!empty($texto))//if the text was changed adds to updates
      {
        $updates[] = "text = '$texto'";
      }
      if (!empty($price)) //if the price was changed adds to updates
      {
        $updates[] = "price = '$price'";
      }
      if (!empty($_FILES["image"]["name"])) //if the image was changed adds to updates
      {
        $fileName = $_FILES["image"]["name"];
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowed = ["jpg", "jpeg", "png", "gif"];
        $tempName = $_FILES["image"]["tmp_name"];
        $path = "upload/" . $fileName;


        if (in_array($ext, $allowed) && move_uploaded_file($tempName, $path)) //if the image type is allowed
        {
            $updates[] = "fileName = '$fileName'";
        } else {
            echo "image type not allowed.";
        }
      }

      if (!empty($updates)) //if there's any update
      {
        $updateFields = implode(", ", $updates); // joins the updates in a string
        $sql = "UPDATE Post SET $updateFields WHERE id = $id";//updates the post with the id = id


        if ($conn->query($sql)) //if it works
        {
            echo "successful update.";
        } else {
          $errno = mysqli_errno($conn);
          $errstr = mysqli_error($conn);
          $errfile = __FILE__;
          $errline = __LINE__;
          $errorHandler->handleError($errno, $errstr, $errfile, $errline);
        }
      } else {
          echo "no change was made.";//if nothing is changed
      }

    }

} else {
    echo "ID of item not found.";//if the id is not found
}


$conn->close();//close database conection
ob_end_flush(); // Envia o buffer de saída ao navegador
?>
