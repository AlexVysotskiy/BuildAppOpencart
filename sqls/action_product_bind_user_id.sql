ALTER TABLE oc_product
  ADD user_group_id INT(11) NOT NULL
  AFTER product_id;

UPDATE oc_product SET user_group_id = 1;