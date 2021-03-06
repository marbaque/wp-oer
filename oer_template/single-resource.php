<?php
/**
 * The Template for displaying all single resource
 */

/** Add default stylesheet for Resource page **/
wp_register_style( "resource-styles", OER_URL . "css/resource-style.css" );
wp_enqueue_style( "resource-styles" );

get_header();

//Add this hack to display top nav and head section on Eleganto theme
$cur_theme = wp_get_theme();
$theme = $cur_theme->get('Name');
if ($theme == "Eleganto"){
	get_template_part( 'template-part', 'topnav' );
	get_template_part( 'template-part', 'head' );
}

global $post;
global $wpdb, $_oer_prefix;

$url = get_post_meta($post->ID, "oer_resourceurl", true);
$url_domain = oer_getDomainFromUrl($url);

$youtube = oer_is_youtube_url($url);
$isSSLResource = oer_is_sll_resource($url);
$isSLLCollection = oer_is_sll_collection($url);
$isPDF = is_pdf_resource($url);
$isExternal = is_external_url($url);

$hide_title = get_option('oer_hide_resource_title');

// Resource Subject Areas
$subject_areas = array();
$post_terms = get_the_terms( $post->ID, 'resource-subject-area' );

if(!empty($post_terms))
{
	$subjects = array();
	foreach($post_terms as $term)
	{
		if($term->parent != 0)
		{
			$parent[] = oer_get_parent_term_list($term->term_id);
			$subjects[] = $term;
		}
		else
		{
			$subject_areas[] = $term;
		}
	}
	
	if(!empty($parent) && array_filter($parent))
	{
		$recur_multi_dimen_arr_obj =  new RecursiveArrayIterator($parent);
		$recur_flat_arr_obj =  new RecursiveIteratorIterator($recur_multi_dimen_arr_obj);
		$flat_arr = iterator_to_array($recur_flat_arr_obj, false);

		$flat_arr = array_values(array_unique($flat_arr));
		
		for($k=0; $k < count($flat_arr); $k++)
		{
			//$idObj = get_category_by_slug($flat_arr[$k]);
			$idObj = get_term_by( 'slug' , $flat_arr[$k] , 'resource-subject-area' );
			
			if(!empty($idObj->name))
				$subject_areas[] = $idObj;
		}
	}
	if (count($subjects)>0)
		$subject_areas = array_merge($subject_areas,$subjects);
}
$embed_disabled = false;
?>
<!--<div id="primary" class="content-area">-->
    <main id="oer_main" class="site-main" role="main">

    <article id="oer-resource-<?php the_ID(); ?>" class="oer_sngl_resource_wrapper post-content">
        <div id="sngl-resource" class="entry-content oer-cntnr post-content oer_sngl_resource_wrapper row">
	<?php if (!$hide_title): ?>
        <header class="entry-header">
            <h1 class="entry-title"><?php echo $post->post_title;?></h1>
        </header>
	<?php endif; ?>
    	
	<?php
	if ($youtube || $isSSLResource || $isSLLCollection)
		include(OER_PATH.'oer_template/single-resource-youtube.php');
	else
		include(OER_PATH.'oer_template/single-resource-standard.php');
	?>

        </div><!-- .single resource wrapper -->

    </article>
</main>
<!--</div>-->

<?php
if ($theme == "Eleganto"){
	get_template_part( 'template-part', 'footernav' );
}

function display_default_thumbnail($post){
	$html = '<a class="oer-featureimg" href="'.esc_url(get_post_meta($post->ID, "oer_resourceurl", true)).'" target="_blank" >';
		$img_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) , "full" );
		$img_path = $new_img_path = parse_url($img_url[0]);
		$img_path = $_SERVER['DOCUMENT_ROOT'] . $img_path['path'];
		$new_image_url = OER_URL.'images/default-icon-528x455.png';
		$img_width = oer_get_image_width('large');
		$img_height = oer_get_image_height('large');
		
	if(!empty($img_url))
	{
		if ( is_wp_error($img_url) ) {
			debug_log("Can't get Image editor to resize Resource screenshot.");
		} else {
			$new_image_url = oer_resize_image($img_url[0], $img_width, $img_height, true);
		}
	}
	
	$html .= '<img src="'.esc_url($new_image_url).'" alt="'.esc_attr(get_the_title()).'"/>';

	$html .= '</a>';
	return $html;
}
function get_embed_code($url){
	$embed_code = '<iframe class="oer-pdf-viewer" width="100%" src="'.$url.'"></iframe>';
	return $embed_code;
}

get_footer();
?>
