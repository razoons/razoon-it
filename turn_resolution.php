<?php


session_start();

include("connection.php");

$req_list_games = $bdd->query('SELECT * FROM games');
while ($list_games=$req_list_games->fetch()){
	if ((date( "w")!=0) AND (date( "w")!=6) AND ($list_games['current_turn']<>-1)){
		$target_reached=false;
		$new_production=array();

		$req_list_teams = $bdd->query('SELECT * FROM teams WHERE game_id='.$list_games['id'].'');
		while ($list_teams=$req_list_teams->fetch()){
			$teams[$list_teams['id']]=$list_teams;
		}


		$req_list_teams = $bdd->query('SELECT * FROM teams WHERE game_id='.$list_games['id'].'');
		while ($list_teams=$req_list_teams->fetch()){
			//-------------------FIREWALL-----------------------------------

				//Count number of firewall from this team
				$req_list_actions = $bdd->query('SELECT COUNT(*) as number_actions_firewall FROM actions WHERE game_id='.$list_games['id'].' AND team_id='.$list_teams['id'].' AND action="firewall" AND turn='.$list_games['current_turn'].'');
				$number_actions_firewall=$req_list_actions->fetch();

				//Counting and ordering the number of hack to this team
				// should represent something like
				//  $parse_order[0]=[3]
				//  $parse_order[1]=[1,2]
				//it means that team3 is the first one to block(because less virus) and team1 and team2 must be treated equally
				$req_list_actions = $bdd->query('SELECT team_id,COUNT(*) as count_hack FROM `actions` WHERE game_id='.$list_games['id'].' AND target_team_id='.$list_teams['id'].' AND action="hack" AND turn='.$list_games['current_turn'].' GROUP BY team_id ORDER by COUNT(*) ASC');
				$parse_order=array();
				$count=0;
				$i=-1;
				while ($list_actions=$req_list_actions->fetch()){
					if ($list_actions['count_hack']!=$count){
						$i++;
					}
					$parse_order[$i][]=$list_actions['team_id'];
					$count=$list_actions['count_hack'];
				}

				// the following should lead to the list of actions ordered by creation date
				$req_list_actions = $bdd->query('SELECT * FROM `actions` WHERE game_id='.$list_games['id'].' AND target_team_id='.$list_teams['id'].' AND action="hack" AND team_id!='.$list_teams['id'].' AND turn='.$list_games['current_turn'].' ORDER by id DESC');
				$hack_actions['team_id']=array();
				$hack_actions['id']=array();
				while ($list_actions=$req_list_actions->fetch()){
					$hack_actions['team_id'][]=$list_actions['team_id'];
					$hack_actions['id'][]=$list_actions['id'];
				}

				// the following should re-order the previous list the way they will be blocked
				$hack_actions_ordered=array();
				while (count($hack_actions['id'])>0){
					for ($j=0;$j<=$i;$j++){
						if (count($hack_actions['id'])==0){
							break 2;
						}
						$index=10000;
						//temp_list is the list of equivalent teams. for example [1,2]
						$temp_list=$parse_order[$j];
						$temp_list_index_search=array();
						for ($k=0;$k<count($temp_list);$k++){
							 //looking for each team, the index of the first item found (if found)
							 if (array_search($temp_list[$k],$hack_actions['team_id'])!==false){
								 //temp_list_index_search contains the index found. result expected is something like $temp_list_index_search=[4,8]
								$temp_list_index_search[$k]=array_search($temp_list[$k],$hack_actions['team_id']);
							 }
						}

						asort($temp_list_index_search);
						foreach ($temp_list_index_search as $key => $val) {
							$hack_actions_ordered['team_id'][]=$hack_actions['team_id'][$val];
							$hack_actions_ordered['id'][]=$hack_actions['id'][$val];
							unset($hack_actions['team_id'][$val]);
							unset($hack_actions['id'][$val]);

						}
					}
				}

				// adding in the end the hacks from own team.
				$req_list_actions = $bdd->query('SELECT * FROM `actions` WHERE game_id='.$list_games['id'].' AND action="deal" AND team_id='.$list_teams['id'].' AND turn='.$list_games['current_turn'].' ORDER by id DESC');
				while ($list_actions=$req_list_actions->fetch()){
					 $hack_actions_ordered['team_id'][]=$list_actions['team_id'];
					 $hack_actions_ordered['id'][]=$list_actions['id'];
				}

				//for the first items (until number of firewall), action is updated and set as blocked
				$update_action=$bdd->prepare('UPDATE actions SET blocked=1 WHERE id=:id');
				for ($i=0;$i<$number_actions_firewall['number_actions_firewall'];$i++){
					$update_action->execute(array(
					'id' => $hack_actions_ordered['id'][$i]
					));
				}
		}

		$req_list_teams = $bdd->query('SELECT * FROM teams WHERE game_id='.$list_games['id'].'');
		while ($list_teams=$req_list_teams->fetch()){



			//-------------------PRODUCTION-----------------------------------

			//Count number of production from this team
			$req_list_actions = $bdd->query('SELECT COUNT(*) as number_actions_code FROM actions WHERE game_id='.$list_games['id'].' AND team_id='.$list_teams['id'].' AND action="code" AND turn='.$list_games['current_turn'].'');
			$number_actions_code=$req_list_actions->fetch();
			$new_production['code'][$list_teams['id']]=$number_actions_code['number_actions_code']*$list_games['code_gain'];
			
			//-------------------BUG-----------------------------------

			//Count number of bugs from this team
			$req_list_actions = $bdd->query('SELECT COUNT(*) as number_actions_bug FROM actions WHERE game_id='.$list_games['id'].' AND team_id='.$list_teams['id'].' AND action="bug" AND turn='.$list_games['current_turn'].'');
			$number_actions_bug=$req_list_actions->fetch();
			$new_production['bug'][$list_teams['id']]=$number_actions_bug['number_actions_bug']*$list_games['bug_gain'];


			//-------------------HACKS-----------------------------------

			//retrieving number of hacks to consider
			$new_production['hack'][$list_teams['id']]=0;
			$req_list_hacks = $bdd->query('SELECT COUNT(*) as count_hack_per_team, target_team_id FROM actions WHERE game_id='.$list_games['id'].' AND turn='.$list_games['current_turn'].' AND blocked=0 AND team_id='.$list_teams['id'].' AND action="hack" GROUP BY target_team_id');
			while($list_hacks=$req_list_hacks->fetch()){
				$new_production['hack'][$list_teams['id']]+=min($teams[$list_hacks['target_team_id']]['production_progress'],$list_hacks['count_hack_per_team']*$list_games['hack_gain']);
			}




			//-------------------DEALS-----------------------------------

			//retrieving number of deals to consider
			$new_production['deal'][$list_teams['id']]=0;
			$req_list_deal = $bdd->query('SELECT COUNT(*) as count_deal_per_team, team_id FROM actions WHERE game_id='.$list_games['id'].' AND turn='.$list_games['current_turn'].' AND blocked=0 AND target_team_id='.$list_teams['id'].' AND action="deal" GROUP BY team_id');
			while($list_deals=$req_list_deal->fetch()){
				if ($list_deals['count_deal_per_team']!=0){
						$new_production['deal'][$list_teams['id']]+=min($teams[$list_deals['team_id']]['production_progress'],$list_deals['count_deal_per_team']*$list_games['deal_gain']);
					}
			}


			//-------------------BLOCKING FIREWALL-----------------------------------

			//retrieving number of blocked hacks and deals to consider
			$new_production['blocked'][$list_teams['id']]=0;
			$req_list_deal = $bdd->query('SELECT COUNT(*) as count_deal_per_team FROM actions WHERE game_id='.$list_games['id'].' AND turn='.$list_games['current_turn'].' AND blocked=1 AND ((target_team_id='.$list_teams['id'].' AND action="hack") OR (team_id='.$list_teams['id'].' AND action="deal"))');
			$list_deals=$req_list_deal->fetch();
			$new_production['blocked'][$list_teams['id']]=$list_deals['count_deal_per_team']*$list_games['firewall_gain'];



			//-------------------STOLEN-----------------------------------

			$new_production['stolen'][$list_teams['id']]=0;
			$req_list_stolen_total = $bdd->query('SELECT COUNT(*) as count FROM actions WHERE game_id='.$list_games['id'].' AND turn='.$list_games['current_turn'].' AND blocked=0 AND team_id='.$list_teams['id'].' AND action="deal"');
			$list_stolen_total=$req_list_stolen_total->fetch();
			if ($list_stolen_total['count']!=0){
				$new_production['stolen'][$list_teams['id']]=min($list_teams['production_progress'],$list_games['deal_gain']*$list_stolen_total['count']);
			}




			//check if the team reaches the total_code
			$new_production['new_total'][$list_teams['id']]=$teams[$list_teams['id']]['production_progress']+$new_production['code'][$list_teams['id']]-$new_production['bug'][$list_teams['id']]+$new_production['hack'][$list_teams['id']]+$new_production['deal'][$list_teams['id']]-$new_production['stolen'][$list_teams['id']]+$new_production['blocked'][$list_teams['id']];
			if (intval($new_production['new_total'][$list_teams['id']])>=intval($list_games['target'])){
				$target_reached=true;
			}
			echo $teams[$list_teams['id']]['production_progress']."+".$new_production['code'][$list_teams['id']]."+".$new_production['hack'][$list_teams['id']]."+".$new_production['deal'][$list_teams['id']]."-".$new_production['stolen'][$list_teams['id']]."+".$new_production['blocked'][$list_teams['id']]."<br/>";
		}


		$req_list_teams = $bdd->query('SELECT * FROM teams WHERE game_id='.$list_games['id'].'');
		while ($list_teams=$req_list_teams->fetch()){
			$update_progress=$bdd->prepare('UPDATE teams SET production_progress=:production_progress WHERE id=:id');
			  $update_progress->execute(array(
			  'id' => $list_teams['id'],
			  'production_progress' =>$new_production['new_total'][$list_teams['id']]
			  ));
			$store_result=$bdd->exec('INSERT INTO reports (game_id,team_id,turn,prod_before,code,hack,deal,stolen,blocked) VALUES ('.$list_games['id'].','.$list_teams['id'].','.$list_games['current_turn'].','.$teams[$list_teams['id']]['production_progress'].','.$new_production['code'][$list_teams['id']].','.$new_production['hack'][$list_teams['id']].','.$new_production['deal'][$list_teams['id']].','.$new_production['stolen'][$list_teams['id']].','.$new_production['blocked'][$list_teams['id']].')');
		}

		//Remove all previous notifications
		$remove_notifications=$bdd->exec('DELETE FROM notifications WHERE game_id="'.$list_games['id'].'"');

		//Get all the exclusions votes
		$req_list_exclusions = $bdd->query('SELECT target_user, COUNT(*) as total, COUNT(vote_result) as total_voters, sum(vote_result) as total_voters_true, turn, team_id FROM exclusions WHERE game_id='.$list_games['id'].' GROUP BY target_user,turn,team_id');
		while ($list_exclusions=$req_list_exclusions->fetch()){
			//if majority for true is the only possibility
			if (($list_exclusions['total_voters_true']/($list_exclusions['total']+1))>0.5){
				$fire_member=$bdd->exec('UPDATE users SET team_id=NULL WHERE user="'.$list_exclusions['target_user'].'"');
				$insert_notif=$bdd->exec('INSERT INTO notifications(game_id,user,team_id,type) VALUES ('.$list_games['id'].',"'.$list_exclusions['target_user'].'",'.$list_exclusions['team_id'].',"exclusion")');
				$remove_votes=$bdd->exec('DELETE FROM exclusions WHERE target_user="'.$list_exclusions['target_user'].'"');
				$remove_votes=$bdd->exec('DELETE FROM exclusions WHERE voter_user="'.$list_exclusions['target_user'].'"');
				$remove_votes=$bdd->exec('DELETE FROM admissions WHERE voter_user="'.$list_exclusions['target_user'].'"');
			}elseif ((($list_exclusions['total_voters']-$list_exclusions['total_voters_true'])/($list_exclusions['total']+1))>=0.5){
				//if majority for true is the only possibility
				$remove_votes=$bdd->exec('DELETE FROM exclusions WHERE target_user="'.$list_exclusions['target_user'].'"');

			}else{
				if ($list_games['current_turn']>$list_exclusions['turn']){
					$remove_votes=$bdd->exec('DELETE FROM exclusions WHERE target_user="'.$list_exclusions['target_user'].'"');
				}
			}
		}

		//Get all the admissions votes
		$req_list_admissions_user = $bdd->query('SELECT target_user FROM admissions WHERE game_id='.$list_games['id'].' GROUP BY target_user');
		while ($list_admissions_user=$req_list_admissions_user->fetch()){
			$team_assigned=false;
			$req_list_admissions = $bdd->query('SELECT target_user, COUNT(*) as total, COUNT(vote_result) as total_voters, sum(vote_result) as total_voters_true, turn, team_id FROM admissions WHERE game_id='.$list_games['id'].' AND target_user="'.$list_admissions_user['target_user'].'" GROUP BY target_user, team_id, turn ORDER by total,total_voters_true DESC,turn DESC');
			while ($list_admissions=$req_list_admissions->fetch()){
				if ($team_assigned==false){
					//if majority for true is the only possibility
					if (($list_admissions['total_voters_true']/$list_admissions['total'])>=0.5){
						$admit_member=$bdd->exec('UPDATE users SET team_id='.$list_admissions['team_id'].' WHERE user="'.$list_admissions['target_user'].'"');
						$team_assigned=true;
						$insert_notif=$bdd->exec('INSERT INTO notifications(game_id,user,team_id,type) VALUES ('.$list_games['id'].',"'.$list_admissions['target_user'].'",'.$list_admissions['team_id'].',"admission")');
						$remove_votes=$bdd->exec('DELETE FROM admissions WHERE target_user="'.$list_admissions['target_user'].'"');
						//code below: if the member to admit should see his votes removed
						//$remove_votes=$bdd->exec('DELETE FROM admissions WHERE voter_user="'.$list_admissions['target_user'].'"');
					}elseif ((($list_admissions['total_voters']-$list_admissions['total_voters_true'])/$list_admissions['total'])>0.5){
						//if majority for false is the only possibility
						$remove_votes=$bdd->exec('DELETE FROM admissions WHERE target_user="'.$list_admissions['target_user'].'"');

					}else{
						if ($list_games['current_turn']>$list_admissions['turn']){
							if ($list_admissions['total_voters']>0){
								if (($list_admissions['total_voters_true']/$list_admissions['total_voters'])>=0.5){
									$admit_member=$bdd->exec('UPDATE users SET team_id='.$list_admissions['team_id'].' WHERE user="'.$list_admissions['target_user'].'"');
									$team_assigned=true;
								}
							}
							$remove_votes=$bdd->exec('DELETE FROM admissions WHERE target_user="'.$list_admissions['target_user'].'"');
						}
					}
				}
			}
		}

		//Incrementing turn
	  $update_turn=$bdd->prepare('UPDATE games SET current_turn=:new_turn WHERE id=:id');
			if ($list_games['current_turn']==$list_games['turns']){
				$new_turn=-1;
			}else{
				if ($target_reached==true){
					$new_turn=-1;
				}else{
					$new_turn=$list_games['current_turn']+1;
				}
			}
			$update_turn->execute(array(
	    'id' => $list_games['id'],
	    'new_turn' => $new_turn
	    ));
		 }
}

	?>
