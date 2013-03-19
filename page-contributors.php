<?php
/*
Template Name: Contributor
*/
# -------------------------------------------------------------------------------------- #
# -------------------------------------------------------------------------------------- #
get_header();

$user_args = array(
	'role' => '',
	'orderby' => 'display_name',
	'order' => 'ASC',
	'number' => '',
	'fields' => 'all',
);

$users = get_users( $user_args );
# -------------------------------------------------------------------------------------- #
# -------------------------------------------------------------------------------------- #
?>

<style>
#contributor-container.CONTRIBUTOR-LIST {
	width: 200px;
	margin:181px auto 90px;
}
#contributor-container.CONTRIBUTOR-LIST h3 {
	font-family: RaisonneDemiBold, Helvetica, Arial, sans-serif;
	font-size: 16px;
	text-align: center;
	text-transform: uppercase;
	line-height: 2em;
}
#contributor-container.CONTRIBUTOR-LIST p {
	font-family: RaisonneDemiBold, Helvetica, Arial, sans-serif;
	font-size: 14px;
	display: block;
	text-align: center;
	text-transform: lowercase;
	margin-left: auto;
	margin-right: auto;
	margin-top: 4px;
}
</style>

<div id="contributor-container" class="CONTRIBUTOR-LIST">
<?php
foreach ($users as $user):
	$contributor_type = get_user_meta( $user->ID, 'title', true );
	if ( $contributor_type != null && $contributor_type != '' ): ?>
		<a href="http://bullettmedia.com/?s=<?php echo urlencode( strtolower( $user->data->display_name ) ); ?>" class="contributor">
			<h3><?php echo _e( $user->data->display_name ); ?></h3>
			<p><?php echo _e( $contributor_type ); ?></p>
		</a>
<?php
	endif;
endforeach;
?>
</div>

<?php get_footer(); ?>
