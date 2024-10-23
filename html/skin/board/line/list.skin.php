<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
if($is_admin) set_session("ss_delete_token", $token = uniqid(time()));

if($is_member)  {
	$comment_token = uniqid(time());
	set_session('ss_comment_token', $comment_token);
}

$is_comment_write = false; 

if($board['bo_table_width']==0) $width="100%";
?>


<div id="page_board_content" style="max-width:<?php echo $width; ?>;margin: 0 auto;">

	 
<!-- 상단 공지 부분 -->
<? if($board['bo_content_head']) { ?>
	<div class="board-notice theme-box">
		<?=stripslashes($board['bo_content_head']);?>
	</div>
	<hr class="padding" />
<? } ?>

	<!-- 버튼 링크 -->
	<? if($admin_href){?><div class="adm-box"><a href="<?=$admin_href?>" class="ui-btn admin" target="_blank">관리자</a></div><?}?> 
	
	<div class="ui-memo-list">
	<div class="ui-top theme-box"><? if ($write_href) { 
		?><div class="ui-write-area">
		<? include ($board_skin_path."/write.php"); ?>
		</div><? } 
	?><div class="search-box">
		<form name="fsearch" method="get" style="margin:0px;">
			<input type="hidden" name="bo_table" value="<?=$bo_table?>">
			<input type="hidden" name="sca"      value="<?=$sca?>">
			<input type="hidden" name="sfl" value='wr_subject||wr_content'>
			<input type="hidden" name="sop" value="and">
			
			<input type="text" name="stx" itemname="검색어" value="<?=$stx?>" ><??><button type="submit" class="ui-btn">?</button>
		</form> 
	</div></div>
	
		<ul>
	<? 
		$lists = array();
		for ($i=0; $i<count($list); $i++) { $lists[$i] = $list[$i]; } 
		
		for ($ii=0; $ii < count($lists); $ii++) {
			$profile = get_member($lists[$ii]['mb_id']);
			include "$board_skin_path/inc.list_main.php"; 
			$lists[$ii]['datetime']=substr($lists[$ii]['wr_datetime'],0,4)."/".substr($lists[$ii]['wr_datetime'],5,2)."/".substr($lists[$ii]['wr_datetime'],8,2)." (".substr($lists[$ii]['wr_datetime'],11,8).")";

			$is_open = false;

			if(get_cookie('read_'.$lists[$ii]['wr_id']) == $lists[$ii]['wr_password']) { 
				$is_open = true;
			}

			$lists[$ii]['content'] = conv_content($lists[$ii]['wr_content'], 0, 'wr_content');
			$lists[$ii]['content'] = search_font($stx, $lists[$ii]['content']);
	?>
				<li class="theme-box">
					<form name="fboardlist" method="post" action="<?=$board_skin_url?>/password.php" style="margin:0">
						<input type="hidden" name="bo_table" value="<?=$bo_table?>">
						<input type="hidden" name="sfl"      value="<?=$sfl?>">
						<input type="hidden" name="stx"      value="<?=$stx?>">
						<input type="hidden" name="spt"      value="<?=$spt?>">
						<input type="hidden" name="page"     value="<?=$page?>">
						<input type="hidden" name="wr_idx"     value="<?=$lists[$ii]['wr_id']?>">
						<input type="hidden" name="sw"       value="">
						
						<div class="memo-content content-area">
							<em>
							<?php if ($is_checkbox) { ?>
							<input type="checkbox" name="chk_wr_id[]" value="<?php echo $lists[$ii]['wr_id'] ?>" id="chk_wr_id_<?php echo $ii ?>" class="chk_id">
							<?php } ?>
							</em>
							<? if($lists[$ii]['is_notice']) { ?>
								<strong class="txt-point notice">!</strong>
							<? } else { ?>
								<strong class="txt-point date"><?=date('Y/m/d',strtotime($lists[$ii]['wr_datetime']))?></strong>
							<? } ?> 
							<?
								if(strstr($lists[$ii]['wr_option'], 'secret') && !$is_admin && !$is_open) {  
							?> 
								<a href="#" class="write_open secret ui-btn">***</a><p class="pass_in"><input type="password" name="wr_password" id="wr_password_<?=$ii?>" value="" placeholder="비밀번호"/>
								<button type="submit" class="ui-btn">입력</button></p>
							<? } else { 
								if ($member['mb_level'] >= $board['bo_comment_level'] ) $is_comment_write = true; ?>
							<? if(strstr($lists[$ii]['wr_option'], 'secret')) { ?>
								<span class="txt-point">***</span>&nbsp;&nbsp;
							<? } ?>
								<span class="con">
								<?= $lists[$ii]['content'] ?>
								<? echo $secret_msg; ?>
								</span>
							<? } ?>  
							<p class="control"><? 
							 if($is_comment_write) { 
								?><a href="javascript:comment_wri('comment_write', '<?=$lists[$ii]['wr_id']?>');">+</a><? 
							} if(($member['mb_id'] && ($member['mb_id'] == $lists[$ii]['mb_id'])) || $is_admin) {
								if($update_href){?><a href="<?=$update_href?>">*</a><?}
								?><a href="<?=$delete_href?>">-</a><? 
							} else if (!$lists[$ii]['mb_id']) {
								 ?><a href="<?=$delete_href?>">-</a><?
							 }
							 ?> </p>
						</div>
					</form>
					<? 
						if(strstr($lists[$ii]['wr_option'], 'secret') && !$is_admin && !$is_open) { 
							if($lists[$ii]['wr_comment']==1){?>
							<?}
						} else { 
							$wr_id = $lists[$ii]['wr_id'];
							include($board_skin_path."/view_comment.php"); 
						}
					?>
				</li>
	<?	}  
	?>
	<? if (count($lists) == 0) { echo "<li class='no-data theme-box'>내역이 없습니다.</li>"; } ?>
		</ul> 
<?php if ($is_checkbox) { ?>  
	<div class="bo_fx txt-right"> 
	
		<form name="fchecklist"  id="fchecklist" action="./board_list_update.php" method="post">
		<input type="hidden" name="write_table" value="<?=$write_table?>">
		<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
		<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
		<input type="hidden" name="stx" value="<?php echo $stx ?>">
		<input type="hidden" name="spt" value="<?php echo $spt ?>">
		<input type="hidden" name="sst" value="<?php echo $sst ?>">
		<input type="hidden" name="sod" value="<?php echo $sod ?>">
		<input type="hidden" name="page" value="<?php echo $page ?>">
		<input type="hidden" name="sw" value=""> 
		<input type="hidden" name="btn_submit" value="">
		</form>
		
		<?if($is_checkbox && count($lists)>0){?>
		<span class="chkall"><input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
		</span>
		<?}?>
		<input type="submit" name="btn_submit" value="선택삭제" onclick="select_delete();" class="ui-btn small admin">
		<input type="submit" name="btn_submit" value="선택복사" onclick="select_copy('copy');" class="ui-btn small admin">
		<input type="submit" name="btn_submit" value="선택이동" onclick="select_copy('move');" class="ui-btn small admin">
	</div>
	<?php } ?> 
	</div>
	<!-- 페이지 -->
	<? echo $write_pages;  ?>
</div>

<script >
//if ("<?=$sca?>") document.fcategory.sca.value = "<?=$sca?>";
if ("<?=$stx?>") {
	document.fsearch.sfl.value = "<?=$sfl?>";
	document.fsearch.sop.value = "<?=$sop?>";
}

$(".write_open").click(function(){
	$(this).next().toggleClass("on");
});

function comment_box(co_id, wr_id) { 
	$('.modify_area').hide();
	$('.comment-content').show();

	$('#c_'+co_id).find('.modify_area').show();
	$('#c_'+co_id).find('.comment-content').hide();

	$('#save_co_comment_'+co_id).focus();

	var modify_form = document.getElementById('frm_modify_comment');
	modify_form.wr_id.value = wr_id;
	modify_form.comment_id.value = co_id;
}

function mod_comment(co_id) { 
	var modify_form = document.getElementById('frm_modify_comment');
	var wr_content = $('#save_co_comment_'+co_id).val(); 
	var wr_option = '';  
	modify_form.wr_content.value = wr_content;
	modify_form.wr_option.value = wr_option;
	modify_form.wr_id.value = co_id;
	modify_form.comment_id.value = co_id;
	$('#frm_modify_comment').submit();
} 
</script>

<? if ($is_checkbox) { ?>
<script>

var count=0; 
$('.chk_id').change(function(){  
	if($(this).prop('checked')){ 
		$("#fchecklist").append('<input type="checkbox" id="ck_id_'+$(this).val()+'" name="chk_wr_id[]" class="chkd" value="'+$(this).val()+'" checked style="display:none;">');
		count++;  
	}
	if($(this).prop('checked')==false){
		$('#ck_id_'+$(this).val()).remove();
		count--; 
	}
});

function all_checked(sw) {
	var clen=$('.chk_id').length;
	$('.chk_id').prop('checked',sw); 
	if(sw==true){
		for(i=0;i<clen;i++){
			$("#fchecklist").append('<input type="checkbox" id="ck_id_'+$('.chk_id').eq(i).val()+'" class="chkd" name="chk_wr_id[]" value="'+$('.chk_id').eq(i).val()+'" checked style="display:none;">');
			count++; 
		}
	 
	}else{
		$('.chkd').remove();
		count--; 
	}
}

function check_confirm(str)
{
	var f = $('.chkd');
	var chk_count = 0;

	for (var i=0; i<f.length; i++) {
		if (f.prop("checked")){
			chk_count++; 
		}
	}

	if (!chk_count) {
		alert(str + "할 게시물을 하나 이상 선택하세요.");
		return false;
	}
	return true;
}

// 선택한 게시물 삭제
function select_delete()
{
	var f = document.fchecklist; 

	str = "삭제";
	if (!check_confirm(str))
		return;

	if (!confirm("선택한 게시물을 정말 "+str+" 하시겠습니까?\n\n한번 "+str+"한 자료는 복구할 수 없습니다"))
		return; 
	f.btn_submit.value="선택삭제";
	f.removeAttribute("target");
	f.action = "./board_list_update.php";
	f.submit();
}

// 선택한 게시물 복사 및 이동
function select_copy(sw)
{
	var f = document.fchecklist; 

	if (sw == "copy")
		str = "복사";
	else
		str = "이동";

	if (!check_confirm(str))
		return;

	var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

	f.sw.value = sw;
	f.btn_submit.vaule="선택"+str;
	f.target = "move";
	f.action = "./move.php";
	f.submit();
}
</script>
<? } ?>
<form name="modify_comment" id="frm_modify_comment"  action="./write_comment_update.php" method="post" autocomplete="off">
	<input type="hidden" name="w" value="cu">
	<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	<input type="hidden" name="sca" value="<?php echo $sca ?>">
	<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
	<input type="hidden" name="stx" value="<?php echo $stx ?>">
	<input type="hidden" name="spt" value="<?php echo $spt ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>">

	<input type="hidden" name="comment_id" value="">
	<input type="hidden" name="wr_id" value="">
	<input type="hidden" name="wr_option" value="" >
	<textarea name="wr_content" style="display: none;"></textarea>
	<button type="submit" style="display: none;"></button>
</form>