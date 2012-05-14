<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if (isset($error_warning)) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?><?php if (isset($success)) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
    <style type="text/css">
        .form button {
        margin:6px 0;
        }
        button {
        border: none;
        cursor: pointer;
        text-decoration: none;
        color: white;
        display: inline-block;
        padding: 5px 15px 5px 15px;
        background: #003A88;
        -webkit-border-radius: 10px 10px 10px 10px;
        -moz-border-radius: 10px 10px 10px 10px;
        -khtml-border-radius: 10px 10px 10px 10px;
        border-radius: 10px 10px 10px 10px;
        }
        .template-info {
        font-size:10px;
        color:#999;
        font-style:italic;
        }
    </style>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/module.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $back; ?></span></a>
            </div>
        </div>
        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td><?php echo $source_language;?></td>
                        <td>
                            <select name="source_language_code" id="source_language_code">
                                <?php foreach ($languages as $language) {
                                echo '<option value="' . $language['code'] . '"' . ($language['code']==$source_language_code?' selected="selected"':'') . '>' . $language['name'] . '</option>';
                                }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $categories;?></td>
                        <td>
                            <label for="categories_template"><?php echo $template;?> </label><input type="text" id="categories_template" name="categories_template" value="<?php echo $categories_template;?>" size="80">

                            <div class="template-info"><?php echo $available_category_tags;?></div>
                                <button type="submit" name="categories" value="categories"><?php echo $generate;?></button>
                            <br/><?php echo $warning_clear;?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $products;?></td>
                        <td>
                            <label for="products_template"><?php echo $template;?> </label><input type="text" id="products_template" name="products_template" value="<?php echo $products_template;?>" size="80"><br/>

                            <div class="template-info"><?php echo $available_product_tags;?></div>
                                <button type="submit" name="products" value="products"><?php echo $generate;?></button>
                            <br/><?php echo $warning_clear;?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $manufacturers;?></td>
                        <td>
                            <label for="manufacturers_template"><?php echo $template;?> </label><input type="text" id="manufacturers_template" name="manufacturers_template" value="<?php echo $manufacturers_template;?>" size="80"><br/>

                            <div class="template-info"><?php echo $available_manufacturer_tags;?></div>
                            <button type="submit" name="manufacturers" value="manufacturers"><?php echo $generate;?></button>
                            <br/><?php echo $warning_clear;?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $meta_keywords;?></td>
                        <td>
                            <label for="meta_template"><?php echo $template;?> </label><input type="text" id="meta_template" name="meta_template" value="<?php echo $meta_template;?>" size="80"><br/>

                            <div class="template-info"><?php echo $available_meta_tags;?></div>
                            <br/><?php if (in_array('curl', get_loaded_extensions())) {?>
                        <input type="checkbox" name="yahoo_checkbox"<?php if ($yahoo_checkbox==1) echo 'checked="checked"';?>><?php echo $add_from_yahoo;?><br/>
                        <label for="yahoo_id"><?php echo $your_yahoo_id;?> </label><input type="text" id="yahoo_id" name="yahoo_id" value="<?php echo $yahoo_id;?>" size="80"><br/>
                        <div class="template-info"><?php echo $get_yahoo_id;?></div><br/>
                        <?php } else {?>
                        <div><?php echo $curl_not_enabled;?></div>
                        <input type="hidden" id="yahoo_id" name="yahoo_id" value="">
                        <?php } ?>
                            <button type="submit" name="meta_keywords" value="meta_keywords"><?php echo $generate;?></button>
                            <br/><?php echo $warning_clear;?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $tags;?></td>
                        <td>
                            <label for="tags_template"><?php echo $template;?> </label><input type="text" id="tags_template" name="tags_template" value="<?php echo $tags_template;?>" size="80"><br/>

                            <div class="template-info"><?php echo $available_tags_tags;?></div>
                            <button type="submit" name="tags" value="tags"><?php echo $generate;?></button>
                            <br/><?php echo $warning_clear_tags;?>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
