<?php echo $header; ?>
<h1 style="background: url('view/image/configuration.png') no-repeat;">Шаг 3 - Конфигурация</h1>
<div style="width: 100%; display: inline-block;">
  <div style="float: left; width: 569px;">
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <p>1 . Введите настройки БД.</p>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 15px;">
        <table>
          <tr>
            <td width="185"><span class="required">*</span>Сервер БД:</td>
            <td><input type="text" name="db_host" value="<?php echo $db_host; ?>" />
              <br />
              <?php if ($error_db_host) { ?>
              <span class="required"><?php echo $error_db_host; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span>Пользователь:</td>
            <td><input type="text" name="db_user" value="<?php echo $db_user; ?>" />
              <br />
              <?php if ($error_db_user) { ?>
              <span class="required"><?php echo $error_db_user; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">&nbsp;</span>Пароль:</td>
            <td><input type="text" name="db_password" value="<?php echo $db_password; ?>" /></td>
          </tr>
          <tr>
            <td><span class="required">*</span>Имя БД:</td>
            <td><input type="text" name="db_name" value="<?php echo $db_name; ?>" />
              <br />
              <?php if ($error_db_name) { ?>
              <span class="required"><?php echo $error_db_name; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">&nbsp;</span>Префикс БД:</td>
            <td><input type="text" name="db_prefix" value="<?php echo $db_prefix; ?>" /></td>
          </tr>
        </table>
      </div>
      <p>2. Введите логин и пароль администратора.</p>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 15px;">
        <table>
          <tr>
            <td width="185"><span class="required">*</span>Логин:</td>
            <td><input type="text" name="username" value="<?php echo $username; ?>" />
              <?php if ($error_username) { ?>
              <span class="required"><?php echo $error_username; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span>Пароль:</td>
            <td><input type="text" name="password" value="<?php echo $password; ?>" />
              <?php if ($error_password) { ?>
              <span class="required"><?php echo $error_password; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span>E-Mail:</td>
            <td><input type="text" name="email" value="<?php echo $email; ?>" />
              <?php if ($error_email) { ?>
              <span class="required"><?php echo $error_email; ?></span>
              <?php } ?></td>
          </tr>
        </table>
      </div>
      <div style="text-align: right;"><a onclick="document.getElementById('form').submit()" class="button"><span class="button_left button_continue"></span><span class="button_middle">Continue</span><span class="button_right"></span></a></div>
    </form>
  </div>
  <div style="float: right; width: 205px; height: 400px; padding: 10px; color: #663300; border: 1px solid #FFE0CC; background: #FFF5CC;">
    <ul>
      <li>Лицензия</li>
      <li>Перед установкой</li>
      <li><b>Конфигурация</b></li>
      <li>Окончание</li>
    </ul>
  </div>
</div>
<?php echo $footer; ?>