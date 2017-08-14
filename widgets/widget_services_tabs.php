<?php

class Nictitate_Toolkit_Widget_Services_Tabs extends Kopa_Widget {

	public function __construct() {

		$this->widget_cssclass    = '';
		$this->widget_description = esc_html__( 'Display a services tabs widget.', 'nictitate-toolkit' );
		$this->widget_id          = 'kopa_widget_services_tabs';
		$this->widget_name        = esc_html__( 'Nictitate - Services Tabs', 'nictitate-toolkit' );
		$this->settings = array(
			'title' => array(
				'type' => 'text',
				'std' => '',
				'label' => esc_html__( 'Title:', 'nictitate-toolkit' ),
				)
			);
		parent::__construct();
	}

	function form($instance) {
		$this->edit_settings();
		parent::form($instance);
	}

	function update($new_instance, $old_instance) {
		$this->edit_settings();
		return parent::update($new_instance, $old_instance);
	}

	function edit_settings(){
		$all_cats = get_terms('service_category');
		$categories = array('' => esc_html__('-- None --', 'nictitate-toolkit'));
		foreach ( $all_cats as $cat ) {
			$categories[ $cat->slug ] = $cat->name;
		}

		$all_tags = get_terms('service_tag');
		$tags = array('' => esc_html__('-- None --', 'nictitate-toolkit'));
		foreach( $all_tags as $tag ) {
			$tags[ $tag->slug ] = $tag->name;
		}

		$this->settings['categories'] = array(
			'type'    => 'multiselect',
			'std'     => '',
			'label'   => esc_html__( 'Categories:', 'nictitate-toolkit' ),
			'options' => $categories,
			'size'    => '5',
		);

		$this->settings['relation']    = array(
			'type'    => 'select',
			'label'   => esc_html__( 'Relation:', 'nictitate-toolkit' ),
			'std'     => 'OR',
			'options' => array(
				'AND' => esc_html__( 'AND', 'nictitate-toolkit' ),
				'OR'  => esc_html__( 'OR', 'nictitate-toolkit' ),
			),
		);

		$this->settings['tags'] = array(
			'type'    => 'multiselect',
			'std'     => '',
			'label'   => esc_html__( 'Tags:', 'nictitate-toolkit' ),
			'options' => $tags,
			'size'    => '5',
		);

		$this->settings['orderby'] = array(
			'type'  => 'select',
			'std'   => 'date',
			'label' => esc_html__( 'Orderby:', 'nictitate-toolkit' ),
			'options' => array(
				'date'         => esc_html__( 'Date', 'nictitate-toolkit' ),
				'random'       => esc_html__( 'Random', 'nictitate-toolkit' ),
			),
		);

		$this->settings['posts_per_page'] = array(
			'type'    => 'number',
			'std'     => '5',
			'label'   => esc_html__( 'Number of posts:', 'nictitate-toolkit' ),
			'min'     => '1',
		);

	}

	public function widget( $args, $instance ) {	

		extract( $args );
		
		extract( $instance );

		$instance['post_type'] = 'services';
		$instance['cat_name']  = 'service_category';
		$instance['tag_name']  = 'service_tag';

		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

		$services = nictitate_toolkit_widget_posttype_build_query($instance);
		
		echo wp_kses_post( $before_widget );

        if ( ! empty ( $title ) )
            echo sprintf( '%s', $before_title . '<span data-icon="&#xf013;"></span>' . $title . $after_title );  
        ?>

        <div class="list-container-2">
            <ul class="tabs-2 clearfix">

        <?php while ( $services->have_posts() ) :
            $services->the_post();
            // initialize & reset for each loop
            $icon_class = '';

            $icon_class = get_post_meta(get_the_ID(), 'icon_class', true);

            if ( $icon_class ) {
                $icon_class = 'fa fa-' . $icon_class;
            }
        ?>
            <li><a href="#<?php echo esc_attr( $this->get_field_id( 'tab' ) . get_the_ID() ); ?>" class="clearfix"><span class="<?php echo esc_attr( $icon_class ); ?>"></span><?php the_title(); ?></a></li>
        <?php endwhile; ?>
            
            </ul><!--tabs-2-->
        </div>

        <div class="tab-container-2">
            <?php while ($services->have_posts()) : $services->the_post(); ?>
                <div class="tab-content-2" 
                    id="<?php echo esc_attr( $this->get_field_id( 'tab' ) . get_the_ID() ); ?>">                        
                    <p><?php echo strip_tags( get_the_content() ); ?></p>
                </div><!--tab-content-2-->       
            <?php endwhile; ?>    
        </div>

        <?php wp_reset_postdata();

		echo wp_kses_post( $after_widget );
			
	}

}
register_widget( 'Nictitate_Toolkit_Widget_Services_Tabs' );