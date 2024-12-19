<?php
    session_start();//starts session
    ob_start(); //turn output buffering on
    
    include("database.php");//include database for the databasee
    include("logout.php");//include logout.php for the functions
    include("ErrorHandler.php");
    include("sessionCheck.php");


  $errorHandler = new SimpleErrorHandler();


  set_error_handler([$errorHandler, 'handleError']);
  set_exception_handler([$errorHandler, 'handleException']);

    $seshrole = $_SESSION["role"];//saves the session role
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
  <a class="navbar-brand" href="viewer.php">Viewer Page</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#"></a>
        </li>
        <form action="buy.php" method="get" class="d-flex">
                <input class="form-control me-2" type="search" name="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" name="searchBtn" type="submit">Search</button>
          </form>
      <form class="d-flex"  action="index.php" method="post">
        <button class="btn btn-outline-success ms-5  " name="logout" type="submit">Logout</button>
      </form>
    </div> 
  </div>
</nav>  
<?php

if (isset($_GET["searchBtn"])) //if the user search
{
  Search($seshrole, $conn);// function search writen in logout.php
}

if (isset($_POST["id"])) //if the id isset
{  
    $id = intval($_POST["id"]); //saves the value of id


    if(isset($_POST["buy"]))//if buy button is pressed
    {
        $sql = "SELECT * FROM Post WHERE id = $id";//select the post with the id
        $result = $conn->query($sql);
      
        if ($result &&$result->num_rows > 0) //if there are any results
        {

            $sqli =$conn->prepare( "INSERT INTO Bought (text, fileName, username, price, postId)
            VALUES (?,?,?,?,?)");//saves teh values into a new table
            $sqli->bind_param("sssii", $text, $fileName, $username, $price,$postid);


            $item = $result->fetch_assoc();//creating array item with the values from the table

            $text = $item["text"];
            $fileName = $item["fileName"];
            $imageUrl = "upload/" . $fileName;
            $price = $item["Price"];
            $updateSql = "UPDATE Post SET Bought = 'yes' WHERE id = $id";
            $username = $_SESSION["username"];
            $postid = $id;

            if ($conn->query($updateSql)) //alters the value of bought in the table to yes
            {

            } else {
              $errno = mysqli_errno($conn);
              $errstr = mysqli_error($conn);
              $errfile = __FILE__;
              $errline = __LINE__;
          
              $errorHandler->handleError($errno, $errstr, $errfile, $errline);
            }
            if($sqli->execute())//if it is succesfull
            {
                generateAudit($_SESSION["username"],'Bought it','user Bought car succesfully ', "audit_Buy.txt");//generateAudit function written in logout.php
                hideMessage(" {$text} Bought succesfully ","black");//hideMessage function written in logout.php
            }else
            {
              $errno = mysqli_errno($conn); 
              $errstr = mysqli_error($conn);
              $errfile = __FILE__; 
              $errline = __LINE__;
          
              $errorHandler->handleError($errno, $errstr, $errfile, $errline);
            }
            }else{
                 echo "empty text or image";
            }
    }
  } 

  if (isset($_POST["Sell"])) //if sell button is pressed
  {
    $id = intval($_POST["id"]); 



        $updateSql = "UPDATE Post SET Bought = NULL WHERE id = $id";//updates table post the value bought to null of the item with id = id
        $sqldel = "DELETE FROM Bought WHERE postId = $id";//deletes from table bought the item with id = id

        if ($conn->query($updateSql)) //if well done the update
        {
            if ($conn->query( $sqldel)) //if well done the delete
            {
                hideMessage("Item selled successfully!","black");//hideMessage function written in logout.php
                generateAudit($_SESSION["username"], 'Sell it', 'User sold car successfully', "audit_Sell.txt");//generateAudit function written in logout.php
            } else {
              $errno = mysqli_errno($conn);
              $errstr = mysqli_error($conn);
              $errfile = __FILE__;
              $errline = __LINE__;
              $errorHandler->handleError($errno, $errstr, $errfile, $errline);
            }
        } else {
          $errno = mysqli_errno($conn);
          $errstr = mysqli_error($conn);
          $errfile = __FILE__;
          $errline = __LINE__;
          $errorHandler->handleError($errno, $errstr, $errfile, $errline);
        }

  }

  $username = mysqli_real_escape_string($conn, $_SESSION["username"]);//sanitize the value from $_SESSION["user"] and save its value
  $sql = "SELECT * FROM Bought WHERE  username = '$username' ";//show the bought veichle from the user
  $result =$conn-> query($sql);

  if($result && $result ->num_rows > 0)//if results greater than 0 
  {
    echo"<div class='container'>
            <div class='row'>";
      while($row = mysqli_fetch_array($result))
      {
          $id = $row["postId"];
          $text = $row["text"];
          $fileName = $row["fileName"];
          $imageUrl = "upload/".$fileName;
          $username = $row["username"];

          echo "<div class='col-4 mt-5 d-flex text-center '>";
              echo "<div class='card ms-5' style='width: 18rem;'>";
                  echo "<img class='card-img-top imageSize'  src='$imageUrl' alt='Image'>";
                  echo "<div class='card-body'>";
                      echo "<p class='card-text'>{$text}</p>";
                          echo"<form action='somewhere.php' method='post' class='mt-2'><button type='submit' name='goSomewhere' class='btn btn-primary '>Go somewhere</button> ";
                          echo "<input type='hidden' name='id' value='$id'></form>";
                          echo"<form action='buy.php' method='post' class='mt-2'><button type='submit' name='Sell' class='btn btn-warning '>Sell it !!!</button> ";
                          echo "<input type='hidden' name='id' value='$id'></form>";
                  echo "</div>";
              echo "</div>";
          echo "</div>"; 

      }
      echo"</div>
      </div>";
  }
                    
             
    $conn->close();//close conection 
    ob_end_flush();//turn output buffering off

?>