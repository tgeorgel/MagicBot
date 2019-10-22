<?php
class Scripts
{
  /*

  // ====== [ WEB BASICS SCRIPTS ] ====== //

  */

  public function checkIfPageExist($p)
  {
    $arr = [  'home',
              'accounts',
          ];

    if (!in_array($p, $arr))
      $p = "home";

    return("pages/" . $p . ".php");
  }


  /*

  // ====== [ SECURITY FUNC ] ====== //

  */

  function hash_PWD($pass, $s1='a-&v+n&=pz$vwn', $s2='73)4fèd{h§éà{@')
  {
    return $s1.$pass.$s2;
  }

  function passcrypt($pass)
  {
    return crypt($this->hash_PWD($pass));
  }

  function passcheck($input, $db_pass)
  {
    if (hash_equals($db_pass, crypt($this->hash_PWD($input), $db_pass))) return 1;
    else return 0;
  }

  function encrypt($decrypted, $password, $salt='!kQm*fF3pXe1Kbm%9')
  {
    $key = hash('SHA256', $salt . $password, true);
    srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
    if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
    $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
    return $iv_base64 . $encrypted;
  }

  function decrypt($encrypted, $password, $salt='!kQm*fF3pXe1Kbm%9')
  {
    $key = hash('SHA256', $salt . $password, true);
    $iv = base64_decode(substr($encrypted, 0, 22) . '==');
    $encrypted = substr($encrypted, 22);
    $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
    $hash = substr($decrypted, -32);
    $decrypted = substr($decrypted, 0, -32);
    if (md5($decrypted) != $hash) return false;
    return $decrypted;
  }

  /*

  // ====== [ PRINT ALERTS ] ====== //

  */

  function printSuccessAlert($a)
  {
    echo '<div class="alert alert-success alert-dismissable">';
    echo   '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    echo   $a;
    echo '</div>';
  }

  function printDangerAlert($a)
  {
    echo '<div class="alert alert-danger alert-dismissable">';
    echo   '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    echo   $a;
    echo '</div>';
  }

  function printWarnAlert($a)
  {
    echo '<div class="alert alert-warn alert-dismissable">';
    echo   '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    echo   $a;
    echo '</div>';
  }

  function printInfoAlert($a)
  {
    echo '<div class="alert alert-info alert-dismissable">';
    echo   '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    echo   $a;
    echo '</div>';
  }



  /*

  // ====== [ PRINT PROGRESSBARS ] ====== //

  */

  function printProgBar($mn = 0, $mx = 100, $c = 0, $a = true, $pr = false)
  {
    if($a) $ac = ' active';
    if(!$pr) $prt = ' class="sr-only"';
    echo   '<div class="progress-bar progress-bar-striped'.$ac.'" role="progressbar" aria-valuenow="'.$c.'" aria-valuemin="'.$mn.'" aria-valuemax="'.$mx.'" style="width: '.$c.'%"';
    echo     '<span'.$prt.'>'.$c.'/'.$mx.'</span>';
    echo   '</div>';
  }
}
?>
