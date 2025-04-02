<div class="entry-comment-count">
	<strong><?php comments_popup_link( '0', '1', '%', 'comments-link', 'Off'); ?></strong>
	<span>
		<?php
		$num_comments = get_comments_number(); 
		if($num_comments > '1' ) {
			echo "comments";
		} else {
			echo "comment";
		}
		?>
	</span>
</div><!-- .entry-comment-count -->