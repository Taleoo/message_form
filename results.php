<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
  <link rel="stylesheet" href="src/style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
    crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
    crossorigin="anonymous"></script>
    <script src="src/script.js"></script>  
  <title>Messages results</title>
  <!-- Page pour afficher les résultats de la requête SQL d'affichage des messages par correspondant -->
</head>
<body>

<?php
/*Test de la connexion à la BDD et die si erreur
  SetAttribute pour récupérer les exceptions PDO à enlever en prod
*/    
  try {      
    $MyDB = new PDO("mysql:host=localhost;dbname=laboadm_mgoudj","laboadm_mehd_goud","fKh^gzx[8]EP", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
  } catch (Exception $ExceptionRaised) {
    die($ExceptionRaised);
  }
  $MyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  ?>  

<!-- Formulaire d'envoi de l'email du correspondant -->
  <form class="col-12 pt-3 d-flex flex-column justify-content-center align-content-center align-items-center" action="results.php" method="post">
    <label class="col-12 text-center" for="correspondant">Recherchez les messages d'un correspondant</label>
    <input class="col-6 col-md-3 text-center" type="email" maxlength="50" class="form-control" id="correspondant" placeholder="Entrez l'email" name="correspondant">
    <button type="submit" class="btn mt-2" id="submitButton">Rechercher</button>
  </form>
  <div class="container-fluid mt-5" id="resultcontainer">


<?php
(array) $dataToParse = [];

// Declaration de la fonction de récupération des données
function getData($DB, $request, $requestVar = NULL){
    try {
      $request->execute($requestVar);
      global $dataToParse;
      $dataToParse = [];
      array_push($dataToParse, 0);

//Sanitize every data column gotten in the fetch then return it in $dataToParse to be read    
      while ($dataline = $request->fetch(PDO::FETCH_ASSOC)){
        filter_var($dataline["sujet"], FILTER_SANITIZE_STRING);
        filter_var($dataline["msg"], FILTER_SANITIZE_STRING);
        filter_var($dataline["id_msg"], FILTER_SANITIZE_NUMBER_INT);
        filter_var($dataline["etat"], FILTER_SANITIZE_STRING);
        filter_var($dataline["prenom"], FILTER_SANITIZE_STRING);
        filter_var($dataline["nom"], FILTER_SANITIZE_STRING);
        array_push($dataToParse, $dataline);
      }

      //Parse de tous les champs de l'objet retourné pour les afficher ensuite dans un printf
      (int) $i = 1;
      foreach ($dataToParse as $data) {
        if (0!==$data){   ?>
                    <div class="card col-9 px-0 mb-5">
                    <div class="card-header d-flex flex-row justify-content-between">
                      <h6>Sujet: <?php printf("%s", $data['sujet']);?></h6>
                      <h6><?php printf("%s", $data['date_msg']);?></h6>
                    </div>
                    <div class="card-body d-flex flex-column flex-md-row justify-content-around">
                      <blockquote class="blockquote mb-0 col-12 col-md-9 d-flex flex-column justify-content-around">
                        <p class="messageP"><?php printf("%s", $data['msg']);?></p>             
                        <footer class="blockquote-footer"><?php printf("%s %s", $data['prenom'],$data['nom']);?></footer>
                      </blockquote>
                      <form class="stateP col-12 col-md-3 d-flex flex-column justify-content-center align-content-center align-items-center">
                        <div class="form-group">
                          <label for="state" class="col-12 text-center">Etat actuel :  <?php printf("%s", $data["etat"]);?> </label>
                          <select class="form-control col-9 mx-auto text-left status" id="state" name="state">
                            <option>À traiter</option>
                            <option>À relancer</option>
                            <option>Attente de réponse</option>
                            <option>RDV pris</option>
                            <option>Sans suite</option>
                          </select>
                        </div>
                        <button type="button" value="<?php printf("%d", $i);?>"class="btn btn-primary mb-2 submitState">Modifier Etat</button>
                      </form>
                    </div>
                  </div>
                  <?php
                  $i ++;
                }   
      }
    }
    catch(Exception $ExceptionRaised){
      printf($ExceptionRaised->getMessage());
      $DB->rollBack();
    }
}


//Declaration des variables contenant les prepare avec request SQL

$default = $MyDB->prepare("SELECT t_msg.sujet, t_msg.id_msg, t_msg.date_msg, t_msg.msg, t_msg.etat, t_personne.prenom, t_personne.nom FROM t_msg INNER JOIN t_personne ON t_msg.id_Email = t_personne.id_Email GROUP BY t_msg.id_msg ORDER BY date_msg DESC");
$messageToDisplay = $MyDB->prepare("SELECT t_msg.sujet, t_msg.id_msg, t_msg.date_msg, t_msg.msg, t_msg.etat, t_personne.prenom, t_personne.nom FROM t_msg INNER JOIN t_personne ON t_msg.id_Email = t_personne.id_Email WHERE t_msg.id_Email = (SELECT id_Email FROM t_email WHERE t_email.Email = :email) GROUP BY t_msg.id_msg ORDER BY date_msg DESC");
$tempStock = $MyDB->prepare("INSERT INTO t_stock (temp_email) VALUES (:email)");
$getPerson = $MyDB->prepare("SELECT temp_email FROM t_stock");



//Validation des données du formulaire et affichage des résultats
if (!empty($_POST["correspondant"])){
  $person = $_POST['correspondant'];
  if (filter_var($person, FILTER_VALIDATE_EMAIL)) {
    if (filter_var($person, FILTER_SANITIZE_EMAIL)) {
      $person = filter_var($person, FILTER_SANITIZE_EMAIL);
          // Envoi des données récupérées via le POST

      getData($MyDB, $messageToDisplay, [":email" => $person]);
      $MyDB->beginTransaction();
      $tempStock->execute([":email" => $person]);
      $MyDB->commit();
    }
  }
}
else {
  getData($MyDB, $default);
}

//Changement de l'etat du mail
if (!empty($_POST["state"]) && !empty($_POST["id"])){
  $state = $_POST["state"];
  $id = intval($dataToParse[$_POST["id"]]["id_msg"]);
  // GET PERSON FROM DB
  $getPerson->execute();
  $lala = $getPerson->fetch(PDO::FETCH_ASSOC);
  $person = $lala["temp_email"];
  if ($person){
    getData($MyDB, $messageToDisplay, [":email" => $person]);
  }
    else {
      getData($MyDB, $default);
    }
  if (filter_var($state, FILTER_VALIDATE_REGEXP, array(
        "options" => array("regexp"=>"/^(À traiter)$|^(À relancer)$|^(Attente de réponse)$|^(RDV pris)$|^(Sans suite)$/")
      ))){
          $state = filter_var($state, FILTER_SANITIZE_STRING);
          $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
          $updateState = $MyDB->prepare("UPDATE t_msg SET etat = :state WHERE id_msg = :id");
          $MyDB->beginTransaction();
          $updateState->execute([":state" => $state, ":id" => $dataToParse[$_POST["id"]]["id_msg"]]);
          $MyDB->prepare("DELETE FROM t_stock WHERE temp_email = :person")->execute([":person" => $person]);
          $MyDB->commit();
          header("refresh:1");
      }
  
  
  }
?>


  </div>
</body>
</html>


