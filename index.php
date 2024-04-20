<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylesheet.css">
    <title>Log In</title>
</head>

<body>
    <div class="main-content" id="center">
        <h1>Please log in bro.</h1>
        <form action="" method="post">
            <li>
                <label for="username">Username</label>
                <input type="text" id="username" name="username">
            </li>
            <li>
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </li>
            <li>
                <input name="submit" type="submit" value="submit">Log In</input>
            </li>
        </form>
    </div>

    <?php
        session_start();

        if (array_key_exists('submit', $_POST)){

            try{
                $dsn = "mysql:host=courses;dbname=z1828609";
                $username = "z1828609";
                $password = "1999Mar31";
                $pdo = new PDO($dsn, $username, $password);
            }
            catch(PDOexception $e) {
                echo "Connection to database failed: " . $e->getMessage();
            }

            $prepared = $pdo->prepare('SELECT accounttype
                                        FROM Associate
                                        WHERE Associate.username = ?
                                        AND Associate.password = ?');   

            $success = $prepared->execute(array($_POST["username"], $_POST["password"]));

            $rows = $prepared->fetch(PDO::FETCH_ASSOC);

            if(!empty($rows))
            {
                $_SESSION["UserSession"] = $_POST["username"];

                //Associate
                if($rows['accounttype'] == '0'){
                    header('Location: https://students.cs.niu.edu/~z1967887/CSCI-467-Quote-System/associate.html');
                }

                //Headquarters
                else if ($rows['accounttype'] == '1'){
                    header("location: https://students.cs.niu.edu/~z1967887/CSCI-467-Quote-System/quote_editor.html");
                }

                //Administration
                else if ($rows['accounttype'] == '2'){
                    header("location: https://students.cs.niu.edu/~z1967887/CSCI-467-Quote-System/admin.html");
                }
            }
            else
            {
                echo "Invalid Username/Password";
            }
        }
    ?>

    <div class="main-content" id="center">
        <p>Temporary test buttons while log in functionality is being worked out:</p>
        <a href="admin.html">
            <button>Admin Interface</button>
        </a>
        <a href="associate.html">
            <button>Associate Interface</button>
        </a>
        <a href="quote_editor.html">
            <button>HQ Interface</button>
        </a>
        <a href="confirmation.html">
            <button>Customer confirmation</button>
        </a>
    </div>
</body>

</html>