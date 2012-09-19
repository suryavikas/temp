<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/filters.css" />
<div class="box">
    <div class="box-heading"><?php echo $heading_title; ?></div>
    <div class="filterBox box-content">
        <div class="box-product">
            <!--Manufactures -->
            <?php
//            print_r($filters);
//            var_dump(in_array('product-option', $filters[0]));
//            die;

            if (!empty($manufacturers)) {
            ?>


            <div class="box-heading-checkbox"><?php echo $text_manufacturer_select_option; ?> </div>
                 <div class="checkboxDiv">

                <?php
                foreach ($manufacturers as $value) {
		?>
                    
                    <div class="checkbox product-options" id="">
                    <?php
                    echo '<input  class="checkbox-label" type="checkbox" id="manufacturer" name="manufacturers[]" value="' . $value['manufacturer_id'] . '">';
                    echo '<label for="'. $value['name'].'">' . $value['name'] . '</label>';
                    echo '</div>';
//                        echo"<option value=" . $value['manufacturer_id'] . ">" . $value['name'] . "</option>";
                }
                    ?>
                    </div>
                    <?php
                }
                    ?>
                    <!-- Product Option Filters -->
                    <?php
                    if (!empty($productOptions)) {
                        foreach ($productOptions as $value) {
                     ?>
                            <div class="box-heading-checkbox"><?php echo  $value['name']; ?> </div>
                            <div class="checkboxDiv">
                
                    <?php
                            //Removing parent name
                            unset($value['name']);
                            foreach ($value as $child) {
                    ?>
                                <div class="checkbox product-options" id="">
                    <?php
                                echo '<input  class="checkbox-label" type="checkbox" id="product-option" name="product-option[]" value="' . $child['child_id'] . '">';
                                echo '<label for="'. $child['child_name'].'">' . $child['child_name'] . '</label>';
                                echo '</div>';
            
                            }
                                echo'</div>';
                        }
                    }
                    ?>

                    <!-- Price Range -->
                    <?php
                    
                    if (!empty($priceRangeArray)) {
                    ?>
                    <div class="box-heading-checkbox"><?php echo  $text_price_filter; ?> </div>
                            <div class="checkboxDiv">
                        
                    <?php
                        foreach ($priceRangeArray as $value) {
                            ?>
                            <div class="checkbox product-options" id="">

                                <?php
                                    echo '<input  class="checkbox-label" type="checkbox" id="price" name="price[]" value="' . $value['min'] . "-" . $value['max'] . '">';
                                    echo '<label for="'. $this->currency->format($value['min']) . " - " .  $this->currency->format($value['max'])  .'">' .  $this->currency->format($value['min']) . " - " . $this->currency->format($value['max']). '</label>';
                                    echo '</div>';
                //                        echo"<option value=" . $value['manufacturer_id'] . ">" . $value['name'] . "</option>";
                                }
                            echo'</div>';
//                           
                    }
                    ?>
                    <!-- Sale items -->
                    <?php	
                    if (!empty($priceRangeArray)) {
                    ?>
<!--                     <div class="box-heading-checkbox"><?php echo $text_sale_items; ?> </div>
                     <div class="checkboxDiv">-->

                    <div class="checkbox product-options" id="">
                    <?php
                    echo '<input  class="checkbox-label" type="checkbox" id="sale_items" name="sale_items">';
                    echo '<label for="'. $text_sale_items.'">' . $text_sale_items . '</label>';
                                           
                    ?>
                    </div>
                    <!-- In Stock products -->
                    <div class="box-single-checkbox">
                    <div class="checkbox product-options" id="">
                    <?php
                    echo '<input  class="checkbox-label" type="checkbox" id="in_stock" name="in_stock">';
                    echo '<label for="'. $text_in_stock_products.'">' . $text_in_stock_products . '</label>';
                    echo '</div>';
                    echo'</div>';
                     }
                    ?>

                </div>
        </div>
    </div>
    <script type="text/javascript" src="catalog/view/javascript/jquery/jquery.custom_radio_checkbox.js"></script>
            <script type="text/javascript"><!--           
            $(document).ready(function(){
                //	$(".radio").dgStyle();
                $(".checkbox").dgStyle();
            });

            
            $('.product-options').click(function(e) {
                //Iterating over multiple select boxes

                var arrData = getFilterParams();
                var getParams = getSortingParams();
                sendRequest(arrData, getParams, <?php echo $categoryId; ?>);
            });

    
        

        //--></script>