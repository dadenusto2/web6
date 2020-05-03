<?php
extract($_POST);
try {
    $user = 'u20295';
    $password = '7045626';
    $db = new PDO('mysql:host=localhost;dbname=u20295', $user, $password);
    $login = $_POST['save'];
    $sth = $db->prepare("DELETE FROM anketa WHERE login=:login");
    $sth->bindParam(':login', $login);
    $sth->execute();
    header('Location: admin.php');
}
catch(PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
}
?>
