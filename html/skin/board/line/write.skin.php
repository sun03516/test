<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
$option = '';
$option_hidden = '';
if ($is_notice || $is_html || $is_secret || $is_mail) {
	$option = '';
	if ($is_notice) {
		// $option .= "\n".'<input type="checkbox" id="notice" name="notice" value="1" '.$notice_checked.'>'."\n".'<label for="notice">공지</label>';
	}

	if ($is_html) {
		if ($is_dhtml_editor) {
			$option_hidden .= '<input type="hidden" value="html1" name="html">';
		} else {
			//$option .= "\n".'<input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="html">html</label>';
		}
	}

	if ($is_secret) {
		if ($is_admin || $is_secret==1) {
			$option .= "\n".'<label for="secret" style="white-space:nowrap;"><input type="checkbox" id="secret" name="secret" value="secret" '.$secret_checked.'>'."\n".'비밀글</label>';
		} else {
			$option_hidden .= '<input type="hidden" name="secret" value="secret">';
		}
	}

	//if ($is_mail) {
	//	$option .= "\n".'<input type="checkbox" id="mail" name="mail" value="mail" '.$recv_email_checked.'>'."\n".'<label for="mail">답변메일받기</label>';
	//}
}

echo $option_hidden;

?> 
<form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="uid" value="<?php echo get_uniqid(); ?>">
	<input type="hidden" name="w" value="<?php echo $w ?>">
	<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	<input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
	<input type="hidden" name="sca" value="<?php echo $sca ?>">
	<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
	<input type="hidden" name="stx" value="<?php echo $stx ?>">
	<input type="hidden" name="spt" value="<?php echo $spt ?>">
	<input type="hidden" name="sst" value="<?php echo $sst ?>">
	<input type="hidden" name="sod" value="<?php echo $sod ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>">
	<input type="hidden" name="wr_subject" value="<?=$board['bo_subject']?>">
	<?= $option_hidden ?> 
	<div class="ui-write-box ui-text-area<?=$w=='u'? " update":"";?>">
		<a href="#" class="write_open ui-btn point">+</a>
		<p> 
		<input type="text" name="wr_content" id="content" class="frm-input full" required value="<?=$content?>">
		<button type="submit" id="btn_submit" class="ui-btn" accesskey='s'>입력</button><?if($w=='u'){?><a href="<?=G5_BBS_URL?>/board.php?bo_table=<?=$bo_table?>" class="ui-btn etc">뒤로</a><?}?>
		<? if(!$is_member){ ?>
			<input type="text" maxlength="20" name="wr_name" id="wr_name" placeholder="이름" required value="<?=$name?>" />
			<input type="password" maxlength="20" id="wr_password" name="wr_password" placeholder="비밀번호" value="<?=$password?>" <?=$password_required?> />
		<? } ?> 
		<?php if ($option) { ?>
			&nbsp;&nbsp;<?php echo $option ?>
		<?php } ?>
		</p>
	</div> 
</form>

<script>
<?php if($write_min || $write_max) { ?>
// 글자수 제한
var char_min = parseInt(<?php echo $write_min; ?>); // 최소
var char_max = parseInt(<?php echo $write_max; ?>); // 최대
check_byte("wr_content", "char_count");

$(function() {
	$("#wr_content").on("keyup", function() {
		check_byte("wr_content", "char_count");
	});
});

<?php } ?>

function fwrite_submit(f)
{
	return true;
}
</script>
