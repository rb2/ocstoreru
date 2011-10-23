# OPENCART UPGRADE SCRIPT v1.5.x
# WWW.OPENCART.COM
# Qphoria
#
# Русскоязычная поддержка
# opencartforum.ru
# myopencart.ru

# THIS UPGRADE ONLY APPLIES TO PREVIOUS 1.5.x VERSIONS. DO NOT RUN THIS SCRIPT IF UPGRADING FROM v1.4.x 

# DO NOT RUN THIS ENTIRE FILE MANUALLY THROUGH PHPMYADMIN OR OTHER MYSQL DB TOOL
# THIS FILE IS GENERATED FOR USE WITH THE UPGRADE.PHP SCRIPT LOCATED IN THE INSTALL FOLDER
# THE UPGRADE.PHP SCRIPT IS DESIGNED TO VERIFY THE TABLES BEFORE EXECUTING WHICH PREVENTS ERRORS

# IF YOU NEED TO MANUALLY RUN THEN YOU CAN DO IT BY INDIVIDUAL VERSIONS. EACH SECTION IS LABELED.
# BE SURE YOU CHANGE THE PREFIX "oc_" TO YOUR PREFIX OR REMOVE IT IF NOT USING A PREFIX

#### START 1.5.1

ALTER TABLE `oc_affiliate` MODIFY `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_affiliate` MODIFY `approved` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_banner` MODIFY `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_category` MODIFY `top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_category` MODIFY `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_country` MODIFY `postcode_required` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_country` MODIFY `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '';
ALTER TABLE `oc_coupon` MODIFY `logged` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_coupon` MODIFY `shipping` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_coupon` MODIFY `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_currency` MODIFY `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_customer` MODIFY `newsletter` tinyint(1) NOT NULL DEFAULT '0' COMMENT '';
ALTER TABLE `oc_customer`  MODIFY `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_customer`  MODIFY `approved` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_information` MODIFY `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '';
ALTER TABLE `oc_language` MODIFY `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_order_history` MODIFY `notify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '';
ALTER TABLE `oc_product` MODIFY `shipping` tinyint(1) NOT NULL DEFAULT '1' COMMENT '';
ALTER TABLE `oc_product` MODIFY `subtract` tinyint(1) NOT NULL DEFAULT '1' COMMENT '';
ALTER TABLE `oc_product` MODIFY `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '';
ALTER TABLE `oc_product_option` MODIFY `required` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_product_option_value` MODIFY `subtract` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_return_history` MODIFY `notify` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_return_product` MODIFY `opened` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_review` MODIFY `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '';
ALTER TABLE `oc_user` MODIFY `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_voucher` MODIFY `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '';
ALTER TABLE `oc_zone`  MODIFY `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '';
ALTER TABLE `oc_setting` ADD `serialized` tinyint(1) NOT NULL DEFAULT 0 COMMENT '' AFTER value;


#### START 1.5.1.3

CREATE TABLE IF NOT EXISTS oc_tax_rate_to_customer_group (
    tax_rate_id int(11) NOT NULL DEFAULT 0 COMMENT '',
    customer_group_id int(11) NOT NULL DEFAULT 0 COMMENT '',
    PRIMARY KEY (tax_rate_id, customer_group_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS oc_tax_rule (
    tax_rule_id int(11) NOT NULL DEFAULT 0 COMMENT '' auto_increment,
    tax_class_id int(11) NOT NULL DEFAULT 0 COMMENT '',
    tax_rate_id int(11) NOT NULL DEFAULT 0 COMMENT '',
    based varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    priority int(5) NOT NULL DEFAULT '1' COMMENT '',
    PRIMARY KEY (tax_rule_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE oc_customer ADD token varchar(255) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER approved;
ALTER TABLE oc_option_value ADD image varchar(255) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER option_id;
ALTER TABLE oc_order MODIFY invoice_prefix varchar(26) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci;
ALTER TABLE oc_product_image ADD sort_order int(3) NOT NULL DEFAULT '0' COMMENT '' AFTER image;
ALTER TABLE oc_tax_rate ADD name varchar(32) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER geo_zone_id;
ALTER TABLE oc_tax_rate ADD type char(1) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER rate;
ALTER TABLE oc_tax_rate DROP tax_class_id;
ALTER TABLE oc_tax_rate DROP priority;
ALTER TABLE oc_tax_rate MODIFY rate decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT '';
ALTER TABLE oc_tax_rate DROP description;

ALTER TABLE oc_product_tag ADD INDEX product_id (product_id);
ALTER TABLE oc_product_tag ADD INDEX language_id (language_id);
ALTER TABLE oc_product_tag ADD INDEX tag (tag);