<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Portal</title>
</head>

<body data-bs-theme="dark">
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">site navigation (temporary)</button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="admin.html">admin</a></li>
            <li><a class="dropdown-item" href="quote_editor.html">hq</a></li>
            <li><a class="dropdown-item" href="associate.html">associate</a></li>
        </ul>
    </div>

    <div class="container position-absolute top-50 start-50 translate-middle" style="width: 300px;">
        <h1>Please log in</h1>

        <!-- Login Form -->
        <form method="post" class="align-items-center">
            <!-- Username Input -->
            <div class="col-auto mb-3">
                <label for="username" class="visually-hidden">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username">
            </div>

            <!-- Password Input -->
            <div class="col-auto mb-3">
                <label for="password" class="visually-hidden">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>

            <!-- Submit Button -->
            <div class="col-auto mb-3">
                <input class="col-auto btn btn-primary" type="submit" name="submit" value="Log In">
            </div>
        </form>
    </div>

    <!-- Login functionality with PHP, compares user input username & password against users in database -->
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

</body>

</html>