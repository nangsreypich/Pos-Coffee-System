<?php if(!empty($error)){?>
         <div class="alert alert-danger">
            <ul>
               <?php foreach($error as $err) : ?>
               <li><?php echo $err ?></li>
               <?php endforeach ?>
            </ul>
      </div>
<?php } ?>