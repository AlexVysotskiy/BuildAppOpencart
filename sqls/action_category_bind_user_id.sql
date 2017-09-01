ALTER TABLE oc_category
  ADD user_id INT(11) NOT NULL
  AFTER category_id;

UPDATE oc_category SET user_id = 1;