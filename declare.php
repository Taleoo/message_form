<?php
//Constantes PDO MYSQL
const CHECK_MAIL = "SELECT Email FROM t_email";
const SEND_MAIL = "INSERT INTO t_email (Email) VALUES (:email)";
const EMAIL_ID = "SET @EMAILID = (SELECT id_Email FROM t_email WHERE Email = :email)";
const SEND_MSG = "INSERT INTO t_msg (sujet, msg, id_Email) VALUES (:sujet, :message, @EMAILID)";
const SEND_PRSN = "INSERT INTO t_personne (prenom, nom, tel, id_Email) VALUES (:prenom, :nom, :tel, @EMAILID)";
?>