<!-- Develop functionalities to create, read, update, and delete (CRUD) content items relevant to the chosen industry
(e.g., articles, products, services).//admin vou fazer adicionar uma foto, artigos e textos e pode editar,
 viewer so vai ver, editor so pode editar -->
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
          <a class="nav-link active text-success fs-5" aria-current="page" href="buy.php">My veichles</a>
        </li> 
        <form action="Viewer.php" method="get" class="d-flex">
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
  session_start();
  $seshrole = $_SESSION['role'];
  include("database.php");
  include("logout.php");
  include("sessionCheck.php");

  if (isset($_GET["searchBtn"]))//if search button is clicked
  {
    Search($seshrole, $conn);//seacrh function written in logout.php
  }
    
   
    
?>

   <div class="container">
        <div class="row">
                <?php
                    include("database.php");
                    $sql = "SELECT * FROM Post WHERE Bought is null";//show all posts that are not bought
                    $result = $conn ->query($sql);
                    if($result ->num_rows > 0)
                    {
                        while($row = $result->fetch_assoc())
                        {
                            $id = $row["id"];
                            $text = $row["text"];
                            $fileName = $row["fileName"];
                            $imageUrl = "upload/".$fileName;
                            $username = $row["username"];

                                echo "<div class='col-4 mt-5 d-flex text-center '>";
                                    echo "<div class='card ms-5' style='width: 18rem;'>";
                                        echo "<img class='card-img-top imageSize border border-danger rounded'   src='$imageUrl' alt='Image'>";
                                        echo "<div class='card-body'>";
                                            echo "<p class='card-text'>{$text}</p>";
                                                echo"<form action='somewhere.php' method='post' class='mt-2'><button type='submit' name='goSomewhere' class='btn btn-primary '>Go somewhere</button> ";
                                                echo "<input type='hidden' name='id' value='$id'></form>";
                                                echo"<form action='buy.php' method='post' class='mt-3 mb-4'><button type='submit' name='buy' class='btn btn-danger '>Buy it!!!</button> ";
                                                echo "<input type='hidden' name='id' value='$id'></form>";
                                            echo "<p class='card-text mt-2'>flyer by - {$username}</p>";
                                        echo "</div>";
                                    echo "</div>";
                                echo "</div>"; 

                        }
                    }

                    
                    if(isset($_POST["edit"]))
                    {
                        header("Edit.php");
                        exit;
                    }

                    
                ?>
        </div>
   </div>
</body>
</html>
