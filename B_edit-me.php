<?php
require_once "./tools/function.php";

session_start();
$ssn = $_SESSION['ssn'];


// connect the datase
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=vital_registration_database', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statement = $pdo->prepare('SELECT * FROM person_table WHERE ssn = :ssn');
$statement->bindValue(':ssn', $ssn);
$statement->execute();
$user_info = $statement->fetch(PDO::FETCH_ASSOC);

/*  to update the contents */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_ssn = $_POST['ssn']; /* 1 */
    // $p_ssn = $_SESSION['ssn']; /* 2 */
    $name = $_POST['name']; /* 4 name */
    $f_name = $_POST['f_name']; /* 5 father name */
    $g_name = $_POST['g_name']; /* 6 grand name */
    $sex = $_POST['sex']; /*7  sex  */
    $birth_date = $_POST['birth_date']; /* 7  birht date */
    $birth_place = $_POST['birth_place']; /* 8 birht place */
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $image = $_FILES['image'] ?? null; /* 3  image */
    $imagePath = $user_info['image'];


    if (!is_dir('images')) {
        mkdir('images');
    }

    if ($image && $image['tmp_name']) {

        $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
        mkdir(dirname($imagePath));
        move_uploaded_file($image['tmp_name'], $imagePath);
    }
    
    /* first update ssn */
    $statement = $pdo->prepare("UPDATE person_table SET
                            ssn = :ssn
                            WHERE name = :name and image = :image");
    $statement->bindValue(':ssn', $new_ssn);
    $statement->bindValue(':image', $user_info['image']);
    $statement->bindValue(':name', $user_info['name']);
    $statement->execute();


    // then update the rest
    $statement = $pdo->prepare("UPDATE person_table SET
                                image = :image,
                                name = :name,
                                f_name = :f_name,
                                g_name = :g_name,
                                sex = :sex,
                                birth_date = :birth_date,
                                birth_place = :birth_place,
                                email = :email,
                                pass = :pass  WHERE ssn = :ssn");
    $statement->bindValue(':ssn', $new_ssn);
    // $statement->bindValue(':p_ssn', $p_ssn); /* should be queried */
    $statement->bindValue(':image', $imagePath);
    $statement->bindValue(':name', $name);
    $statement->bindValue(':f_name', $f_name);
    $statement->bindValue(':g_name', $g_name);
    $statement->bindValue(':sex', $sex);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':pass', $pass);
    $statement->bindValue(':birth_date', $birth_date);
    $statement->bindValue(':birth_place', $birth_place);
    $statement->execute();

    
    $_SESSION['ssn'] = $new_ssn;
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['pass'] = $password;

    header('Location: _me.php');

}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<meta http-equiv="refresh" content="2"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Me</title>
    <script defer src="./script/edit_account.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat&family=Open+Sans:wght@700&family=Poppins:wght@500&display=swap"
        rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="./style/style.css">
</head>

<body>

  


    <div>
        <header>
            <div id="company-name">
                <a href="index.php">
                    <h1 id="vital-events">Vital Events Registration</h1>
                </a>
            </div>

            <nav>
                <a href="index.php">Home</a>
                <a href="about.php">About Us</a> <!-- kale -->
                <a href="#to-footer">Contact Us</a>

                <!-- log in or logout -->
                <?php if (isset($_SESSION['ssn'])) {?>
                <a id="nav-login"  href="choice.php"> <?php echo $_SESSION['name'] ?> </a>
                <!-- <a href="./tools/logout.php"> Logout</a> -->
                <?php } else {?>
                <a id="nav-login" href="_login.php"> LogIn</a>
                <?php }?>
            </nav>


        </header>

 

    </div>

    <!-- common nav bar end -->

    <!--  for editiong marriage content -->
    <div class="container">
        <div id="error-div"></div>

        <form id="myForm" method="post" enctype="multipart/form-data">

            <fieldset class="fieldset">
                <legend class="legend">Put yours personal info here</legend>

                <div>
                    <img class="image-display" src="<?php echo $user_info['image'] ?> " alt="yours imag is put here">
                </div>
                <div class="part">
                    <label for="image">Change Image </label> <br>
                    <input class="input" style="border:none" type="file" name="image" id="image">
                </div>

                <!-- content one is here -->
                <div class="content-one">
                    <div class="part">
                        <label for="name">Name </label><br>
                        <input class="input" type="text" name="name" id="name" value="<?php echo $user_info['name'] ?>">
                    </div>

                    <div class="part">
                        <label for="f_name">Father Name </label> <br>
                        <input class="input" type="text" name="f_name" id="f_name"
                            value="<?php echo $user_info['f_name'] ?>">
                    </div>

                    <div class="part">
                        <label for="g_name">Grand father Name </label> <br>
                        <input class="input" type="text" name="g_name" id="g_name"
                            value="<?php echo $user_info['g_name'] ?>">
                    </div>

                </div>


                <!-- content two is here -->
                <div class="content-two">

                    <div class="part">
                        <label for="ssn">SSN </label><br>
                        <input class="input" type="text" name="ssn" id="ssn" value="<?php echo $user_info['ssn'] ?>">
                    </div>

                    <div class="part">
                        <label for="birth_date">Birth Date </label> <br>
                        <input class="input" type="date" name="birth_date" id="birth_date"
                            value="<?php echo $user_info['birth_date'] ?>">
                    </div>

                    <div class="part">
                        <label for="birth_place">Birth Place</label> <br>
                        <input class="input" type="text" name="birth_place" id="birth_place"
                            value="<?php echo $user_info['birth_place'] ?>">
                    </div>

                </div>

                <!-- content three is here -->
                <div class="content-three">
                    <div class="part">
                        <label for="email">Email</label> <br>
                        <input class="input" type="email" name="email" id="email"
                            value="<?php echo $user_info['email'] ?>" required>
                    </div>

                    <div class="part">
                        <label for="pass">Password</label> <br>
                        <input class="input" type="password" name="pass" id="pass"
                            value="<?php echo $user_info['pass'] ?>" required>
                    </div>
                </div>


                <div class="part part-gender">
                    <label>Sex</label required>
                    <div class="one">

                        <div>
                            <input type="radio" name="sex" value="M" id="male"> Male <br>
                        </div>

                        <div>
                            <input type="radio" name="sex" value="F" id="female"> Female <br>
                        </div>
                    </div>

                </div>

                <input class="input" type="submit" id="submit">
            </fieldset>
        </form>

    </div>



    <!--  for footer -->
    <!-- footer for all pages -->
    <div id="to-footer" class="footer">
        <h3>Vital Events Registration Agency</h3>
        <div class="footer-links">
            <a href="#">Site Map</a>
            <a href="#">Copyright</a>
            <a href="#">Privacy</a>
            <a href="#">Disclaimer</a>
            <a href="#">Accessibility</a>
        </div>
        <div class="soc-media-icons">
            <a href="https://www.facebook.com/Addis-Ababa-University-496255483792611/?ref=br_tf">
                <ion-icon name="logo-linkedin"></ion-icon>
            </a>
            <a href="https://www.facebook.com/Addis-Ababa-University-496255483792611/?ref=br_tf">
                <ion-icon name="logo-facebook"></ion-icon>
            </a>
            <a href="https://www.facebook.com/Addis-Ababa-University-496255483792611/?ref=br_tf">
                <ion-icon name="logo-twitter"></ion-icon>
            </a>
        </div>
        <p>Copyright &copy; 2022 Events Registration Agency</p>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>