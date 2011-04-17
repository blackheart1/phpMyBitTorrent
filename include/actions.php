<?php
function getlastaction($wht){
        global $db, $db_prefix, $user;
		$wht = htmlspecialchars_decode(stripslashes($wht));
		$wht = str_replace('pmbt','',$wht);
		//return $wht;
	preg_match('#^([a-z/_]+)#i', $wht, $on_page);
	if (!sizeof($on_page))
	{
		$on_page[1] = '';
	}

	switch ($on_page[1])
	{
		case 'index':
		$where = 'Viewing Index';
		break;
		case 'forums':
    		preg_match('#action=([a-z]+)#', $wht, $on_page);
             $forum_mode = (!empty($on_page[1])) ? $on_page[1] : '';
						switch ($forum_mode)
						{
							case 'newtopic':
                        		preg_match('#forumid=([0-9]+)#', $wht, $on_page);
                                $forum_id_num = (!empty($on_page[1])) ? $on_page[1] : '';
								if($forum_id_num == '')
								{
								$where = 'Viewing ERROR';
								break;
								}
								    $res = $db->sql_query("SELECT * FROM ".$db_prefix."_forum_forums WHERE id=$forum_id_num");
								    $arr = $db->sql_fetchrow($res);
								    $forum = stripslashes($arr["name"]);
								    $where = 'Creating Thread IN '.$forum;
								    break;
							case 'viewtopic':
                        		preg_match('#topicid=([0-9]+)#', $wht, $on_page);
                                $topic_id_num = (!empty($on_page[1])) ? $on_page[1] : '';
								if($topic_id_num == '')
								{
								$where = 'Viewing ERROR';
								break;
								}
								    $res = $db->sql_query("SELECT * FROM ".$db_prefix."_forum_topics WHERE id=$topic_id_num");
								    $arr = $db->sql_fetchrow($res);
								    $forum_subject = stripslashes($arr["subject"]);
								    $where = 'Viewing Thread '.$forum_subject;
								    break;
							case 'viewforum':
                        		preg_match('#forumid=([0-9]+)#', $wht, $on_page);
                                $forum_id_num = (!empty($on_page[1])) ? $on_page[1] : '';
								if($forum_id_num == '')
								{
								$where = 'Viewing ERROR';
								break;
								}
								    $res = $db->sql_query("SELECT * FROM ".$db_prefix."_forum_forums WHERE id=$forum_id_num");
								    $arr = $db->sql_fetchrow($res);
								    $forum = stripslashes($arr["name"]);
								    $where = 'Viewing Forum '.$forum;
								    break;
							case 'editpost':
                        		preg_match('#postid=([0-9]+)#', $wht, $on_page);
                                $topic_id_num = (!empty($on_page[1])) ? $on_page[1] : '';
								if($topic_id_num == '')
								{
								$where = 'Viewing ERROR';
								break;
								}
								    $res = $db->sql_query("SELECT * FROM ".$db_prefix."_forum_posts WHERE id=$topic_id_num");
								    $arr = $db->sql_fetchrow($res);
								    $top_ed = stripslashes($arr["topicid"]);
								    $res = $db->sql_query("SELECT * FROM ".$db_prefix."_forum_topics WHERE id=$top_ed");
								    $arr = $db->sql_fetchrow($res);
								    $forum_subject = stripslashes($arr["subject"]);
								    $where = 'Editing Posts In '.$forum_subject;
								    break;
							case 'viewunread':
								    $where = 'Viewing Unread Posts';
								    break;
							case 'search':
							$search_w = '';
							preg_match('#search_id=([0-9]+)#', $wht, $on_page);
							if(!empty($on_page[1]))
							{
							$search_w = "Posts By ".username_is($on_page[1]);
							}
							preg_match('#keywords=([a-z A-Z 0-9 +]+)#', $wht, $on_page);
							if(!empty($on_page[1]))
							{
							$search_w = "For ".str_replace("+"," ",$on_page[1]);
							}
								    $where = 'Searching Forum '.$search_w;
								    break;
							default:
							$where = 'Viewing Forum';
							break;
						}
			break;
		case 'admin':
		$where = 'Viewing Admin Panel';
		break;
		case 'torrents':
							preg_match('#cat=([0-9]+)#', $wht, $on_page);
							$cat_nam = '';
							if(!empty($on_page[1]))
							{
							$sql = "SELECT name FROM `".$db_prefix."_categories` WHERE `id` = '".$on_page[1]."' LIMIT 1 ";
                			$res = $db->sql_query($sql) or btsqlerror($sql);
                			$row = $db->sql_fetchrow($res);
							$cat_nam = " By Category ".$row['name'];
							}
							preg_match('#page=([0-9]+)#', $wht, $on_page);
							$page = '';
							if(!empty($on_page[1]))
							{
							$page = " On Page ".$on_page[1];
							}
		$where = 'Viewing Torrent List'.$cat_nam.$page;
		break;
		case 'details':
							preg_match('#id=([0-9]+)#', $wht, $on_page);
							$t_name = '';
							if(!empty($on_page[1]))
							{
							$sql = "SELECT name FROM `".$db_prefix."_torrents` WHERE `id` = '".$on_page[1]."' LIMIT 1 ";
                			$res = $db->sql_query($sql) or btsqlerror($sql);
                			$row = $db->sql_fetchrow($res);
							$t_name = stripslashes($row['name']);
							}
		$where = 'Viewing Torrent '.$t_name;
		break;
		case 'pm':
			 preg_match('#op=([a-z]+)#', $wht, $on_page);
             $op_pm = (!empty($on_page[1])) ? $on_page[1] : '';
						switch ($op_pm)
						{
						case 'readmsg':
						      preg_match('#mid=([0-9]+)#', $wht, $on_page);
							  $rd_ms = (!empty($on_page[1])) ? $on_page[1] : '';
							  break;
						case 'send':
						      preg_match('#mid=([0-9]+)#', $wht, $on_page);
						      preg_match('#to=([0-9]+)#', $wht, $on_page1);
						      preg_match('#replyto=([0-9]+)#', $wht, $on_page2);
							  $rd_ms = (!empty($on_page[1])) ? $on_page[1] : ((!empty($on_page2[1])) ? $on_page2[1] : '');
							       if($rd_ms != ''){
								   $sql = "SELECT P.*, IF (U.name IS NULL, U.username, U.name) AS recipient_name, U.avatar AS avatar FROM ".$db_prefix."_private_messages P LEFT JOIN ".$db_prefix."_users U ON P.recipient = U.id LEFT JOIN ".$db_prefix."_private_messages_bookmarks M ON M.slave = P.recipient AND M.master = P.sender LEFT JOIN ".$db_prefix."_private_messages_blacklist B ON B.slave = P.recipient AND B.master = P.sender WHERE P.sender = '".$user->id."' AND P.id = '".$rd_ms."';";
		                                $res = $db->sql_query($sql);
		                           		$row = $db->sql_fetchrow($res);
		                           		$db->sql_freeresult($res);
										$where = "Sending Private Messages<br /><a href=\"user.php?op=profile&id=".$row["recipient"]."\">".$row["recipient_name"]."</a>".$rd_ms;
								   break;
								   }
							  $rd_ms = (!empty($on_page1[1])) ? $on_page1[1] : '';
							       if($rd_ms != ''){
								   $sql = "SELECT can_do as can_do, IF (name IS NULL, username, name) AS name FROM ".$db_prefix."_users WHERE id = '".$rd_ms."';";
		                                $res = $db->sql_query($sql);
		                           		$row = $db->sql_fetchrow($res);
		                           		$db->sql_freeresult($res);
										$where = "Sending Private Messages<br /><a href=\"user.php?op=profile&id=".$rd_ms."\"><font color=\"".getusercolor($row["can_do"])."\">".htmlspecialchars($row['name'])."</font></a>";
								   break;
								   }
		                default:
						$where = 'Viewing Private Messages';
		                break;
		                }
		break;
		case 'slove':
		$where = 'Viewing help ticket in help desk';
		break;
		case 'problems':
		$where = 'Viewing help problems in help desk';
		break;
		case 'helpdesk':
		$where = 'Viewing help desk';
		break;
		case 'redirect':
		$where = 'Being Redirected';
		break;
		case 'polls':
		$where = 'Viewing Polls list';
		break;
		case 'polloverview':
		$where = 'Viewing Poll Over View';
		break;
		case 'makepoll':
		$where = 'Making A New Poll';
		break;
		case 'httperror':
		$where = 'Playing Keno';
		break;
		case 'invite':
		$where = 'Inviting A frend to Join Us';
		break;
		case 'keno':
		$where = 'Playing Keno';
		break;
		case 'casino':
		$where = 'Playing';
		break;
		case 'blackjack':
		$where = 'Playing';
		break;
		case 'arcade':
		$where = 'Playing';
		break;
		case 'flash':
		$where = 'Playing';
		break;
		case 'flashscores':
		$where = 'Viewing Scores';
		break;
		case 'flashscores2':
		$where = 'Viewing Advanced Scores';
		break;
		case 'chat':
		$where = 'In IRC Chat';
		break;
		case 'donate':
		$where = 'Viewing Donation Page';
		break;
		case 'edit':
		$where = 'Editting Torrent';
		break;
		case 'user':
		$where = 'User Panel';
		break;
		case 'games':
		$where = 'Viewing Games';
		break;
		case 'faq':
		$where = 'Viewing FAQ\'S';
		break;
		case 'upload':
		$where = 'Uploading';
		break;
		case 'mytorrents':
		$where = 'Viewing Ther Torrents';
		break;
		case 'offer':
		$where = 'Making A Offer';
		break;
		case 'offers':
		$where = 'Viewing Offers';
		break;
		case 'requests':
		$where = 'Making A Requests';
		break;
		case 'viewrequests':
		$where = 'Viewing Requests';
		break;
		default:
		$where = 'Viewing Index';
		break;
	}
		return $where;
}

?>