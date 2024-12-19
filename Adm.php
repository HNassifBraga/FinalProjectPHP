<!-- Develop functionalities to create, read, update, and delete (CRUD) content items relevant to the chosen industry
(e.g., articles, products, services).//admin vou fazer adicionar uma foto, artigos e textos e pode editar,
 viewer so vai ver, editor so pode editar -->
 <?php
session_start();
include("database.php");
include("logout.php");
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
        <li>
            <form action="Adm.php" method="get" class="d-flex">
                <input class="form-control me-2" type="search" name="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" name="searchBtn" type="submit">Search</button>
            </form>
        </li>
    <form action="Adm.php" method ="post" >   
    <button class="btn btn-outline-success ms-5  " name="Add" type="submit">Add flyer</button>
     
    </form>
      <form class="d-flex"  action="index.php" method="post">
        <button class="btn btn-outline-success ms-5  " name="logout" type="submit">Logout</button>
      </form>
      
    </div> 
  </div>
</nav>  

   

        <?php

        $seshrole =  $_SESSION["role"] ;//geting the value of the role\

        if (isset($_GET["searchBtn"])) //if the user search something 
        {
            Search($seshrole, $conn);//function search that is located in the file logout.php
        }
        ?>

<?php
echo"<div class='ms-5'>";
    if(isset($_POST["Add"]))//if the button add is clicked it includes adm.html
    {   
        include("Adm.html");
       
    }
echo"</div>";
   
    
    if(isset($_POST["submit"]))//if the button on the Adm.html is clicked
    {
       if (!empty($_POST["text"]) && !empty($_FILES["image"]["name"])&&!empty($_POST["price"])&&!empty($_POST["date"])&&!empty($_POST["kilo"]))//if neither of the inputs is empty
       {
           $sql = $conn->prepare("INSERT INTO Post(text, fileName, username, Price, year, kilometers) 
           VALUES (?, ?, ?, ?, ?, ?)");
           $sql->bind_param("sssisi", $texto, $fileName, $username, $price, $year, $kilometer);
           

            $price = filter_input(INPUT_POST,"price", FILTER_SANITIZE_NUMBER_INT);
            $year = filter_input(INPUT_POST,"date", FILTER_SANITIZE_NUMBER_INT);
            $kilometer = filter_input(INPUT_POST,"kilo", FILTER_SANITIZE_NUMBER_INT);
            $texto = filter_input(INPUT_POST,"text", FILTER_SANITIZE_SPECIAL_CHARS);
            $fileName = $_FILES["image"]["name"];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $allowed = array("jpg","jpeg","png","gif") ;
            $tempName = $_FILES["image"]["tmp_name"];
            $username =  $_SESSION["username"];
            $path = "upload/".$fileName;


            if(in_array($ext, $allowed))//if the media type is permited
            {
                if(move_uploaded_file($tempName, $path))//if the file is uploaded into the folder 
                {
                    //saves the informations in the database
                    
                    if($sql->execute() )//if the sql works in the database
                    {
                        generateAudit($_SESSION["username"],'Post it','user Posted flyer succesfully ', "audit_Post.txt");//function generateAudit written in logout.php
                        echo hideMessage("image inserted succesfully","black");//function hideMessage writen in logout.php

                    }else
                    {
                        $errno = 1; 
                        $errstr = "SQL execution failed"; 
                        $errfile = __FILE__; 
                        $errline = __LINE__;


                        $errorHandler->handleError($errno, $errstr, $errfile, $errline);
                    }
                }else{
                    echo "file not allowed";
                }
            }
       }else{
            echo "empty text or image";
       }
    }


    
?>

   <div class="container">
        <div class="row">
                <?php
                 if (isset($_POST["delete"])) //if the button delete is clicked
                 {
                    if (isset($_POST["id"])) //if the id is set
                    {
                        $id = intval($_POST["id"]); 
                

                        $queryImage = "SELECT fileName FROM Post WHERE id = $id";//select the Post with the id

                        $resultImage =$conn->query($queryImage);//select the image from the database
                        
                        if ($resultImage && $resultImage->num_rows > 0) //if there are any result
                        {
                            $rowImage = $resultImage->fetch_assoc();
                            $imagePath = "upload/" . $rowImage['fileName'];
                            

                            if (file_exists($imagePath)) //if the image exists
                            {
                                unlink($imagePath);//deletes the image from the folder
                            }
                        }
                

                        $queryText = "SELECT textFile FROM textFile WHERE postId = $id";//select the text file related to the post with the id

                        $resultText = $conn->query($queryText);//savees the value
                
                        if ($resultText && $resultText->num_rows > 0) 
                        {
                            $rowText = $resultText->fetch_assoc();
                            $textFilePath = "textFiles/" . $rowText['textFile'];
                            

                            if (file_exists($textFilePath)) //if the text file exists
                            {
                                unlink($textFilePath);//delete it from the folder
                            }
                        }

                        $sqlPost = "DELETE FROM Post WHERE id = $id";//delete the post with the id
                        $sqlTextFile = "DELETE FROM textFile WHERE postId = $id";//delete the textfile related to the post
                
                        if ($conn->query($sqlPost) && $conn->query($sqlTextFile)) //if the querry works
                        {   
                            generateAudit($_SESSION["username"], 'Delete it', 'User deleted flyer successfully', "audit_Post.txt");//function generateAudit that is written in logout

                        } else {
                            $errno = mysqli_errno($conn);
                            $errstr = mysqli_error($conn); 
                            $errfile = __FILE__;
                            $errline = __LINE__; 
                            $errorHandler->handleError($errno, $errstr, $errfile, $errline);
                        }
                    } else {
                        echo "No ID received for deletion.";//if no id is passed
                    }
                }

                    $sql = "SELECT * FROM Post WHERE Bought is null";//show all the posts that are not bought, if it is bought Bought= yes
                    $result = $conn->query($sql);
                    if($result ->num_rows > 0)//if there's any result
                    {
                        while($row = $result->fetch_assoc()) 
                        {
                            //get the values of the not bought post
                            $id = $row["id"];
                            $text = $row["text"];
                            $fileName = $row["fileName"];
                            $imageUrl = "upload/".$fileName;
                            $username = $row["username"];
                            $price = $row["Price"];
                            $year = $row["year"];
                            $kilometer = $row["kilometers"];

                                //prints it in a card
                                echo "<div class='col-4 mt-5 d-flex text-center '>";
                                    echo "<div class='card ms-5 border' style='width: 18rem;'>";
                                        echo "<img class='card-img-top imageSize border border-danger rounded'  src='$imageUrl' alt='Image'>";
                                        echo "<div class='card-body'>";
                                            echo "<p class='card-text'>{$text}<br> <br>price: \${$price} <br> Year: {$year} <br> Kilometers: {$kilometer}</p>";
                                                echo"<form action='somewhere.php' method='post'><button type='submit' name='goSomewhere' class='btn btn-primary '>Add information</button> ";
                                                echo "<input type='hidden' name='id' value='$id'>";
                                                echo "<input type='hidden' name='name' value='$text'></form>";
                                            echo"<form action='Edit.php' method='post' enctype='multipart/form-data' class=''>";
                                                echo "<input type='hidden' name='id' value='$id'>";
                                                echo" <button type='submit' name='edit' class='mt-2 btn btn-warning '>Edit</button>";
                                            echo"</form>";
                                            echo"<form action='Adm.php' method='post' enctype='multipart/form-data' class=' '>";
                                                echo "<input type='hidden' name='id' value='$id'>";
                                                echo"<button type='submit' name='delete' class='btn btn-danger mt-2'>delete</button></form>";
                                            echo "<p class='card-text mt-2'>flyer by - {$username}</p>";
                                        echo "</div>";
                                    echo "</div>";
                                echo "</div>"; 

                        }
                    }

                    
                    if(isset($_POST["edit"]))//if edit button is clicked
                    {
                        header("location: Edit.php");//sends user to edit page
                        exit;
                    }
                    if(isset($_POST["logout"]))//if logout button is clicked
                    {
                       logout($seshrole);//logout function created in logout.php
                    }
                ?>
                
        </div>
   </div>
</body>
</html>
