<?php 
// [swiper_slider category_id=123 number=10 items=3 format="post"]
class SwiperSlider {
	public static function render($attr) {
		$attr = shortcode_atts(['category_id' => 123, 'number' => 30, 'items' => 3, 'format' => 'post'], $attr, 'swiper_slider');
		// $attr['category_id'] = 331;
		return self::HTML($attr);
	}
	static function posts($attr) {
		$data = [];
		$query = ['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $attr['number'], 'cat' => $attr['category_id']];
		$posts = new WP_Query($query);
	    // $posts = $posts->found_posts;
	    $posts = $posts->posts;
		return $posts;
	}
	static function HTML($attr) {
		$html = '';
		$posts = self::posts($attr);
		// return '<br><pre>'. print_r($posts, true) .'</pre>';
		if (!empty($posts)) {
			$html .= '<div style="width:100%; height: 180px;">';
				$html .= self::HTMLHeader($attr);
				$html .= '<div class="swiper-container-'.$attr['category_id'].'">';
				    $html .= '<div class="swiper-wrapper">';
				    	foreach ($posts as $post) {
				    		$thumb = get_the_post_thumbnail_url($post);
				    		$link = esc_url(home_url()).'/'.$post->post_name;
				    		
				    		$newbadge_title=get_post_meta($post->ID,'newbadge_title',true);
                            $newbadge_title=($newbadge_title!="")? $newbadge_title : '';
                            $newbadge_color=get_post_meta($post->ID,'newbadge_color',true);  
                            $tmpl_display_badge = '';

                            if($newbadge_title!='') $tmpl_display_badge = '<span class="badge-status" style="background:'. $newbadge_color.'">'. $newbadge_title.'</span>&nbsp;';
				        	$html .= '<div class="swiper-slide">';
				    			$html .= '<a href="'.$link.'">';
					    		$html .= '<img src="'.$thumb.'" class="swiperThumb">';
				                $html .= '<div class="swiperTitle">'.$post->post_title.' <div class="swiperDate">'. date('d M Y', strtotime($post->post_date)).' '.$tmpl_display_badge.'</div></div>';
				                $html .= '<div class="swiperExcerpt">'.$post->post_excerpt.'</div>';
				                // $html .= '<div class="swiperDate">'. date('d M Y', strtotime($post->post_date)).'</div>';
				    			$html .= '</a>';
				        	$html .= '</div>';
				    	}
				    $html .= '</div>';
				    $html .= '<div class="swiper-pagination-'.$attr['category_id'].'"></div>';
				$html .= '</div>';
				$html .= self::HTMLFooter($attr);
			$html .= '</div>';
		}
		return $html;
	}
	static function HTMLFooter($attr) {
		$html = '';
		$html .= '<script src="https://unpkg.com/swiper/js/swiper.min.js"></script>';
		$html .= '<!-- Initialize Swiper -->';
		$html .= '<script>';
		$html .= '(function($) {';
			$html .= ' var swiper = new Swiper(".swiper-container-'.$attr['category_id'].'", {';
			    $html .= ' slidesPerView: 1.25,';
			    $html .= ' spaceBetween: 10,';
			    $html .= ' freeMode: true,';
			    $html .= ' pagination: {';
			        $html .= ' el: ".swiper-pagination-'.$attr['category_id'].'",';
			        $html .= ' clickable: true,';
			    $html .= ' },';
			    $html .= ' breakpoints: {';
			    $html .= ' 320: {slidesPerView: 1.25, spaceBetween: 10, },';
			    $html .= ' 640: {slidesPerView: 2.25, spaceBetween: 10, },';
			    $html .= ' 768: {slidesPerView: 3.25, spaceBetween: 10, },';
			    $html .= ' 1024: {slidesPerView: 3.25, spaceBetween: 10, },';
			    $html .= ' },';
			$html .= ' });';
		$html .= '})(jQuery);';
		$html .= '</script>';
		return $html;
	}
	static function HTMLHeader($attr) {
		$html = '';
		$html .= '<link rel="stylesheet" href="https://unpkg.com/swiper/css/swiper.min.css">';
		$html .= '<style>';
		    $html .= ' .swiper-container-'.$attr['category_id'].' {width: 100%; height: 100%;}';
		    $html .= ' .swiper-slide {background: #fff}';
		    $html .= ' .swiper-slide a {display: block}';
		    $html .= ' .swiperTitle {padding: 0 10px; font-weight: bold;height:40px;overflow:hidden;}';
		    $html .= ' .swiperExcerpt {padding: 0 10px;}';
		$html .= '</style>';
		return $html;
	}
}
add_shortcode('swiper_slider', ['SwiperSlider', 'render']);