<?php

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) die;

?>
    <main>
      <nav class="navbar navbar-expand-md bg-light fixed-top2">
        <strong class="2navbar-brand"><?=TITLE;?></strong>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
          <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link" href="./?task=events&p=list"><i class="fa fa-calendar-o"></i> Events</a></li>
            <li class="nav-item"><a class="nav-link" href="./?task=events&p=edit&i=0"><i class="fa fa-calendar-plus-o"></i> Add new</a></li>
            <li class="nav-item"><a class="nav-link" href="./?task=tickets&p=check"><i class="fa fa-ticket"></i> Check in</a></li>
            <li class="nav-item"><a class="nav-link" href="./?task=bot&p=users&b=tg"><i class="fa fa-user"></i> Chatbot admins</a></li>
          </ul>
        </div>
        <ul class="nav nav-pills">
          <li class="nav-item nav-link"><?=($U['org_id'] ? $C_orgs[$U['org_id']]['title'] : '');?></li>
          <li class="nav-item nav-link"><?=($U['city_id'] ? $C_cities[$U['city_id']]['title'] : '');?></li>
        </ul>
        <form class="form-inline" action="?task=logout" method="post">
          <button class="btn btn-outline-primary" style="margin-bottom: -15px !important;" name="submit" type="submit" value="logout-submit"><i class="fa fa-sign-out"></i> Logout</button>
        </form>
        <button class="navbar-toggler" style="margin-bottom: -15px !important;" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <strong class="navbar-toggler-icon navbar-brand2">&#9776;</strong>
        </button>
      </nav>
      <div class="container-fluid">
<?php

if (in_array($_GET['task'], ['events', 'bot', 'users', 'tickets'], true))
    $f = 'tpl/'.$_GET['task'].($_GET['p'] ? '.'.$_GET['p'] : '').'.php';

if (file_exists($f))
    include $f;
else
    include 'tpl/events.list.php';

?>
      </div>
    </main>