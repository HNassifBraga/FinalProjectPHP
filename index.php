

<?php
    session_start();    //starts session
    include("logout.php");
    include("database.php");
    include("login.html");//includes login html, where the form is
  
    $seshrole = isset($_SESSION["role"]) ? $_SESSION["role"] : null;//if the session is set, then get the role

    //after login button is pressed{
    if(isset($_POST["login"]))
    {   
        //if no one is conected{
            if($_SESSION["username"] == null)
            {
                $userlogin = filter_input(INPUT_POST,"user", FILTER_SANITIZE_SPECIAL_CHARS);
                $passlogin = filter_input(INPUT_POST,"pass", FILTER_SANITIZE_SPECIAL_CHARS);
        
                
                //see if the database has a user with the user input value{
                    $checking = "SELECT * FROM registered WHERE user = '$userlogin'";
                //}
        
        
                $result = $conn->query( $checking);
                //if is there any row{
                    if($result && $result->num_rows > 0)
                    {   
                        //if neither pass or user is empty{
                            if( !empty($userlogin) && !empty($passlogin))
                            {
                                $userData = mysqli_fetch_assoc($result);
                                $role = $userData["role"];
                                //if the pass is correct{
                                    if (password_verify($passlogin, $userData['pass'])) 
                                    {
                                        $_SESSION["username"] = $userlogin;
                                        $_SESSION["role"] = $role;
                                        $seshrole = $_SESSION["role"];
                                        //send the user to the right page depending on the role{
                                            if($seshrole == "admin")
                                            {
                                                header("location: Adm.php");
                                                
                                            }elseif($seshrole == "editor")
                                            {
                                                header("location: editors.php");
                                                
                                            }elseif($seshrole == "viewer"){
                        
                                                header("location: Viewer.php");
                                            }
                                        //send the user to the right page depending on the role}  
                                        generateAudit($_SESSION["username"],'Login','user loged in succesfully ', "audit_log.txt");//audit
                                    }
                                //if the pass is correct}
                                else
                                {
                                    //if the pass is incorrect
                                    echo hideMessage("wrong pass", "red");
                                }
                            }
                        //if neither pass or user is empty}
                    }
                //if is there any row}
                else{
                    //if there is no row with that user
                    echo hideMessage("wrong user", "red");
                }
            }
        //if no one is conected}
        else{
            //if you already are conected
            echo hideMessage("your are already loged in","red");
        }
    }
    //after login button is pressed}

    include("sessionCheck.php");
   //initiates in the login page

    //if the user doesn't have an account, he clicks in register button that will take him to the register page{
        if(isset($_POST["register"]))
        {
        header("location: register.html");
        exit;
        }
    //if the user doesn't have an account, he clicks in register button that will take him to the register page}
   
    //if registerUser button is clicked{
        if(isset($_POST["registerUser"]))
        {   
            //save the information from user input in variables{
                if(isset($_POST["role"]))
                {
                    $role = $_POST["role"];
                }
                $user = filter_input(INPUT_POST,"user", FILTER_SANITIZE_SPECIAL_CHARS);
                $name = filter_input(INPUT_POST,"name", FILTER_SANITIZE_SPECIAL_CHARS);
                $email = filter_input(INPUT_POST,"email", FILTER_SANITIZE_SPECIAL_CHARS);
                $age =  filter_input(INPUT_POST,"age", FILTER_SANITIZE_SPECIAL_CHARS);
                $pass = filter_input(INPUT_POST,"pass", FILTER_SANITIZE_SPECIAL_CHARS);
            //save the information from user input in variables}

            //if any variable is empty{
                if(empty($user) || empty($pass)|| empty($name) || empty($email) || empty($age) || empty($role))
                {   
                    echo hideMessage("empty user/pass", "red");
                }
            //if any variable is empty}
            else
            {   
                //if no variable is empty{
                    $checking = "SELECT * FROM registered WHERE user = '$user'";
                    $result = $conn->query($checking);
                    //if the user already exists{
                        if($result && $result->num_rows > 0)
                        {
                            echo hideMessage("user already exist", "red");
                        }
                    //if the user already exists}
                    else{
                        //if the user doesn't exists{
                            $hash = password_hash($pass, PASSWORD_DEFAULT);

                            //put some safety there!!  inserir nos campos do banco de datos os valores dos inputs
                            $sql = $conn->prepare("INSERT INTO registered (user, pass, name, email, age,role) 
                            VALUES (?,?,?,?,?,?)");
                            $sql->bind_param("ssssis",$user, $hash, $name, $email, $age, $role);
                            //salva dentro do banco de dados
                            $sql->execute();
                            echo hideMessage("you are registered", "green");

                            generateAudit($_SESSION["username"],'Register user','user registered', "audit_reg.txt");
                        //if the user doesn't exists}
                    }
                //if no variable is empty}
            }
        }
    //if registerUser button is clicked{

    //if logout button is pressed{
        if(isset($_POST["logout"]))
        {
        logout($seshrole);
        }
    //if logout button is pressed}

    //send the user to the right page depending on the role{
        if($seshrole == "admin")
        {
            header("location: Adm.php");
            
        }elseif($seshrole == "editor")
        {
            header("location: editors.php");
            
        }elseif($seshrole == "viewer"){

            header("location: Viewer.php");
        }
    //send the user to the right page depending on the role}
        mysqli_close($conn); 
?>
