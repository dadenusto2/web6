<?php
 if (!empty($messages)) {
  print('<div id="messages">');
  // Выводим все сообщения.
  foreach ($messages as $message) {
    print($message);
  }
  print('</div>');
}
function authenticate() {
  header('HTTP/l.1 401 Unauthorized');
  header('WWW-Authenticate: Basic rеаlm="admin.php"');
  print('<h1>401 Требуется авторизация</h1>');
  exit();
}
if(isset($_POST['OldAuth'])){
  $p = $_POST['OldAuth'];
}
else{
  $p=0;
}
if (!isset($_SERVER['PHP_AUTH_USER']) ||
isset($_POST['SeenBefore']) && $p == $_SERVER['PHP_AUTH_USER']) {
  authenticate();
}
else{
  try {
    $db = new PDO('mysql:host=localhost;dbname=u20295', 'u20295', '7045626');
    $row=$db->query("SELECT login FROM admin where login='".(string)$_SERVER['PHP_AUTH_USER']."' AND password='".(string)md5($_SERVER['PHP_AUTH_PW'])."'")->fetch();
  }
  catch(PDOException $e){
    header('HTTP/l.1 401 Unauthorized');
    header('WWW-Authenticate: Basic rеаlm=".login"'); 
    exit('');
  }
  if (!empty($row)){//строка с логином админа и полями таблицы
    echo "<p>Добро пожаловать: " . htmlspecialchars($_SERVER['PHP_AUTH_USER']) . "<br />";
    echo "<form action='' method='post'>\n";
    echo "<input type='hidden' name='SeenBefore' value='1' />\n";
    echo "<input type='hidden' name='OldAuth' value=\"" . htmlspecialchars($_SERVER['PHP_AUTH_USER']) . "\" />\n";
    echo "<input type='submit' value='Авторизоваться повторно' />\n";
    echo "</form></p>\n";
    $num=1;
    $messages[] = sprintf("
    <head>
      <meta charset='utf-8'>
      <link rel='stylesheet' href = 'style.css'>
    </head>
    <table>
      <th>num</th>
      <th class='short'>login</th>
      <th class='long'>password</th>
      <th >name</th>
      <th >email</th>
      <th >date</th>
      <th class='middle'>gender</th>
      <th class='short'>limb</th>
      <th class='middle'>super1</th>
      <th class='middle'>super2</th>
      <th class='middle'>super3</th>
      <th class='long'>message</th>
      <th class='middle'>checker</th>
      <th class='middle'>Удалить</th>
    </table>
    ");
    //строки из таблицы anketa
    foreach($db->query('SELECT * FROM anketa') as $row){
      $messages[] = sprintf("
      <head>
        <meta charset='utf-8'>
        <link rel='stylesheet' href = 'style.css'>
      </head>
      <table>
        <td>%s</td>
        <td class='short'>%s</td>
        <td class='long'>%s</td>
        <td >%s</td>
        <td >%s</td>
        <td >%s</td>
        <td class='middle'>%s</td>
        <td class='short'>%s</td>
        <td class='middle'>%s</td>
        <td class='middle'>%s</td>
        <td class='middle'>%s</td>
        <td class='long'>%s</td>
        <td class='middle'>%s</td>
        <td class='middle'>
          <form method='POST' action='delete.php'>
            <input type='submit' name='save' value='%s' />
          </form>
        </td>
      </table>
      ",
      strip_tags($num),
      strip_tags($row['login']),
      strip_tags($row['password']),
      strip_tags($row['name']),
      strip_tags($row['email']),
      strip_tags($row['date']),
      strip_tags($row['gender']),
      strip_tags($row['limb']),
      strip_tags($row['super1']),
      strip_tags($row['super2']),
      strip_tags($row['super3']),
      strip_tags($row['message']),
      strip_tags($row['checker']),
      strip_tags($row['login']),
      );
      $num=$num+1;
    }
    if (!empty($messages)) {
      print('<div id="messages">');
      // Выводим все сообщения.
      foreach ($messages as $message) {
        print($message);
      }
      print('</div>');
    }
  }
  else{
    authenticate();
  }
}
?>