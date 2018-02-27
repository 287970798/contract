/*
Navicat MySQL Data Transfer

Source Server         : uniteedu.aliyun
Source Server Version : 50718
Source Host           : dev.uniteedu.cn:3306
Source Database       : contract_db

Target Server Type    : MYSQL
Target Server Version : 50718
File Encoding         : 65001

Date: 2018-02-27 18:39:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for approval
-- ----------------------------
DROP TABLE IF EXISTS `approval`;
CREATE TABLE `approval` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '// sponsor  发起人id',
  `contract_id` int(10) unsigned NOT NULL,
  `current_node_id` int(10) unsigned DEFAULT NULL COMMENT '// 当前所在节点',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '// 审批流程名称',
  `total_nodes` int(2) unsigned NOT NULL DEFAULT '0' COMMENT '// 该审批总节点数',
  `process` int(2) unsigned NOT NULL DEFAULT '0' COMMENT '// 已审批的人数',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '// 审批状态 0 待审 1 审批中 2 通过 3 驳回',
  `start` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '// 是否启用  1 启用 2 未启用',
  `note` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of approval
-- ----------------------------
INSERT INTO `approval` VALUES ('3', '0', '13', '20', '花木成畦手自栽', '1', '0', '1', '2', 'sss', '1519214120', '1519637838', '1519637838');
INSERT INTO `approval` VALUES ('4', '0', '2', '6', '人资2号审批流', '2', '3', '2', '2', '人资2号审批流备注', '1519214857', '1519637840', '1519637840');
INSERT INTO `approval` VALUES ('6', '0', '1', '12', 'test approval', '3', '4', '3', '2', 'asdfdfasdf', '1519284068', '1519637841', '1519637841');
INSERT INTO `approval` VALUES ('7', '16', '22', '14', 'asdfsdf approval', '2', '0', '1', '2', '', '1519285159', '1519637843', '1519637843');
INSERT INTO `approval` VALUES ('8', '15', '23', '16', 'this is a test approval', '2', '0', '1', '2', '', '1519361802', '1519637845', '1519637845');
INSERT INTO `approval` VALUES ('9', '15', '24', null, 'another contract\'s approval', '0', '0', '0', '2', '', '1519361932', '1519637847', '1519637847');
INSERT INTO `approval` VALUES ('10', '15', '25', '18', 'hh approval2', '2', '1', '1', '2', '', '1519362802', '1519637849', '1519637849');
INSERT INTO `approval` VALUES ('11', '16', '26', '23', '又一个审批', '1', '2', '3', '1', '', '1519553208', '1519644236', null);
INSERT INTO `approval` VALUES ('12', '16', '2', '22', '关于人资2号合同的审批', '1', '1', '2', '1', '', '1519637933', '1519638035', null);
INSERT INTO `approval` VALUES ('13', '16', '31', '24', '正审', '1', '0', '1', '2', '', '1519695435', '1519726619', null);

-- ----------------------------
-- Table structure for approval_node
-- ----------------------------
DROP TABLE IF EXISTS `approval_node`;
CREATE TABLE `approval_node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `approval_id` int(10) unsigned NOT NULL,
  `node_number` int(2) DEFAULT NULL COMMENT '// 节点排序 节点号 用于控制审批顺序',
  `user_id` int(10) unsigned NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '-1' COMMENT '// 审批状态 -1 未进入 0 待审 1 审批中 2 通过 3 驳回',
  `note` varchar(255) NOT NULL DEFAULT '' COMMENT '// 审批意见',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of approval_node
-- ----------------------------
INSERT INTO `approval_node` VALUES ('1', '4', '1', '2', '-1', '', '1519214857', '1519282341', '1519282341');
INSERT INTO `approval_node` VALUES ('2', '4', '2', '3', '-1', '', '1519214857', '1519277729', '1519277729');
INSERT INTO `approval_node` VALUES ('3', '4', '3', '15', '-1', '', '1519214857', '1519277773', '1519277773');
INSERT INTO `approval_node` VALUES ('4', '4', '4', '16', '1', '通过', '1519277583', '1519637840', '1519637840');
INSERT INTO `approval_node` VALUES ('5', '4', '5', '14', '-1', '', '1519277773', '1519282341', '1519282341');
INSERT INTO `approval_node` VALUES ('6', '4', '5', '14', '1', '通过', '1519282808', '1519637840', '1519637840');
INSERT INTO `approval_node` VALUES ('11', '6', '1', '16', '1', '通过', '1519284068', '1519637841', '1519637841');
INSERT INTO `approval_node` VALUES ('12', '6', '2', '15', '2', '驳回', '1519284068', '1519637841', '1519637841');
INSERT INTO `approval_node` VALUES ('13', '6', '3', '14', '-1', '', '1519284068', '1519637841', '1519637841');
INSERT INTO `approval_node` VALUES ('14', '7', '1', '2', '0', '', '1519285159', '1519637843', '1519637843');
INSERT INTO `approval_node` VALUES ('15', '7', '2', '3', '-1', '', '1519285250', '1519637843', '1519637843');
INSERT INTO `approval_node` VALUES ('16', '8', '1', '1', '0', '', '1519361802', '1519637845', '1519637845');
INSERT INTO `approval_node` VALUES ('17', '8', '2', '6', '-1', '', '1519361802', '1519637845', '1519637845');
INSERT INTO `approval_node` VALUES ('18', '10', '2', '14', '0', '驳加', '1519365019', '1519637849', '1519637849');
INSERT INTO `approval_node` VALUES ('19', '10', '1', '15', '1', '通过', '1519365019', '1519637849', '1519637849');
INSERT INTO `approval_node` VALUES ('20', '3', '1', '1', '0', '', '1519373485', '1519637838', '1519637838');
INSERT INTO `approval_node` VALUES ('21', '11', '1', '16', '1', '通过', '1519553208', '1519637851', '1519637851');
INSERT INTO `approval_node` VALUES ('22', '12', '1', '14', '1', '通过', '1519637933', '1519638035', null);
INSERT INTO `approval_node` VALUES ('23', '11', '1', '14', '2', '驳回', '1519642850', '1519644236', null);
INSERT INTO `approval_node` VALUES ('24', '13', '1', '1', '0', '', '1519695435', '1519695435', null);

-- ----------------------------
-- Table structure for approval_notice
-- ----------------------------
DROP TABLE IF EXISTS `approval_notice`;
CREATE TABLE `approval_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `approval_id` int(10) unsigned NOT NULL COMMENT '// ',
  `note` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of approval_notice
-- ----------------------------
INSERT INTO `approval_notice` VALUES ('2', '16', '12', '已通过', '1519638035', '1519704222', '1519704222');
INSERT INTO `approval_notice` VALUES ('3', '16', '11', '已驳加', null, '1519694961', '1519694961');
INSERT INTO `approval_notice` VALUES ('4', '16', '11', '已驳回', '1519644236', '1519644417', '1519644417');

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of category
-- ----------------------------
INSERT INTO `category` VALUES ('1', '人事管理2', '这个分类是人事管理2', '1519098973', '1519541636', null);
INSERT INTO `category` VALUES ('2', '联创团5', '联创团合同5', '1519099825', '1519099843', '1519099843');
INSERT INTO `category` VALUES ('3', '联创团', '联创团合同分类', '1519099993', '1519099993', null);
INSERT INTO `category` VALUES ('4', '分类测试', '基材', '1519119616', '1519119664', '1519119664');
INSERT INTO `category` VALUES ('5', '软件采购', '', '1519614341', '1519614341', null);

-- ----------------------------
-- Table structure for contract
-- ----------------------------
DROP TABLE IF EXISTS `contract`;
CREATE TABLE `contract` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT '// 合同录入人',
  `type_id` int(10) unsigned NOT NULL,
  `short_sn` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '// 用于获取最大sn',
  `contract_sn` varchar(255) NOT NULL DEFAULT '' COMMENT '// 合同编号',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '// 合同名称',
  `customer_id` int(10) unsigned NOT NULL COMMENT '// 客户id',
  `linkman_id` int(10) unsigned DEFAULT NULL COMMENT '// 客户联系人 默认为null',
  `department` varchar(255) NOT NULL DEFAULT '' COMMENT '// 合同所属部门',
  `manager` varchar(255) NOT NULL DEFAULT '' COMMENT '// 合同负责人',
  `contract_date` int(11) DEFAULT NULL COMMENT '// 合同签约日期',
  `due_date` int(11) DEFAULT NULL COMMENT '// 合同到期在日',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '// 合同状态 备用 0 审核中 1 审核通过 2 审核未通过 3 已完成',
  `note` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contract
-- ----------------------------
INSERT INTO `contract` VALUES ('1', '1', '0', '1', '1', '人事管理2-001-01', 'test', '1', '4', '技术研发部', '月上禾', '1519142400', '-28800', null, '', null, '1519192562', null);
INSERT INTO `contract` VALUES ('2', '1', '0', '1', '2', '人事管理2-001-02', '人资2号合同文件', '1', '3', '技术研发部', '月上禾', '1519142400', '1553097600', null, '123456', '1519144012', '1519144012', null);
INSERT INTO `contract` VALUES ('13', '1', '0', '1', '3', '人事管理2-001-03', 'asdf', '2', '5', '技术研发部', '月上禾', '1519142400', '1519228800', null, 'asdf', '1519150901', '1519193655', null);
INSERT INTO `contract` VALUES ('16', '1', '0', '1', '4', '人事管理2-001-04', 'asdfx', '1', '3', '技术研发部', '月上禾', '1519228800', '1519228800', null, 'asdfasdfsadf', '1519154951', '1519194235', '1519194235');
INSERT INTO `contract` VALUES ('22', '1', '0', '1', '5', '人事管理2-001-05', 'asdfasd', '1', '3', '技术研发部', '月上禾', '1519228800', '1519660800', null, 'asdfsdfddddd', '1519284816', '1519284816', null);
INSERT INTO `contract` VALUES ('23', '1', '0', '1', '6', '人事管理2-001-06', 'this is a contract', '2', '5', '腾讯社交部', '马化腾', '1519315200', '1519747200', null, '', '1519361765', '1519361765', null);
INSERT INTO `contract` VALUES ('24', '3', '0', '2', '1', '联创团-001-01', 'another contract', '2', '5', '腾讯社交部', '马化腾', '1519315200', '1522252800', null, '', '1519361907', '1519361907', null);
INSERT INTO `contract` VALUES ('25', '3', '0', '2', '2', '联创团-001-02', '这是个合同', '2', '5', '腾讯社交部', '马化腾', '1519315200', '1519747200', null, '', '1519362768', '1519362768', null);
INSERT INTO `contract` VALUES ('26', '1', '0', '1', '7', '人事管理2-001-07', '测试上传', '1', '3', '腾讯社交部', '马化腾', '1519315200', '1519747200', null, '', '1519386023', '1519386023', null);
INSERT INTO `contract` VALUES ('27', '3', '0', '2', '3', '联创团-001-03', '另一个测试上传', '1', '4', '腾讯社交部', '马化腾', '1519315200', '1519747200', null, '', '1519386135', '1519386135', null);
INSERT INTO `contract` VALUES ('28', '1', '0', '1', '8', '人事管理2-001-08', '基本原则', '1', '3', '腾讯社交部', '马化腾', '1519315200', '1519747200', null, '', '1519386478', '1519386478', null);
INSERT INTO `contract` VALUES ('29', '1', '0', '1', '9', '人事管理2-001-09', '枯干上十', '1', '3', '腾讯社交部', '马化腾', '1519315200', '1519747200', null, '', '1519386511', '1519462871', null);
INSERT INTO `contract` VALUES ('30', '1', '16', '3', '1', '人事管理2-002-01', '教育代理机构合同', '2', '5', '技术研发部', '月上禾', '1519488000', '1519747200', null, '', '1519557234', '1519557234', null);
INSERT INTO `contract` VALUES ('31', '1', '16', '1', '10', '人事管理2-001-10', '测试劳动合同1', '5', '8', '技术研发部', '公老师', '1519574400', '1551110400', null, '测试用', '1519612464', '1519724552', null);
INSERT INTO `contract` VALUES ('34', '1', '16', '1', '11', '人事管理2-001-11', '日志合同', '4', '7', '技术研发部', '月上禾', '1519660800', '1519747200', null, '', '1519720142', '1519720861', '1519720861');
INSERT INTO `contract` VALUES ('35', '3', '15', '2', '4', '联创团-001-04', '另一个日志合同', '4', '7', '腾讯社交部', '马化腾', '1519660800', '1519747200', null, '', '1519721477', '1519721549', '1519721549');

-- ----------------------------
-- Table structure for contract_extra
-- ----------------------------
DROP TABLE IF EXISTS `contract_extra`;
CREATE TABLE `contract_extra` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '// 合同附加项 标题',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '// 合同附加项 内容',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contract_extra
-- ----------------------------
INSERT INTO `contract_extra` VALUES ('1', '13', 't1', 'c1', '1519150901', '1519193655', null);
INSERT INTO `contract_extra` VALUES ('2', '13', 't2', 'c2', '1519150901', '1519193655', null);
INSERT INTO `contract_extra` VALUES ('3', '13', 't3', 'c3', '1519150901', '1519193655', null);
INSERT INTO `contract_extra` VALUES ('15', '16', 'asdf', 'asdf', '1519193755', '1519194235', '1519194235');
INSERT INTO `contract_extra` VALUES ('16', '16', 'asdf', 'asdf', '1519193755', '1519194235', '1519194235');
INSERT INTO `contract_extra` VALUES ('17', '22', 'test', 'ccc', '1519284816', '1519284816', null);
INSERT INTO `contract_extra` VALUES ('18', '31', '测试字段1', '测试内容', '1519627318', '1519724552', null);

-- ----------------------------
-- Table structure for contract_file
-- ----------------------------
DROP TABLE IF EXISTS `contract_file`;
CREATE TABLE `contract_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) unsigned NOT NULL,
  `src` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contract_file
-- ----------------------------
INSERT INTO `contract_file` VALUES ('2', '16', '20180221/44b5baff2b3cbc568225c901f9564469.doc', '公司介绍2.doc', '1519194186', '1519194235', '1519194235');
INSERT INTO `contract_file` VALUES ('3', '22', '20180222/b88f7bdccf1aac6763d7444f1e8fbed5.jpg', '微信图片_20180209170601.jpg', '1519284816', '1519284816', null);
INSERT INTO `contract_file` VALUES ('9', '29', '20180223/8c0b78e5375aab3c1f046ee87b4b7985.jpg', 'logo@sbnl.jpg', '1519386511', '1519462871', null);
INSERT INTO `contract_file` VALUES ('10', '31', '20180226/3f98051be97568f1ee0eaf4110c83500.pdf', '劳动合同-联创优学.pdf', '1519612464', '1519724552', null);
INSERT INTO `contract_file` VALUES ('11', '34', '', '', '1519720840', '1519720861', '1519720861');
INSERT INTO `contract_file` VALUES ('12', '35', '', '', '1519721510', '1519721549', '1519721549');

-- ----------------------------
-- Table structure for contract_log
-- ----------------------------
DROP TABLE IF EXISTS `contract_log`;
CREATE TABLE `contract_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '// 修改人用户id',
  `contract_id` int(10) NOT NULL,
  `note` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `location` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contract_log
-- ----------------------------
INSERT INTO `contract_log` VALUES ('3', '15', '35', '创建了新合同', '223.98.165.222', '中国山东济南移动', '1519721477', '1519721477', null);
INSERT INTO `contract_log` VALUES ('4', '15', '35', '修改了合同', '223.98.165.222', '中国山东济南移动', '1519721510', '1519721510', null);
INSERT INTO `contract_log` VALUES ('5', '15', '35', '删除了合同', '223.98.165.222', '中国山东济南移动', '1519721549', '1519721549', null);
INSERT INTO `contract_log` VALUES ('6', '14', '31', '修改了合同', '223.98.165.222', '中国山东济南移动', '1519724552', '1519724552', null);

-- ----------------------------
-- Table structure for customer
-- ----------------------------
DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '// 创建人',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '客户名称',
  `note` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customer
-- ----------------------------
INSERT INTO `customer` VALUES ('1', '14', '济南万成教育有限公司2', '这里是万成备注2', '1518324616', '1518325558', null);
INSERT INTO `customer` VALUES ('2', '16', '大成教育', '大成教育备注', '1518325700', '1518332504', null);
INSERT INTO `customer` VALUES ('3', null, '透彻塔顶asdf', 'asdfsdfadsfxxxx', '1518325755', '1518325829', '1518325829');
INSERT INTO `customer` VALUES ('4', '15', '王者荣耀游戏科技有限公司', '一个热门游戏开发公司', '1519451320', '1519451320', null);
INSERT INTO `customer` VALUES ('5', '16', '联创优学员工', '包含所有需要签订劳动合同的员工', '1519609507', '1519609507', null);

-- ----------------------------
-- Table structure for linkman
-- ----------------------------
DROP TABLE IF EXISTS `linkman`;
CREATE TABLE `linkman` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '// 所属用户',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `phone` varchar(11) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of linkman
-- ----------------------------
INSERT INTO `linkman` VALUES ('1', '2', '16', '张三2', '15633258746', '张三备注3', '1518328005', '1518333000', null);
INSERT INTO `linkman` VALUES ('2', '1', null, 'test3', '15966666666', 'asdf', '1518329887', '1518329899', '1518329899');
INSERT INTO `linkman` VALUES ('3', '1', '16', '王二小', '15988756487', '', '1519143243', '1519143243', null);
INSERT INTO `linkman` VALUES ('4', '1', '14', '放牛娃', '15455652145', '', '1519143277', '1519143277', null);
INSERT INTO `linkman` VALUES ('5', '2', '14', '隔壁老王', '16955874512', '', '1519143307', '1519143307', null);
INSERT INTO `linkman` VALUES ('6', '4', '15', '王小龙', '18955789965', '业务负责人', '1519451512', '1519451512', null);
INSERT INTO `linkman` VALUES ('7', '4', '15', '德清', '15988748963', '前端设计工程师', '1519451695', '1519451695', null);
INSERT INTO `linkman` VALUES ('8', '5', '16', '张某', '18866775521', '测试用1', '1519609714', '1519609714', null);

-- ----------------------------
-- Table structure for login_log
-- ----------------------------
DROP TABLE IF EXISTS `login_log`;
CREATE TABLE `login_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `location` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of login_log
-- ----------------------------
INSERT INTO `login_log` VALUES ('1', '16', '223.98.165.222', '中国山东济南移动', '1519705131', '1519705131', null);
INSERT INTO `login_log` VALUES ('2', '16', '223.98.165.222', '中国山东济南移动', '1519708427', '1519708427', null);
INSERT INTO `login_log` VALUES ('3', '16', '223.98.165.222', '中国山东济南移动', '1519709403', '1519709403', null);
INSERT INTO `login_log` VALUES ('4', '16', '223.98.165.222', '中国山东济南移动', '1519713802', '1519713802', null);
INSERT INTO `login_log` VALUES ('5', '15', '223.98.165.222', '中国山东济南移动', '1519721213', '1519721213', null);
INSERT INTO `login_log` VALUES ('6', '14', '223.98.165.222', '中国山东济南移动', '1519724520', '1519724520', null);
INSERT INTO `login_log` VALUES ('7', '16', '223.98.165.222', '中国山东济南移动', '1519726515', '1519726515', null);
INSERT INTO `login_log` VALUES ('8', '16', '113.129.133.154', '中国山东济南电信', '1519726727', '1519726727', null);
INSERT INTO `login_log` VALUES ('9', '16', '113.129.133.154', '中国山东济南电信', '1519726805', '1519726805', null);

-- ----------------------------
-- Table structure for type
-- ----------------------------
DROP TABLE IF EXISTS `type`;
CREATE TABLE `type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sn` varchar(200) NOT NULL DEFAULT '' COMMENT '// 001 累加 小于3位补0',
  `category_id` int(10) unsigned NOT NULL COMMENT '// 分类id',
  `name` varchar(255) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of type
-- ----------------------------
INSERT INTO `type` VALUES ('1', '001', '1', '劳动合同', '1234563', '1519101157', '1519119818', null);
INSERT INTO `type` VALUES ('2', '001', '3', '1团22', '联创团1团合同2222', '1519101241', '1519119982', null);
INSERT INTO `type` VALUES ('3', '002', '1', '制度', '123', null, null, null);

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1 超级管理员 >2 普通管理员',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '// 登录用户名',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '// 姓名',
  `department` varchar(50) NOT NULL DEFAULT '' COMMENT '部门',
  `phone` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 启用 2 锁定',
  `password` varchar(40) NOT NULL DEFAULT '',
  `note` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '1', 'yyloon', '任心伟', '互联网运营中心', '15966326431', '1', '7c4a8d09ca3762af61e59520943dc26494f8941b', '这是一个测试帐号', null, '1519548613', null);
INSERT INTO `user` VALUES ('2', '2', 'tom', '张三', '技术部', '15966326431', '1', '7c4a8d09ca3762af61e59520943dc26494f8941b', '测试 ', '1518173924', '1519383400', null);
INSERT INTO `user` VALUES ('3', '2', 'jack2', '李四2', '技术部2', '15966326433', '1', '7c4a8d09ca3762af61e59520943dc26494f8941b', '测试 23', '1518173969', '1519373275', null);
INSERT INTO `user` VALUES ('6', '2', 'test', '王五', 'internation', '15999999999', '2', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'adsfasdf', '1518270635', '1519285302', null);
INSERT INTO `user` VALUES ('11', '2', 'test2', '123', '123', '123', '2', '7c4a8d09ca3762af61e59520943dc26494f8941b', '123', '1518310331', '1518310331', null);
INSERT INTO `user` VALUES ('12', '2', 'test3', '123', '123', '15966326432', '2', '7c4a8d09ca3762af61e59520943dc26494f8941b', '', '1518310719', '1519373405', null);
INSERT INTO `user` VALUES ('13', '2', 'test5', 'tom', 'fashion', '15966326431', '2', '7c4a8d09ca3762af61e59520943dc26494f8941b', '', '1518311468', '1519373406', null);
INSERT INTO `user` VALUES ('14', '2', 'jackma', '马云', '阿里团', '15966326431', '1', '7c4a8d09ca3762af61e59520943dc26494f8941b', '', '1518312151', '1518312151', null);
INSERT INTO `user` VALUES ('15', '2', 'tony', '马化腾', '腾讯社交部', '15966326424', '1', '7c4a8d09ca3762af61e59520943dc26494f8941b', '', '1518312243', '1518321965', null);
INSERT INTO `user` VALUES ('16', '1', 'admin', '月上禾', '技术研发部', '15699658757', '1', '7c4a8d09ca3762af61e59520943dc26494f8941b', '超管理员拥有所有权限', '1518320754', '1519453143', null);
