<?php
    session_start();
    ob_start(); 
    include("database.php");
    include("logout.php");
    $seshrole = $_SESSION["role"];
    include("ErrorHandler.php");
    include("sessionCheck.php");

    $errorHandler = new SimpleErrorHandler();
    
    set_error_handler([$errorHandler, 'handleError']);
    set_exception_handler([$errorHandler, 'handleException']);

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
        // dinamic link depending on the user role
        if ($seshrole == 'admin') {
          echo '<a class="navbar-brand" href="adm.php">Admin Page</a>';
        } elseif ($seshrole == 'editor') {
          echo '<a class="navbar-brand" href="editors.php">Editor Page</a>';
        } elseif ($seshrole == 'viewer') {
          echo '<a class="navbar-brand" href="viewer.php">Viewer Page</a>';
        } else {
          echo '<a class="navbar-brand" href="#">Home</a>'; 
        }
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



if($seshrole == "admin")//if user role = admin
{
    if (isset($_POST["id"])) 
    {
      $id = intval($_POST["id"]); 
        $name = $_POST["name"];
      // echo "<form action='somewhere.php' method='post'>
      //         <button type='submit' name='addFile' class='btn btn-primary'>Add File</button>
      //       </form>";

      //   if(isset($_POST[]))
        echo "<h4>Add text file to the name: $name</h4>";
        $sql = "SELECT * FROM Post WHERE id = $id";//select the post with id = id
        $result = $conn->query( $sql);
    
        if ($result &&$result->num_rows > 0) 
        {
            $item = $result->fetch_assoc();
            $text = $item["text"];
            $fileName = $item["fileName"];
            $imageUrl = "upload/" . $fileName;

    
            echo "
            <form action='somewhere.php' method='post' enctype='multipart/form-data'>
                <input type='hidden' name='id' value='$id'>
                <label for='textFile'>Text File:</label>
                <input type='file' name='textFile' id='textFile'>
                <input type='submit' name='submit' value='submit'>
            </form>";
        } else {
            echo "Nenhum item encontrado com o ID: $id";
        }
    } 
    
    if (isset($_POST["submit"])) //if the submit button on the form of the textFile is clicked
    {
        $id = intval($_POST["id"]); 
        if ( !empty($_FILES["textFile"]["name"])) //if the textFile is not empty
        {
            $fileName = $_FILES["textFile"]["name"];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $allowed = array("txt");
            $tempName = $_FILES["textFile"]["tmp_name"];
            $username = $_SESSION["username"];
            $path = "textFiles/" . $fileName;
    
            if (!is_dir("textFiles")) //if it is not in the directorie
            {
                mkdir("textFiles", 0755, true);//saves it there
            }
    
            if (in_array($ext, $allowed)) 
            {
                if (move_uploaded_file($tempName, $path))//if it is a txt file
                {
                    $sql = "INSERT INTO textFile (textFile, username,postId) VALUES ('$fileName', '$username','$id')";//saves the file in the database
    
                    if ($conn->query( $sql)) 
                    {
                       echo hideMessage("File uploaded successfully.", "black");//hideMessage written in logout.php
                       generateAudit($_SESSION["username"],'File upload','user Puploaded file succesfully', "audit_FileUpload.txt");//generateAudit written in logout.php
                    } else {
                      $errno = mysqli_errno($conn);
                      $errstr = mysqli_error($conn);
                      $errfile = __FILE__;
                      $errline = __LINE__;
                      $errorHandler->handleError($errno, $errstr, $errfile, $errline);//if there's an error
                    }
                } else {
                    echo "File upload failed.";
                }
            } else {
                echo "File type not allowed.";
            }
        } else {
            echo "Text or file is empty.";
        }
    }

      showText($conn,$id);//show the text file
}else//if seshrole is not admin
{
  $id = intval($_POST["id"]); //if the button of adding a new text file is not pressed 
    showText($conn, $id);//show the text file
}

$conn->close();
ob_end_flush(); // Envia o buffer de saída ao navegador
?>
