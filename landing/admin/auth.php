<?php

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) die;

?>
    <style>
    .div-center {
        width: 400px;
        height: 400px;
        background-color: #fff;
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        margin: auto;
        max-width: 100%;
        max-height: 100%;
        overflow: auto;
        display: table;
    }
    .div-content {
        display: table-cell;
        vertical-align: middle;
    }
    </style>
    <div class="div-center">
      <div class="div-content jumbotron bg-light">
        <h3 class="mb-5 text-center"><small><?=TITLE;?></small></h3>
        <?php if($MSG): ?>
        <div class="alert alert-danger alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?=$MSG;?>
        </div>
        <?php endif; ?>
        <form action="<?=$URI;?>?task=login" method="post">
          <div class="form-group">
            <div class="input-group">
               <div class="input-group-prepend">
                  <div class="input-group-text"><i class="fa fa-user"></i></div>
               </div>
              <input type="text" name="email" class="form-control" id="email" placeholder="Login" />
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
               <div class="input-group-prepend">
                  <div class="input-group-text"><i class="fa fa-lock"></i></div>
               </div>
              <input type="password" name="passw" class="form-control" id="passw" placeholder="Password" />
            </div>
          </div>
          <div class="form-group">
            <button type="submit" name="submit" value="login-submit" class="btn btn-primary w-100"><i class="fa fa-sign-in"></i> Enter</button>
          </div>
        </form>
      </div>
    </div>
    