<?php
//открываем сессию
session_start();
// переменная, в которую будем сохранять результат работы
$data['result']='error';
 
// функция для проверки длины строки
function validStringLength($string,$min,$max) {
  $length = mb_strlen($string,'UTF-8');
  if (($length<$min) || ($length>$max)) {
    return false;
  }
  else {
    return true;
  }
}
 
// если данные были отправлены методом POST, то...
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // устанавливаем результат, равный success
    $data['result']='success';
    //получить имя, которое ввёл пользователь
    if (isset($_POST['name'])) {
      $name = $_POST['name'];
      if (!validStringLength($name,2,30)) {
        $data['name']='Поля имя содержит недопустимое количество символов.';   
        $data['result']='error';     
      }
    } else {
      $data['result']='error';
    } 
    //получить email, который ввёл пользователь
    if (isset($_POST['email'])) {
      $email = $_POST['email'];
      if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $data['email']='Поле email введено неправильно';
        $data['result']='error';
      }
    } else {
      $data['result']='error';
    }
    //получить phone, который ввёл пользователь
    if (isset($_POST['phone'])) {
      $email = $_POST['phone'];
      if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $data['phone']='Поле телефона введено неправильно';
        $data['result']='error';
      }
    } else {
      $data['result']='error';
    }
   
  } else {
    //данные не были отправлены методом пост
    $data['result']='error';
  }    
 
  // дальнейшие действия (ошибок не обнаружено)
  if ($data['result']=='success') {
 
    //1. Сохранение формы в файл
    $output = "---------------------------------" . "\n";
    $output .= date("d-m-Y H:i:s") . "\n";
    $output .= "Имя пользователя: " . $name . "\n";
    $output .= "Адрес email: " . $email . "\n";
    $output .= "Сообщение: " . $phone . "\n";
    if (file_put_contents(dirname(__FILE__).'/message.txt', $output, FILE_APPEND | LOCK_EX)) {
      $data['result']='success';
    } else {
      $data['result']='error';         
    } 
 
    //2. Отправляем на почту
    // включить файл PHPMailerAutoload.php
    require_once dirname(__FILE__) . '/phpmailer/PHPMailerAutoload.php';
    //формируем тело письма
    $output = "Дата: " . date("d-m-Y H:i") . "\n";
    $output .= "Имя пользователя: " . $name . "\n";
    $output .= "Адрес email: " . $email . "\n";
    $output .= "Телефон: " . "\n" . $phone . "\n";
 
    // создаём экземпляр класса PHPMailer
    $mail = new PHPMailer;
 
    $mail->CharSet = 'UTF-8'; 
    $mail->From      = 'email@mysite.ru';
    $mail->FromName  = 'Имя сайта';
    $mail->Subject   = 'Сообщение с формы обратной связи';
    $mail->Body      = $output;
    $mail->AddAddress( 'dimone-163@yandex.ru' );
 
    // отправляем письмо
    if ($mail->Send()) {
      $data['result']='success';
    } else {
      $data['result']='error';
    }      
 
  }
  // формируем ответ, который отправим клиенту
  echo json_encode($data);
?>