<?php
    function logout(&$seshrole)//if the user logout
    {
        $seshrole = null;//gives the value of the variable seshrole to null
        generateAudit($_SESSION["username"],'Login','user loged out succesfully ', "audit_log.txt");//generate a audit that the user loged out
        session_destroy();//destroy session
        header("location: index.php");//sends back to login page
        exit;
    }

    function generateAudit($userId, $action, $details, $logfile) //function to generate audit
    {
        $timezone = new DateTimeZone('America/Vancouver');//saves the timezone 
        $dateTime = new DateTime('now', $timezone);//saves the datetime
        $timestamp = $dateTime->format('Y-m-d H:i:s');//date format
        $logEntry = "\n\n[$timestamp], \nusername: $userId, \naction: $action,\ndetails: $details";//how it wwill be written
        file_put_contents($logfile,$logEntry, FILE_APPEND);//put contents into the file
    }

    function hideMessage($message, $color)//function to hide a message after it is apeared for 5 sec
    {
        echo '<div id="success-message" style="color:'.$color.'">   
                        <p>'. htmlspecialchars($message, ENT_QUOTES, "UTF-8") . '</p>
                </div>';
        echo '<script>
                setTimeout(function() {
                    document.getElementById("success-message").style.display = "none";
                }, 2000);
                </script>';
    }

    function Search($seshrole,$conn)
    {
        if (isset($_GET["searchBtn"])) 
        { 
            $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : ''; // get the search input
            $results = []; // initializate in a empty array

            if (!empty($searchTerm) ) 
            {//if it is not empty
                $currentPage = basename($_SERVER["PHP_SELF"]);

                if ($currentPage != "buy.php") //if the page is not buy.php 
                {
                    $stmt = $conn->prepare("SELECT * FROM Post WHERE text LIKE ? AND Bought IS NULL");//show the unbought posts
                } elseif ($currentPage == "buy.php") 
                {
                    $stmt = $conn->prepare("SELECT * FROM Post WHERE text LIKE ? AND Bought IS NOT NULL");//show the bought post
                }

                $searchTermWithWildcard = "%" . $searchTerm . "%";//if there is a word like the searchterm
                $stmt->bind_param("s", $searchTermWithWildcard);//show similar results to searchterm
                $stmt->execute();//execute
                $results = $stmt->get_result();//get the results
                echo'<div class="container-fluid mb-5 border border-6 ">
                        <div class="row mb-5">';
                echo" <h3>Search Results:</h3>";

                if ($results && $results->num_rows > 0) 
                { // if there are any
                    while ($row = $results->fetch_assoc()) 
                    { //iterate over the results
                        $id = $row["id"];
                        $text = $row["text"];
                        $fileName = $row["fileName"];
                        $imageUrl = "upload/" . $fileName;
                        $username = $row["username"];

                        if($seshrole == "admin")//how the post will apear depending on the seshrole and if it is bought
                        {
                            echo "<div class='col-4 mt-3 d-flex text-center mb-5'>";
                                echo "<div class='card ms-5' style='width: 18rem;'>";
                                    echo "<img class='card-img-top imageSize border border-danger rounded'  src='$imageUrl' alt='Image'>";
                                    echo "<div class='card-body ' >";
                                        echo "<p class='card-text'>{$text}</p>";
                                            echo"<form action='somewhere.php' method='post'><button type='submit' name='goSomewhere' class='btn btn-primary '>Go somewhere</button> ";
                                            echo "<input type='hidden' name='id' value='$id'></form>";
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
                        }elseif($seshrole == "editor")
                        {
                            echo "<div class='col-4 mt-3 text-center mb-5'>";
                            echo "<div class='card' style='width: 18rem;'>";
                                echo "<img class='card-img-top imageSize border border-danger rounded' src='$imageUrl' alt='Image'>";
                                echo "<div class='card-body'>";
                                    echo "<p class='card-text'>{$text}</p>";
                                    echo "<form action='somewhere.php' method='post'><button type='submit' name='goSomewhere' class='btn btn-primary'>Go somewhere</button>";
                                    echo "<input type='hidden' name='id' value='$id'></form>";
                                    echo "<form action='Edit.php' method='post' enctype='multipart/form-data' class='>";
                                    echo "<input type='hidden' name='id' value='$id'>";
                                    echo "<button type='submit' name='edit' class='mt-3 btn btn-warning '>Edit</button>";
                                    echo "</form>";
                                    echo "<p class='card-text mt-2'>flyer by - {$username}</p>";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                        }elseif($seshrole == "viewer" && $currentPage != "buy.php")
                        {
                            echo "<div class='col-4 mt-3 d-flex text-center mb-5'>";
                            echo "<div class='card ms-5' style='width: 18rem;'>";
                                echo "<img class='card-img-top imageSize border border-danger rounded'  src='$imageUrl' alt='Image'>";
                                echo "<div class='card-body'>";
                                    echo "<p class='card-text'>{$text}</p>";
                                        echo"<form action='somewhere.php' method='post'><button type='submit' name='goSomewhere' class='btn btn-primary '>Go somewhere</button> ";
                                        echo "<input type='hidden' name='id' value='$id'></form>";
                                    echo "<p class='card-text mt-2'>flyer by - {$username}</p>";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>"; 
                        }elseif($seshrole == "viewer" && $currentPage == "buy.php")
                        {
                                echo "<div class='col-4 mt-5 d-flex text-center '>";
                                echo "<div class='card ms-5' style='width: 18rem;'>";
                                    echo "<img class='card-img-top imageSize border border-danger rounded'  src='$imageUrl' alt='Image'>";
                                    echo "<div class='card-body'>";
                                        echo "<p class='card-text'>{$text}</p>";
                                            echo"<form action='somewhere.php' method='post' class='mt-2'><button type='submit' name='goSomewhere' class='btn btn-primary '>Go somewhere</button> ";
                                            echo "<input type='hidden' name='id' value='$id'></form>";
                                            echo"<form action='buy.php' method='post' class='mt-2'><button type='submit' name='Sell' class='btn btn-warning '>Sell it !!!</button> ";
                                            echo "<input type='hidden' name='id' value='$id'></form>";
                                    echo "</div>";
                                echo "</div>";
                            echo "</div>"; 
                        }else{

                        }
                    }
                } else {
                    echo "<p>No cars found matching your search.</p>";
                } // Fim do if de resultados

            
            } // Fim do if de searchTerm !== ''
        }
    }

    function showText($conn, $id)
    {
        $sql = "SELECT textFile.*, Post.*
        FROM textFile
        INNER JOIN Post ON textFile.postId = Post.id
        where Post.id = $id;
        ";


        $result = $conn ->query($sql);
        if($result ->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            

                $textFile = $row["textFile"];
                $TextUrl = "textFiles/".$textFile;
                $username = $row["username"];
                $text = $row["text"];
                $fileName = $row["fileName"];
                $imageUrl = "upload/".$fileName;
            echo "<div class='col-4 mt-3 text-center float-start mb-2'>";
                echo "<div class='card ms-5' style='width: 25rem;'>";
                    echo "<img class='card-img-top  border border-danger rounded' style='width:25rem;'  src='$imageUrl' alt='Image'>";
                    echo "<div class='card-body'>";
                        echo "<p class='card-text'>{$text}</p>";
                        echo "<p class='card-text mt-2'>flyer by - {$username}</p>";
                    echo "</div>";
                echo "</div>";
            echo "</div>"; 
    
    
                $file = fopen($TextUrl, "r");
                echo"<div class='m-5 '>";
                while(!feof($file))
                {   
                    echo '<div class="">'.fgets($file).'</div>';
                }
                echo"</div>";
                fclose($file);
                
            
        }
    }
?>