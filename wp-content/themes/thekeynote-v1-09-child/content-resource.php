<?php
/**
 * @package GlobMob
 */

global $privateFavorites;
global $publicFavorites;
?>

<div class="resource group">
	<a href="<?php echo get_permalink($post->ID); ?>">
		<?php the_post_thumbnail('resource-thumb', array('class' => 'resource-img framed-img')); ?>
	</a>
	<?php 
	if( empty($post->post_password) && !is_page_template('single-resource.php') )
		$wideformat = false;
	else 
		$wideformat = true;
	?>
<?php if( $wideformat ) : ?>
	<div class="resource-content-wide">
<?php else: ?>
	<div class="resource-content">
<?php endif; ?>
		<h2><a href="<?php globmob_field('find_it_here_1_direct'); ?>" target="_blank"><?php the_title(); ?></a></h2>
		<h5>
			<?php 
			echo globmob_tax('resource_author', false);
	
			$resauthor = wp_get_post_terms( $post->ID, 'resource_author' );
			$pubdate = get_post_meta($post->ID, 'publication_date', true);
			if( !empty($resauthor) && !empty($pubdate) )
				echo ", ";
		
			//globmob_field('publication_date');
			$pubdate = get_post_meta($post->ID, 'publication_date', true);
			if( !empty($pubdate) ) {
				$date = DateTime::createFromFormat("Y-m-d", $pubdate);
				echo $date->format("Y");
			}			
			
			?>
		</h5>
		<div class="resource-post">
			<p><?php the_content(); ?></p>
		</div>
<?php if( !$wideformat ) : ?>	
		<h3>Find it here:</h3>
		<?php globmob_field('find_it_here_1_direct','<p><a href="','" target="_blank">'); globmob_field('find_it_here_1_display','','</a></p>'); ?>
		<?php globmob_field('find_it_here_2_direct','<p><a href="','" target="_blank">'); globmob_field('find_it_here_2_display','','</a></p>'); ?>
		<?php globmob_field('find_it_here_3_direct','<p><a href="','" target="_blank">'); globmob_field('find_it_here_3_display','','</a></p>'); ?>
<?php endif; ?>
	</div>
<?php if( !$wideformat ) : ?>	
	<ul class="resource-stats">
		<li><h3>Region</h3></li>
		<li><?php echo globmob_tax_url('region'); ?></li>
		<li><h3>Mobilization Level</h3></li>
		<li><?php echo globmob_tax_url('mobilizationlevel'); ?></li>
		<li><h3>Language</h3></li>
		<li><?php echo globmob_tax_url('language'); ?></li>
	</ul>
<?php endif; ?>
</div>