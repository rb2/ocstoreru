# OPENCART UPGRADE SCRIPT v1.5.x
# WWW.OPENCART.COM
# Qphoria

# THIS UPGRADE ONLY APPLIES TO PREVIOUS 1.5.x VERSIONS. DO NOT RUN THIS SCRIPT IF UPGRADING FROM v1.4.x 

# DO NOT RUN THIS ENTIRE FILE MANUALLY THROUGH PHPMYADMIN OR OTHER MYSQL DB TOOL
# THIS FILE IS GENERATED FOR USE WITH THE UPGRADE.PHP SCRIPT LOCATED IN THE INSTALL FOLDER
# THE UPGRADE.PHP SCRIPT IS DESIGNED TO VERIFY THE TABLES BEFORE EXECUTING WHICH PREVENTS ERRORS

# IF YOU NEED TO MANUALLY RUN THEN YOU CAN DO IT BY INDIVIDUAL VERSIONS. EACH SECTION IS LABELED.
# BE SURE YOU CHANGE THE PREFIX "oc_" TO YOUR PREFIX OR REMOVE IT IF NOT USING A PREFIX

# OCSTORE UPGRADE SCRIPT v1.0.x
#
# opencartforum.ru
# myopencart.ru

#### START UPGRADE OCSTORE 1.5.1.3

CREATE TABLE IF NOT EXISTS oc_manufacturer_description (
  manufacturer_id INT(11) NOT NULL,
  language_id INT(11) NOT NULL,
  description TEXT NOT NULL COLLATE utf8_general_ci,
  meta_description VARCHAR(255) NOT NULL COLLATE utf8_general_ci,
  meta_keyword VARCHAR(255) NOT NULL COLLATE utf8_general_ci,
  seo_title VARCHAR(255) NOT NULL COLLATE utf8_general_ci,
  seo_h1 VARCHAR(255) NOT NULL COLLATE utf8_general_ci,
  PRIMARY KEY (manufacturer_id, language_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS oc_tax_rate_to_customer_group (
  tax_rate_id int(11) NOT NULL,
  customer_group_id int(11) NOT NULL,
  PRIMARY KEY (tax_rate_id, customer_group_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS oc_tax_rule (
  tax_rule_id int(11) NOT NULL auto_increment,
  tax_class_id int(11) NOT NULL,
  tax_rate_id int(11) NOT NULL,
  based varchar(10) NOT NULL COLLATE utf8_general_ci,
  priority int(5) NOT NULL DEFAULT '1',
  PRIMARY KEY (tax_rule_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE oc_category_description ADD seo_title VARCHAR(255) NOT NULL COLLATE utf8_general_ci AFTER meta_keyword;
ALTER TABLE oc_category_description ADD seo_h1 VARCHAR(255) NOT NULL COLLATE utf8_general_ci AFTER seo_title;

ALTER TABLE oc_customer ADD token varchar(255) NOT NULL COLLATE utf8_general_ci AFTER approved;

ALTER TABLE oc_information_description ADD meta_description VARCHAR(255) NOT NULL COLLATE utf8_general_ci AFTER description;
ALTER TABLE oc_information_description ADD meta_keyword VARCHAR(255) NOT NULL COLLATE utf8_general_ci AFTER meta_description;
ALTER TABLE oc_information_description ADD seo_title VARCHAR(255) NOT NULL COLLATE utf8_general_ci AFTER meta_keyword;
ALTER TABLE oc_information_description ADD seo_h1 VARCHAR(255) NOT NULL COLLATE utf8_general_ci AFTER seo_title;

ALTER TABLE oc_option_value ADD image varchar(255) NOT NULL COLLATE utf8_general_ci AFTER option_id;

ALTER TABLE oc_product_description ADD seo_title VARCHAR(255) NOT NULL COLLATE utf8_general_ci AFTER meta_keyword;
ALTER TABLE oc_product_description ADD seo_h1 VARCHAR(255) NOT NULL COLLATE utf8_general_ci AFTER seo_title;

ALTER TABLE oc_product_image ADD sort_order int(3) NOT NULL DEFAULT '0' AFTER image;

ALTER TABLE oc_tax_rate ADD `name` varchar(32) NOT NULL COLLATE utf8_general_ci AFTER geo_zone_id;
ALTER TABLE oc_tax_rate ADD `type` char(1) NOT NULL COLLATE utf8_general_ci AFTER rate;

ALTER TABLE oc_order MODIFY invoice_prefix varchar(26) NOT NULL COLLATE utf8_general_ci;

ALTER TABLE oc_tax_rate MODIFY rate decimal(15,4) NOT NULL DEFAULT '0.0000' AFTER `name`;
ALTER TABLE oc_tax_rate MODIFY date_added DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `type`;
ALTER TABLE oc_tax_rate MODIFY date_modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER date_added;

ALTER TABLE oc_tax_rate DROP tax_class_id ;
ALTER TABLE oc_tax_rate DROP priority ;
ALTER TABLE oc_tax_rate DROP description ;

ALTER TABLE oc_product_tag ADD INDEX product_id (product_id);
ALTER TABLE oc_product_tag ADD INDEX language_id (language_id);
ALTER TABLE oc_product_tag ADD INDEX tag (tag);

INSERT IGNORE INTO oc_manufacturer_description (manufacturer_id, language_id) SELECT manufacturer_id, language_id FROM oc_manufacturer , oc_language;
