-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `user_hongbao`;
CREATE TABLE `user_hongbao` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增长ID',
  `telegram_id` int(11) NOT NULL COMMENT '用户ID',
  `max_quantity` bigint(20) NOT NULL COMMENT '总红包数量',
  `min_quantity` bigint(20) NOT NULL COMMENT '剩余红包数量',
  `max_flow` bigint(20) NOT NULL COMMENT '总流量',
  `min_flow` bigint(20) NOT NULL COMMENT '剩余流量',
  `nowTime` bigint(20) NOT NULL COMMENT '过期时间',
  `token` varchar(255) NOT NULL COMMENT '红包唯一URL标识符',
  `state` int(11) NOT NULL DEFAULT '1' COMMENT '红包状态',
  `draw` varchar(2550) NOT NULL DEFAULT '[]' COMMENT '这个红包领取者的TG_ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2020-01-30 01:40:16
