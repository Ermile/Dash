ALTER TABLE `termusages` ADD `type`
ENUM(
'cat',
'tag',
'term',
'code',
'other',
'barcode1',
'barcode2',
'barcode3',
'qrcode1',
'qrcode2',
'qrcode3',
'rfid1',
'rfid2',
'rfid3',
'fingerprint1',
'fingerprint2',
'fingerprint3',
'fingerprint4',
'fingerprint5',
'fingerprint6',
'fingerprint7',
'fingerprint8',
'fingerprint9',
'fingerprint10'
) CHARACTER SET utf8 COLLATE utf8_general_ci  NULL DEFAULT NULL;

ALTER TABLE `terms` CHANGE `type` `type` ENUM('cat','tag','code','other', 'term')  NULL DEFAULT NULL;
ALTER TABLE `terms` CHANGE `url` `url` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;