<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style>
	#wpcontent {padding-left: 0}
	.cvb-close-btn1 {display: none !important}
	.loading {
		background: url(<?php echo site_url( '/wp-includes/images/spinner.gif' )?>) center no-repeat;
		min-height: 300px;
	}
	.intro {margin-top: 50px; font-size: 16px; line-height: 25px;}
	.intro a {text-decoration: underline;}
</style>

<div class="text-center intro">
	You can click the "Copy" button below and paste into the Block Editor of your posts, pages (<a href="https://contentviewspro.com/documentation/article/how-to-copy-a-block-pattern-template/" target="_blank">read more</a>).<br>
	Or in the Block Editor, click the "Content Views Library" button at the top toolbar, explore, then click "Import" (<a href="https://contentviewspro.com/documentation/article/how-to-use-prebuilt-patterns/" target="_blank">read more</a>).<br>
</div>
<div id="cv-block-library-page">
	<div class="loading"></div>
</div>