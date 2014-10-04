/*
-- Query: SELECT * FROM akeneo.MappingCode where source = 'vikings'
LIMIT 0, 1000

-- Date: 2014-06-26 12:01
*/
DELETE FROM  `MappingCode` where `type`='attribute' and `source` = 'vikings';
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('name','vikings','attribute','Name','name',0);
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('image','vikings','attribute','Image',NULL,1);
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('image file name','vikings','attribute','Image File Name','image_1',0);
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('url','vikings','attribute','Url',NULL,1);
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('attributes','vikings','attribute','Attributes','attributes',0);
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('manufacturer','vikings','attribute','Manufacturer','manufacturer',0);
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('mfgpart','vikings','attribute','MFGPart','mpn',0);
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('upc','vikings','attribute','UPC','upc',0);
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('vendorpart','vikings','attribute','VendorPart',NULL,1);
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('specification','vikings','attribute','Specification','spec_sheet',0);
INSERT INTO `MappingCode` (`code`,`source`,`type`,`initialValue`,`value`,`ignored`) VALUES ('description','vikings','attribute','Description','short_description',0);
