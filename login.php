<?php
/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();
// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  // Если есть логин в сессии, то пользователь уже авторизован.
  // TODO: Сделать выход (окончание сессии вызовом session_destroy()
  //при нажатии на кнопку Выход).
  // Делаем перенаправление на форму.
  header('Location: index.php');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();
  $errors = array();
  $errors['login'] = !empty($_COOKIE['login_error']);
  $errors['pass'] = !empty($_COOKIE['pass_error']);

  // TODO: аналогично все поля.

  // Выдаем сообщения об ошибках.
  //printf($error[0]);
  if (!empty($errors['login'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('login_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Неверный login</div>';
  }
  else if(!empty($errors['pass'])){
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('pass_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Неверный пароль </div>';
  }
?>
<html lang="ru">
  	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">   <meta name="viewport" content="width=device-wedth,initial-scale=1.0">
		<link rel="stylesheet" href = "style.css">
		<title>Вход пользователя</title>
	</head>
  <?php
    if (!empty($messages)) {
      print('<div id="messages">');
      // Выводим все сообщения.
      foreach ($messages as $message) {
        print($message);
      }
      print('</div>');
    }
  ?>
  <div class="container justify-content-center p=0 m=0" id="content">
    <form action="login.php" method="post">
      <p><h2>Войдите для изменения данных </p>
      <p>Логин:</p>
      <input name="login" id="login"  placeholder="11111"/>
      <p>Пароль:</p>
      <input name="pass" id="pass" placeholder="пароль"/></br>
      <input type="submit" id="in" value="Войти"/>
      </h2>
    </form>
    <form method="POST" action="admin.php">
      <input type='hidden' name='SeenBefore' value='1' />
        <input type='hidden' name='OldAuth' value="<?php print($_SERVER['PHP_AUTH_USER']); ?>"/>
      <input type='submit' name="save" id="in" value='Авторизоваться повторно как администратор'/>
    </form>
    <form action='index.php' method='post'>
      <input type='submit' name='save' id="in" value='Создать нового пользователя'/>
    </form></p>
  </div>
</html>
<?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
  $errors = FALSE;
    if (empty($_POST['login'])) {
      // Выдаем куку на день с флажком об ошибке в поле fio.
      setcookie('login_error', '1', time() + 24 * 60 * 60);
      $errors = TRUE;
    }
    else {
      // Сохраняем ранее введенное в форму значение на месяц.
      setcookie('login_value', $_POST['login'], time() + 30 * 24 * 60 * 60);
    }
    if (empty($_POST['pass'])) {
      setcookie('pass_error', '1', time() + 24 * 60 * 60);
      $errors = TRUE;
    }
    else{
      setcookie('pass_value', $_POST['pass'], time() + 30 * 24 * 60 * 60);
    }
    if ($errors) {
      // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
      header('Location: login.php');
      exit();
    }
    else {
      try {
        //setcookie('loginConnect_error', '1', time() + 24 * 60 * 60);
        $db = new PDO('mysql:host=localhost;dbname=u20295', 'u20295', '7045626');
        $row=$db->query("SELECT login FROM anketa where login='".(string)$_POST['login']."' AND password='".(string)md5($_POST['pass'])."'")->fetch();
        $db = null;
      }
      catch(PDOException $e){}
      if (!empty($row)) {
        // Если все ок, то авторизуем пользователя.
        $_SESSION['login'] = (string)$_POST['login'];
        $_SESSION['pass'] = (string)md5($_POST['pass']);
        // Записываем ID пользователя.
        $_SESSION['uid'] = $_SESSION['login'];
        // Делаем перенаправление..
        header('Location: index.php');
      }
      else{
        setcookie('login_error', '1', time() + 24 * 60 * 60);
        header('Location: login.php'); 
      }
    }
  }