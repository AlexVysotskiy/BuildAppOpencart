<ul id="menu">
    <li id="dashboard"><a href="<?php echo $home; ?>"><i class="fa fa-dashboard fa-fw"></i> <span><?php echo $text_dashboard; ?></span></a></li>
    <li id="catalog">
        <a class="parent"><i class="fa fa-tags fa-fw"></i> <span><?php echo $text_catalog; ?></span></a>
        <ul>
            <li><a href="<?php echo $category; ?>"><?php echo $text_category; ?></a></li>
            <li><a href="<?php echo $product; ?>"><?php echo $text_product; ?></a></li>
            <li><a href="<?php echo $recurring; ?>"><?php echo $text_recurring; ?></a></li>
            <li><a href="<?php echo $filter; ?>"><?php echo $text_filter; ?></a></li>
            <li><a class="parent"><?php echo $text_attribute; ?></a>
                <ul>
                    <li><a href="<?php echo $attribute; ?>"><?php echo $text_attribute; ?></a></li>
                    <li><a href="<?php echo $attribute_group; ?>"><?php echo $text_attribute_group; ?></a></li>
                </ul>
            </li>
            <li><a href="<?php echo $option; ?>"><?php echo $text_option; ?></a></li>
            <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
            <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
            <li><a href="<?php echo $review; ?>"><?php echo $text_review; ?></a></li>
            <li><a href="<?php echo $information; ?>"><?php echo $text_information; ?></a></li>
        </ul>
    </li>
    
    <li id="sale"><a class="parent"><i class="fa fa-shopping-cart fa-fw"></i> <span><?php echo $text_sale; ?></span></a>
        <ul>
            <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
            <li><a href="<?php echo $order_recurring; ?>"><?php echo $text_order_recurring; ?></a></li>
            <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
            <li><a class="parent"><?php echo $text_customer; ?></a>
                <ul>
                    <li><a href="<?php echo $customer; ?>"><?php echo $text_customer; ?></a></li>
                    <li><a href="<?php echo $customer_group; ?>"><?php echo $text_customer_group; ?></a></li>
                    <li><a href="<?php echo $custom_field; ?>"><?php echo $text_custom_field; ?></a></li>
                    <li><a href="<?php echo $customer_ban_ip; ?>"><?php echo $text_customer_ban_ip; ?></a></li>
                </ul>
            </li>
            <li><a class="parent"><?php echo $text_voucher; ?></a>
                <ul>
                    <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
                    <li><a href="<?php echo $voucher_theme; ?>"><?php echo $text_voucher_theme; ?></a></li>
                </ul>
            </li>
            <li><a class="parent"><?php echo $text_paypal ?></a>
                <ul>
                    <li><a href="<?php echo $paypal_search ?>"><?php echo $text_paypal_search ?></a></li>
                </ul>
            </li>
        </ul>
    </li>
    
    
</ul>
