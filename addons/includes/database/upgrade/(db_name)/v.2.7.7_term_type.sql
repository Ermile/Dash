ALTER TABLE `terms` CHANGE `type` `type` enum('cat','tag','code','other','term', 'support_tag', 'mag', 'mag_tag','help', 'help_tag') DEFAULT NULL;
ALTER TABLE `termusages` CHANGE `type` `type` enum('cat','tag','term','code','other','support_tag', 'mag', 'mag_tag','help', 'help_tag','barcode1','barcode2','barcode3','qrcode1','qrcode2','qrcode3','rfid1','rfid2','rfid3','fingerprint1','fingerprint2','fingerprint3','fingerprint4','fingerprint5','fingerprint6','fingerprint7','fingerprint8','fingerprint9','fingerprint10') DEFAULT NULL