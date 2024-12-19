
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
    <a class="navbar-brand" href="#">Navbar</a>
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
        <form action="editors.php" method="get" class="d-flex">
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
  session_start();//starts session 
  $seshrole = $_SESSION['role'];//saves the value of session role
  include("database.php");
  include("logout.php");
  include("sessionCheck.php");
  if (isset($_GET["searchBtn"])) //if searchbutton is clicked
  {
    Search($seshrole, $conn);//search function written in logout.php
  }
?>

<?php

?>
            <div class="container">
                <div class="row">
                    <?php
                    $sql = "SELECT * FROM Post WHERE Bought is null";//select post that werent bought
                    $result =$conn->query( $sql);
                    if ($result->num_rows > 0) //if there are any
                    {
                        while ($row = mysqli_fetch_array($result)) 
                        {
                            $id = $row["id"];
                            $text = $row["text"];
                            $fileName = $row["fileName"];
                            $imageUrl = "upload/" . $fileName;
                            $username = $row["username"];
                            $price = $row["Price"];
                            $year = $row["year"];
                            $kilometer = $row["kilometers"];
                            
                            //prints the card
                            echo "<div class='col-4 mt-5 text-center'>";
                                echo "<div class='card' style='width: 18rem;'>";
                                    echo "<img class='card-img-top imageSize border border-danger rounded' src='$imageUrl' alt='Image'>";
                                    echo "<div class='card-body'>";
                                        echo "<p class='card-text'>{$text}<br>price: \${$price}<br>year: {$year}<br>km: {$kilometer}</p>";
                                        echo "<form action='somewhere.php' method='post'><button type='submit' name='goSomewhere' class='btn btn-primary'>See information</button>";
                                        echo "<input type='hidden' name='id' value='$id'>";
                                        echo "<input type='hidden' name='name' value='$text'></form>";
                                        echo "<form action='Edit.php' method='post' enctype='multipart/form-data' >";
                                        echo "<input type='hidden' name='id' value='$id'>";
                                        echo "<button type='submit' name='edit' class='mt-3 btn btn-warning '>Edit</button>";
                                        echo "</form>";
                                        echo "<p class='card-text mt-2'>flyer by - {$username}</p>";
                                    echo "</div>";
                                echo "</div>";
                            echo "</div>"; 
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
   </div>
</body>
</html>
