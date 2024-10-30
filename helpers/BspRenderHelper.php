<?php
/**
 * Get a portfolio item using its ID
 * @method bonwaybsp_get_portfolio_by_id
 * @param  int       $id ID of the block
 * @return string        Block content
 */
function bonwaybsp_get_portfolio_by_id($id){
    $post = get_post($id);

    return bonwaybsp_get_portfolio_content($post);
}

/**
 * Get a portfolio item using its Identifier
 * @method bonwaybsp_get_portfolio_by_identifier
 * @param  string       $identifier Identifier of the block
 * @return string        Block content
 */
function bonwaybsp_get_portfolio_by_identifier($identifier){
    //Get the post ID
    $post = get_post(bonwaybsp_select_meta($identifier)->post->ID);

    return bonwaybsp_get_portfolio_content($post);
}

/**
 * Get the content of a requested portfolio item
 * @method bonwaybsp_get_portfolio_content
 * @param  Object          $post Post to get data from
 * @return string                Content of the post
 */
function bonwaybsp_get_portfolio_content($post) {
    $meta = get_post_meta($post->ID);
    $identifier = $meta["bsp-identifier"][0];
    $mainClass = $meta["bsp-class"][0];
    $uniqueClass = "bsp--$identifier";

    if(get_post_type($post) == "bonway-portfolio") {
        $class = $meta['bsp-width'][0];
        $css = bonwaybsp_generate_item_style($meta, $mainClass, $uniqueClass);
        $block = $css;
        $block .= "<div class='bonwaybsp__container $uniqueClass js-bsp-container $mainClass $class'><div class='bsp-inner'>";

        ob_start();
        $title          = apply_filters('the_title', $post->post_title);
        $content        = apply_filters('the_content', $post->post_content);
        $url            = (isset($meta['bsp-pageurl'])) ? apply_filters('the_url', $meta['bsp-pageurl'][0]) : "";
        $img            = wp_get_attachment_image_src($meta['bsp-image'][0], array(1920, 1080));
        $readmore       = ($meta['bsp-readmore'][0] === "used") ? true : false;
        $external_link  = ($meta['bsp-external-link'][0] === "used") ? true : false;
        $target         = "";
        $maxchars       = (isset($meta['bsp-maxchars'][0])) ? $meta['bsp-maxchars'][0] : 0;

        $blockTitle = "<div class='bsp-item__title'>" . $title . "</div>";
        $blockBanner = (isset($meta['bsp-image'])) ? "<div class='bsp-item__image'><img src='$img[0]'></img></div>" : "" ;
        if($readmore) {
            //If the link should point to an external site, prefix '//'
            if($external_link) {
                if(strpos($url, "http")) {
                    $url = '//' . $url;
                }
                $target = "target='_blank'";
            }

            $blockContent = "<div class='bsp-item__content'>" . bonwaybsp_cuttext($content, $maxchars) . "</div>";
            $blockReadMore = "<a href='" . $url . "' class='bsp-item__readmore' " . $target . ">Read more</a>";
        } else {
            $blockContent = "<div class='bsp-item__content'>" . $content . "</div>";
        }
        ob_end_clean();

        $block .= $blockTitle . $blockBanner . $blockContent;
        if($readmore) $block .= $blockReadMore;
        $block .= "</div></div>";
    }

    return $block;
}

function bonwaybsp_generate_item_style($meta, $mainClass, $uniqueClass) {
    //Colours
    $textColour = $meta['bsp-text-colour'][0];
    $backgroundColour = $meta['bsp-background-colour'][0];
    $titleColour = $meta['bsp-title-colour'][0];
    $titleBgColour = $meta['bsp-titlebg-colour'][0];
    $linkColour = $meta['bsp-link-colour'][0];
    $linkBackgroundColour = $meta['bsp-linkbg-colour'][0];

    $css = "<style> 
        .$uniqueClass .bsp-inner {
            background-color: $backgroundColour;
            color: $textColour;
        }
        .$uniqueClass .bsp-item__title {
            background-color: $titleBgColour;
            color: $titleColour;
        }
        .$uniqueClass .bsp-item__readmore,
        .$uniqueClass .bsp-item__readmore:hover,
        .$uniqueClass .bsp-item__readmore:active,
        .$uniqueClass .bsp-item__readmore:focus {
            background-color: $linkBackgroundColour;
            color: $linkColour;
            margin-bottom: 8px;
        }
    </style>";

    return $css;
}