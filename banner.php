<div class="c5145"><b><?php echo $_SESSION['user']?></b><br/><i>
  <?php
    if ($_SESSION['current_turn']==-1){
      echo $current_spy_team['team'];
    }else{
      if (isset($current_team['team'])){
        echo $current_team['team'];
      }else{
        echo "no team ";
      }
    }?>
  </i></div>
  <?php  if ($_SESSION['current_turn']==-1){?>
    <div class="c5145"> <img src="../resources/chrono_over.png" style="height: 100px;"></div>
  <?php  }else{?>
    <div class="c5145"> <img src="../resources/chrono.png" style="height: 100px;"><div class="chrono_current"><?php echo $current_game['current_turn'];?></div><div class="chrono_total"><?php echo $current_game['turns'];?></div></div>
  <?php  }?>
