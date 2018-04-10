<?php
/*
 * Template Name: Substandard Page Template
 */
add_filter( 'body_class','standards_body_classes' );
function standards_body_classes( $classes ) {
 
    $classes[] = 'substandards-template';
     
    return $classes;
     
}

get_header();

global $wp_query;

$standard_name_slug = $wp_query->query_vars['substandard'];
$standard = get_substandard_by_slug($standard_name_slug);

$parent_id = 0;
if (strpos($standard->parent_id,"core_standards")!==false){
    $pIds = explode("-",$standard->parent_id);
    if (count($pIds)>1)
	$parent_id=(int)$pIds[1];
    
    $core_standard = get_standard_by_id($parent_id);
} else {
    $core_standard = get_corestandard_by_standard($standard->parent_id);
}

$sub_standards = get_substandards($standard->id, false);
$notations = get_standard_notations($standard->id);
?>
<div class="oer-cntnr">
	<section id="primary" class="site-content">
		<div id="content" role="main">
		    <div class="oer-allftrdrsrc">
			<div class="oer-snglrsrchdng"><?php printf(__("Browse %s", OER_SLUG), $core_standard->standard_name); ?></div>
			<div class="oer-allftrdrsrccntr">
			    <ul class="oer-standard">
				<li><?php echo $standard->standard_title; ?>
				    <?php if ($sub_standards) {  ?>
				    <ul class="oer-substandards">
					<?php foreach($sub_standards as $sub_standard) {
					     $cnt = get_resource_count_by_substandard($sub_standard->id);
					    $slug = "resource/standards/".sanitize_title($core_standard->standard_name)."/".sanitize_title($sub_standard->standard_title);
					?>
					<li><a href="<?php echo home_url($slug); ?>"><?php echo $sub_standard->standard_title; ?></a> <span class="res-count"><?php echo $cnt; ?></span></li>
					<?php } ?>
				    </ul>
				    <?php } ?>
				    <?php if ($notations) {  ?>
				    <ul class="oer-notations">
					<?php foreach($notations as $notation) {
					    $cnt = get_resource_count_by_notation($notation->id);
					    $slug = "resource/standards/".sanitize_title($core_standard->standard_name)."/".$standard_name_slug."/".$notation->standard_notation;
					?>
					<li><a href="<?php echo home_url($slug); ?>"><strong><?php echo $notation->standard_notation; ?></strong> <?php echo $notation->description; ?></a> <span class="res-count"><?php echo $cnt; ?></span></li>
					<?php } ?>
				    </ul>
				    <?php } ?>
				</li>
			    </ul>
			</div>
		    </div>
		</div><!-- #content -->
	</section><!-- #primary -->
</div>
<?php
get_footer();
?>