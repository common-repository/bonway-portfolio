<?php
/**
 * Returns meta-information based on the provided identifier-value
 * @method bonwaybsp_select_meta
 * @param  string     $value Identifier for the requested block
 */
function bonwaybsp_select_meta($value) {
    $metaArgs = array(
        'post_type'     => 'bonway-portfolio',
        'meta_query'    => array(
            array(
                'key'   => 'bsp-identifier',
                'value' => $value
           )
       )
   );

   return new WP_Query($metaArgs);
}

/**
  * Adds a meta box to the post editing screen
  * @method bonway_portfolio_meta_box
  */
function bonway_portfolio_meta_box() {
    $metaBoxes = array(
        'bonway_portfolio_general' => array('label' => 'General', 'function' => 'bonway_portfolio_meta_fields'),
        'bonway_portfolio_readmore' => array('label' => 'Read more', 'function' => 'bonway_portfolio_meta_readmore'),
        'bonway_portfolio_colours' => array('label' => 'Colours', 'function' => 'bonway_portfolio_meta_colours'),
        'bonway_portfolio_banner' => array('label' => 'Banner', 'function' => 'bonway_portfolio_meta_banner')
    );

    foreach($metaBoxes as $key => $value) {
        $id = $key;
        $label = ''; 
        $function = '';

        if(is_array($value)) {
            foreach($value as $key => $val) {
                switch($key) {
                    case 'label':
                        $label = $val;
                    break;
                    case 'function':
                        $function = $val;
                    break;
                }
            }
        }

        add_meta_box(
            $id,
            $label,
            $function,
            'bonway-portfolio'
        );

        $filter_name = 'postbox_classes_bonway-portfolio_' . $id;
        add_filter( $filter_name , 'bonwaybsp_add_metabox_class' );
    }
}
add_action('add_meta_boxes', 'bonway_portfolio_meta_box');

/**
* Outputs the content of the meta box
* @method bonway_portfolio_meta_fields
* @param  Object                $post The post being used
*/
function bonway_portfolio_meta_fields($post) {
    $bonway_portfolio_meta = get_post_meta($post->ID);
    ?>

    <div class="bsp-section bsp-section__general">
       <div class="bsp-section__inner">
           <div class="bsp-section__container">
               <span>Block Identifier <span class="required">*</span></span>
               <input type="text" name="bsp-identifier" id="bsp-identifier" required value="<?php if (isset($bonway_portfolio_meta['bsp-identifier'])) echo $bonway_portfolio_meta['bsp-identifier'][0]; ?>" />
           </div>
           <div class="bsp-section__container">
               <span>Block Class</span>
               <input type="text" name="bsp-class" id="bsp-class" value="<?php if (isset($bonway_portfolio_meta['bsp-class'])) echo $bonway_portfolio_meta['bsp-class'][0]; ?>" />
           </div>
           <div class="bsp-section__container">
               <span>Item width</span>
               <?php 
                    $widths = array(
                        "0" => array("label" => "25%", "value" => "quart"),
                        "1" => array("label" => "33%", "value" => "third"),
                        "2" => array("label" => "50%", "value" => "half"),
                        "3" => array("label" => "66%", "value" => "twothird"),
                        "4" => array("label" => "75%", "value" => "threequart"),
                        "5" => array("label" => "100%", "value" => "full")
                    );
                    $width = (isset($bonway_portfolio_meta['bsp-width'])) ? $bonway_portfolio_meta['bsp-width'][0] : "centre";
               ?>
               <select class="bsp-width" name="bsp-width" id="bsp-width">
                    <?php 
                        foreach($widths as $key => $val) {
                            $selected = ($width == $val['value']) ? "selected" : "";
                            echo "<option value='" . $val['value'] . "'" . $selected .">" . $val['label'] . "</option>";
                        }
                    ?>
                </select>
           </div>
       </div>
   </div>

   <?php
}

/**
* Outputs the content of the meta box
* @method bonway_portfolio_meta_readmore
* @param  Object                $post The post being used
*/
function bonway_portfolio_meta_readmore($post) {
    $bonway_portfolio_meta = get_post_meta($post->ID);
    $use_readmore = true;
    $external_link = false;
    if(isset($bonway_portfolio_meta['bsp-readmore'][0])) {
        if($bonway_portfolio_meta['bsp-readmore'][0] == "used") {
            $use_readmore = true;
        } else {
            $use_readmore = false;
        }
    }
    if(isset($bonway_portfolio_meta['bsp-external-link'][0])) {
        if($bonway_portfolio_meta['bsp-external-link'][0] == "used") {
            $external_link = true;
        } else {
            $external_link = false;
        }
    }

    $additional_class = ($use_readmore) ? "" : "hide";
    $additional_input_class = ($use_readmore) ? "" : "readonly";
    ?>

    <div class="bsp-section bsp-section__general">
       <div class="bsp-section__inner">
           <div class="bsp-section__container no-space">
               <span class="no-lineheight">Use read more</span>
               <input type="checkbox" name="bsp-readmore" id="bsp-readmore" class="bsp-readmore js-readmore" value="used" <?= ($use_readmore) ? "checked" : "" ?> />
           </div>
           <div class="bsp-section__container no-space">
               <span class="no-lineheight">Link to external site</span>
               <input type="checkbox" name="bsp-external-link" id="bsp-external-link" class="bsp-external-link js-readmore-field <?= $additional_input_class; ?>" value="used" <?= ($external_link) ? "checked" : "" ?> />
           </div>
           <div class="bsp-section__container">
               <span>Page link <span class="required js-readmore-req <?= $additional_class; ?>">*</span></span>
               <input type="text" name="bsp-pageurl" id="bsp-pageurl" class="js-readmore-field <?= $additional_input_class; ?>" <?= ($use_readmore) ? "required" : ""; ?> value="<?php if (isset($bonway_portfolio_meta['bsp-pageurl'])) echo $bonway_portfolio_meta['bsp-pageurl'][0]; ?>" />
           </div>
           <div class="bsp-section__container">
               <span>Max characters <span class="required js-readmore-req <?= $additional_class; ?>">*</span></span>
               <input type="number" min=128 max=512 name="bsp-maxchars" id="bsp-maxchars" class="js-readmore-field <?= $additional_input_class; ?>" <?= ($use_readmore) ? "required" : ""; ?> value="<?php echo (isset($bonway_portfolio_meta['bsp-maxchars'])) ? $bonway_portfolio_meta['bsp-maxchars'][0] :  "128"; ?>" />
           </div>
        </div>
    </div>

    <?php
}

/**
* Outputs the content of the meta box
* @method bonway_portfolio_meta_colours
* @param  Object                $post The post being used
*/
function bonway_portfolio_meta_colours($post) {
    $bonway_portfolio_meta = get_post_meta($post->ID);
    ?>
   <div class="bsp-section bsp-section__colours middle-box">
       <div class="bsp-section__inner">
           <?php 
               $textColour = (isset($bonway_portfolio_meta['bsp-text-colour'])) ? $bonway_portfolio_meta['bsp-text-colour'][0] : "#000000";
               $backgroundColour = (isset($bonway_portfolio_meta['bsp-background-colour'])) ? $bonway_portfolio_meta['bsp-background-colour'][0] : "#f4f4f4";
               $linkColour = (isset($bonway_portfolio_meta['bsp-link-colour'])) ? $bonway_portfolio_meta['bsp-link-colour'][0] : "#ffffff";
               $linkBackgroundColour = (isset($bonway_portfolio_meta['bsp-linkbg-colour'])) ? $bonway_portfolio_meta['bsp-linkbg-colour'][0] : "#0080c0";
               $titleColour = (isset($bonway_portfolio_meta['bsp-title-colour'])) ? $bonway_portfolio_meta['bsp-title-colour'][0] : "#ffffff";
               $titleBgColour = (isset($bonway_portfolio_meta['bsp-titlebg-colour'])) ? $bonway_portfolio_meta['bsp-titlebg-colour'][0] : "#004080";
               
           ?>

           <span>Text colour</span>
           <div class="colour-wrapper no-space bsp-section__container">
               <input type="color" id="bsp-text-colour" class="js-colour-selector" name="bsp-text-colour" value=<?= $textColour ?>>
               <label for="bsp-text-colour"><?= $textColour ?></label>
           </div>
           <span>Background colour</span>
           <div class="colour-wrapper no-space bsp-section__container">
               <input type="color" id="bsp-background-colour" class="js-colour-selector" name="bsp-background-colour" value=<?= $backgroundColour ?>>
               <label for="bsp-background-colour"><?= $backgroundColour ?></label>
           </div>
           <span>Title colour</span>
           <div class="colour-wrapper no-space bsp-section__container">
               <input type="color" id="bsp-title-colour" class="js-colour-selector" name="bsp-title-colour" value=<?= $titleColour ?>>
               <label for="bsp-title-colour"><?= $titleColour ?></label>
           </div>
           <span>Title background colour</span>
           <div class="colour-wrapper no-space bsp-section__container">
               <input type="color" id="bsp-titlebg-colour" class="js-colour-selector" name="bsp-titlebg-colour" value=<?= $titleBgColour ?>>
               <label for="bsp-titlebg-colour"><?= $titleBgColour ?></label>
           </div>
           <span>Link colour</span>
           <div class="colour-wrapper no-space bsp-section__container">
               <input type="color" id="bsp-link-colour" class="js-colour-selector" name="bsp-link-colour" value=<?= $linkColour ?>>
               <label for="bsp-link-colour"><?= $linkColour ?></label>
           </div>
           <span>Link background colour</span>
           <div class="colour-wrapper no-space bsp-section__container">
               <input type="color" id="bsp-linkbg-colour" class="js-colour-selector" name="bsp-linkbg-colour" value=<?= $linkBackgroundColour ?>>
               <label for="bsp-linkbg-colour"><?= $linkBackgroundColour ?></label>
           </div>
       </div>
   </div>

   <?php
}

/**
* Outputs the content of the meta box
* @method bonway_portfolio_meta_banner
* @param  Object                $post The post being used
*/
function bonway_portfolio_meta_banner($post) {
    wp_nonce_field(basename(__FILE__), 'bonway_portfolio_nonce');
    $bonway_portfolio_meta = get_post_meta($post->ID);
    ?>
   <div class="bsp-section bsp-section__image">
       <div class="bsp-section__inner">
           <div class="bsp-section__container">
               <?php bonway_portfolio_banner($postID = $post->ID); ?>
           </div>
       </div>
   </div>

   <?php
}

/**
* Saves the custom meta input
* @method bonway_portfolio_meta_save
* @param  int              $post_id ID of the saved post
*/
function bonway_portfolio_meta_save($post_id) {
    // Checks save status
    $is_autosave = wp_is_post_autosave($post_id);
    $is_revision = wp_is_post_revision($post_id);
    $is_valid_nonce = (isset($_POST['bonway_portfolio_nonce']) && wp_verify_nonce($_POST['bonway_portfolio_nonce'], basename(__FILE__))) ? 'true' : 'false';

    // Exits script depending on save status
    if ($is_autosave || $is_revision || !$is_valid_nonce) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if(isset($_POST['bsp-identifier'])) {
        $query =  bonwaybsp_select_meta($_POST['bsp-identifier']);
        $identifierId = $query->post->ID;

        /*
        Check if the identifier is unique, return an error if it's not.
        Post data is still saved, because it's annoying if you lost a
        bunch of work simply because you did not enter a unique identifier.
        */
        if($query->have_posts() == false || $identifierId == $post_id) {
            update_post_meta($post_id, 'bsp-identifier', sanitize_text_field($_POST['bsp-identifier']));
        } else {
            $bonway_portfolio_error = new WP_Error(
                "noUniqueSbeIdentifierError",
                "Portfolio data has been saved, but the provided Identifier is not unique. Please use another."
            );

            if ($bonway_portfolio_error) {
                $_SESSION['bonway_portfolio-error_identifier'] = $bonway_portfolio_error->get_error_message();
            }

            return;
        }

        $fields = array(
            "class", 
            "image", 
            "pageurl",
            "width",
            "text-colour",
            "title-colour",
            "titlebg-colour",
            "background-colour",
            "link-colour",
            "linkbg-colour",
            "readmore",
            "maxchars",
            "external-link"
        );

        foreach($fields as $field) {
            $identifier = "bsp-" . $field;

            if(isset($_POST[$identifier])) {
                update_post_meta($post_id, $identifier, sanitize_text_field($_POST[$identifier]));
            } else if($identifier === "bsp-readmore" || $identifier === "bsp-external-link") {
                $_POST[$identifier] = "";
                update_post_meta($post_id, $identifier, sanitize_text_field($_POST[$identifier]));
            }
        }
    }
}
add_action('save_post', 'bonway_portfolio_meta_save');

function bonway_portfolio_banner($postID) {
   // Set variables
   $options = get_option('bonway_portfolio_banner');
   $default_image = plugins_url('../images/no-image.png', __FILE__);
   $meta = get_post_meta($postID);
   $src = $default_image;
   $value = '';

   if(isset($meta['bsp-image']) && strlen($meta['bsp-image'][0]) > 0) {
       $image_attributes = wp_get_attachment_image_src($meta['bsp-image'][0], array(1920, 1080));
       $src = $image_attributes[0];
       $value = $meta['bsp-image'][0];
   }

   $text = __('Select image', 'RSSFI_TEXT');

   // Print HTML field
   echo '
       <div class="bsp-image__upload">
           <div class="bsp-image__container">
               <img data-src="' . $default_image . '" src="' . $src . '" class="js-image-thumb" />
           </div>
           <div class="bsp-image__buttons">
               <input type="hidden" name="bsp-image" id="bsp-image" class="js-bsp-image" value="' . $value . '" />
               <button type="submit" class="bsp-image__select-btn js-bsp-select-btn button">' . $text . '</button>
           </div>
       </div>
   ';
}

function bonwaybsp_add_metabox_class($class) {
    array_push($class, 'bsp-metabox');
    return $class;
}