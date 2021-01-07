<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
  <link rel="stylesheet" href="src/style.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
    crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="src/style.css">
  <title>Messages results</title>
  <!-- Page pour afficher les résultats de la requête SQL d'affichage des messages par correspondant -->
</head>
<body>

<?php
/*Test de la connexion à la BDD et die si erreur
  SetAttribute pour récupérer les exceptions PDO à enlever en prod
*/
      try {      
        $MyDB = new PDO("mysql:host=labo-ve.fr;dbname=laboadm_mgoudj","laboadm_mehd_goud","fKh^gzx[8]EP", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
      } catch (Exception $ExceptionRaised) {
        die($ExceptionRaised);
      }
      $MyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      ?>  

      <!-- Formulaire d'envoi de l'email du correspondant -->
      <form class="col-12 pt-3 d-flex flex-column justify-content-center align-content-center align-items-center" action="results.php" method="post">
      <label class="col-12 text-center" for="correspondant">Recherchez les messages d'un correspondant</label>
      <input class="col-3 text-center" type="email" maxlength="50" class="form-control" id="correspondant" placeholder="Entrez l'email du correspondant" name="correspondant">
      <button type="submit" class="btn mt-2" id="submitButton">Let's go baby!</button>
    </form>
  <div class="container-fluid mt-5" id="resultcontainer">

    <?php
    // Envoi des données récupérées via le POST
      $person = $_POST['correspondant'];
      $dataToDisplay = $MyDB->prepare("SELECT * FROM t_msg WHERE id_Email = (SELECT id_Email FROM t_email WHERE Email = :email) ORDER BY date_msg DESC");
      $dataToDisplay->execute([":email" => $person]);
      $dataToParse = $dataToDisplay->fetchall();
      //Parse de tous les champs de l'objet retourné pour les afficher ensuite dans un printf
      foreach ($dataToParse as $row) {
        $sujet = $row['sujet'];
        $date = $row['date_msg'];
        $msg = $row['msg'];
        $format = '<div class="card col-9 px-0 mb-5"><div class="card-header d-flex flex-row justify-content-between"><h6>Sujet: ' . $sujet . '</h6><h6>' . $date . '</h6></div><div class="card-body"><blockquote class="blockquote mb-0"><p>' . $msg . '</p><footer class="blockquote-footer">' . $sujet . '</footer></blockquote></div></div>';
        printf($format);
    }
    ?>
  </div>
</body>
</html>