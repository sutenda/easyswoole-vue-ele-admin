/*
 Navicat Premium Data Transfer

 Source Server         : 47.254.175.194(pubgaming)
 Source Server Type    : MySQL
 Source Server Version : 50728
 Source Host           : 47.254.175.194:3306
 Source Schema         : pubgaming_admin_dev

 Target Server Type    : MySQL
 Target Server Version : 50728
 File Encoding         : 65001

 Date: 27/08/2021 17:53:43
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for pg_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `pg_admin_user`;
CREATE TABLE `pg_admin_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `password` varchar(50) NOT NULL COMMENT '密码',
  `salt` varchar(10) NOT NULL COMMENT '密码盐',
  `avatar` text COMMENT '头像',
  `role_id` int(10) NOT NULL DEFAULT '0' COMMENT '角色组id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pg_admin_user
-- ----------------------------
BEGIN;
INSERT INTO `pg_admin_user` VALUES (1, 'admin', 'dev123', 'y3peZG', 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif', 1, 1, 1619162612, 1619407581);
INSERT INTO `pg_admin_user` VALUES (2, 'visit', 'dev123', 'y3peZG', 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif', 2, 1, 1619424226, 1619424226);
INSERT INTO `pg_admin_user` VALUES (3, 'test1', '123456', '0mTcg4', 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif', 3, 1, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for pg_role
-- ----------------------------
DROP TABLE IF EXISTS `pg_role`;
CREATE TABLE `pg_role` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '角色组名称',
  `rules` text NOT NULL COMMENT '可访问路由id（*表示所有权限）',
  `key` varchar(30) NOT NULL DEFAULT '' COMMENT '角色组key值',
  `description` varchar(255) NOT NULL COMMENT '角色组描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1正常，0禁用',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pg_role
-- ----------------------------
BEGIN;
INSERT INTO `pg_role` VALUES (1, '超级管理员', '*', 'admin', '超级管理员组', 1, 1619402057, 1619402057);
INSERT INTO `pg_role` VALUES (2, '游客组', '1,3', 'visitor', '游客', 1, 1619422236, 1619422236);
INSERT INTO `pg_role` VALUES (3, '管理', '7,9,8', 'manager', '管理', 1, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for pg_routes
-- ----------------------------
DROP TABLE IF EXISTS `pg_routes`;
CREATE TABLE `pg_routes` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pid` int(10) NOT NULL COMMENT '父id（二级路由）',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '路由名称',
  `path` varchar(100) NOT NULL DEFAULT '' COMMENT '路由地址',
  `component` varchar(100) NOT NULL DEFAULT '' COMMENT '组件地址',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `redirect` varchar(100) NOT NULL DEFAULT '' COMMENT '重定向地址',
  `alwaysShow` tinyint(1) NOT NULL DEFAULT '0' COMMENT '总是显示：1是，0否',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏：1是，0否',
  `affix` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否固钉：1是，0否',
  `noCache` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否缓存：1是，0否',
  `sort` int(2) NOT NULL DEFAULT '0' COMMENT '排序：0-99',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(19) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of pg_routes
-- ----------------------------
BEGIN;
INSERT INTO `pg_routes` VALUES (1, 0, '权限管理', '/permission', '#', 'lock', '/permission/admin', 0, 0, 0, 0, 0, 1, 1619337685, 1619341001);
INSERT INTO `pg_routes` VALUES (2, 1, '路由管理', 'routes', '/permission/routes', '', '', 0, 0, 0, 0, 0, 1, 1619338795, 1619338795);
INSERT INTO `pg_routes` VALUES (3, 1, '角色组', 'role', '/permission/role', '', '', 0, 0, 0, 0, 0, 1, 1619400341, 1619400341);
INSERT INTO `pg_routes` VALUES (4, 1, '管理员', 'admin', '/permission/admin', '', '', 0, 0, 0, 0, 0, 1, 1619403548, 1619403548);
INSERT INTO `pg_routes` VALUES (7, 0, '游戏管理', '/game-manager', '#', 'example', '#', 0, 0, 0, 0, 99, 1, NULL, NULL);
INSERT INTO `pg_routes` VALUES (8, 7, '发送金币历史', 'sendToken', '/game-manager/send-token/index', '', '', 0, 0, 0, 0, 2, 1, NULL, NULL);
INSERT INTO `pg_routes` VALUES (9, 7, '用户列表', 'userList', '/game-manager/user/index', '', '', 0, 0, 0, 0, 3, 1, NULL, NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
