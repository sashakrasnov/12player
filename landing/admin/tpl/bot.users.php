<?php

if(basename($_SERVER['PHP_SELF']) == basename(__FILE__)) die;

$org_id = $U['org_id'] ? $U['org_id'] : intval($_GET['i']);

?>
<script>
  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
  });
</script>


<div class="row">
  <div class="col">
    <p><h3 class="text-center"><small>Telegram Chatbot admin users</small></h3></p>
    <?php if(!$U['org_id']): ?>
    <ul class="nav nav-tabs">
      <?php foreach($C_orgs as $i => $o): ?>
      <li class="nav-item">
        <a class="nav-link <?=($i == $org_id ? 'active' : '');?>" href="./?<?=url_query_make('task=bot&p=users&b='.$_GET['b'].'&i='.$i, false);?>"><?=$o['title'];?></a>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
</div>
<?php if($org_id): ?>
<form action="./?<?=url_query_make('task=bot&p=users&b='.$_GET['b'].'&i='.$org_id);?>" method="post">
<input type="hidden" name="org_id" value="<?=$org_id;?>">
<div class="row mt-4">
  <?php foreach($U['city_id'] ? array($U['city_id']) : array_keys($C_cities) as $c): ?>
  <div class="col<?=($U['city_id'] ? '' : '-sm-6');?> form-group">
     <label for="city-<?=$c;?>"><?=($c ? $C_cities[$c]['title'] : 'All cities');?>:</label>
     <input type="text" name="city-<?=$c;?>" class="form-control" id="city-<?=$c;?>" placeholder="Enter comma separated list of the Chatbot admin users" value="<?=htmlentities(implode(',',array_column($DB->query("SELECT `uname` FROM `{$_GET['b']}_admins` WHERE `org_id`=$org_id AND `city_id`=$c")->fetch_all(MYSQLI_NUM), '0')));?>" title="Enter comma separated list of the Chatbot admin users" data-toggle="tooltip">
  </div>
  <?php endforeach; ?>
</div>
<div class="row mt-4">
    <div class="col-sm-3">
      <button class="btn btn-primary" type="submit" name="submit" value="submit-bot-users">Save</button>
    </div>
</div>
</form>
<?php endif; ?>
