<?php

/**
* This View is responsible for generating layout parts for the com_virtuemart data source
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_com_virtuemart_View extends NSP_GK5_View {
	// article image generator
	static function image($config, $item, $only_url = false, $pm = false, $links = false){		
		if($config['news_content_image_pos'] != 'disabled' || $pm || $links) {			
			$news_title = str_replace('"', "&quot;", $item['title']);
			$IMG_SOURCE = $item['image'];
			$IMG_LINK = static::itemLink($item, $config);
			//
			$full_size_img = $IMG_SOURCE;
			//
			if($config['create_thumbs'] == 1 && $IMG_SOURCE != ''){
				// try to override standard image
				if(strpos($IMG_SOURCE,'http://') == FALSE) {
					$img_file = NSP_GK5_Thumbs::createThumbnail($IMG_SOURCE, $config, false, false, '', $links);
					
					if(is_array($img_file)) {
						$uri = JURI::getInstance();
						$IMG_SOURCE = $uri->root().'modules/mod_news_pro_gk5/cache/'.$img_file[1];
					} elseif($config['create_thumbs'] == 1) {
						jimport('joomla.filesystem.file');
						
						if(is_file(JPATH_ROOT.DS.'modules'.DS.'mod_news_pro_gk5'.DS.'cache'.DS.'default'.DS.'default'.$config['module_id'].'.png')) {
							$IMG_SOURCE = $uri->root().'modules/mod_news_pro_gk5/cache/default/default'.$config['module_id'].'.png';
						}
					} else {
						$IMG_SOURCE = '';
					}
				}	
			} elseif($config['create_thumbs'] == 1) {
				jimport('joomla.filesystem.file');
				
				if(is_file(JPATH_ROOT.DS.'modules'.DS.'mod_news_pro_gk5'.DS.'cache'.DS.'default'.DS.'default'.$config['module_id'].'.png')) {
					$IMG_SOURCE = $uri->root().'modules/mod_news_pro_gk5/cache/default/default'.$config['module_id'].'.png';			
				}
			}
			//
			if($IMG_SOURCE != '') {
				if($only_url) {
					return $IMG_SOURCE;
				} else {
					$class = '';
					
					if(!$links) {
						$class = ' t'.$config['news_content_image_pos'].' f'.$config['news_content_image_float'];
					} 
					
					$size = '';
					$margins = '';
					// 
					if(!$links && $config['responsive_images'] == 1) {
						$class .= ' gkResponsive'; 
					}
					//
					if(!$links) {
						if($config['img_width'] != 0 && !$config['img_keep_aspect_ratio'] && $config['responsive_images'] == 0) $size .= 'width:'.$config['img_width'].'px;';
						if($config['img_height'] != 0 && !$config['img_keep_aspect_ratio'] && $config['responsive_images'] == 0) $size .= 'height:'.$config['img_height'].'px;';
						if($config['img_margin'] != '') $margins = ' style="margin:'.$config['img_margin'].';"';
					} else {
						if($config['links_img_width'] != 0 && !$config['img_keep_aspect_ratio'] && $config['responsive_images'] == 0) $size .= 'width:'.$config['links_img_width'].'px;';
						if($config['links_img_height'] != 0 && !$config['img_keep_aspect_ratio'] && $config['responsive_images'] == 0) $size .= 'height:'.$config['links_img_height'].'px;';
						if($config['links_img_margin'] != '') $margins = ' style="margin:'.$config['links_img_margin'].';"';
					}
					//
					$size = ($size == '') ? '' : ' style="' . $size . '"';
					//
					//
					if($item['featured'] && $config['vm_show_featured_badge']) {
						$badge = '<sup class="nspBadge">'.JText::_('MOD_NEWS_PRO_GK5_NSP_FEATURED').'</sup>';
					} else {
						$badge = '';
					}
					if($config['news_image_link'] == 1) {
						if($config['news_image_modal'] == 1) {
							return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><a href="'.$full_size_img.'" class="modal nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a></div>' : '<a href="'.$full_size_img.'" class="modal nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a>' . $badge;
						} else {
							return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><a href="'.$IMG_LINK.'" class="nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a></div>' : '<a href="'.$IMG_LINK.'" class="nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a>' . $badge;
							
						}
					} else {
						return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><span class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" '.$size.' /></span></div>' : '<span class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'" /></span>' . $badge;
					}
				}
			} else {
				return '';
			}
		} else {
			return '';
		}
	}

	// article information generator
	static function info($config, $item, $num = 1) {
		// %AUTHOR %DATE %HITS %CATEGORY
		$news_info = '';
		//
		if($num == 1){
			if($config['news_content_info_pos'] != 'disabled') {
				$class = 'nspInfo1 t'.$config['news_content_info_pos'].' f'.$config['news_content_info_float'];	
			}
		} else {
			if($config['news_content_info2_pos'] != 'disabled') {
				$class = 'nspInfo2 t'.$config['news_content_info2_pos'].' f'.$config['news_content_info2_float'];
			}			
		}
		//
		if(
			($config['news_content_info_pos'] != 'disabled' && $num == 1) || 
			($config['news_content_info2_pos'] != 'disabled' && $num == 2)
		) {
			$news_info = '<div class="nspInfo '.$class.'"> '.$config['info'.(($num == 2) ? '2' : '').'_format'].' </div>';
	        $info_category = ($config['category_link'] == 1) ? '<a href="'.static::categoryLink($item).'" target="'.$config['open_links_window'].'">'.$item['cat_name'].'</a>' : $news_catname;
	        //          
	        $info_date = JHTML::_('date', $item['date'], $config['date_format']);			
	        //          
            if(!isset($item['comments']) || $item['comments'] == 0){
                $comments_amount = JText::_('MOD_NEWS_PRO_GK5_NO_COMMENTS');
            } else {
                $comments_amount = JText::_('MOD_NEWS_PRO_GK5_COMMENTS').' ('.(isset($item['comments']) ? $item['comments'] : '0' ) . ')';
            }
	        $info_comments = '<a class="nspComments" href="'.static::itemLink($item, $config).'#reviewform" target="'.$config['open_links_window'].'">'.$comments_amount.'</a>';
	        $info_manufacturer = JText::_('MOD_NEWS_PRO_GK5_MANUFACTURER').$item['manufacturer'];
	        // Replace the following phrases:
	        // %COMMENTS %DATE %CATEGORY %MANUFACTURER %STORE
            $news_info = str_replace('%DATE', $info_date, $news_info);
            $news_info = str_replace('%CATEGORY', $info_category, $news_info);
            $news_info = str_replace('%MANUFACTURER', $info_manufacturer, $news_info);
            $news_info = str_replace('%COMMENTS', $info_comments, $news_info);
            $news_info = str_replace('%STORE', static::store($config, $item), $news_info);
	    } else {
	    	return '';
	    }
		//
		return $news_info;		
	}
	
	// function used to show the store details
	static function store($config, $item) {        
        // Load the language file of com_virtuemart.
        JFactory::getLanguage()->load('com_virtuemart');
        // load necessary classes
        if (!class_exists( 'calculationHelper' )) {
        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'calculationh.php');
        }
        if (!class_exists( 'CurrencyDisplay' )) {
        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'currencydisplay.php');
        }
        if (!class_exists( 'VirtueMartModelVendor' )) {
        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'models'.DS.'vendor.php');
        }
        if (!class_exists( 'VmImage' )) {
        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'image.php');
        }
        if (!class_exists( 'shopFunctionsF' )) {
        	require(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'shopfunctionsf.php');
        }
        if (!class_exists( 'calculationHelper' )) {
        	require(JPATH_COMPONENT_SITE.DS.'helpers'.DS.'cart.php');
        }
        if (!class_exists( 'VirtueMartModelProduct' )){
           JLoader::import( 'product', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );
        }
        // load the base
        $productModel = new VirtueMartModelProduct();
	    $product = $productModel->getProduct($item['id'], 100, true, true, true);
	    
	    $mainframe = JFactory::getApplication();
	    $virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id', 0);
	    $currency = CurrencyDisplay::getInstance($virtuemart_currency_id != 0 ? $virtuemart_currency_id : $product->allPrices[0]['product_currency']);
	    
	    $price = '<strong>'.$currency->createPriceDiv($config['vm_show_price_type'], '', $product->prices, true).'</strong>';

        if($config['vm_add_to_cart'] == 1) {
            vmJsApi::jPrice();
            vmJsApi::writeJS();
        }

        $news_price = '<div>';
        //
        if($config['vm_show_price_type'] != 'none') {
            if($config['vm_display_type'] == 'text_price') {
            	$news_price .=  '<span>'.JText::_('MOD_NEWS_PRO_GK5_PRODUCT_PRICE').' '.$price.'</span>';
            } else {
            	$news_price .= '<span>'.$price.'</span>';
            }
        } 
        // 'Add to cart' button
        if($config['vm_add_to_cart'] == 1) {
            if(isset($product->customfields) &&count($product->customfields)) {
            	foreach($product->customfields as $field) {
            		if(isset($field->is_cart_attribute) && $field->is_cart_attribute == 1) {
            			$product->orderable = 0;
            			break;
            		}
            	}
            }
            
            $code = '<div class="addtocart-area">';
            
            if($product->orderable != 0) {
            	$code .= '<form method="post" class="product" action="index.php">';
            } else {
            	$code .= '<form method="post" class="product" action="'.static::itemLink($item, $config).'">';
            }
            
            $code .= '<div class="addtocart-bar">';
            $code .= '<span class="quantity-box" style="display: none"><input type="text" class="quantity-input" name="quantity[]" value="1" /></span>';
            $addtoCartButton = '';

			if($product->addToCartButton){
				$addtoCartButton = $product->addToCartButton;
			} else {
				$addtoCartButton = shopFunctionsF::getAddToCartButton($product->orderable);
			}

            $code .= str_replace('addtocart-button-disabled"', 'addtocart-button" type="submit"', $addtoCartButton);
               
            if($product->orderable != 0) { 
	            $code .= '</div>
	                    <input type="hidden" class="pname" value="'.$product->product_name.'"/>
	                    <input type="hidden" name="option" value="com_virtuemart" />
	                    <input type="hidden" name="view" value="cart" />
	                    <noscript><input type="hidden" name="task" value="add" /></noscript>
	                    <input type="hidden" name="virtuemart_product_id[]" value="'.$product->virtuemart_product_id.'" />
	                    <input type="hidden" name="virtuemart_category_id[]" value="'.$product->virtuemart_category_id.'" />
	                </form>';   
            } else {
            	$code .= '</div></form>';  
            } 
            $code .= '</div>'; 
            $news_price .= $code;
		} 
       	// display discount
        if($config['vm_show_discount_amount'] == 1) {
            $disc_amount = $currency->priceDisplay($product->prices['discountAmount'], $product->allPrices[0]['product_currency']);
            $news_price.= '<small class="nspDiscount">' . JText::_('MOD_NEWS_PRO_GK5_PRODUCT_DISCOUNT_AMOUNT'). $disc_amount . '</small>';
        }
		// display tax
        if($config['vm_show_tax'] == 1) {
          	$taxAmount = $currency->priceDisplay($product->prices['taxAmount'], $product->allPrices[0]['product_currency']);
            $news_price.= '<small class="nspTax">' . JText::_('MOD_NEWS_PRO_GK5_PRODUCT_TAX_AMOUNT'). $taxAmount . '</small>';  
        }
  		// results
        return ($news_price != '<div>') ? $news_price.'</div>' : '';
	}
	// article link generator
	static function itemLink($item, $config) {
		if(isset($item['overrided_url'])) {
			return $item['overrided_url'];
		}
		
		$itemid = $config['vm_itemid'];
		$link = 'index.php?option=com_virtuemart&amp;view=productdetails&amp;virtuemart_product_id='.$item['id'].'&amp;virtuemart_category_id='.$item['cid'].'&amp;Itemid='.$itemid;
		return $link;
	}
	// category link generator
	static function categoryLink($item) {
		return 'index.php?option=com_virtuemart&amp;view=category&amp;virtuemart_category_id='.$item['cid'];
	}
	// user link generator
	static function authorLink($item) {
		return '';
	}
}

// EOF
