var Sequelize = require('sequelize');

var db = new Sequelize('ermile', 'ermile', 'ermile@#$567', {
  dialect: 'mysql',
  port: 3306
});

var File = db.define('files', {
  id: {
    type: Sequelize.INTEGER(10),
    primaryKey: true,
    autoIncrement: true
  },
  file_server: Sequelize.INTEGER(5),
  file_folder: Sequelize.INTEGER(5),
  file_name: Sequelize.INTEGER(10),
  file_code: Sequelize.STRING(64),
  file_size: Sequelize.FLOAT(12, 0),
  file_status: Sequelize.ENUM('init', 'inprogress', 'ready', ''),
  file_server: Sequelize.INTEGER(5),
  date_modified: Sequelize.TIME
}, {
  timestamps: false
});

var FilePart = db.define('fileparts', {
  id: {
    type: Sequelize.INTEGER(10),
    primaryKey: true,
    autoIncrement: true
  },
  file_id: Sequelize.INTEGER(10),
  filepart_part: Sequelize.INTEGER(5),
  filepart_code: Sequelize.STRING(64),
  filepart_status: Sequelize.ENUM('awaiting', 'start', 'inprogress', 'appended'),
  date_modified: Sequelize.TIME
}, {
  timestamps: false
});

var Attachment = db.define('attachments', {
  id: {
    type: Sequelize.INTEGER(10),
    primaryKey: true,
    autoIncrement: true
  },
  file_id: Sequelize.INTEGER(10),
  attachment_title: Sequelize.STRING(100),
  attachment_type: Sequelize.ENUM('productcategory', 'product', 'admin', 'banklogo',
                                  'post', 'system', 'other', 'file', 'folder'),
  attachment_addr: Sequelize.STRING(100),
  attachment_name: Sequelize.STRING(50),
  attachment_ext: Sequelize.STRING(10),
  attachment_size: Sequelize.FLOAT(12, 0),
  attachment_desc: Sequelize.STRING(200),
  attachment_parent: Sequelize.INTEGER(10),
  attachment_depth: Sequelize.INTEGER(5),
  attachment_count: Sequelize.INTEGER(5),
  attachment_order: Sequelize.INTEGER(5),
  user_id: Sequelize.INTEGER(5),
  date_modified: Sequelize.TIME
}, {
  timestamps: false
});

module.exports = {
  db: db,
  File: File,
  FilePart: FilePart,
  Attachment: Attachment
};