<?php
require_once "declare.php"; //Require the file with all the SQL requests

/* assert all the mandatory fields are there and then validate and sanitize them
  print error messages otherwise
*/
if (isset($_POST["email"]) && isset($_POST["message"]) && isset($_POST["sujet"])) {
    if (!empty($_POST["email"]) && !empty($_POST["message"]) && !empty($_POST["sujet"])) {

        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $telephone = $_POST["telephone"];
        $email = $_POST["email"];
        $sujet = $_POST["sujet"];
        $petitMot = $_POST["message"];

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (filter_var($email, FILTER_SANITIZE_EMAIL)) {
                if (filter_var($petitMot, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    $petitMot = filter_var($petitMot, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                    $sujet = filter_var($sujet, FILTER_SANITIZE_STRING);
                    if (isset($prenom)) {
                        if (!empty($prenom)) {
                            $prenom = filter_var($prenom, FILTER_SANITIZE_STRING);
                        }
                    }
                    if (isset($nom)) {
                        if (!empty($nom)) {
                            $nom = filter_var($nom, FILTER_SANITIZE_STRING);
                        }
                    }
                    if (isset($telephone)) {
                        if (!empty($telephone)) {
                            filter_var($telephone, FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"#[0][7][- \.?]?([0-9][0-9][- \.?]?){4}$#")));
                            $telephone = filter_var($telephone, FILTER_SANITIZE_STRIPPED, array("options" => array('min_range' => 10, 'max_range' => 10)));
                        }
                    }
                    /*Start of MYSQL work. 
                    First a Try and catch to verify the connection to the MySQL server.
                    Then Prepare the MySQL requests.
                    If not already in transaction, begin transaction by retrieving the data from POST data.
                    Commit everything and catch if something goes wrong.
                    */
                    try {      
                      $MyDB = new PDO("mysql:host=localhost;dbname=laboadm_mgoudj","laboadm_mehd_goud","fKh^gzx[8]EP", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
                    } catch (Exception $ExceptionRaised) {
                      die($ExceptionRaised);
                  }
                  $MyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                  $MySendmessage = $MyDB->prepare(SEND_MSG);
                  $MyEmailID = $MyDB->prepare(EMAIL_ID);
                  $MySendmail = $MyDB->prepare(SEND_MAIL);
                  $MySendperson = $MyDB->prepare(SEND_PRSN);  
                    if (! ($MyDB->inTransaction())) {
                      try {
                        $MyDB->beginTransaction();
                        $ReqParamMail = [
                          ":email" => $email
                        ];
                        $ReqParamPerson = [
                          ":prenom" => $prenom,
                          ":nom" => $nom,
                          ":tel" => $telephone
                        ];
                        $ReqParamMsg = [
                          ":message" => $petitMot,
                          ":sujet" => $sujet
                        ];
                        $MySendmail->execute($ReqParamMail);
                        $MyEmailID->execute($ReqParamMail);
                        $MySendmessage->execute($ReqParamMsg);
                        $MySendperson->execute($ReqParamPerson);
                        $MyDB->commit();
                      }
                      catch (Exception $ExceptionRaised) {
                        printf($ExceptionRaised->getMessage());
                        $MyDB->rollBack();
                    }
                    }

                }
            }
        }
    } else {
      printf("Merci de revenir en arrière et remplir les champs obligatoires");
    }
}
else {
  printf('Ne pas modifier les champs du formulaire.');
}
?>