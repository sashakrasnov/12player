<?php

if(basename($_SERVER['PHP_SELF']) == basename(__FILE__)) die;

$e = $DB->query("SELECT *, DATE_FORMAT(`dt`, '%d.%m.%Y') AS `d`, DATE_FORMAT(`dt`, '%H:%i') AS `t` FROM `events` WHERE `id`=".intval($_GET['i']).($U['city_id'] ? ' AND `city_id`='.$U['city_id'] : '').($U['org_id'] ? ' AND `org_id`='.$U['org_id'] : ''))->fetch_assoc();

$id = $e ? $e['id'] : 0;

?>
<script>
$(document).ready( function() {
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

            input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {
        var input = $(this).parents('.input-group').find(':text'),
            log = label;
            
        if(input.length) {
            input.val(log);
        } else {
            if(log) alert(log);
        }
    });

    function readURL(input, num) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img-upload-'+num).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('[data-img_input]').change(function() {
        readURL(this, $(this).data('img_input'));
    });     
});

function event_check()
{
  return true;
}
</script>
<style>
.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}
.img-upload {
    width: 100%;
    margin-top: 0.5rem;
    border-radius: 0.25rem;
}
</style>
<div class="row">
  <div class="col"></div>
  <div class="col-md-9 col-lg-7 col-xl-6">
    <form class="was-validated" action="./?<?=url_query_make('task=events&p=edit&i='.$id, $id==0);?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="event_id" value="<?=$id;?>">
      <div class="row">
        <div class="col-sm">
          <!-- Header -->
          <p><h3><small>
          <?php if($e): ?>
            Edit the event
          <?php else: ?>
            Add new event
          <?php endif; ?>
          </small></h3></p>
          <!-- Error message -->
          <?php if($MSG): ?>
          <div class="alert alert-danger alert-danger">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <?=$MSG;?>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="row">
        <!-- Companies -->
        <div class="col-sm <?=($U['org_id'] ? 'd-none' : '');?> form-group">
          <label for="org">Company/Sponsor:</label>
          <?php if(!$U['org_id'] && !$id): // Chief admin ?>
          <select name="org" id="org" class="form-control custom-select" required>
            <option value="">Select company (required)</option>
            <?php foreach($C_orgs as $o => $t):?>
            <option value="<?=$o;?>" <?=($o == $_POST['org'] ? 'selected' : '');?>><?=$t['title'];?></option>
            <?php endforeach;?>
          </select>
          <?php else: ?>
          <input type="text" disabled class="form-control" id="org" value="<?=$C_orgs[($e ? $e['org_id'] : $U['org_id'])]['title'];?>">
          <input type="hidden" name="org" value="<?=($e ? $e['org_id'] : $U['org_id']);?>">
          <?php endif; ?>
        </div>
        <!-- Cities -->
        <div class="col-sm <?=($U['city_id'] ? 'd-none' : '');?> form-group">
          <label for="city">City of event:</label>
          <?php if(!$U['city_id'] && !$id): // Admin that allowed to choose a city ?>
          <select name="city" id="city" class="form-control custom-select" required>
            <option value="">Select city of event (required)</option>
            <?php foreach($C_cities as $c => $t):?>
            <option value="<?=$c;?>" <?=($c == $_POST['city'] ? 'selected' : '');?>><?=$t['title'];?></option>
            <?php endforeach;?>
          </select>
          <?php else: ?>
          <input type="text" disabled class="form-control" id="city" value="<?=$C_cities[($e ? $e['city_id'] : $U['city_id'])]['title'];?>">
          <input type="hidden" name="city" value="<?=($e ? $e['city_id'] : $U['city_id']);?>">
          <?php endif; ?>
        </div>
      </div>
      <div class="row">
        <!-- Language -->
        <div class="col-sm form-group">
          <label for="lang">Event language:</label>
          <?php if($e): ?>
          <input type="text" disabled class="form-control" value="<?=$C_langs[$e['lang_id']]['title'];?>">
          <input type="hidden" name="lang" value="<?=$e['lang_id'];?>">
          <?php else: ?>
          <select name="lang" id="lang" class="form-control custom-select" required>
            <option value="">Select language (required)</option>
            <?php foreach($C_langs as $l => $t):?>
            <option value="<?=$l;?>"<?=($l == $_POST['lang'] ? ' selected' : '');?>><?=$t['title'];?></option>
            <?php endforeach;?>
          </select>
          <?php endif; ?>
        </div>
        <!-- Date of event -->
        <div class="col-sm form-group">
          <label for="dd">Date:</label>
          <?php if($e): ?>
          <input type="text" disabled class="form-control" value="<?=$e['d'];?>">
          <input type="hidden" name="d" value="<?=$e['d'];?>">
          <?php else: ?>
          <div class="input-group date" id="d" data-target-input="nearest">
            <input type="text" id="dd" class="form-control datetimepicker-input" data-target="#d" name="d" value="<?=$_POST['d'];?>" data-toggle="datetimepicker" data-target="#d" required>
            <div class="input-group-append" data-target="#d" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <script type="text/javascript">
            $(function () {
                $('#d').datetimepicker({'locale': 'ru', 'format': 'L'});
                $('#dd').datetimepicker({'locale': 'ru', 'format': 'L'});
            });
        </script>
        <!-- Time -->
        <div class="col-sm form-group">
          <label for="tt">Time:</label>
          <?php if($e): ?>
          <input type="text" disabled class="form-control" value="<?=$e['t'];?>">
          <input type="hidden" name="t" value="<?=$e['t'];?>">
          <?php else: ?>
          <div class="input-group date" id="t" data-target-input="nearest">
            <input type="text" id="tt" class="form-control datetimepicker-input" data-target="#t" name="t" value="<?=$_POST['t'];?>" data-toggle="datetimepicker" data-target="#t" required>
            <div class="input-group-append" data-target="#t" data-toggle="datetimepicker">
              <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <script type="text/javascript">
            $(function () {
                $('#t').datetimepicker({'format': 'LT', 'locale': 'ru'});
                $('#tt').datetimepicker({'format': 'LT', 'locale': 'ru'});
            });
        </script>
      </div>
      <div class="row">
        <!-- Price -->
        <div class="col-sm form-group">
          <label for="price">Price:</label>
          <div class="input-group">
            <?php if($e): ?>
            <input type="number" id="price" class="form-control" value="<?=$e['price'];?>" disabled>
            <input type="hidden" name="price" value="<?=$e['price'];?>">
            <?php else: ?>
            <input type="number" name="price" class="form-control" value="<?=$_POST['price'];?>" id="price" min="0" placeholder="Enter price" required>
            <?php endif; ?>
            <div class="input-group-append"><span class="input-group-text"><?=RUB;?></span></div>
          </div>
        </div>
        <!-- Free of charge tickets -->
        <div class="col-sm form-group">
          <label for="count_free">Free tickets:</label>
          <?php if($e): ?>
          <input type="text" id="count_free" disabled class="form-control" value="<?=$e['count_free'];?>">
          <input type="hidden" name="count_free" value="<?=$e['count_free'];?>">
          <?php else: ?>
          <input type="number" name="count_free" class="form-control custom-input" value="<?=$_POST['count_free'];?>" id="count_free" value="" min="0" placeholder="Enter number of free of charge tickets" required>
          <?php endif; ?>
        </div>
        <!-- Minimum tickets -->
        <div class="col-sm form-group">
          <label for="count_min">Minimum:</label>
          <?php if($e): ?>
          <input type="text" id="count_min" disabled class="form-control" value="<?=$e['count_min'];?>">
          <input type="hidden" name="count_min" value="<?=$e['count_min'];?>">
          <?php else: ?>
          <input type="number" name="count_min" class="form-control custom-input" value="<?=$_POST['count_min'];?>" id="count_min" min="0" placeholder="Enter minimum number of tickets to start the event" required>
          <?php endif; ?>
        </div>
        <!-- Maximum tickets -->
        <div class="col-sm form-group">
          <label for="count_max">Maximum:</label>
          <?php if($e): ?>
          <input type="text" id="count_max" disabled class="form-control" value="<?=$e['count_max'];?>">
          <input type="hidden" name="count_max" value="<?=$e['count_max'];?>">
          <?php else: ?>
          <input type="number" name="count_max" class="form-control custom-input" value="<?=$_POST['count_max'];?>" id="count_max" min="0" placeholder="Enter maximum number of tickets for the event" required>
          <?php endif; ?>
        </div>
      </div>
      <div class="row">
        <!-- Event type -->
        <div class="col form-group">
          <label for="game">Event type:</label>
          <?php if($e && $e['status'] < 0): ?>
          <input type="text" disabled class="form-control" value="<?=$C_games[$e['game_id']]['title'];?>">
          <input type="hidden" name="game" value="<?=$e['game_id'];?>">
          <?php else: ?>
          <select name="game" id="game" class="form-control custom-select" required>
            <option value="">Select type of event (required)</option>
            <?php foreach($C_games as $g => $t):?>
<?php print_r($e); ?>
            <option value="<?=$g;?>" <?=($g == $_POST['game'] || $g == $e['game_id'] ? 'selected' : '');?>><?=$t['title'];?></option>
            <?php endforeach;?>
          </select>
          <?php endif; ?>
        </div>
      </div>
      <!-- Title -->
      <div class="row">
        <div class="col form-group">
          <label for="title">Event title:</label>
          <?php if($e && $e['status'] < 0): ?>
          <input type="text" disabled class="form-control" value="<?=htmlentities($e['title']);?>">
          <input type="hidden" name="title" value="<?=htmlentities($e['title']);?>">
          <?php else: ?>
          <input type="text" name="title" class="form-control custom-input" id="title" placeholder="Enter title of the event (required)" value="<?=htmlentities($e ? $e['title'] : $_POST['title']);?>" required>
          <?php endif; ?>
        </div>
      </div>
      <!-- Address -->
      <div class="row">
        <div class="col form-group">
          <label for="addr">Address:</label>
          <?php if($e && $e['status'] != 0): ?>
          <input type="text" disabled class="form-control" value="<?=htmlentities($e['addr']);?>">
          <input type="hidden" name="addr" value="<?=htmlentities($e['addr']);?>">
          <?php else: ?>
          <input type="text" name="addr" class="form-control custom-input" id="addr" placeholder="Enter address (required)" value="<?=htmlentities($e ? $e['addr'] : $_POST['addr']);?>" required>
          <?php endif; ?>
        </div>
      </div>
      <!-- Map -->
      <div class="row">
        <div class="col form-group">
          <label for="map">Link to the map:</label>
          <?php if($e && $e['status'] != 0): ?>
          <input type="text" disabled class="form-control" value="<?=htmlentities($e['map']);?>">
          <input type="hidden" name="map" value="<?=htmlentities($e['map']);?>">
          <?php else: ?>
          <input type="url" name="map" class="form-control custom-input" id="map" placeholder="Enter link to the map of the event place (required)" value="<?=htmlentities($e ? $e['map'] : $_POST['map']);?>" required>
          <?php endif; ?>
        </div>
      </div>
      <!-- Description -->
      <div class="row">
        <div class="col form-group">
          <label for="descr">Short description:</label>
          <?php if($e && $e['status'] < 0): ?>
          <textarea disabled class="form-control" rows="5"><?=htmlentities($e['descr']);?></textarea>
          <input type="hidden" name="descr" value="<?=htmlentities($e['descr']);?>">
          <?php else: ?>
          <textarea name="descr" class="form-control" id="descr" placeholder="Enter short description" rows="3" required><?=($e ? htmlentities($e['descr']) : $_POST['descr']);?></textarea>
          <?php endif; ?>
        </div>
      </div>
      <!-- Description -->
      <div class="row">
        <div class="col form-group">
          <label for="long_descr">Long description:</label>
          <?php if($e && $e['status'] < 0): ?>
          <textarea disabled class="form-control" rows="5"><?=htmlentities($e['long_descr']);?></textarea>
          <input type="hidden" name="long_descr" value="<?=htmlentities($e['long_descr']);?>">
          <?php else: ?>
          <textarea name="long_descr" class="form-control" id="long_descr" placeholder="Enter long description" rows="6" required><?=($e ? htmlentities($e['long_descr']) : $_POST['long_descr']);?></textarea>
          <?php endif; ?>
        </div>
      </div>
      <!-- Link to the report -->
      <?php if($e): ?>
      <div class="row">
        <div class="col form-group">
          <?php if($e['status'] > 0): ?>
          <label for="descr">Link to the event:</label>
          <input type="url" name="link" class="form-control" id="link" placeholder="Enter link to the event" value="<?=($_POST['link'] ? $_POST['link'] : htmlentities($e['link']));?>">
          <?php elseif($e['link']): ?>
          <label for="descr">Link to the report:</label>
          <div><a href="<?=$e['link'];?>" target="_blank"><?=$e['link'];?></a></div>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
      <!-- Photos -->
      <?php for($i = 1; $i <= NUM_IMAGES; $i++): ?>
      <div class="row">
        <div class="col form-group">
            <?php if($e && $e['status'] < 0): if($e['img_ext_'.$i]): ?>
            <label for="imgDisp-<?=$i;?>">Photo #<?=$i;?>:</label>
            <img id="img-upload-<?=$i;?>" src="<?=UPLOAD_DIR.'/'.$e['id'].'-'.$i.'.'.$e['img_ext_'.$i];?>" class="img-upload">
            <?php endif; ?>
            <?php else:  ?>
            <label for="imgDisp-<?=$i;?>">Photo #<?=$i;?>:</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text btn btn-outline-primary btn-file" id="imgDisp-<?=$i;?>">Choose…
                    <input type="file" id="imgInp" accept="image/*" name="event_img_<?=$i;?>" data-img_input="<?=$i;?>">
                </span>
              </div>
              <input type="text" class="form-control form-control-file border">
            </div>
            <img id="img-upload-<?=$i;?>" <?=($e['img_ext_'.$i] ? 'src="'.UPLOAD_DIR.'/'.$e['id'].'-'.$i.'.'.$e['img_ext_'.$i].'"' : '');?> class="img-upload">
            <?php endif; ?>
        </div>
      </div>
      <?php endfor; ?>
      <!-- Save/Add -->
      <?php if(!($e && $e['status'] < 0)): ?>
      <div class="row">
        <div class="col form-group">
          <button type="submit" name="submit" value="event-save" class="btn btn-primary" onClick="return event_check();"><?=($id ? "Save" : "Add");?></button>
        </div>
      </div>
      <?php endif; ?>
    </form>
  </div>
  <div class="col"></div>
</div>
