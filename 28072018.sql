-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2018 at 11:05 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dsp_portal`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `tree_recover`()
    MODIFIES SQL DATA
BEGIN

    DECLARE currentId, currentParentId  CHAR(36);
    DECLARE currentLeft                 INT;
    DECLARE startId                     INT DEFAULT 1;

    # Determines the max size for MEMORY tables.
    SET max_heap_table_size = 1024 * 1024 * 512;

    START TRANSACTION;	

    # Establishing starting numbers for all root elements.
    WHILE EXISTS (SELECT * FROM tmp_cat WHERE parent_bcategory_id = 0 AND cat_lftnode = 0 AND cat_rgtnode = 0 ORDER BY cat_lftnode_old LIMIT 1) DO

        UPDATE tmp_cat
        SET    cat_lftnode  = startId,
               cat_rgtnode = startId + 1
        WHERE  parent_bcategory_id = 0
          AND  cat_lftnode       = 0
          AND  cat_rgtnode      = 0
        LIMIT  1;

        SET startId = startId + 2;

    END WHILE;

    # Numbering all child elements
    WHILE EXISTS (SELECT * FROM tmp_cat WHERE cat_lftnode = 0 ORDER BY cat_lftnode_old LIMIT 1) DO

        # Picking an unprocessed element which has a processed parent.
        SELECT     tmp_cat.bcategory_id
          INTO     currentId
        FROM       tmp_cat
        INNER JOIN tmp_cat AS parents
                ON tmp_cat.parent_bcategory_id = parents.bcategory_id
        WHERE      tmp_cat.cat_lftnode = 0
          AND      parents.cat_lftnode !=0
    ORDER BY tmp_cat.cat_lftnode_old DESC
        LIMIT      1;

        # Finding the element's parent.
        SELECT  parent_bcategory_id
          INTO  currentParentId
        FROM    tmp_cat
        WHERE   bcategory_id = currentId;

        # Finding the parent's cat_lftnode value.
        SELECT  cat_lftnode
          INTO  currentLeft
        FROM    tmp_cat
        WHERE   bcategory_id = currentParentId;

        # Shifting all elements to the right of the current element 2 to the right.
        UPDATE tmp_cat
        SET    cat_rgtnode = cat_rgtnode + 2
        WHERE  cat_rgtnode > currentLeft;

        UPDATE tmp_cat
        SET    cat_lftnode = cat_lftnode + 2
        WHERE  cat_lftnode > currentLeft;

        # Setting cat_lftnode and rght values for current element.
        UPDATE tmp_cat
        SET    cat_lftnode  = currentLeft + 1,
               cat_rgtnode = currentLeft + 2
        WHERE  bcategory_id   = currentId;

    END WHILE;

    # Writing calculated values back to physical table.
    UPDATE bcategory_tree, tmp_cat
    SET    bcategory_tree.cat_lftnode  = tmp_cat.cat_lftnode,
           bcategory_tree.cat_rgtnode = tmp_cat.cat_rgtnode
    WHERE  bcategory_tree.bcategory_id   = tmp_cat.bcategory_id;

    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `account_af_ranking_log`
--

CREATE TABLE IF NOT EXISTS `account_af_ranking_log` (
  `ar_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) NOT NULL DEFAULT '0',
  `af_rank_id` smallint(4) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `is_verified` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `verified_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ar_id`),
  KEY `verified_by` (`verified_by`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `account_af_ranking_log`
--

INSERT INTO `account_af_ranking_log` (`ar_id`, `account_id`, `af_rank_id`, `status`, `is_verified`, `verified_by`, `created_on`, `updated_on`) VALUES
(25, 279, 1, 1, 1, 0, '2018-06-23 07:53:51', '2018-06-22 20:53:51'),
(24, 278, 1, 1, 1, 0, '2018-06-23 07:50:21', '2018-06-22 20:50:21'),
(23, 277, 1, 1, 1, 0, '2018-06-23 06:45:50', '2018-06-22 19:45:50'),
(22, 276, 1, 1, 1, 0, '2018-06-23 06:19:15', '2018-06-22 19:19:15'),
(21, 232, 1, 1, 1, 0, '2018-05-10 07:26:19', '2018-05-09 20:26:19'),
(10, 102, 1, 1, 1, 0, '2018-03-27 06:35:34', '2018-03-26 19:35:34'),
(11, 235, 1, 1, 1, 0, '2018-03-27 07:17:17', '2018-03-26 20:17:17'),
(12, 239, 1, 1, 1, 0, '2018-03-28 07:07:10', '2018-03-27 20:07:10'),
(13, 240, 1, 1, 1, 0, '2018-02-05 08:57:55', '2018-03-27 21:57:55'),
(14, 241, 4, 1, 1, 0, '2018-02-10 09:17:38', '2018-03-27 22:17:38'),
(15, 242, 1, 1, 1, 0, '2018-03-28 10:36:35', '2018-03-27 23:36:35'),
(16, 243, 4, 1, 1, 0, '2018-02-12 08:36:36', '2018-03-27 23:41:37'),
(17, 244, 4, 1, 1, 0, '2018-03-29 11:07:50', '2018-03-29 00:07:50'),
(18, 246, 1, 1, 1, 0, '2018-03-29 13:01:30', '2018-03-29 02:01:30'),
(19, 245, 4, 1, 1, 0, '2018-02-12 04:13:12', '2018-04-01 18:03:54'),
(20, 249, 4, 1, 1, 0, '2018-02-12 20:28:00', '2018-04-01 18:25:08');

-- --------------------------------------------------------

--
-- Table structure for table `account_creation_steps`
--

CREATE TABLE IF NOT EXISTS `account_creation_steps` (
  `step_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `account_type_id` tinyint(2) unsigned NOT NULL,
  `priority` tinyint(2) unsigned NOT NULL,
  `route` varchar(255) NOT NULL,
  PRIMARY KEY (`step_id`),
  KEY `account_type_id` (`account_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `account_creation_steps`
--

INSERT INTO `account_creation_steps` (`step_id`, `name`, `account_type_id`, `priority`, `route`) VALUES
(1, 'Account Details', 3, 1, 'seller.sign-up.account-details'),
(2, 'Store Banking', 3, 2, 'seller.store-banking'),
(3, 'Primary Store Details', 3, 3, 'seller.account-update'),
(4, 'Verification', 3, 4, 'seller.kyc-verification');

-- --------------------------------------------------------

--
-- Table structure for table `account_details`
--

CREATE TABLE IF NOT EXISTS `account_details` (
  `account_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `salutation` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `profile_img` varchar(255) NOT NULL DEFAULT 'profile_image_blank.jpg',
  `status_id` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Draft,1-Active,2-Inactive,3-Expired',
  `gender` tinyint(1) unsigned DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`account_id`),
  KEY `gender` (`gender`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=114 ;

--
-- Dumping data for table `account_details`
--

INSERT INTO `account_details` (`account_id`, `salutation`, `firstname`, `lastname`, `profile_img`, `status_id`, `gender`, `dob`, `created_on`, `updated_on`) VALUES
(1, 1, 'System', 'Admin', 'profile_image_blank.jpg', 1, 1, NULL, '2016-04-06 23:12:05', '2017-05-16 20:03:36'),
(2, 1, 'Prakash', 'S', 'profile_image_blank.jpg', 1, 1, '1990-06-02', '2017-03-17 01:32:45', '2018-07-07 07:49:21'),
(3, 1, 'Supplier', 'Sup', 'profile_image_blank.jpg', 1, 1, NULL, '2016-04-06 23:33:16', '2017-05-16 20:03:41'),
(4, 0, 'Spice', 'webs', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-17 09:01:54', '2018-07-17 09:01:54'),
(50, 0, 'Senthil', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-21 07:38:17', '2018-07-27 05:11:21'),
(74, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-23 09:32:35', '2018-07-23 09:32:35'),
(75, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 05:55:15', '2018-07-24 05:55:15'),
(77, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:07:28', '2018-07-24 06:07:28'),
(78, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:09:25', '2018-07-24 06:09:25'),
(79, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:12:46', '2018-07-24 06:12:46'),
(80, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:13:36', '2018-07-24 06:13:36'),
(81, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:14:38', '2018-07-24 06:14:38'),
(83, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:17:28', '2018-07-24 06:17:28'),
(84, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:21:13', '2018-07-24 06:21:13'),
(86, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:23:01', '2018-07-24 06:23:01'),
(87, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:38:29', '2018-07-24 06:38:29'),
(88, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:42:52', '2018-07-24 06:42:52'),
(89, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:52:14', '2018-07-24 06:52:14'),
(90, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 06:54:27', '2018-07-24 06:54:27'),
(91, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 09:33:06', '2018-07-24 09:33:06'),
(92, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 09:35:54', '2018-07-24 09:35:54'),
(93, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 11:27:06', '2018-07-24 11:27:06'),
(94, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 12:06:05', '2018-07-24 12:06:05'),
(95, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 13:02:43', '2018-07-24 13:02:43'),
(96, 0, 'Jayaprakash', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-24 13:54:45', '2018-07-24 13:54:45'),
(97, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-25 07:20:42', '2018-07-25 07:20:42'),
(98, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-25 07:40:52', '2018-07-25 07:40:52'),
(99, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-25 08:14:51', '2018-07-25 08:14:51'),
(100, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-25 08:58:53', '2018-07-25 08:58:53'),
(101, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-28 08:03:28', '2018-07-28 08:03:28'),
(103, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-28 08:11:10', '2018-07-28 08:11:10'),
(106, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-28 08:15:02', '2018-07-28 08:15:02'),
(108, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-28 08:17:38', '2018-07-28 08:17:38'),
(111, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-28 08:20:06', '2018-07-28 08:20:06'),
(112, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-28 08:34:53', '2018-07-28 08:34:53'),
(113, 0, 'Jayaprakash S', '', 'profile_image_blank.jpg', 1, NULL, NULL, '2018-07-28 08:42:19', '2018-07-28 08:42:19');

-- --------------------------------------------------------

--
-- Table structure for table `account_feedback`
--

CREATE TABLE IF NOT EXISTS `account_feedback` (
  `feedback_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_type_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `relation_type_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(50) DEFAULT NULL,
  `description` varchar(50) NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `account_feedback`
--

INSERT INTO `account_feedback` (`feedback_id`, `post_type_id`, `relation_type_id`, `subject`, `description`, `account_id`, `status`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 0, 1, 'Test', 'Test desc', 5, 0, '2016-06-06 22:30:53', '2016-06-06 17:34:46', 0),
(2, 0, 0, 'Test Employee Feedback', 'Test Employee Feedback', 7, 1, '2016-06-06 23:02:30', '2016-07-07 20:34:20', 0);

-- --------------------------------------------------------

--
-- Table structure for table `account_gender_lookups`
--

CREATE TABLE IF NOT EXISTS `account_gender_lookups` (
  `gender_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `gender` varchar(10) NOT NULL,
  PRIMARY KEY (`gender_id`),
  UNIQUE KEY `gender` (`gender`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `account_gender_lookups`
--

INSERT INTO `account_gender_lookups` (`gender_id`, `gender`) VALUES
(2, 'Female'),
(1, 'Male'),
(3, 'Others');

-- --------------------------------------------------------

--
-- Table structure for table `account_log`
--

CREATE TABLE IF NOT EXISTS `account_log` (
  `account_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `token` text,
  `fcm_registration_id` text COMMENT 'Firebase Cloud Messaging (FCM)',
  `account_login_ip` varchar(40) NOT NULL,
  `device_id` smallint(5) unsigned DEFAULT NULL,
  `country_id` tinyint(3) unsigned DEFAULT NULL,
  `toggle_app_lock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `account_log_time` datetime NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  PRIMARY KEY (`account_log_id`),
  KEY `account_id` (`account_id`),
  KEY `device_id` (`device_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `account_log`
--

INSERT INTO `account_log` (`account_log_id`, `account_id`, `token`, `fcm_registration_id`, `account_login_ip`, `device_id`, `country_id`, `toggle_app_lock`, `account_log_time`, `is_deleted`) VALUES
(1, 100, NULL, NULL, '', 1, 77, 0, '2018-07-28 07:02:44', 0),
(2, 100, NULL, NULL, '', 1, 77, 0, '2018-07-28 07:06:41', 0),
(3, 100, NULL, NULL, '', 1, 77, 0, '2018-07-28 07:08:25', 0),
(4, 100, '8de6eec42e5beae57a9f73bd4be07ed460c313f4-a87ff679a2f3e71d9181a67b7542122c', NULL, '', 1, 77, 0, '2018-07-28 07:13:09', 0),
(5, 100, '751cb4bdac9cc574904de1ccde60f8ab671e269a-e4da3b7fbbce2345d7772b0674a318d5', NULL, '', 1, 77, 0, '2018-07-28 07:14:36', 0),
(6, 100, 'e54d91744868b409aa59640df47d6493ad0e201c-1679091c5a880faf6fb5e6087eb1b2dc', NULL, '', 1, 77, 0, '2018-07-28 07:15:35', 0),
(7, 100, '8aa02d46599f70ce098026fc7e35b341bfd3426c-8f14e45fceea167a5a36dedd4bea2543', NULL, '', 1, 77, 0, '2018-07-28 07:16:59', 0),
(8, 100, '75e8e9c2e133a1a33a53bf1d657e9b1cbf99443b-c9f0f895fb98ab9159f51fd0297e236d', NULL, '', 1, 77, 0, '2018-07-28 07:17:16', 0),
(9, 100, '6a984d65beba258f49dca966abb31456322f6f20-45c48cce2e2d7fbdea1afc51c7c6ad26', NULL, '', 1, 77, 0, '2018-07-28 07:20:19', 0),
(10, 100, '9e0357e79ba8dddbdbb9fb0e9716e7bf9ebc3bc3-d3d9446802a44259755d38e6d163e820', NULL, '', 1, 77, 0, '2018-07-28 07:20:35', 0),
(11, 100, '1a00b1d561c9b50b6a8fce6d6b28e07bc4832dad-6512bd43d9caa6e02c990b0a82652dca', NULL, '', 1, 77, 0, '2018-07-28 07:20:53', 0),
(12, 100, '7dc4d87c7004a5b54fce9256680019e39a086765-c20ad4d76fe97759aa27a0c99bff6710', NULL, '', 1, 77, 0, '2018-07-28 07:21:17', 0),
(13, 100, '19e82b0199cee18739bb88a4dbcf43c93cd60ce3-c51ce410c124a10e0db5e4b97fc2af39', NULL, '', 1, 77, 0, '2018-07-28 07:21:29', 0),
(14, 100, '24652d5f3d34e0695cb26d4d063e19aa1d4728cc-aab3238922bcc25a6f606eb525ffdc56', NULL, '', 1, 77, 0, '2018-07-28 07:21:59', 0),
(15, 100, '896fa1181708431a7237f89e5ea66c7047a265d0-9bf31c7ff062936a96d3c8bd1f8f2ff3', NULL, '', 1, 77, 0, '2018-07-28 07:22:09', 0),
(16, 100, 'df75ca437387bcd080a0e49a5a1b92d5daf10ec5-c74d97b01eae257e44aa9d5bade97baf', NULL, '', 1, 77, 0, '2018-07-28 07:25:43', 0),
(17, 100, '7ce26016b57b5ce4664fb857adba3592e358fa83-70efdf2ec9b086079795c442636b55fb', NULL, '', 1, 77, 0, '2018-07-28 07:32:33', 0),
(18, 100, '7ce26016b57b5ce4664fb857adba3592e358fa83-6f4922f45568161a8cdf4ad2299f6d23', NULL, '', 1, 77, 0, '2018-07-28 07:40:56', 0),
(19, 100, '485b82e1d22b123975fb796a91344dedef331ada-1f0e3dad99908345f7439f8ffabdffc4', NULL, '', 1, 77, 0, '2018-07-28 07:41:08', 0),
(20, 100, '76a54bd7613d20a5a9c45f2964ba82fae6dc9469-98f13708210194c475687be6106a3b84', NULL, '', 1, 77, 0, '2018-07-28 07:41:38', 0),
(21, 100, '76a54bd7613d20a5a9c45f2964ba82fae6dc9469-3c59dc048e8850243be8079a5c74d079', NULL, '', 1, 77, 0, '2018-07-28 07:42:21', 0),
(22, 100, '3d6bc78b48a4b1a85116b676966d8db0c9e84cc9-b6d767d2f8ed5d21a44b0e5886680cb9', NULL, '', 1, 77, 0, '2018-07-28 07:43:45', 0),
(23, 100, '4a1808e17a05f9e3fd393ee9db1cb215023eafa1-37693cfc748049e45d87b8c7d8b9aacd', NULL, '', 1, 77, 0, '2018-07-28 07:45:45', 0),
(24, 100, '9946d3ab39ddd703b93e786cbb316fc25860423d-1ff1de774005f8da13f42943881c655f', NULL, '', 1, 77, 0, '2018-07-28 07:46:34', 0),
(25, 100, '55df8026d82036365539c938d414dbe7ee01ebd0-8e296a067a37563370ded05f5a3bf3ec', NULL, '', 1, 77, 0, '2018-07-28 07:47:49', 0),
(26, 100, 'e3bc90c8263fdd4390c2cb93977d92cb0c4a865f-4e732ced3463d06de0ca9a15b6153677', NULL, '', 1, 77, 0, '2018-07-28 07:51:33', 0),
(27, 100, 'bbab85e7cf13b7e7f85bc611a6d6fdd5fd2ec45d-02e74f10e0327ad868d138f2b4fdd6f0', NULL, '', 1, 77, 0, '2018-07-28 07:55:31', 0),
(28, 100, '93d808755c893754186f7cbc24ce6d9e344f2f56-33e75ff09dd601bbe69f351039152189', NULL, '', 1, 77, 0, '2018-07-28 07:56:50', 0),
(29, 100, '96e593085ac774e997a840359e563b62c40a29f1-6ea9ab1baa0efb9e19094440c317e21b', NULL, '', 1, 77, 0, '2018-07-28 07:57:06', 0),
(30, 106, '705bd235cea6512f1749097b1d824e4fc9aeebca-34173cb38f07f89ddbebc2ac9128303f', NULL, '', 1, 77, 0, '2018-07-28 08:15:02', 0),
(31, 108, 'e9367e3bee67a75b9caf45f709e6e2b9246e23bb-c16a5320fa475530d9583c34fd356ef5', NULL, '', 1, 77, 0, '2018-07-28 08:17:38', 0),
(32, 111, '2b2da997ab1a83cbcd61611f820d3b1760e831d3-6364d3f0f495b6ab9dcf8d3b5c6e0b01', NULL, '', 1, 77, 0, '2018-07-28 08:20:06', 0),
(33, 100, NULL, NULL, '', 1, 77, 0, '2018-07-28 08:31:52', 0),
(34, 100, 'c932904abf668e375e92ea0a768978a024ac9b00-e369853df766fa44e1ed0ff613f563bd', NULL, '', 1, 77, 0, '2018-07-28 08:32:25', 0),
(35, 100, '9e092023b84caaa50c3d413c49a57c8cdf8a70a2-1c383cd30b7c298ab50293adfecb7b18', NULL, '', 1, 77, 0, '2018-07-28 08:33:59', 0),
(36, 112, '5341e9c64dd3eea39df144b6e627606967b0a025-19ca14e7ea6328a42e0eb13d585e4c22', NULL, '', 1, 77, 0, '2018-07-28 08:34:53', 0),
(37, 113, '92d1233f19ee3a83675f4d24211bb9ad2b266af8-a5bfc9e07964f8dddeb95fc584cd965d', NULL, '', 1, 77, 0, '2018-07-28 08:42:19', 0),
(38, 100, '76a54bd7613d20a5a9c45f2964ba82fae6dc9469-a5771bce93e200c36f7cd9dfd0e5deaa', NULL, '', 1, 77, 0, '2018-07-28 09:01:34', 0);

-- --------------------------------------------------------

--
-- Table structure for table `account_login_log`
--

CREATE TABLE IF NOT EXISTS `account_login_log` (
  `user_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `device_log_id` bigint(20) unsigned NOT NULL,
  `login_on` datetime DEFAULT NULL,
  PRIMARY KEY (`user_log_id`),
  KEY `account_id` (`account_id`),
  KEY `device_log_id` (`device_log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;

--
-- Dumping data for table `account_login_log`
--

INSERT INTO `account_login_log` (`user_log_id`, `account_id`, `device_log_id`, `login_on`) VALUES
(121, 100, 4, '2018-07-27 11:38:07'),
(122, 100, 4, '2018-07-27 11:40:43'),
(123, 100, 5, '2018-07-27 11:45:50'),
(124, 100, 5, '2018-07-27 11:46:58'),
(125, 100, 5, '2018-07-27 11:54:10'),
(126, 100, 5, '2018-07-27 11:54:27'),
(127, 100, 5, '2018-07-27 11:57:23'),
(128, 100, 5, '2018-07-27 13:18:50'),
(129, 100, 5, '2018-07-27 13:19:00'),
(130, 100, 5, '2018-07-28 05:22:07'),
(131, 100, 5, '2018-07-28 05:28:44'),
(132, 100, 5, '2018-07-28 05:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `account_logistics`
--

CREATE TABLE IF NOT EXISTS `account_logistics` (
  `logistic_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `logistic` varchar(100) NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_system_carrier` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`logistic_id`),
  KEY `account_id` (`account_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `account_logistics`
--

INSERT INTO `account_logistics` (`logistic_id`, `logistic`, `account_id`, `is_default`, `is_system_carrier`, `status`, `is_deleted`) VALUES
(1, 'Logistic', 2, 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `account_mst`
--

CREATE TABLE IF NOT EXISTS `account_mst` (
  `account_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_type_id` tinyint(1) unsigned NOT NULL,
  `user_code` varchar(15) DEFAULT NULL,
  `uname` varchar(35) DEFAULT NULL,
  `email` varchar(75) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `pass_key` varchar(50) DEFAULT NULL,
  `trans_pass_key` varchar(50) DEFAULT NULL,
  `is_affiliate` int(10) unsigned NOT NULL DEFAULT '0',
  `security_pin` varchar(50) DEFAULT NULL,
  `signedup_on` datetime DEFAULT NULL,
  `activated_on` datetime DEFAULT NULL,
  `expiry_on` datetime DEFAULT NULL,
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `block` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `last_active` datetime DEFAULT NULL,
  `login_block` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `activation_key` varchar(100) DEFAULT NULL,
  UNIQUE KEY `user_id` (`account_id`) USING BTREE,
  UNIQUE KEY `uname` (`uname`),
  UNIQUE KEY `account_type_id_2` (`account_type_id`,`mobile`),
  UNIQUE KEY `account_type_id_3` (`account_type_id`,`email`),
  KEY `account_type_id` (`account_type_id`),
  KEY `is_affiliate` (`is_affiliate`,`status`),
  KEY `is_deleted` (`is_deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=114 ;

--
-- Dumping data for table `account_mst`
--

INSERT INTO `account_mst` (`account_id`, `account_type_id`, `user_code`, `uname`, `email`, `mobile`, `pass_key`, `trans_pass_key`, `is_affiliate`, `security_pin`, `signedup_on`, `activated_on`, `expiry_on`, `status`, `block`, `is_closed`, `is_deleted`, `last_login`, `last_active`, `login_block`, `activation_key`) VALUES
(1, 1, 'admin', 'admin', 'admin@virob.com', '', 'e10adc3949ba59abbe56e057f20f883e', NULL, 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, NULL, '2018-07-23 05:30:17', 0, NULL),
(2, 2, 'customer', 'customer', 'customer@gmail.com', '9865797657', 'fcea920f7412b5da7be0cf42b8c93759', NULL, 1, NULL, NULL, NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-23 13:21:32', 0, NULL),
(3, 3, 'SUP42428129', 'SUP42428129', 'supplier@gmail.com', '9865797656', 'e10adc3949ba59abbe56e057f20f883e', NULL, 0, NULL, NULL, NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-10 05:58:14', 0, NULL),
(50, 2, 'USR1406', 'USR1406', 'jayaprakash.ejugiter@gmail.com', '9865797655', 'e10adc3949ba59abbe56e057f20f883e', '108c05962eb577730a7d6627aef0983c', 0, NULL, '2018-07-21 07:38:17', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 12:04:45', 0, NULL),
(74, 2, 'USR7793', 'USR7793', 'jayaprakashs.in@gmail.com', NULL, 'fcea920f7412b5da7be0cf42b8c93759', 'bacde42e874185d641f2621d4d2c6461', 0, NULL, '2018-07-23 09:32:35', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-23 09:45:19', 0, NULL),
(75, 2, 'USR1694', 'USR1694', 'jayaprakash.ssk@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '38d24354317f97e271e3b2925756852d', 0, NULL, '2018-07-24 05:55:14', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 05:55:14', 0, NULL),
(77, 2, 'USR9370', 'USR9370', 'jayaprakash.ssk1@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '39c399b0061206cd03b02b9e3ec5590f', 0, NULL, '2018-07-24 06:07:28', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:07:28', 0, NULL),
(78, 2, 'USR7130', 'USR7130', 'jayaprakash.ssk3@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '33d08651ac8e8a55d114c4696c9305da', 0, NULL, '2018-07-24 06:09:25', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:09:26', 0, NULL),
(79, 2, 'USR2050', 'USR2050', 'jayaprakash.ssk4@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '64028f1beb8a3e620b6a890492ffb5ac', 0, NULL, '2018-07-24 06:12:46', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:12:46', 0, NULL),
(80, 2, 'USR2819', 'USR2819', 'jayaprakash.ssk5@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '716e5df0b99ab054a44785fe7095cc1c', 0, NULL, '2018-07-24 06:13:36', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:13:36', 0, NULL),
(81, 2, 'USR1691', 'USR1691', 'jayaprakash.ssk6@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '7a1167312a8e83032069aa397a801fbc', 0, NULL, '2018-07-24 06:14:38', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:14:38', 0, NULL),
(83, 2, 'USR3876', 'USR3876', 'jayaprakash.ssk7@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '0aeca9558a93c95a9e25345f14c8178b', 0, NULL, '2018-07-24 06:17:28', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:17:28', 0, NULL),
(84, 2, 'USR2437', 'USR2437', 'jayaprakash.ssk8@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '58077085273aeb4bdaf70d0f73c54c47', 0, NULL, '2018-07-24 06:21:13', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:21:13', 0, NULL),
(86, 2, 'USR8572', 'USR8572', 'jayaprakash.ssk9@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', 'f869fc9201578b7f032726d702e9fd17', 0, NULL, '2018-07-24 06:23:01', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:23:02', 0, NULL),
(87, 2, 'USR3217', 'USR3217', 'jayaprakash.ssk10@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', 'f1d1ef9673418148665aed65d4ccb2d4', 0, NULL, '2018-07-24 06:38:29', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:42:02', 0, NULL),
(88, 2, 'USR8390', 'USR8390', 'jayaprakash.ssk11@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '70626a3ce2b4a448f778e077e4363a67', 0, NULL, '2018-07-24 06:42:52', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:42:52', 0, NULL),
(89, 2, 'USR6697', 'USR6697', 'jayaprakash.ssk12@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '83bca420966e9f5ecbd63c372f333881', 0, NULL, '2018-07-24 06:52:14', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:52:14', 0, NULL),
(90, 2, 'USR8556', 'USR8556', 'jayaprakash.ssk13@gmail.com', NULL, 'e10adc3949ba59abbe56e057f20f883e', '34b0dde75db4ebf8da3a58db491c3659', 0, NULL, '2018-07-24 06:54:26', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 06:54:27', 0, NULL),
(91, 2, 'USR2676', 'USR2676', 'jayaprakash.ssk14@gmail.com', '8668081112', 'e10adc3949ba59abbe56e057f20f883e', '3c5e028cc6ebc200a664fed0d394417b', 0, NULL, '2018-07-24 09:33:06', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 09:33:06', 0, NULL),
(92, 2, 'USR6017', 'USR6017', 'jayaprakash.ssk15@gmail.com', '8668081113', 'e10adc3949ba59abbe56e057f20f883e', '187c07b0358ae1eee05363852b5fd5f5', 0, NULL, '2018-07-24 09:35:54', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 09:35:54', 0, NULL),
(93, 2, 'USR3295', 'USR3295', 'jayaprakash.ssk16@gmail.com', '8668081114', 'e10adc3949ba59abbe56e057f20f883e', '19de21ebdfb2a60daf20a8a333a5d32e', 0, NULL, '2018-07-24 11:27:06', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 11:27:07', 0, NULL),
(94, 2, 'USR3564', 'USR3564', 'jayaprakash.ssk17@gmail.com', '8668081115', 'e10adc3949ba59abbe56e057f20f883e', 'd7d7393f7ac3018b5d7a3db6cbacbef7', 0, NULL, '2018-07-24 12:06:05', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 12:06:19', 0, NULL),
(95, 2, 'USR9708', 'USR9708', 'jayaprakash.ssk18@gmail.com', '8668081116', 'e10adc3949ba59abbe56e057f20f883e', '7481009296e97098a3a939a3f519c7d1', 0, NULL, '2018-07-24 13:02:43', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 13:02:43', 0, NULL),
(96, 2, 'USR8732', 'USR8732', 'jayaprakash.ssk19@gmail.com', '8668081119', 'e10adc3949ba59abbe56e057f20f883e', '52cfca12b1b2b505beed91d9e7f0a1bf', 0, NULL, '2018-07-24 13:54:45', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-24 13:54:45', 0, NULL),
(97, 2, 'USR3011', 'USR3011', 'jayaprakash.ssk20@gmail.com', '8668081122', 'e10adc3949ba59abbe56e057f20f883e', '896a9bb1ebe25b418c646c417ae5e451', 0, NULL, '2018-07-25 07:20:42', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-25 07:20:43', 0, NULL),
(98, 2, 'USR2028', 'USR2028', 'jayaprakash.ssk2@gmail.com', '8668081118', 'e10adc3949ba59abbe56e057f20f883e', 'bec5cf39b247ed6faf4dfc6a11813129', 0, NULL, '2018-07-25 07:40:52', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-25 08:50:35', 0, NULL),
(99, 2, 'USR2915', 'USR2915', 'jayaprakash.ssk22@gmail.com', '8668081123', '25f9e794323b453885f5181f1b624d0b', '7b508f951c8d531bb31c824c384e94ff', 0, NULL, '2018-07-25 08:14:51', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-25 09:23:25', 0, NULL),
(100, 2, 'USR8148', 'USR8148', 'jayaprakash.ssk23@gmail.com', '8668081124', 'e10adc3949ba59abbe56e057f20f883e', 'bde5300a57eed1db3513bccdc7b429e1', 0, NULL, '2018-07-25 08:58:53', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-28 05:31:07', 0, NULL),
(101, 2, 'USR5747', 'USR5747', 'jayaprakash.ssk25@gmail.com', '8668081125', 'e10adc3949ba59abbe56e057f20f883e', 'ccbfad76fde53edcf078baa49bad4d20', 0, NULL, '2018-07-28 08:03:28', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-28 08:03:28', 0, NULL),
(103, 2, 'USR1191', 'USR1191', 'jayaprakash.ssk26@gmail.com', '8668081126', 'e10adc3949ba59abbe56e057f20f883e', '0dc37f8b8c5ddc0875547775a700b89a', 0, NULL, '2018-07-28 08:11:10', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-28 08:11:10', 0, NULL),
(106, 2, 'USR2065', 'USR2065', 'jayaprakash.ssk27@gmail.com', '8668081127', 'e10adc3949ba59abbe56e057f20f883e', 'fcf0187aed8d2efaba570aa7df917f0b', 0, NULL, '2018-07-28 08:15:02', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-28 08:15:02', 0, NULL),
(108, 2, 'USR6474', 'USR6474', 'jayaprakash.ssk28@gmail.com', '8668081128', 'e10adc3949ba59abbe56e057f20f883e', '1ce8a1974c774e6fa1b9ba9c3727471c', 0, NULL, '2018-07-28 08:17:38', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-28 08:17:38', 0, NULL),
(111, 2, 'USR6105', 'USR6105', 'jayaprakash.ssk30@gmail.com', '8668081130', 'e10adc3949ba59abbe56e057f20f883e', 'dce2b2394d4fd277692e55c45b742ceb', 0, NULL, '2018-07-28 08:20:06', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-28 08:20:06', 0, NULL),
(112, 2, 'USR9299', 'USR9299', 'jayaprakash.ssk31@gmail.com', '8668081131', 'e10adc3949ba59abbe56e057f20f883e', 'ccd7460f11604f12dfa409be8f9235e1', 0, NULL, '2018-07-28 08:34:53', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-28 08:34:53', 0, NULL),
(113, 2, 'USR1641', 'USR1641', 'jayaprakash.ssk32@gmail.com', '8668081132', 'e10adc3949ba59abbe56e057f20f883e', 'b31e71559d5a531d4a31b9fd3cb651a7', 0, NULL, '2018-07-28 08:42:19', NULL, NULL, 1, 0, 0, 0, NULL, '2018-07-28 08:42:19', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `account_notifications`
--

CREATE TABLE IF NOT EXISTS `account_notifications` (
  `notification_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `notification_type` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `access_type` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `statementline_id` smallint(4) unsigned NOT NULL DEFAULT '0',
  `account_ids` text,
  `data` text NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`notification_id`),
  KEY `notification_type` (`notification_type`,`access_type`,`statementline_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65 ;

--
-- Dumping data for table `account_notifications`
--

INSERT INTO `account_notifications` (`notification_id`, `notification_type`, `access_type`, `statementline_id`, `account_ids`, `data`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-05 23:52:56', '2017-05-05 18:52:56', 0),
(2, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-06 00:59:34', '2017-05-05 19:59:34', 0),
(3, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-06 01:35:06', '2017-05-05 20:35:06', 0),
(4, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-06 01:50:59', '2017-05-05 20:50:59', 0),
(5, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-06 01:51:53', '2017-05-05 20:51:53', 0),
(6, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-06 01:52:39', '2017-05-05 20:52:39', 0),
(7, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-06 01:52:46', '2017-05-05 20:52:46', 0),
(8, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-06 01:53:30', '2017-05-05 20:53:30', 0),
(9, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-06 02:19:12', '2017-05-05 21:19:12', 0),
(10, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-06 02:19:19', '2017-05-05 21:19:19', 0),
(11, 0, 0, 0, '2', '{"title":"This is a title. title","body":"here is a message. message","icon":"small_icon","color":"#111111","sound":true,"vibrate":true,"click_action":"http:\\/\\/localhost\\/5dg-shopping-portal"}', '2017-05-06 02:19:29', '2017-05-05 21:19:29', 0),
(32, 0, 0, 0, '1', '{"title":"admin.sms.order.placed_to_approved.title","body":"admin.sms.order.placed_to_approved.body","click_action":"my-orders","icon":"","color":"#111111","sound":true,"vibrate":true}', '2017-06-20 00:24:28', '2017-06-19 19:24:28', 0),
(33, 0, 0, 0, '2', '{"title":"Your Order has been approved","body":"Your Order has been approved","click_action":"my-orders","icon":"","color":"#111111","sound":true,"vibrate":true}', '2017-06-20 00:24:29', '2017-06-19 19:24:29', 0),
(34, 0, 0, 0, '3', '{"title":"supplier.sms.order.placed_to_approved.title","body":"supplier.sms.order.placed_to_approved.body","click_action":"my-orders","icon":"","color":"#111111","sound":true,"vibrate":true}', '2017-06-20 00:24:32', '2017-06-19 19:24:32', 0),
(35, 0, 0, 0, '2', '{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}', '2017-07-15 00:59:15', '2017-07-14 19:59:15', 0),
(36, 0, 0, 0, '2', '{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}', '2017-07-15 00:59:47', '2017-07-14 19:59:47', 0),
(37, 0, 0, 0, '2', '{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}', '2017-07-15 01:00:04', '2017-07-14 20:00:04', 0),
(38, 0, 0, 0, '2', '{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}', '2017-07-15 01:06:55', '2017-07-14 20:06:55', 0),
(39, 0, 0, 0, '2', '{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}', '2017-07-15 01:12:44', '2017-07-14 20:12:44', 0),
(40, 0, 0, 0, '2', '{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}', '2017-07-15 01:13:03', '2017-07-14 20:13:03', 0),
(41, 0, 0, 0, '2', '{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}', '2017-07-15 01:13:08', '2017-07-14 20:13:08', 0),
(42, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:16:02', '2017-07-14 20:16:02', 0),
(43, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:16:17', '2017-07-14 20:16:17', 0),
(44, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:16:37', '2017-07-14 20:16:37', 0),
(45, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:16:42', '2017-07-14 20:16:42', 0),
(46, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:25:29', '2017-07-14 20:25:29', 0),
(47, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:25:36', '2017-07-14 20:25:36', 0),
(48, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:26:02', '2017-07-14 20:26:02', 0),
(49, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:33:32', '2017-07-14 20:33:32', 0),
(50, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:33:40', '2017-07-14 20:33:40', 0),
(51, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:36:18', '2017-07-14 20:36:18', 0),
(52, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:37:03', '2017-07-14 20:37:03', 0),
(53, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:38:05', '2017-07-14 20:38:05', 0),
(54, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:38:17', '2017-07-14 20:38:17', 0),
(55, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:38:28', '2017-07-14 20:38:28', 0),
(56, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:43:58', '2017-07-14 20:43:58', 0),
(57, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:44:26', '2017-07-14 20:44:26', 0),
(58, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:44:40', '2017-07-14 20:44:40', 0),
(59, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:46:14', '2017-07-14 20:46:14', 0),
(60, 0, 0, 0, '2', '{"notification":{"title":"test","body":"test","click_action":"","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-07-15 01:46:29', '2017-07-14 20:46:29', 0),
(61, 0, 0, 0, '1', '{"notification":{"title":"Your order item has been approved","body":"Your order item has been approved","click_action":"my-orders","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-08-11 03:11:11', '2017-08-10 22:11:11', 0),
(62, 0, 0, 0, '3', '{"notification":{"title":"Your order item has been approved","body":"Your order item has been approved","click_action":"my-orders","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-08-11 03:11:12', '2017-08-10 22:11:12', 0),
(63, 0, 0, 0, '1', '{"notification":{"title":"Your Order has been approved","body":"Your Order has been approved","click_action":"my-orders","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-08-11 03:11:13', '2017-08-10 22:11:13', 0),
(64, 0, 0, 0, '3', '{"notification":{"title":"Your Order has been approved","body":"Your Order has been approved","click_action":"my-orders","icon":"","color":"#111111","sound":true,"vibrate":true}}', '2017-08-11 03:11:13', '2017-08-10 22:11:13', 0);

-- --------------------------------------------------------

--
-- Table structure for table `account_notifications_read`
--

CREATE TABLE IF NOT EXISTS `account_notifications_read` (
  `notification_id` bigint(20) unsigned NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `read_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`,`account_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `account_preference`
--

CREATE TABLE IF NOT EXISTS `account_preference` (
  `us_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) NOT NULL,
  `language_id` smallint(2) unsigned DEFAULT '0',
  `country_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `currency_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `is_email_verified` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `is_mobile_verified` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `activation_key` varchar(50) DEFAULT NULL,
  `pwd_reset_key` varchar(50) DEFAULT NULL,
  `pwd_reset_key_sess` time DEFAULT NULL,
  `email_verification_key` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `change_email` tinyint(4) NOT NULL COMMENT '0 - No , 1 - Yes',
  `change_payment` tinyint(4) NOT NULL COMMENT '0 - No, 1 - Yes',
  `transaction_pswd_user_edit` tinyint(4) NOT NULL COMMENT '0 - No, 1 - Yes',
  `deposite` tinyint(4) NOT NULL COMMENT '0 - No, 1 -Yes',
  `withdrawal` smallint(6) NOT NULL DEFAULT '1' COMMENT '0 - No, 1 - Yes',
  `create_tickets` tinyint(4) NOT NULL COMMENT '0 - No, 1- Yes',
  `refer_friend` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0 - No, 1 - Yes',
  `promotion_tool` tinyint(4) NOT NULL COMMENT '0 - No, 1 - Yes',
  `send_sms` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `send_email` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `send_notification` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `otp_status` smallint(6) NOT NULL,
  `ip_access` tinyint(1) NOT NULL DEFAULT '0',
  `system_fee` int(1) NOT NULL DEFAULT '0',
  `campaign_edit` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - KYC compulsor, 1 - KYC not compulsory',
  `rewards` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `is_verified` tinyint(4) NOT NULL DEFAULT '0',
  `activation_log` text,
  `is_paid` tinyint(1) unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`us_id`),
  KEY `currency_id` (`currency_id`),
  KEY `ip_access` (`ip_access`),
  KEY `country_id` (`country_id`),
  KEY `send_sms` (`send_sms`),
  KEY `send_notification` (`send_notification`),
  KEY `send_email` (`send_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=101 ;

--
-- Dumping data for table `account_preference`
--

INSERT INTO `account_preference` (`us_id`, `account_id`, `language_id`, `country_id`, `currency_id`, `is_email_verified`, `is_mobile_verified`, `activation_key`, `pwd_reset_key`, `pwd_reset_key_sess`, `email_verification_key`, `change_email`, `change_payment`, `transaction_pswd_user_edit`, `deposite`, `withdrawal`, `create_tickets`, `refer_friend`, `promotion_tool`, `send_sms`, `send_email`, `send_notification`, `otp_status`, `ip_access`, `system_fee`, `campaign_edit`, `rewards`, `is_verified`, `activation_log`, `is_paid`, `updated_on`) VALUES
(1, 1, 1, 77, 2, 0, 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(3, 2, 1, 77, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(4, 3, 1, 77, 2, 1, 1, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(48, 50, 0, 77, 2, 0, 0, '85241532158697', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(70, 74, 0, 77, 2, 0, 0, '70081532338355', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(71, 75, 0, 77, 2, 0, 0, '37551532411714', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(72, 77, 0, 77, 2, 0, 0, '57521532412448', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(73, 78, 0, 77, 2, 0, 0, '30761532412565', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(74, 79, 0, 77, 2, 0, 0, '59911532412766', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(75, 80, 0, 77, 2, 0, 0, '60671532412816', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(76, 81, 0, 77, 2, 0, 0, '95111532412878', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(77, 83, 0, 77, 2, 0, 0, '88231532413048', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(78, 84, 0, 77, 2, 0, 0, '54361532413273', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(79, 86, 0, 77, 2, 0, 0, '41501532413381', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(80, 87, 0, 77, 2, 0, 0, '11391532414309', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(81, 88, 0, 77, 2, 0, 1, '30591532414572', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(82, 89, 0, 77, 2, 0, 1, '42191532415134', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(83, 90, 0, 77, 2, 0, 1, '97071532415266', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(84, 91, 0, 77, 2, 0, 1, '90161532424786', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(85, 92, 0, 77, 2, 0, 1, '92881532424954', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(86, 93, 0, 77, 2, 0, 1, '52101532431626', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(87, 94, 0, 77, 2, 0, 1, '88771532433965', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(88, 95, 0, 77, 2, 0, 1, '95381532437363', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(89, 96, 0, 77, 2, 0, 1, '85391532440485', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(90, 97, 0, 77, 2, 0, 1, '62271532503242', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(91, 98, 0, 77, 2, 0, 1, '24051532504452', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(92, 99, 0, 77, 2, 0, 1, '17021532506491', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(93, 100, 0, 77, 2, 0, 1, '17941532509133', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(94, 101, 0, 77, 2, 0, 1, '36181532765008', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(95, 103, 0, 77, 2, 0, 1, '22101532765470', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(96, 106, 0, 77, 2, 0, 1, '87581532765702', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(97, 108, 0, 77, 2, 0, 1, '19681532765858', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(98, 111, 0, 77, 2, 0, 1, '15361532766006', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(99, 112, 0, 77, 2, 0, 1, '79461532766893', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(100, 113, 0, 77, 2, 0, 1, '49631532767339', NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `account_ratings`
--

CREATE TABLE IF NOT EXISTS `account_ratings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_type_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `relative_post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `account_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comments` smallint(6) DEFAULT NULL,
  `rating` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status_id` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1-publish,2-unpublish',
  `is_verified` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-unverified,1-verified,2-rejected',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_type_id` (`post_type_id`),
  KEY `relative_post_id` (`relative_post_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `account_ratings`
--

INSERT INTO `account_ratings` (`id`, `post_type_id`, `relative_post_id`, `account_id`, `comments`, `rating`, `status_id`, `is_verified`, `is_deleted`, `created_on`, `updated_on`) VALUES
(1, 5, 1, 2, NULL, 3, 1, 1, 0, NULL, '2018-03-14 12:38:00'),
(2, 3, 0, 2, NULL, 4, 0, 0, 0, '2017-06-26 03:52:54', '2017-06-25 22:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `account_sale_points`
--

CREATE TABLE IF NOT EXISTS `account_sale_points` (
  `account_id` bigint(20) unsigned NOT NULL,
  `bv` double unsigned NOT NULL DEFAULT '0' COMMENT 'Business Volume',
  `cv` double NOT NULL DEFAULT '0' COMMENT 'Commission Volume',
  `qv` double NOT NULL DEFAULT '0' COMMENT 'Qualified Volume',
  `gqv` double NOT NULL DEFAULT '0' COMMENT 'Group Qualification Volume',
  `af_rank_id` smallint(4) unsigned NOT NULL DEFAULT '0',
  `my_package` int(10) unsigned NOT NULL DEFAULT '0',
  `package_updated_on` datetime DEFAULT NULL,
  KEY `account_id` (`account_id`),
  KEY `af_rank_id` (`af_rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `account_status_lookups`
--

CREATE TABLE IF NOT EXISTS `account_status_lookups` (
  `status_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(30) NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `status_name` (`status_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `account_status_lookups`
--

INSERT INTO `account_status_lookups` (`status_id`, `status_name`, `is_deleted`, `updated_on`) VALUES
(1, 'Active', 0, '2016-08-03 21:00:54'),
(2, 'Inactive', 0, '2016-08-03 21:00:54'),
(3, 'Expired', 0, '2016-08-03 21:00:54'),
(4, 'Draft', 0, '2016-08-03 21:00:54');

-- --------------------------------------------------------

--
-- Table structure for table `account_subscription`
--

CREATE TABLE IF NOT EXISTS `account_subscription` (
  `subscribe_id` bigint(20) NOT NULL,
  `topup_id` bigint(20) DEFAULT NULL,
  `purchase_code` varchar(20) DEFAULT NULL,
  `account_id` bigint(20) NOT NULL DEFAULT '0',
  `package_id` int(10) NOT NULL DEFAULT '0',
  `package_level` smallint(3) NOT NULL DEFAULT '0',
  `transaction_id` varchar(50) DEFAULT NULL,
  `payment_type` smallint(4) NOT NULL DEFAULT '0' COMMENT '1-E-Wallet, 2-Bank Transfer, 3-Paypal,4-Perfect Money',
  `pg_relation_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `currency_id` smallint(3) NOT NULL DEFAULT '0',
  `wallet_id` smallint(2) DEFAULT NULL,
  `amount` decimal(10,3) unsigned NOT NULL DEFAULT '0.000',
  `handle_amt` decimal(10,3) unsigned NOT NULL,
  `paid_amt` decimal(10,3) unsigned NOT NULL DEFAULT '0.000',
  `package_qv` smallint(7) unsigned NOT NULL DEFAULT '0',
  `weekly_capping_qv` smallint(7) unsigned NOT NULL DEFAULT '0',
  `shopping_points` smallint(7) unsigned NOT NULL DEFAULT '0',
  `is_adjustment_package` tinyint(1) NOT NULL DEFAULT '0',
  `is_upgradable` tinyint(1) NOT NULL DEFAULT '0',
  `is_refundable` tinyint(1) NOT NULL DEFAULT '0',
  `refund_expire_on` datetime DEFAULT NULL,
  `order_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-purchased, 2-renewel',
  `status` smallint(2) NOT NULL DEFAULT '0' COMMENT '0-Pending, 1 - Confirmed, 2 - Expired,3 - Cancelled,4-Wait for Approval',
  `payment_status` smallint(2) NOT NULL DEFAULT '0' COMMENT '0->Pending ,1->Paid , 2->Not Paid(Failed),3-Declined',
  `purchased_date` datetime DEFAULT NULL,
  `confirm_date` datetime DEFAULT NULL,
  `expire_on` datetime DEFAULT NULL,
  `upgraded_on` datetime DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `account_subscription_topup`
--

CREATE TABLE IF NOT EXISTS `account_subscription_topup` (
  `subscribe_topup_id` bigint(20) NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `subscribe_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `purchase_code` varchar(20) DEFAULT NULL,
  `from_package_id` int(10) unsigned NOT NULL DEFAULT '0',
  `package_id` int(10) unsigned NOT NULL DEFAULT '0',
  `package_level` smallint(3) NOT NULL DEFAULT '0',
  `transaction_id` varchar(30) DEFAULT NULL,
  `order_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `payment_type` smallint(4) NOT NULL DEFAULT '0' COMMENT '1-E-Wallet, 2-Bank Transfer, 3-Paypal,4-Perfect Money',
  `pg_relational_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `amount` double unsigned NOT NULL DEFAULT '0',
  `handle_amt` double unsigned NOT NULL DEFAULT '0',
  `paid_amt` double unsigned NOT NULL DEFAULT '0',
  `package_qv` smallint(7) unsigned NOT NULL DEFAULT '0',
  `weekly_capping_qv` bigint(20) unsigned NOT NULL DEFAULT '0',
  `shopping_points` smallint(7) unsigned NOT NULL DEFAULT '0',
  `currency_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `wallet_id` smallint(2) unsigned NOT NULL,
  `invoice_path` text,
  `expire_days` smallint(3) NOT NULL DEFAULT '0',
  `create_date` datetime DEFAULT NULL,
  `confirm_date` datetime DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `is_adjustment_package` tinyint(1) NOT NULL DEFAULT '0',
  `is_upgradable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_refundable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `refundable_days` smallint(3) unsigned DEFAULT '0',
  `refund_expire_on` datetime DEFAULT NULL,
  `payment_status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0->Pending ,1->Paid , 2->Not Paid(Failed),3-Declined',
  `status` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '0-Pending, 1 - Confirmed, 2 - Expired,3 - Cancelled,4-Wait for Approval',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  `processing_data` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `account_transaction`
--

CREATE TABLE IF NOT EXISTS `account_transaction` (
  `id` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `from_account_id` int(10) unsigned DEFAULT '0',
  `to_account_id` int(10) unsigned DEFAULT '0',
  `transaction_id` varchar(100) DEFAULT NULL,
  `transaction_type` tinyint(1) unsigned DEFAULT '0' COMMENT '0-Debit, 1-Credit',
  `relation_id` text,
  `statementline_id` tinyint(3) unsigned DEFAULT NULL,
  `amt` double(12,2) NOT NULL DEFAULT '0.00',
  `paid_amt` double(12,2) NOT NULL DEFAULT '0.00',
  `handle_amt` double(12,2) NOT NULL DEFAULT '0.00',
  `tax` double(12,2) NOT NULL DEFAULT '0.00',
  `tds` double(12,2) NOT NULL DEFAULT '0.00',
  `current_balance` double(12,2) NOT NULL DEFAULT '0.00',
  `payment_type_id` tinyint(3) unsigned DEFAULT NULL,
  `currency_id` tinyint(3) unsigned DEFAULT NULL,
  `wallet_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Pending, 1-Confirmed, 2-Cancelled',
  `ip_address` varchar(50) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `payment_type_id` (`payment_type_id`),
  KEY `currency_id` (`currency_id`),
  KEY `wallet_id` (`wallet_id`),
  KEY `statementline_id` (`statementline_id`),
  KEY `from_account_id` (`from_account_id`),
  KEY `to_account_id` (`to_account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `account_transaction`
--

INSERT INTO `account_transaction` (`id`, `account_id`, `from_account_id`, `to_account_id`, `transaction_id`, `transaction_type`, `relation_id`, `statementline_id`, `amt`, `paid_amt`, `handle_amt`, `tax`, `tds`, `current_balance`, `payment_type_id`, `currency_id`, `wallet_id`, `remark`, `status`, `ip_address`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 100, 100, 2, '5209072018111611569', 0, '21', 30, 1111.00, 1111.00, 0.00, 0.00, 0.00, 5000.00, 21, 2, 1, 'Purchase', 1, '::1', '2018-07-09 11:16:11', '2018-07-27 04:50:30', 0),
(2, 100, 50, 100, '7409072018111722271', 1, '22', 31, 2222.00, 2222.00, 0.00, 0.00, 0.00, 5000.00, 21, 2, 1, '{"data":[]}', 0, '::1', '2018-07-09 11:17:22', '2018-07-27 05:11:05', 0),
(3, 1, 2, 157, '9209072018111834735', 1, '23', NULL, 333.00, 333.00, 0.00, 0.00, 0.00, 5000.00, 21, 2, 1, 'Purchase - Credit', 2, '::1', '2018-07-09 11:18:34', '2018-07-26 13:20:28', 0),
(4, 1, 2, 157, '9909072018111926293', 1, '24', NULL, 333.00, 333.00, 0.00, 0.00, 0.00, 5000.00, 21, 2, 1, 'Purchase - Debit', 2, '::1', '2018-07-09 11:19:26', '2018-07-26 13:20:41', 0),
(5, 1, 2, 157, '3309072018112029879', 1, '25', NULL, 100.00, 100.00, 0.00, 0.00, 0.00, 5000.00, 21, 2, 1, '{"data":[]}', 2, '::1', '2018-07-09 11:20:29', '2018-07-17 08:44:14', 0);

-- --------------------------------------------------------

--
-- Table structure for table `account_tree`
--

CREATE TABLE IF NOT EXISTS `account_tree` (
  `account_id` bigint(20) DEFAULT NULL,
  `upline_id` bigint(20) DEFAULT NULL,
  `sponsor_id` bigint(20) unsigned DEFAULT '0',
  `my_extream_right` bigint(20) DEFAULT NULL,
  `lft_node` bigint(20) NOT NULL DEFAULT '0',
  `rgt_node` bigint(20) NOT NULL DEFAULT '0',
  `sponsor_lineage` text,
  `referral_cnts` int(10) unsigned NOT NULL DEFAULT '0',
  `referral_paid_cnts` int(10) unsigned NOT NULL DEFAULT '0',
  `nwroot_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lineage` text,
  `rank` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `position_lineage` text,
  `can_sponsor` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `recent_package_id` int(10) unsigned NOT NULL DEFAULT '0',
  `recent_package_purchased_on` datetime DEFAULT NULL,
  UNIQUE KEY `account_id` (`account_id`),
  KEY `sponsor_id` (`sponsor_id`),
  KEY `rank` (`rank`),
  KEY `level` (`level`),
  KEY `lft_node` (`lft_node`),
  KEY `rgt_node` (`rgt_node`),
  KEY `upline_id` (`upline_id`),
  KEY `recent_package_id` (`recent_package_id`),
  KEY `root_id` (`nwroot_id`),
  KEY `can_sponsor` (`can_sponsor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `account_tree`
--

INSERT INTO `account_tree` (`account_id`, `upline_id`, `sponsor_id`, `my_extream_right`, `lft_node`, `rgt_node`, `sponsor_lineage`, `referral_cnts`, `referral_paid_cnts`, `nwroot_id`, `lineage`, `rank`, `level`, `position_lineage`, `can_sponsor`, `recent_package_id`, `recent_package_purchased_on`) VALUES
(2, NULL, 0, NULL, 0, 0, NULL, 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(50, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(74, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(75, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(77, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(78, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(79, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(80, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(81, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(83, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(84, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(86, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(87, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(88, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(89, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(90, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(91, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(92, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(93, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(94, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(95, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(96, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(97, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(98, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(99, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(100, NULL, 2, NULL, 0, 0, '2/', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(101, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(103, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(106, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(108, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(111, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(112, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL),
(113, NULL, 0, NULL, 0, 0, '', 0, 0, 0, NULL, 0, 0, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE IF NOT EXISTS `account_types` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `account_privillage` tinyint(2) unsigned DEFAULT '0',
  `hierarchy` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `account_type_name` varchar(30) DEFAULT NULL,
  `account_type_key` varchar(100) DEFAULT NULL,
  `has_wallet` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_system_user` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_type_key` (`account_type_key`),
  UNIQUE KEY `account_type_name` (`account_type_name`),
  KEY `account_privillage` (`account_privillage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `account_types`
--

INSERT INTO `account_types` (`id`, `account_privillage`, `hierarchy`, `account_type_name`, `account_type_key`, `has_wallet`, `is_system_user`) VALUES
(1, 1, 0, 'Admin', 'admin', 0, 1),
(2, 1, 1, 'Customer', 'customer', 1, 0),
(3, 1, 2, 'Seller', 'seller', 1, 0),
(4, 1, 3, 'Franchisee', 'franchisee', 1, 0),
(6, 1, 6, 'Sys-Merchant', 'sys-merchant', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `account_verification`
--

CREATE TABLE IF NOT EXISTS `account_verification` (
  `uv_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `path` varchar(200) DEFAULT NULL,
  `content_type` varchar(10) DEFAULT NULL,
  `document_type_id` tinyint(3) unsigned DEFAULT NULL,
  `other_fields` text,
  `status_id` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Pending, 1-Verified, 2-Cancelled ',
  `is_verified` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  `verified_on` datetime DEFAULT NULL,
  `cancelled_on` datetime DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uv_id`),
  KEY `document_type_id` (`document_type_id`),
  KEY `status` (`status_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `account_verification`
--

INSERT INTO `account_verification` (`uv_id`, `account_id`, `path`, `content_type`, `document_type_id`, `other_fields`, `status_id`, `is_verified`, `is_deleted`, `verified_on`, `cancelled_on`, `created_on`, `updated_on`) VALUES
(1, 4, 'resources/uploads/suppliers/pan_cards/PANCARD_53_resort.jpg', NULL, 19, NULL, 0, 0, 0, NULL, NULL, '2018-07-17 11:43:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `account_wallet_balance`
--

CREATE TABLE IF NOT EXISTS `account_wallet_balance` (
  `balance_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned DEFAULT '0',
  `wallet_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `current_balance` double NOT NULL DEFAULT '0',
  `tot_credit` double NOT NULL DEFAULT '0',
  `tot_debit` double NOT NULL DEFAULT '0',
  `order_freezed_balance` double unsigned NOT NULL DEFAULT '0',
  `freezed_balance` double unsigned NOT NULL DEFAULT '0',
  `updated_on` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`balance_id`),
  KEY `user_id` (`account_id`),
  KEY `ewallet_id` (`wallet_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `account_wallet_balance`
--

INSERT INTO `account_wallet_balance` (`balance_id`, `account_id`, `currency_id`, `wallet_id`, `current_balance`, `tot_credit`, `tot_debit`, `order_freezed_balance`, `freezed_balance`, `updated_on`) VALUES
(1, 100, 2, 1, 5000, 5000, 0, 0, 0, '2018-07-25 18:30:00'),
(2, 100, 2, 2, 5000, 5000, 0, 0, 0, '2018-07-25 18:30:00'),
(3, 100, 2, 3, 5000, 5000, 0, 0, 0, '2018-07-25 18:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `account_wish_list`
--

CREATE TABLE IF NOT EXISTS `account_wish_list` (
  `wish_list_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_product_id` bigint(15) unsigned NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`wish_list_id`),
  UNIQUE KEY `supplier_product_id_2` (`supplier_product_id`,`account_id`),
  UNIQUE KEY `supplier_product_id` (`supplier_product_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `address_mst`
--

CREATE TABLE IF NOT EXISTS `address_mst` (
  `address_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '3 - Seller, 4 - Store',
  `relative_post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `address_type_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `flatno_street` varchar(100) DEFAULT NULL COMMENT 'FlatNo & street',
  `address` varchar(100) DEFAULT NULL,
  `landmark` varchar(75) DEFAULT NULL,
  `city_id` int(10) unsigned DEFAULT '0' COMMENT 'City,Town',
  `state_id` int(10) unsigned NOT NULL DEFAULT '0',
  `country_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `postal_code` varchar(15) NOT NULL,
  `geolat` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `geolng` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `status` tinyint(1) unsigned NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`address_id`),
  KEY `account_id` (`relative_post_id`),
  KEY `address_type_id` (`address_type_id`),
  KEY `country_id` (`country_id`),
  KEY `state_id` (`state_id`),
  KEY `city_id` (`city_id`),
  KEY `post_type` (`post_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `address_mst`
--

INSERT INTO `address_mst` (`address_id`, `post_type`, `relative_post_id`, `address_type_id`, `flatno_street`, `address`, `landmark`, `city_id`, `state_id`, `country_id`, `postal_code`, `geolat`, `geolng`, `status`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 3, 53, 1, 'Add1', 'Add2', NULL, 839874, 31, 77, '600033', '0.000000', '0.000000', 0, NULL, '2018-07-17 11:41:38', 0),
(2, 4, 2, 1, 'add1', 'add2', NULL, 839874, 31, 77, '600033', '0.000000', '0.000000', 0, NULL, '2018-07-17 06:12:40', 0);

-- --------------------------------------------------------

--
-- Table structure for table `address_type_lookup`
--

CREATE TABLE IF NOT EXISTS `address_type_lookup` (
  `address_type_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `address_type` varchar(200) NOT NULL,
  PRIMARY KEY (`address_type_id`),
  UNIQUE KEY `address_type` (`address_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `address_type_lookup`
--

INSERT INTO `address_type_lookup` (`address_type_id`, `address_type`) VALUES
(1, 'Primary Address'),
(2, 'Secondary Address'),
(3, 'Shippinh Address'),
(4, 'Warehouse');

-- --------------------------------------------------------

--
-- Table structure for table `add_fund_mst`
--

CREATE TABLE IF NOT EXISTS `add_fund_mst` (
  `uaf_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(50) NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `payment_type_id` tinyint(3) unsigned DEFAULT NULL,
  `from_currency_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `wallet_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `currency_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `handle_amt` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `paid_amt` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `relation_id` bigint(20) unsigned DEFAULT NULL,
  `comment` text,
  `payment_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Pending, 1-Confirmed, 2-Cancelled, 3-Failed',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 -Pending, 1- Confirmed, 3- Cancelled, 4 - failure, 5 - process',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  `auto_credit` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-No, 1-Yes',
  `created_on` datetime DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uaf_id`),
  KEY `account_id` (`account_id`),
  KEY `payment_type_id` (`payment_type_id`),
  KEY `wallet_id` (`wallet_id`),
  KEY `currency_id` (`currency_id`),
  KEY `updated_by` (`updated_by`),
  KEY `from_currency_id` (`from_currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `admin_mst`
--

CREATE TABLE IF NOT EXISTS `admin_mst` (
  `admin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `admin_name` varchar(35) DEFAULT NULL,
  `privilege` text,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - Inactive, 1 - Active',
  `login_block` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`admin_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin_mst`
--

INSERT INTO `admin_mst` (`admin_id`, `account_id`, `admin_name`, `privilege`, `status`, `login_block`, `created_on`, `is_deleted`) VALUES
(1, 1, 'admin', '', 1, 0, '2016-08-17 04:37:09', 0);

-- --------------------------------------------------------

--
-- Table structure for table `aff_package_lang`
--

CREATE TABLE IF NOT EXISTS `aff_package_lang` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `package_id` int(10) NOT NULL,
  `lang_id` smallint(3) unsigned NOT NULL DEFAULT '1',
  `package_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `description` text CHARACTER SET utf8,
  `package_features` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `aff_package_lang`
--

INSERT INTO `aff_package_lang` (`id`, `package_id`, `lang_id`, `package_name`, `description`, `package_features`) VALUES
(1, 1, 1, 'Starter', NULL, NULL),
(2, 2, 1, 'Basic d', 'slideUp() method animates the height of the matched elements. This causes lower parts of the page to slide up, appearing to conceal the items.', NULL),
(3, 3, 1, 'Premium', 'slideUp() method animates the height of the matched elements. This causes lower parts of the page to slide up, appearing to conceal the items.', NULL),
(4, 4, 1, 'Business', NULL, NULL),
(5, 5, 1, 'Platinum', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `aff_package_mst`
--

CREATE TABLE IF NOT EXISTS `aff_package_mst` (
  `package_id` int(10) NOT NULL AUTO_INCREMENT,
  `package_code` varchar(30) DEFAULT NULL,
  `package_level` smallint(3) unsigned NOT NULL DEFAULT '0',
  `refundable_days` smallint(2) unsigned NOT NULL DEFAULT '0',
  `expire_days` smallint(3) unsigned NOT NULL DEFAULT '0',
  `package_image` varchar(80) DEFAULT NULL,
  `is_refundable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_upgradable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_adjustment_package` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `instant_benefit_credit` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0 - InActive, 1 - Active',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `expire_on` datetime DEFAULT NULL,
  PRIMARY KEY (`package_id`),
  KEY `pack_level` (`package_level`),
  KEY `status` (`status`),
  KEY `is_upgradable` (`is_upgradable`),
  KEY `is_adjustment_package` (`is_adjustment_package`),
  KEY `is_refundable` (`is_refundable`),
  KEY `order_type` (`order_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `aff_package_mst`
--

INSERT INTO `aff_package_mst` (`package_id`, `package_code`, `package_level`, `refundable_days`, `expire_days`, `package_image`, `is_refundable`, `is_upgradable`, `is_adjustment_package`, `instant_benefit_credit`, `order_type`, `status`, `is_deleted`, `created_on`, `updated_on`, `expire_on`) VALUES
(1, '1212', 0, 11, 12, 'Jellyfish.jpg', 0, 0, 0, 0, 0, 1, 0, '2017-02-08 16:06:08', '2017-02-08 16:06:08', NULL),
(2, '1212', 1, 10, 30, 'Desert.jpg', 1, 1, 0, 0, 0, 1, 0, '2017-02-10 16:06:08', '2017-02-08 16:06:08', NULL),
(3, '1111', 2, 10, 11, 'Hydrangeas.jpg', 0, 1, 0, 0, 0, 1, 0, '2017-02-09 16:06:08', '2017-02-08 16:06:08', NULL),
(4, '1211', 3, 10, 11, NULL, 0, 1, 0, 0, 0, 1, 0, '2017-02-02 06:08:00', '2017-02-08 16:06:08', NULL),
(5, '123554', 4, 10, 0, NULL, 0, 0, 0, 0, 0, 1, 0, '2017-02-03 00:00:00', '2017-02-08 16:06:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `aff_package_pricing`
--

CREATE TABLE IF NOT EXISTS `aff_package_pricing` (
  `pack_price_id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned NOT NULL DEFAULT '0',
  `currency_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `price` double unsigned NOT NULL DEFAULT '0',
  `package_qv` double unsigned DEFAULT '0',
  `weekly_capping_qv` double unsigned NOT NULL DEFAULT '0',
  `shopping_points` double unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pack_price_id`),
  KEY `currency_id` (`currency_id`),
  KEY `package_id` (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `aff_package_pricing`
--

INSERT INTO `aff_package_pricing` (`pack_price_id`, `package_id`, `currency_id`, `price`, `package_qv`, `weekly_capping_qv`, `shopping_points`, `created_on`, `modified_date`, `modified_by`) VALUES
(1, 1, 12, 45, 0, 500, 3000, '2017-02-08 06:21:27', '2017-02-08 05:29:39', 1),
(2, 2, 1, 75, 25, 1000, 5000, '0000-00-00 00:00:00', '2017-02-08 18:44:41', 1),
(3, 3, 1, 150, 50, 1500, 10000, '2017-02-09 18:44:41', '2017-02-09 18:44:41', 1),
(4, 4, 1, 375, 125, 2000, 25000, '2017-02-10 18:44:41', '2017-02-10 18:44:41', 1),
(5, 5, 1, 750, 300, 2500, 50000, '2017-02-18 18:44:41', '2017-02-18 18:44:41', 1);

-- --------------------------------------------------------

--
-- Table structure for table `af_binary_bonus`
--

CREATE TABLE IF NOT EXISTS `af_binary_bonus` (
  `bid` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) NOT NULL DEFAULT '0',
  `package_id` int(10) NOT NULL DEFAULT '0',
  `bonus_value` double NOT NULL,
  `bonus_value_in` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - Percentage, 1 - Flat',
  `bonus_type` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '3-team commission,4-leader ship commission',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1-1G:2G,2=1G+2G:3G',
  `leftbinpnt` double NOT NULL,
  `rightbinpnt` double NOT NULL,
  `leftclubpoint` double NOT NULL,
  `rightclubpoint` double NOT NULL,
  `clubpoint` double NOT NULL,
  `totleftbinpnt` double NOT NULL,
  `totrightbinpnt` double NOT NULL,
  `leftcarryfwd` double NOT NULL,
  `rightcarryfwd` double NOT NULL,
  `income` double NOT NULL,
  `flushamt` double NOT NULL,
  `paidinc` double NOT NULL,
  `wallet_id` smallint(1) unsigned NOT NULL DEFAULT '0',
  `currency_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - Pending, 1 - Confirm , 2-lapsed',
  `from_date` datetime DEFAULT NULL,
  `to_date` datetime DEFAULT NULL,
  `date_for` date DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `confirmed_date` datetime DEFAULT NULL,
  `timeflag` datetime DEFAULT NULL,
  PRIMARY KEY (`bid`),
  KEY `account_id` (`account_id`),
  KEY `status` (`status`),
  KEY `walletid` (`wallet_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `af_bonus_cv_percentages`
--

CREATE TABLE IF NOT EXISTS `af_bonus_cv_percentages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_type` smallint(2) unsigned NOT NULL DEFAULT '0',
  `min_cv` int(10) unsigned NOT NULL DEFAULT '0',
  `max_cv` int(10) unsigned NOT NULL DEFAULT '0',
  `perc` decimal(3,1) unsigned NOT NULL DEFAULT '0.0',
  PRIMARY KEY (`id`),
  KEY `bonus_type` (`bonus_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `af_bonus_cv_percentages`
--

INSERT INTO `af_bonus_cv_percentages` (`id`, `bonus_type`, `min_cv`, `max_cv`, `perc`) VALUES
(1, 6, 10, 499, '10.0'),
(2, 6, 500, 999, '15.0'),
(3, 6, 1000, 1999, '20.0'),
(4, 6, 2000, 3999, '24.0'),
(5, 6, 4000, 6999, '28.0'),
(6, 6, 7000, 9999, '32.0'),
(7, 6, 10000, 14999, '36.0'),
(8, 6, 15000, 24999, '40.0'),
(9, 6, 25000, 49999, '45.0'),
(10, 6, 50000, 10000000, '50.0');

-- --------------------------------------------------------

--
-- Table structure for table `af_bonus_types_lookups`
--

CREATE TABLE IF NOT EXISTS `af_bonus_types_lookups` (
  `bonus_type_id` int(5) NOT NULL,
  `bonus_name` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`bonus_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `af_bonus_types_lookups`
--

INSERT INTO `af_bonus_types_lookups` (`bonus_type_id`, `bonus_name`) VALUES
(1, 'Personal Customer Commiss'),
(2, 'Fast Start Bonus'),
(3, 'Team Commission'),
(4, 'Leadership Commission'),
(5, 'Car Bonus'),
(6, 'Ambassador Bonus'),
(7, 'Star Bonus');

-- --------------------------------------------------------

--
-- Table structure for table `af_directors_bonus`
--

CREATE TABLE IF NOT EXISTS `af_directors_bonus` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `bonus_type` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '5-car bonus,6-ambassador bonus,7-star bonus',
  `account_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `wallet_id` smallint(2) unsigned NOT NULL DEFAULT '0',
  `currency_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `amount` double unsigned NOT NULL DEFAULT '0',
  `status` smallint(2) unsigned NOT NULL DEFAULT '0',
  `confirm_date` datetime DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `af_ranking_lookup`
--

CREATE TABLE IF NOT EXISTS `af_ranking_lookup` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `af_rank_id` smallint(4) unsigned NOT NULL DEFAULT '0',
  `rank` text NOT NULL,
  `qualification` longtext NOT NULL,
  `qv` int(10) unsigned DEFAULT '0',
  `gqv` int(10) unsigned DEFAULT '0',
  `cv` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `af_ranking_lookup`
--

INSERT INTO `af_ranking_lookup` (`id`, `af_rank_id`, `rank`, `qualification`, `qv`, `gqv`, `cv`) VALUES
(1, 1, 'Promoter', 'Any active promoter', 20, 0, 10),
(2, 2, 'Director', '3 active lines with total of 2,000 Group Qualification Volume (GQV) per\nmonth', 0, 2000, 0),
(3, 3, 'Area Director', '3 active lines with total of 6,000 GQV per month with 60% rank criteria.', 0, 6000, 0),
(4, 4, 'Regional Director', '3 active lines with total of 12,500 GQV per month with 60% rank criteria.', 0, 12500, 0),
(5, 5, 'National Director', '3 active lines with total of 25,000 GQV per month with 60% rank criteria', 0, 25000, 0),
(6, 6, 'Executive Director', '3 active lines with total of 50,000 GQV per month with 60% rank criteria.', 0, 50000, 0),
(7, 7, 'Presidential Director', '3 active lines with total of 100,000 GQV per month with 60% rank criteria.', 0, 100000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `banking_account_types_lookups`
--

CREATE TABLE IF NOT EXISTS `banking_account_types_lookups` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `payout_type_id` tinyint(3) unsigned NOT NULL,
  `account_type` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `payout_type_id` (`payout_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `banking_account_types_lookups`
--

INSERT INTO `banking_account_types_lookups` (`id`, `payout_type_id`, `account_type`) VALUES
(1, 2, 'Savings'),
(2, 2, 'Current');

-- --------------------------------------------------------

--
-- Table structure for table `bcategory`
--

CREATE TABLE IF NOT EXISTS `bcategory` (
  `bcategory_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(150) DEFAULT NULL,
  `profileimg` varchar(50) DEFAULT NULL,
  `icon` varchar(255) DEFAULT 'default.png',
  `category_img` varchar(255) DEFAULT NULL,
  `allowed_countries` varchar(40) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Draft,1-Active,2-InActive,3-Closed',
  `is_visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `category_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1- In store 2-Deal 3-Online store',
  `is_featured` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `is_popular` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `position` smallint(5) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bcategory_id`),
  UNIQUE KEY `slug` (`slug`,`category_type`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `bcategory`
--

INSERT INTO `bcategory` (`bcategory_id`, `slug`, `profileimg`, `icon`, `category_img`, `allowed_countries`, `status`, `is_visible`, `category_type`, `is_featured`, `is_popular`, `position`, `created_on`, `created_by`, `updated_by`, `updated_on`, `is_deleted`) VALUES
(1, 'eat', NULL, 'default.gif', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2018-07-27 00:00:00', NULL, NULL, NULL, 0),
(2, 'beauty', NULL, 'default.gif', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2018-07-27 00:00:00', NULL, NULL, NULL, 0),
(3, 'services', NULL, 'default.gif', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2018-07-27 00:00:00', NULL, NULL, NULL, 0),
(4, 'activities', NULL, 'default.gif', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2018-07-27 00:00:00', NULL, NULL, NULL, 0),
(5, 'health', NULL, 'default.gif', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2018-07-27 00:00:00', NULL, NULL, NULL, 0),
(6, 'shopping', NULL, 'default.gif', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2018-07-27 00:00:00', NULL, NULL, NULL, 0),
(7, 'travel', NULL, 'default.gif', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2018-07-27 00:00:00', NULL, NULL, NULL, 0),
(8, 'events', NULL, 'default.gif', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2018-07-27 00:00:00', NULL, NULL, NULL, 0),
(9, 'vi-Stores', NULL, 'default.gif', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2018-07-27 00:00:00', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `bcategory_extras`
--

CREATE TABLE IF NOT EXISTS `bcategory_extras` (
  `bcategory_id` smallint(5) unsigned DEFAULT NULL,
  `meta_title` varchar(250) NOT NULL,
  `meta_desc` varchar(250) NOT NULL,
  `meta_keywords` varchar(250) NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  UNIQUE KEY `bcategory_id` (`bcategory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bcategory_lang`
--

CREATE TABLE IF NOT EXISTS `bcategory_lang` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bcategory_id` smallint(5) unsigned DEFAULT NULL,
  `lang_id` tinyint(3) unsigned DEFAULT NULL,
  `bcategory_name` varchar(50) DEFAULT NULL,
  `cancellation_policy` text,
  `bcategory_desc` text,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bcategory_id` (`bcategory_id`,`lang_id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `bcategory_lang`
--

INSERT INTO `bcategory_lang` (`id`, `bcategory_id`, `lang_id`, `bcategory_name`, `cancellation_policy`, `bcategory_desc`, `meta_title`, `meta_desc`, `meta_keywords`) VALUES
(1, 1, NULL, 'Eat', NULL, NULL, NULL, NULL, NULL),
(2, 2, NULL, 'Beauty', NULL, NULL, NULL, NULL, NULL),
(3, 3, NULL, 'Services', NULL, NULL, NULL, NULL, NULL),
(4, 4, NULL, 'Activities', NULL, NULL, NULL, NULL, NULL),
(5, 5, NULL, 'Health', NULL, NULL, NULL, NULL, NULL),
(6, 6, NULL, 'Shopping', NULL, NULL, NULL, NULL, NULL),
(7, 7, NULL, 'Travel', NULL, NULL, NULL, NULL, NULL),
(8, 8, NULL, 'Events', NULL, NULL, NULL, NULL, NULL),
(9, 9, NULL, 'Vi-Stores', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bcategory_status_lang`
--

CREATE TABLE IF NOT EXISTS `bcategory_status_lang` (
  `id` bigint(20) NOT NULL,
  `bcstatus_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lang_id` tinyint(3) unsigned DEFAULT '1',
  `bcstatus_name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bcstatus_id` (`bcstatus_id`,`lang_id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bcategory_status_lookup`
--

CREATE TABLE IF NOT EXISTS `bcategory_status_lookup` (
  `bcstatus_id` smallint(2) unsigned DEFAULT '0',
  `disp_class` varchar(15) DEFAULT NULL,
  UNIQUE KEY `status_id` (`bcstatus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bcategory_tree`
--

CREATE TABLE IF NOT EXISTS `bcategory_tree` (
  `bcategory_id` smallint(5) unsigned NOT NULL,
  `parent_bcategory_id` smallint(5) unsigned DEFAULT NULL,
  `category_type` int(10) NOT NULL,
  `parents` varchar(255) DEFAULT NULL,
  `root_bcategory_id` smallint(5) unsigned DEFAULT NULL,
  `cat_lftnode` int(10) unsigned DEFAULT NULL,
  `cat_rgtnode` int(10) unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`bcategory_id`),
  UNIQUE KEY `bcategory_id` (`bcategory_id`),
  KEY `parent_bcategory_id` (`parent_bcategory_id`),
  KEY `lft_node` (`cat_lftnode`),
  KEY `rgt_node` (`cat_rgtnode`),
  KEY `root_bcategory_id` (`root_bcategory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bcategory_tree`
--

INSERT INTO `bcategory_tree` (`bcategory_id`, `parent_bcategory_id`, `category_type`, `parents`, `root_bcategory_id`, `cat_lftnode`, `cat_rgtnode`, `updated_on`) VALUES
(1, 0, 1, NULL, 1, 1, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `carrier`
--

CREATE TABLE IF NOT EXISTS `carrier` (
  `carrier_id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_name` varchar(250) DEFAULT NULL,
  `is_default` smallint(1) NOT NULL DEFAULT '0',
  `is_system_carrier` smallint(1) NOT NULL DEFAULT '0',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`carrier_id`),
  KEY `courier_name` (`carrier_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `carrier`
--

INSERT INTO `carrier` (`carrier_id`, `carrier_name`, `is_default`, `is_system_carrier`, `created_date`, `is_deleted`) VALUES
(1, 'ABC', 1, 1, '2017-03-21 14:34:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cashfree_transaction`
--

CREATE TABLE IF NOT EXISTS `cashfree_transaction` (
  `cashfree_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `purpose` tinyint(2) unsigned NOT NULL COMMENT '0 - Add Fund, 1 - Package/Product Purchase',
  `email` varchar(50) CHARACTER SET latin1 NOT NULL,
  `customername` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `customermobile` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `paymentMode` varchar(50) CHARACTER SET latin1 NOT NULL,
  `txMsg` varchar(50) CHARACTER SET latin1 NOT NULL,
  `referenceId` varchar(50) CHARACTER SET latin1 NOT NULL,
  `transaction_id` text CHARACTER SET latin1 NOT NULL,
  `currency` varchar(3) CHARACTER SET latin1 NOT NULL,
  `amount` double unsigned NOT NULL,
  `signature` smallint(4) unsigned DEFAULT NULL,
  `response_data` longtext CHARACTER SET latin1,
  `status` varchar(15) CHARACTER SET latin1 DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `addedon` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cashfree_id`),
  KEY `user_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `category_info`
--
CREATE TABLE IF NOT EXISTS `category_info` (
`category_id` smallint(6) unsigned
,`parent_category_id` smallint(6) unsigned
,`category` varchar(100)
,`url` varchar(341)
,`category_code` varchar(3)
);
-- --------------------------------------------------------

--
-- Table structure for table `courier_mode_lookups`
--

CREATE TABLE IF NOT EXISTS `courier_mode_lookups` (
  `mode_id` smallint(4) NOT NULL AUTO_INCREMENT,
  `mode` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-Disabled, 1-Enabled',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`mode_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `courier_mode_lookups`
--

INSERT INTO `courier_mode_lookups` (`mode_id`, `mode`, `status`, `is_deleted`) VALUES
(1, 'Standard', 1, 0),
(2, 'Mode2', 1, 0),
(3, 'Mode2', 1, 1),
(4, 'Test', 1, 0),
(5, 'Test', 1, 0),
(6, 'Test5', 1, 0),
(7, 'Test', 1, 0),
(8, 'Tweste', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `courier_service_providers`
--

CREATE TABLE IF NOT EXISTS `courier_service_providers` (
  `courier_id` smallint(4) NOT NULL AUTO_INCREMENT,
  `courier` varchar(255) NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state_id` int(11) unsigned DEFAULT NULL,
  `country_id` tinyint(3) unsigned DEFAULT NULL,
  `zones` text NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`courier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE IF NOT EXISTS `currencies` (
  `currency_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `currency` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `currency_symbol` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `decimal_places` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `flag_char` varchar(8) DEFAULT NULL,
  `default_currency` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`currency_id`),
  UNIQUE KEY `currency` (`currency`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`currency_id`, `currency`, `currency_symbol`, `decimal_places`, `flag_char`, `default_currency`, `status`, `updated_on`) VALUES
(1, 'USD', '$', 2, 'us.png', 1, 1, '2017-04-21 23:09:22'),
(2, 'INR', '&#8377;', 2, 'in.png', 0, 1, '2017-04-21 23:09:30'),
(3, 'IDR', 'Rp', 2, 'id.png', 0, 1, '2017-04-21 22:48:45'),
(4, 'MYR', 'RM', 2, 'my.png', 0, 1, '2017-04-21 22:48:56'),
(5, 'SGD', 'S$', 2, 'sg.png', 0, 1, '2017-04-21 22:49:08'),
(6, 'PHP', '₱', 2, 'ph.png', 0, 1, '2017-04-21 22:49:17'),
(7, 'BDT', '৳', 2, 'bd.png', 0, 1, '2017-04-21 22:49:27'),
(8, 'CNY', '¥', 2, 'cn.png', 0, 1, '2017-04-21 22:49:41'),
(9, 'EUR', '€', 2, 'eu.png', 0, 1, '2017-04-21 22:49:50'),
(10, 'GBP', '£', 2, 'gb.png', 0, 1, '2017-04-21 22:50:03'),
(11, 'AUD', 'A$', 2, 'au.png', 0, 1, '2017-04-21 22:50:17'),
(12, 'THB', '฿', 2, 'th.png', 0, 1, '2017-04-21 22:50:27');

-- --------------------------------------------------------

--
-- Table structure for table `currency_exchange_settings`
--

CREATE TABLE IF NOT EXISTS `currency_exchange_settings` (
  `cex_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_currency_id` tinyint(3) unsigned NOT NULL,
  `to_currency_id` tinyint(3) unsigned NOT NULL,
  `rate` double unsigned DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cex_id`),
  UNIQUE KEY `from_currency_id_2` (`from_currency_id`,`to_currency_id`),
  KEY `from_currency_id` (`from_currency_id`),
  KEY `to_currency_id` (`to_currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=204 ;

--
-- Dumping data for table `currency_exchange_settings`
--

INSERT INTO `currency_exchange_settings` (`cex_id`, `from_currency_id`, `to_currency_id`, `rate`, `created_on`, `updated_on`) VALUES
(1, 1, 2, 0, NULL, '2015-02-20 08:50:23'),
(2, 2, 1, 65, NULL, '2015-02-20 08:50:23'),
(3, 8, 1, 0, NULL, '2015-02-20 08:50:23'),
(4, 1, 8, 0, NULL, '2015-02-20 08:50:23'),
(5, 1, 3, 0, NULL, '2015-02-20 08:50:23'),
(6, 3, 1, 0, NULL, '2015-02-20 08:50:23'),
(7, 2, 3, 0, NULL, '2015-02-20 08:50:23'),
(8, 3, 2, 0, NULL, '2015-02-20 08:50:23'),
(9, 12, 1, 0, NULL, '2015-02-20 08:50:23'),
(10, 1, 12, 0, NULL, '2015-02-20 08:50:23'),
(11, 1, 9, 0, NULL, '2015-02-20 08:50:23'),
(12, 9, 1, 0, NULL, '2015-02-20 08:50:23'),
(13, 1, 4, 0, NULL, '2015-02-20 08:50:23'),
(14, 4, 1, 0, NULL, '2015-02-20 08:50:23'),
(15, 1, 5, 0, NULL, '2015-02-20 08:50:23'),
(16, 5, 1, 0, NULL, '2015-02-20 08:50:23'),
(17, 1, 6, 0, NULL, '2015-02-20 08:50:23'),
(18, 6, 1, 0, NULL, '2015-02-20 08:50:23'),
(19, 1, 7, 0, NULL, '2015-02-20 08:50:23'),
(20, 7, 1, 0, NULL, '2015-02-20 08:50:23'),
(21, 1, 10, 0, NULL, '2015-02-20 08:50:23'),
(22, 10, 1, 0, NULL, '2015-02-20 08:50:23'),
(23, 1, 11, 0, NULL, '2015-02-20 08:50:23'),
(24, 11, 1, 0, NULL, '2015-02-20 08:50:23'),
(37, 2, 4, 0, NULL, '2015-02-20 08:50:23'),
(38, 2, 5, 0, NULL, '2015-02-20 08:50:23'),
(39, 2, 6, 0, NULL, '2015-02-20 08:50:23'),
(40, 2, 7, 0, NULL, '2015-02-20 08:50:23'),
(41, 2, 8, 0, NULL, '2015-02-20 08:50:23'),
(42, 2, 9, 0, NULL, '2015-02-20 08:50:23'),
(43, 2, 10, 0, NULL, '2015-02-20 08:50:23'),
(44, 2, 11, 0, NULL, '2015-02-20 08:50:23'),
(45, 2, 12, 0, NULL, '2015-02-20 08:50:23'),
(52, 3, 4, 0, NULL, '2015-02-20 08:50:23'),
(53, 3, 5, 0, NULL, '2015-02-20 08:50:23'),
(54, 3, 6, 0, NULL, '2015-02-20 08:50:23'),
(55, 3, 7, 0, NULL, '2015-02-20 08:50:23'),
(56, 3, 8, 0, NULL, '2015-02-20 08:50:23'),
(57, 3, 9, 0, NULL, '2015-02-20 08:50:23'),
(58, 3, 10, 0, NULL, '2015-02-20 08:50:23'),
(59, 3, 11, 0, NULL, '2015-02-20 08:50:23'),
(60, 3, 12, 0, NULL, '2015-02-20 08:50:23'),
(67, 4, 2, 0, NULL, '2015-02-20 08:50:23'),
(68, 4, 5, 0, NULL, '2015-02-20 08:50:23'),
(69, 4, 6, 0, NULL, '2015-02-20 08:50:23'),
(70, 4, 7, 0, NULL, '2015-02-20 08:50:23'),
(71, 4, 8, 0, NULL, '2015-02-20 08:50:23'),
(72, 4, 9, 0, NULL, '2015-02-20 08:50:23'),
(73, 4, 10, 0, NULL, '2015-02-20 08:50:23'),
(74, 4, 11, 0, NULL, '2015-02-20 08:50:23'),
(75, 4, 12, 0, NULL, '2015-02-20 08:50:23'),
(82, 5, 2, 0, NULL, '2015-02-20 08:50:23'),
(83, 5, 3, 0, NULL, '2015-02-20 08:50:23'),
(84, 5, 4, 0, NULL, '2015-02-20 08:50:23'),
(85, 5, 6, 0, NULL, '2015-02-20 08:50:23'),
(86, 5, 7, 0, NULL, '2015-02-20 08:50:23'),
(87, 5, 8, 0, NULL, '2015-02-20 08:50:23'),
(88, 5, 9, 0, NULL, '2015-02-20 08:50:23'),
(89, 5, 10, 0, NULL, '2015-02-20 08:50:23'),
(90, 5, 11, 0, NULL, '2015-02-20 08:50:23'),
(91, 5, 12, 0, NULL, '2015-02-20 08:50:23'),
(98, 6, 2, 0, NULL, '2015-02-20 08:50:23'),
(99, 6, 3, 0, NULL, '2015-02-20 08:50:23'),
(100, 6, 4, 0, NULL, '2015-02-20 08:50:23'),
(101, 6, 5, 0, NULL, '2015-02-20 08:50:23'),
(102, 6, 7, 0, NULL, '2015-02-20 08:50:23'),
(103, 6, 8, 0, NULL, '2015-02-20 08:50:23'),
(104, 6, 9, 0, NULL, '2015-02-20 08:50:23'),
(105, 6, 10, 0, NULL, '2015-02-20 08:50:23'),
(106, 6, 11, 0, NULL, '2015-02-20 08:50:23'),
(107, 6, 12, 0, NULL, '2015-02-20 08:50:23'),
(114, 7, 2, 0, NULL, '2015-02-20 08:50:23'),
(115, 7, 3, 0, NULL, '2015-02-20 08:50:23'),
(116, 7, 4, 0, NULL, '2015-02-20 08:50:23'),
(117, 7, 5, 0, NULL, '2015-02-20 08:50:23'),
(118, 7, 6, 0, NULL, '2015-02-20 08:50:23'),
(119, 7, 8, 0, NULL, '2015-02-20 08:50:23'),
(120, 7, 9, 0, NULL, '2015-02-20 08:50:23'),
(121, 7, 10, 0, NULL, '2015-02-20 08:50:23'),
(122, 7, 11, 0, NULL, '2015-02-20 08:50:23'),
(123, 7, 12, 0, NULL, '2015-02-20 08:50:23'),
(130, 8, 2, 0, NULL, '2015-02-20 08:50:23'),
(131, 8, 3, 0, NULL, '2015-02-20 08:50:23'),
(132, 8, 4, 0, NULL, '2015-02-20 08:50:23'),
(133, 8, 5, 0, NULL, '2015-02-20 08:50:23'),
(134, 8, 6, 0, NULL, '2015-02-20 08:50:23'),
(135, 8, 7, 0, NULL, '2015-02-20 08:50:23'),
(136, 8, 9, 0, NULL, '2015-02-20 08:50:23'),
(137, 8, 10, 0, NULL, '2015-02-20 08:50:23'),
(138, 8, 11, 0, NULL, '2015-02-20 08:50:23'),
(139, 8, 12, 0, NULL, '2015-02-20 08:50:23'),
(146, 9, 2, 0, NULL, '2015-02-20 08:50:23'),
(147, 9, 3, 0, NULL, '2015-02-20 08:50:23'),
(148, 9, 4, 0, NULL, '2015-02-20 08:50:23'),
(149, 9, 5, 0, NULL, '2015-02-20 08:50:23'),
(150, 9, 6, 0, NULL, '2015-02-20 08:50:23'),
(151, 9, 7, 0, NULL, '2015-02-20 08:50:23'),
(152, 9, 8, 0, NULL, '2015-02-20 08:50:23'),
(153, 9, 10, 0, NULL, '2015-02-20 08:50:23'),
(154, 9, 11, 0, NULL, '2015-02-20 08:50:23'),
(155, 9, 12, 0, NULL, '2015-02-20 08:50:23'),
(162, 10, 2, 0, NULL, '2015-02-20 08:50:23'),
(163, 10, 3, 0, NULL, '2015-02-20 08:50:23'),
(164, 10, 4, 0, NULL, '2015-02-20 08:50:23'),
(165, 10, 5, 0, NULL, '2015-02-20 08:50:23'),
(166, 10, 6, 0, NULL, '2015-02-20 08:50:23'),
(167, 10, 7, 0, NULL, '2015-02-20 08:50:23'),
(168, 10, 8, 0, NULL, '2015-02-20 08:50:23'),
(169, 10, 9, 0, NULL, '2015-02-20 08:50:23'),
(170, 10, 11, 0, NULL, '2015-02-20 08:50:23'),
(171, 10, 12, 0, NULL, '2015-02-20 08:50:23'),
(178, 11, 2, 0, NULL, '2015-02-20 08:50:23'),
(179, 11, 3, 0, NULL, '2015-02-20 08:50:23'),
(180, 11, 4, 0, NULL, '2015-02-20 08:50:23'),
(181, 11, 5, 0, NULL, '2015-02-20 08:50:23'),
(182, 11, 6, 0, NULL, '2015-02-20 08:50:23'),
(183, 11, 7, 0, NULL, '2015-02-20 08:50:23'),
(184, 11, 8, 0, NULL, '2015-02-20 08:50:23'),
(185, 11, 9, 0, NULL, '2015-02-20 08:50:23'),
(186, 11, 10, 0, NULL, '2015-02-20 08:50:23'),
(187, 11, 12, 0, NULL, '2015-02-20 08:50:23'),
(194, 12, 2, 0, NULL, '2015-02-20 08:50:23'),
(195, 12, 3, 0, NULL, '2015-02-20 08:50:23'),
(196, 12, 4, 0, NULL, '2015-02-20 08:50:23'),
(197, 12, 5, 0, NULL, '2015-02-20 08:50:23'),
(198, 12, 6, 0, NULL, '2015-02-20 08:50:23'),
(199, 12, 7, 0, NULL, '2015-02-20 08:50:23'),
(200, 12, 8, 0, NULL, '2015-02-20 08:50:23'),
(201, 12, 9, 0, NULL, '2015-02-20 08:50:23'),
(202, 12, 10, 0, NULL, '2015-02-20 08:50:23'),
(203, 12, 11, 0, NULL, '2015-02-20 08:50:23');

-- --------------------------------------------------------

--
-- Table structure for table `customercare_enquiry_mst`
--

CREATE TABLE IF NOT EXISTS `customercare_enquiry_mst` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enquiry_code` varchar(20) DEFAULT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `mode_of_response` smallint(2) NOT NULL DEFAULT '0' COMMENT '1-email 2-phone',
  `language_id` smallint(2) NOT NULL DEFAULT '0',
  `order_item_id` text,
  `order_issues_type` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - existing order 2-any other assistance',
  `order_type` smallint(1) NOT NULL DEFAULT '0' COMMENT '1-order 2 item',
  `subject` text,
  `contact` varchar(40) DEFAULT NULL,
  `comments` text,
  `admin_comments` text,
  `question_id` int(11) NOT NULL DEFAULT '0',
  `status` smallint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  `created_on` datetime DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enquiry_code` (`enquiry_code`),
  KEY `mode_of_response` (`mode_of_response`),
  KEY `language_id` (`language_id`),
  KEY `account_id` (`account_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customercare_modeof_response`
--

CREATE TABLE IF NOT EXISTS `customercare_modeof_response` (
  `mode_of_response` int(11) NOT NULL AUTO_INCREMENT,
  `mode` text NOT NULL COMMENT '1 -email 2 -phone',
  PRIMARY KEY (`mode_of_response`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `customercare_modeof_response`
--

INSERT INTO `customercare_modeof_response` (`mode_of_response`, `mode`) VALUES
(1, 'email'),
(2, 'mobile');

-- --------------------------------------------------------

--
-- Table structure for table `customer_care_enquiry_replies`
--

CREATE TABLE IF NOT EXISTS `customer_care_enquiry_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `replied_user_type` smallint(2) NOT NULL DEFAULT '0' COMMENT '1-admin 2-user',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `customer_care_enquiry_id` int(11) NOT NULL DEFAULT '0',
  `comments` text NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(2) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`id`),
  KEY `replied_user_type` (`replied_user_type`,`created_by`,`customer_care_enquiry_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_type_lookups`
--

CREATE TABLE IF NOT EXISTS `delivery_type_lookups` (
  `delivery_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `status` tinyint(2) NOT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`delivery_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `delivery_type_lookups`
--

INSERT INTO `delivery_type_lookups` (`delivery_type_id`, `description`, `status`, `updated_on`) VALUES
(1, 'Standard Delivery', 0, '2016-09-02 05:04:04');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE IF NOT EXISTS `devices` (
  `device_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `device` varchar(255) DEFAULT NULL,
  `device_label` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`device_id`),
  UNIQUE KEY `device` (`device`),
  UNIQUE KEY `device_label` (`device_label`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`device_id`, `device`, `device_label`, `icon`) VALUES
(1, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `device_log`
--

CREATE TABLE IF NOT EXISTS `device_log` (
  `device_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_info` text,
  `ip` varchar(15) DEFAULT NULL,
  `token` varchar(100) NOT NULL,
  `account_id` bigint(20) unsigned DEFAULT NULL,
  `fcm_registration_id` text COMMENT 'Firebase Cloud Messaging (FCM)',
  `code` varchar(6) DEFAULT NULL,
  `login_ip` varchar(15) DEFAULT NULL,
  `cookies` text,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-Inactive, 1-Active',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`device_log_id`),
  UNIQUE KEY `token` (`token`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `device_log`
--

INSERT INTO `device_log` (`device_log_id`, `device_info`, `ip`, `token`, `account_id`, `fcm_registration_id`, `code`, `login_ip`, `cookies`, `status`, `created_on`, `updated_on`) VALUES
(1, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'aee00da3aac17c36bbb2600fc5369302', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 11:37:44', '2018-07-27 11:37:44'),
(2, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'd9a0e07e63ee3ac413909bb46b2faef3', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 11:37:52', '2018-07-27 11:37:52'),
(3, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '2ce9bcd624be26af21fd5c370e751603', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 11:38:02', '2018-07-27 11:38:02'),
(4, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'a85171098bfea86dcc0302033276f806', 100, NULL, NULL, NULL, NULL, 1, '2018-07-27 11:38:07', '2018-07-27 11:38:08'),
(5, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'aa17b38d5a6cb7a70eb76e4764f390dd', 100, NULL, NULL, NULL, NULL, 1, '2018-07-27 11:45:41', '2018-07-27 11:45:50'),
(6, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '5ddcc683d1c962d6772d64a7736e6fc6', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 11:46:11', '2018-07-27 11:46:11'),
(7, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '7c80b72b89b52b32c45982102ac7a6a0', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 11:46:37', '2018-07-27 11:46:37'),
(8, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'd473d20f3d693dc06fe1d6b1255a3dd6', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 11:58:28', '2018-07-27 11:58:28'),
(9, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'fe7512549aed21bf8927bfcb9ed255cc', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:25:03', '2018-07-27 12:25:03'),
(10, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '14d377b91b5e104cd2881096d8a8e815', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:25:20', '2018-07-27 12:25:20'),
(11, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '1fc35644d9df5c10571be4b659f34bca', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:30:31', '2018-07-27 12:30:31'),
(12, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'a9c507de0a568e890b9adcf171d6d83c', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:31:34', '2018-07-27 12:31:34'),
(13, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'a566b83ee6d27440cbdd999704d2165a', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:32:38', '2018-07-27 12:32:38'),
(14, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'a781a2bda3468d29579c2c77813b02fe', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:32:54', '2018-07-27 12:32:54'),
(15, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'a2cfc92fe885629dafdbc66652193c39', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:34:31', '2018-07-27 12:34:31'),
(16, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'a1224a208943d695e779655321106451', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:34:48', '2018-07-27 12:34:48'),
(17, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '7354d863df8dbe4ad5914f53cf02e253', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:37:08', '2018-07-27 12:37:08'),
(18, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '8dfa76042168272848b15581d46f8a09', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:37:23', '2018-07-27 12:37:23'),
(19, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '7aa00ef42e9d8d0b7dddf997ab6c7504', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:40:22', '2018-07-27 12:40:22'),
(20, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '7cd8c65b08e9bea76cf7613303c3495e', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:44:19', '2018-07-27 12:44:19'),
(21, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '63cd833b3dab2cbab928f8bbfa9a7ff7', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:45:51', '2018-07-27 12:45:51'),
(22, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '39284ac7c726111aa8a2399e46f2239d', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:46:38', '2018-07-27 12:46:38'),
(23, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '10dbdef8db00e7eb3f925cefc85318cc', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:46:54', '2018-07-27 12:46:54'),
(24, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '4e04ebcf70c2e6fa09919f49585d4bfa', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:47:08', '2018-07-27 12:47:08'),
(25, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '7b99090ebcb3adf48459cb17c3926f80', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:47:31', '2018-07-27 12:47:31'),
(26, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '86cf7c8c7a81529f64595064209e13e6', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:50:23', '2018-07-27 12:50:23'),
(27, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '511f7476fa153073627779030123516c', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:56:00', '2018-07-27 12:56:00'),
(28, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'a6aa0e7acbaa234b6a42b0e7db10ce4f', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:56:15', '2018-07-27 12:56:15'),
(29, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '349fe8fc9084f71aa9f03ff5222a7f8e', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:58:37', '2018-07-27 12:58:37'),
(30, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'f077e127716b1892154058294129ed2a', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:59:11', '2018-07-27 12:59:11'),
(31, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '7277dc2eb7a48082c67b76b7e0ebca82', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 12:59:21', '2018-07-27 12:59:21'),
(32, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'fe2a6d40b01422a48a99e1fc2fbe0bd5', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 13:00:15', '2018-07-27 13:00:16'),
(33, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '8bd271b45a5c85c6757d6bd518e7b429', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 13:04:47', '2018-07-27 13:04:47'),
(34, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '207b2766c21d390b4dd709233807d0b3', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 13:05:51', '2018-07-27 13:05:51'),
(35, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '6b7abbb722aec65eb88e1e1ac7e577cd', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 13:06:08', '2018-07-27 13:06:08'),
(36, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'a108b086fccca3c0cc5dfdbde3e8a34a', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 13:06:27', '2018-07-27 13:06:27'),
(37, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '4a0bf9695cfef136e5d580764fbcc6bb', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 13:08:06', '2018-07-27 13:08:06'),
(38, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'af0bf9a6e9b2200b0004233739be6f70', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 13:14:20', '2018-07-27 13:14:20'),
(39, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', '4a58595230e3f5e8a16e093b52ae2a5b', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-27 13:19:17', '2018-07-27 13:19:17'),
(40, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36', '::1', 'dfeb9e187d04096418f397b8a1599966', NULL, NULL, NULL, NULL, NULL, 1, '2018-07-28 05:31:33', '2018-07-28 05:31:33');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE IF NOT EXISTS `discounts` (
  `discount_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `discount` varchar(100) NOT NULL,
  `description` text,
  `discount_type_id` tinyint(2) unsigned DEFAULT NULL,
  `country_id` tinyint(3) unsigned DEFAULT NULL,
  `discount_by` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '1-admin, 2-customer, 3-supplier',
  `is_extra` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `priority` smallint(6) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`discount_id`),
  KEY `discount_type_id` (`discount_type_id`),
  KEY `country_id` (`country_id`),
  KEY `discount_by` (`discount_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`discount_id`, `discount`, `description`, `discount_type_id`, `country_id`, `discount_by`, `is_extra`, `start_date`, `end_date`, `priority`, `status`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 'Special-Discount-1', 'Special-Discount-1', 1, 77, 1, 0, '2017-08-01 00:00:00', '2017-08-31 23:59:59', NULL, 1, NULL, '2017-07-31 18:23:40', 1, 0),
(2, 'Special-Discount-1', 'Special-Discount-1', 2, 77, 1, 0, '2017-08-01 00:00:00', '2017-08-31 23:59:59', NULL, 1, '2017-04-27 22:10:49', '2017-07-31 18:23:58', 1, 0),
(3, 'Special-Discount-1', 'Special-Discount-1', 2, 77, 1, 0, '2017-08-01 00:00:00', '2017-08-31 23:59:59', NULL, 1, '2017-04-27 22:11:00', '2017-07-31 18:24:01', 1, 0),
(4, 'Special-Discount-1', 'Special-Discount-1', 2, 77, 1, 0, '2017-08-01 00:00:00', '2017-08-31 23:59:59', NULL, 1, '2017-04-27 22:32:27', '2017-07-31 18:24:05', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `discount_posts`
--

CREATE TABLE IF NOT EXISTS `discount_posts` (
  `dp_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `discount_id` mediumint(8) unsigned NOT NULL,
  `brand_ids` text,
  `category_ids` text,
  `supplier_ids` text,
  `product_ids` text,
  `product_cmb_ids` text,
  `discount_value_type` tinyint(1) unsigned NOT NULL COMMENT '1-Fixed, 2-Percentage',
  `is_qty_based` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`dp_id`),
  KEY `discount_id` (`discount_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `discount_posts`
--

INSERT INTO `discount_posts` (`dp_id`, `discount_id`, `brand_ids`, `category_ids`, `supplier_ids`, `product_ids`, `product_cmb_ids`, `discount_value_type`, `is_qty_based`, `is_deleted`) VALUES
(1, 1, '1', '2,3', NULL, NULL, NULL, 1, 0, 0),
(2, 1, '2', '4,5', NULL, NULL, NULL, 1, 0, 0),
(3, 2, '3', '6,7', NULL, NULL, NULL, 1, 0, 0),
(4, 2, '1', '8,9', NULL, NULL, NULL, 2, 0, 0),
(5, 3, '2', '2,3', NULL, NULL, NULL, 2, 0, 0),
(6, 3, '3', '4,5', NULL, NULL, NULL, 2, 0, 0),
(7, 4, '1', '6,7', NULL, NULL, NULL, 2, 0, 0),
(8, 4, '2,3', '7,8', NULL, NULL, NULL, 2, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `discount_type_lookups`
--

CREATE TABLE IF NOT EXISTS `discount_type_lookups` (
  `discount_type_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `discount_type` varchar(200) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  PRIMARY KEY (`discount_type_id`),
  UNIQUE KEY `discount_type` (`discount_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `discount_type_lookups`
--

INSERT INTO `discount_type_lookups` (`discount_type_id`, `discount_type`, `status`) VALUES
(1, 'Seasonal Offers', 1),
(2, 'Special Offers', 1),
(3, 'Combo Offers', 1);

-- --------------------------------------------------------

--
-- Table structure for table `discount_value`
--

CREATE TABLE IF NOT EXISTS `discount_value` (
  `dv_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dp_id` int(9) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned DEFAULT NULL,
  `discount_value` double unsigned NOT NULL DEFAULT '0',
  `min_qty` smallint(6) unsigned NOT NULL DEFAULT '0',
  `max_qty` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`dv_id`),
  UNIQUE KEY `discount_id` (`dp_id`,`currency_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `discount_value`
--

INSERT INTO `discount_value` (`dv_id`, `dp_id`, `currency_id`, `discount_value`, `min_qty`, `max_qty`, `is_deleted`) VALUES
(1, 1, NULL, 5, 0, 0, 0),
(2, 2, NULL, 6, 0, 0, 0),
(3, 3, NULL, 7, 0, 0, 0),
(4, 4, NULL, 8, 0, 0, 0),
(5, 5, NULL, 9, 0, 0, 0),
(6, 6, NULL, 10, 0, 0, 0),
(7, 7, NULL, 11, 0, 0, 0),
(8, 8, NULL, 12, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE IF NOT EXISTS `document_types` (
  `document_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  `proof_type` smallint(2) NOT NULL DEFAULT '0' COMMENT '0-Photo ID,1-ID Proof, 2-Address Proof',
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '0-Inactive, 1-Active',
  `other_fields` text,
  PRIMARY KEY (`document_type_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`document_type_id`, `type`, `proof_type`, `status`, `other_fields`) VALUES
(1, 'Driver''s License', 1, 1, NULL),
(2, 'Passport', 1, 1, NULL),
(3, 'National ID', 1, 1, NULL),
(4, 'Bank Statement', 2, 1, NULL),
(5, 'Credit Card Statement', 2, 1, NULL),
(6, 'Utility Bill', 2, 1, NULL),
(7, 'Phone Bill', 2, 1, NULL),
(8, 'Photo ID', 0, 1, NULL),
(9, 'Identity Card', 1, 1, NULL),
(10, 'Insurance Bill', 2, 1, NULL),
(11, 'Medical Bill', 2, 1, NULL),
(12, 'Other', 2, 1, NULL),
(13, 'Head &amp; Shoulders Personal Photo', 0, 1, NULL),
(14, 'Void Check', 0, 1, NULL),
(15, 'Direct Deposit Form', 2, 1, NULL),
(16, 'Bank Agreement (USA)', 1, 1, NULL),
(17, 'Bank Agreement (Canada)', 1, 1, NULL),
(18, 'Credit/Debit Card Statement', 1, 1, NULL),
(19, 'PAN Card', 2, 1, '{"pan_card_no":{"id":"pan_card_no","label":"PAN Card No","type":"text","validate":{"rules":"required|regex:\\/^[A-Z]{5}\\\\d{4}[A-Z]{1}$\\/","message":{"required":"Please enter your PANcard No","regex":"Please enter the valid PAN card No"}}},"pan_card_name":{"id":"pan_card_name","label":"PAN Card Name","type":"text","validate":{"rules":"required|min:3","message":{"required":"Please enter your PAN Card Name","min":"PAN Card Name must be min 3 characters length"}}},"dob":{"id":"dob","label":"DOB","type":"date","validate":{"rules":"required|date_format:Y-m-d","message":{"required":"Please enter your DOB","date_format":"Please enter your valid DOB"}}}}');

-- --------------------------------------------------------

--
-- Table structure for table `driver_type_lookups`
--

CREATE TABLE IF NOT EXISTS `driver_type_lookups` (
  `driver_type_id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `drive` varchar(50) NOT NULL,
  PRIMARY KEY (`driver_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `driver_type_lookups`
--

INSERT INTO `driver_type_lookups` (`driver_type_id`, `drive`) VALUES
(1, 'SMTP'),
(2, 'Send Grid');

-- --------------------------------------------------------

--
-- Table structure for table `email_settings`
--

CREATE TABLE IF NOT EXISTS `email_settings` (
  `email_settings_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `driver_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1- SMTP Settings, Send Grid Settings',
  `sender_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `settings` text,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`email_settings_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `email_settings`
--

INSERT INTO `email_settings` (`email_settings_id`, `driver_type`, `sender_name`, `email`, `settings`, `status`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 2, 'Test', 'tijo1.ejugiter@gmail.com', '{\\"host\\":\\"smtp.sendgrid.net\\",\\"port\\":\\"587\\",\\"username\\":\\"Virob\\",\\"password\\":\\"ejugiter@123\\",\\"encryption\\":\\"tls\\",\\"api_user\\":\\"Virob\\",\\"api_key\\":\\"ejugiter@123\\"}', 0, '2016-06-28 04:14:39', '2018-03-13 18:29:42', 0),
(3, 1, 'Test', 'tijo.ejugiter@gmail.com', '{\\"host\\":\\"mail.Virob.com\\",\\"port\\":\\"465\\",\\"username\\":\\"noreply@Virob.com\\",\\"password\\":\\"KH&73DZ31H72\\",\\"encryption\\":\\"ssl\\"}', 1, '2016-08-02 05:59:20', '2018-03-13 18:29:36', 0),
(4, 1, 'Test', 'test@gmail.com', '{\\"host\\":\\"google.com\\",\\"port\\":\\"52\\",\\"username\\":\\"abcd\\",\\"password\\":\\"123456\\",\\"encryption\\":\\"ssl\\",\\"api_user\\":\\"\\",\\"api_key\\":\\"\\"}', 0, '2016-08-02 06:01:12', '2016-08-02 01:01:12', 0),
(5, 1, 'Test', 'test@gmail.com', '{\\"host\\":\\"google.com\\",\\"port\\":\\"52\\",\\"username\\":\\"abcd\\",\\"password\\":\\"123456\\",\\"encryption\\":\\"ssl\\",\\"api_user\\":\\"\\",\\"api_key\\":\\"\\"}', 0, '2016-08-02 06:02:43', '2016-08-02 01:02:43', 0),
(6, 1, 'Test', 'test@gmail.com', '{\\"host\\":\\"google.com\\",\\"port\\":\\"52\\",\\"username\\":\\"abcd\\",\\"password\\":\\"123456\\",\\"encryption\\":\\"ssl\\",\\"api_user\\":\\"\\",\\"api_key\\":\\"\\"}', 0, '2016-08-02 07:11:41', '2016-08-02 02:11:41', 0),
(7, 1, 'Test', 'test@gmail.com', '{\\"host\\":\\"google.com\\",\\"port\\":\\"52\\",\\"username\\":\\"abcd\\",\\"password\\":\\"123456\\",\\"encryption\\":\\"ssl\\",\\"api_user\\":\\"\\",\\"api_key\\":\\"\\"}', 0, '2016-08-02 07:14:32', '2016-08-02 02:14:32', 0),
(8, 1, 'Test', 'test@gmail.com', '{\\"host\\":\\"google.com\\",\\"port\\":\\"52\\",\\"username\\":\\"abcd\\",\\"password\\":\\"123456\\",\\"encryption\\":\\"ssl\\",\\"api_user\\":\\"\\",\\"api_key\\":\\"\\"}', 0, '2016-08-03 04:55:36', '2016-08-02 23:55:36', 0),
(9, 1, 'Test', 'test@gmail.com', '{\\"host\\":\\"google.com\\",\\"port\\":\\"52\\",\\"username\\":\\"abcd\\",\\"password\\":\\"123456\\",\\"encryption\\":\\"ssl\\",\\"api_user\\":\\"\\",\\"api_key\\":\\"\\"}', 0, '2016-08-03 23:48:50', '2016-08-03 18:48:50', 0),
(10, 1, 'Test', 'test@gmail.com', '{\\"host\\":\\"google.com\\",\\"port\\":\\"52\\",\\"username\\":\\"abcd\\",\\"password\\":\\"123456\\",\\"encryption\\":\\"ssl\\",\\"api_user\\":\\"\\",\\"api_key\\":\\"\\"}', 0, '2016-08-04 00:39:00', '2016-08-03 19:39:00', 0),
(11, 1, 'Test', 'test@gmail.com', '{\\"host\\":\\"google.com\\",\\"port\\":\\"52\\",\\"username\\":\\"abcd\\",\\"password\\":\\"123456\\",\\"encryption\\":\\"ssl\\",\\"api_user\\":\\"\\",\\"api_key\\":\\"\\"}', 0, '2016-08-04 00:39:26', '2016-08-03 19:39:26', 0),
(12, 1, 'Test', 'test@gmail.com', '{\\"host\\":\\"google.com\\",\\"port\\":\\"52\\",\\"username\\":\\"abcd\\",\\"password\\":\\"123456\\",\\"encryption\\":\\"ssl\\",\\"api_user\\":\\"\\",\\"api_key\\":\\"\\"}', 0, '2016-08-04 01:26:22', '2016-08-03 20:26:22', 0);

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE IF NOT EXISTS `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text,
  `is_feature` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL COMMENT '0-draft,1-published,2-unpublished',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `is_feature` (`is_feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `faq_categories`
--

CREATE TABLE IF NOT EXISTS `faq_categories` (
  `faq_category_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `category` varchar(60) NOT NULL,
  `related_faq_category_id` int(11) NOT NULL DEFAULT '0',
  `slug` text,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`faq_category_id`),
  UNIQUE KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `featured_sliders`
--

CREATE TABLE IF NOT EXISTS `featured_sliders` (
  `slider_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `slider_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-Featured Slider, 2-Img Slider ',
  `title` varchar(100) NOT NULL,
  `block_ids` text COMMENT 'comma seperated block_ids',
  `page_id` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Inactive, 1-Active',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '1',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`slider_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `featured_sliders`
--

INSERT INTO `featured_sliders` (`slider_id`, `slider_type`, `title`, `block_ids`, `page_id`, `sort_order`, `status`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 1, 'Test', NULL, 1, 3, 1, '2016-10-14 20:07:02', '2016-11-22 23:04:55', 1, 0),
(2, 1, 'Test2', NULL, 1, 1, 1, '2016-10-16 18:42:45', '2016-11-22 23:04:47', 1, 0),
(3, 2, 'Test 3', NULL, 1, 2, 1, '2016-10-18 17:23:41', '2016-11-22 23:04:55', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_access_location`
--

CREATE TABLE IF NOT EXISTS `franchisee_access_location` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) NOT NULL,
  `access_location_type` tinyint(5) NOT NULL COMMENT 'country,Region,state,district,city',
  `relation_id` varchar(255) DEFAULT NULL,
  `country_id` int(10) unsigned NOT NULL DEFAULT '0',
  `region_id` int(10) unsigned NOT NULL DEFAULT '0',
  `state_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `district_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `created_by` bigint(20) NOT NULL DEFAULT '0',
  `updated_by` bigint(20) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`,`region_id`,`state_id`,`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_benifits`
--

CREATE TABLE IF NOT EXISTS `franchisee_benifits` (
  `fb_id` int(11) NOT NULL AUTO_INCREMENT,
  `franchisee_type` smallint(6) NOT NULL,
  `charity_donation_per` smallint(3) NOT NULL,
  `wallet_purchase_per` smallint(3) NOT NULL,
  `diff_commission_per` int(11) DEFAULT NULL,
  `flexible_commission_per` decimal(10,2) DEFAULT NULL,
  `timeflag` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fb_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `franchisee_benifits`
--

INSERT INTO `franchisee_benifits` (`fb_id`, `franchisee_type`, `charity_donation_per`, `wallet_purchase_per`, `diff_commission_per`, `flexible_commission_per`, `timeflag`) VALUES
(1, 1, 0, 10, 2, '1.00', '2016-11-06 17:09:36'),
(2, 2, 10, 8, 1, '0.50', '2016-11-06 17:09:37'),
(3, 3, 9, 7, 2, '1.00', '2016-11-06 17:09:37'),
(4, 4, 7, 5, 2, '1.00', '2016-11-06 17:09:37'),
(5, 5, 5, 3, 0, '0.00', '2016-11-06 17:09:37');

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_commission`
--

CREATE TABLE IF NOT EXISTS `franchisee_commission` (
  `fr_com_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) DEFAULT NULL,
  `commission_type` smallint(6) NOT NULL DEFAULT '1' COMMENT '1 - Fund Transfer, 2 - Add Funds, 3 - Fixed Contribution, 4 - Flexible Contribution,5-Franchisee Add Fund Commission,6-Admin Add Fun to Franchisee, 7 - Fixed Special Bonus, 8 - Flexible Special Bonus',
  `relation_id` bigint(20) NOT NULL DEFAULT '0',
  `amount` double NOT NULL DEFAULT '0',
  `currency_id` int(11) DEFAULT NULL,
  `from_currency_id` int(11) DEFAULT NULL,
  `currency_rate` double DEFAULT '1',
  `actual_commission_amount` double DEFAULT NULL,
  `commission_amount` double NOT NULL DEFAULT '0',
  `commission_perc` decimal(10,2) NOT NULL DEFAULT '0.00',
  `remark` text,
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '1 - Confirmed, 2 - Pending, 3 - Waiting, 4 - Cancelled',
  `created_date` datetime DEFAULT NULL,
  `confirmed_date` datetime DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  UNIQUE KEY `fr_com_id_2` (`fr_com_id`),
  KEY `fr_com_id` (`fr_com_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_commission_details`
--

CREATE TABLE IF NOT EXISTS `franchisee_commission_details` (
  `fr_com_id` bigint(20) unsigned NOT NULL,
  `country_id` int(11) unsigned DEFAULT NULL,
  `state_id` bigint(20) unsigned DEFAULT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `region_id` int(10) unsigned DEFAULT NULL,
  `city_id` bigint(20) unsigned DEFAULT NULL,
  UNIQUE KEY `fr_com_id` (`fr_com_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_commission_releasing`
--

CREATE TABLE IF NOT EXISTS `franchisee_commission_releasing` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `franchisee_id` bigint(20) NOT NULL,
  `from_currency_id` int(11) DEFAULT NULL,
  `currency_id` smallint(10) NOT NULL,
  `commission_type` varchar(10) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `actual_amount` double DEFAULT NULL,
  `amount` double NOT NULL,
  `charges` float(3,2) NOT NULL,
  `paid_amount` double NOT NULL,
  `tax` double NOT NULL,
  `tds` double DEFAULT NULL,
  `remarks` text,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `released_on` datetime DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_commission_status_lookup`
--

CREATE TABLE IF NOT EXISTS `franchisee_commission_status_lookup` (
  `com_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) DEFAULT NULL,
  `status_description` text,
  `label_class` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`com_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `franchisee_commission_status_lookup`
--

INSERT INTO `franchisee_commission_status_lookup` (`com_status_id`, `status_name`, `status_description`, `label_class`) VALUES
(1, 'Confirmed', '', 'label-success'),
(2, 'Pending', 'Eligible waiting for admin approval', 'label-warning'),
(3, 'Waiting', 'if goes below 40% of required deposit for continuous 5 days any time in a month ', 'label-warning'),
(4, 'Cancelled', 'DisApproved by the admin', 'label-danger');

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_commission_type_lookups`
--

CREATE TABLE IF NOT EXISTS `franchisee_commission_type_lookups` (
  `commission_type_id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `commission_type` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`commission_type_id`),
  UNIQUE KEY `commission_type` (`commission_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `franchisee_commission_type_lookups`
--

INSERT INTO `franchisee_commission_type_lookups` (`commission_type_id`, `commission_type`, `status`, `is_deleted`) VALUES
(1, 'Fund Transfer', 1, 0),
(2, 'Add Funds ', 1, 0),
(3, 'Fixed Contribution', 1, 0),
(4, 'Flexible Contribution', 1, 0),
(5, 'Franchisee Add Fund Commission', 1, 0),
(6, 'Admin Add Fun to Franchisee', 1, 0),
(7, 'Fixed Special Bonus', 1, 0),
(8, 'Flexible Special Bonus', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_fund_transfer`
--

CREATE TABLE IF NOT EXISTS `franchisee_fund_transfer` (
  `fft_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `from_account_id` bigint(20) DEFAULT NULL,
  `from_user_type` smallint(6) DEFAULT NULL COMMENT '0-User, 1-Admin, 2-Network Franchisee,3 -Franchisee, 4 - System Admin',
  `to_account_id` bigint(20) DEFAULT NULL,
  `to_user_type` smallint(6) DEFAULT NULL COMMENT '0-User, 1-Admin, 2-Network Franchisee,3 -Franchisee,4 -System Admin',
  `is_commission` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `fft_relation_id` bigint(20) DEFAULT '0',
  `transaction_id` varchar(255) DEFAULT NULL,
  `from_user_wallet_id` int(11) DEFAULT NULL,
  `to_user_wallet_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT '0',
  `paidamt` double DEFAULT '0',
  `handleamt` double DEFAULT '0',
  `commission_perc` decimal(10,2) DEFAULT '0.00',
  `commission` double DEFAULT '0',
  `remark` text,
  `status` smallint(6) DEFAULT '0' COMMENT '0 - Pending, 1 - Confirmed, 2 - Cancelled',
  `is_inflow` tinyint(1) unsigned DEFAULT '1',
  `ip_address` varchar(100) DEFAULT NULL,
  `transferred_on` datetime DEFAULT NULL,
  `cancelled_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `admin_id` bigint(20) DEFAULT NULL,
  `is_deleted` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  PRIMARY KEY (`fft_id`),
  KEY `is_inflow` (`is_inflow`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_lookup`
--

CREATE TABLE IF NOT EXISTS `franchisee_lookup` (
  `franchisee_typeid` int(2) NOT NULL AUTO_INCREMENT,
  `franchisee_type` varchar(60) DEFAULT NULL,
  `level` smallint(2) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `is_deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`franchisee_typeid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `franchisee_lookup`
--

INSERT INTO `franchisee_lookup` (`franchisee_typeid`, `franchisee_type`, `level`, `status`, `is_deleted`) VALUES
(1, 'Country Support Center', 1, 1, 0),
(2, 'Regional Support Center', 2, 1, 0),
(3, 'State Support Center', 3, 1, 0),
(4, 'District Support Center', 4, 1, 0),
(5, 'City Support Center', 5, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_package`
--

CREATE TABLE IF NOT EXISTS `franchisee_package` (
  `fr_pack_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `currency` int(11) DEFAULT NULL,
  `fr_pack_amount` decimal(10,2) DEFAULT '0.00',
  `franchisee_type` smallint(6) DEFAULT NULL COMMENT '1 - Master, 2 - Regional, 3 - State, 4 - District, 5 - City',
  `franchisee_min_perc` int(11) NOT NULL DEFAULT '0' COMMENT 'minimum balance maintenance in percentage ',
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fr_pack_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `franchisee_package`
--

INSERT INTO `franchisee_package` (`fr_pack_id`, `currency`, `fr_pack_amount`, `franchisee_type`, `franchisee_min_perc`, `created_date`, `updated_date`) VALUES
(1, 2, NULL, 1, 40, NULL, '2016-11-02 18:29:23'),
(2, 2, '1000000.00', 2, 40, NULL, '2016-11-02 18:29:23'),
(3, 2, '500000.00', 3, 40, NULL, '2016-11-02 18:29:23'),
(4, 2, '150000.00', 4, 40, NULL, '2016-11-02 18:29:23'),
(5, 2, '50000.00', 5, 40, NULL, '2016-11-02 18:29:23');

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_settings`
--

CREATE TABLE IF NOT EXISTS `franchisee_settings` (
  `fr_sett_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) DEFAULT NULL COMMENT 'franchisee id',
  `franchisee_type` smallint(6) DEFAULT NULL COMMENT '1 - Master, 2 - Regional, 3 - State, 4 - District, 5 - City',
  `office_available` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `is_deposited` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `deposited_amount` decimal(10,2) DEFAULT NULL,
  `currency` int(11) DEFAULT NULL,
  `low_transaction_details` longtext,
  `created_by` bigint(20) NOT NULL DEFAULT '0',
  `updated_by` bigint(20) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1- active 0-inactive',
  PRIMARY KEY (`fr_sett_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_tds_lookup`
--

CREATE TABLE IF NOT EXISTS `franchisee_tds_lookup` (
  `ftds_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `tds_current_com` double DEFAULT '0',
  `tds_tot_com_credit` double DEFAULT '0',
  `tds_tot_com_debit` double DEFAULT '0',
  `transferred_on` datetime DEFAULT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ftds_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `franchisee_tds_transaction`
--

CREATE TABLE IF NOT EXISTS `franchisee_tds_transaction` (
  `ftds_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) DEFAULT NULL,
  `currency_id` int(11) DEFAULT '2',
  `total_commission` double NOT NULL,
  `tds_per` decimal(10,2) NOT NULL,
  `tds` double NOT NULL,
  `transferred_on` datetime NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` smallint(6) DEFAULT '0',
  PRIMARY KEY (`ftds_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `geo_zone`
--

CREATE TABLE IF NOT EXISTS `geo_zone` (
  `geo_zone_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `zone` varchar(255) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`geo_zone_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `geo_zone`
--

INSERT INTO `geo_zone` (`geo_zone_id`, `zone`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 'India', '2016-07-27 23:48:36', '2017-05-03 19:15:40', 1, 0),
(2, 'ABCD', '2017-11-28 04:57:30', '2017-11-28 00:57:30', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `geo_zone_locations`
--

CREATE TABLE IF NOT EXISTS `geo_zone_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `geo_zone_id` smallint(5) NOT NULL,
  `country_id` tinyint(3) unsigned NOT NULL,
  `state_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `geo_zone_locations`
--

INSERT INTO `geo_zone_locations` (`location_id`, `geo_zone_id`, `country_id`, `state_id`, `is_deleted`) VALUES
(1, 1, 77, 1203, 0);

-- --------------------------------------------------------

--
-- Table structure for table `imgs`
--

CREATE TABLE IF NOT EXISTS `imgs` (
  `img_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_type_id` tinyint(2) unsigned NOT NULL,
  `relative_post_id` bigint(20) unsigned NOT NULL,
  `img_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-Image, 2-Logo, 3-Banner',
  `img_path` varchar(255) DEFAULT NULL,
  `img_file` varchar(255) DEFAULT NULL,
  `primary` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort_order` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `status_id` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-Disabled, 1-Enabled',
  `is_verified` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`img_id`),
  KEY `post_type_id` (`post_type_id`),
  KEY `updated_by` (`updated_by`),
  KEY `img_type` (`img_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=49 ;

--
-- Dumping data for table `imgs`
--

INSERT INTO `imgs` (`img_id`, `post_type_id`, `relative_post_id`, `img_type`, `img_path`, `img_file`, `primary`, `sort_order`, `status_id`, `is_verified`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 3, 1, 1, 'gl0', 'img8t2lbq.jpg', 0, 2, 1, 1, '2016-08-05 06:49:20', '2017-06-12 21:03:09', 1, 0),
(2, 3, 1, 1, 'gl0', 'img8t2lba.jpg', 0, 1, 1, 1, '2016-08-05 06:50:36', '2017-06-12 21:03:06', 1, 0),
(3, 3, 1, 1, 'gl0', 'img8t3lbc.jpg', 1, 3, 1, 1, '2016-08-05 06:51:01', '2017-04-23 21:07:39', 1, 0),
(4, 1, 1, 1, 'gl0', 'img4u6gvz.jpg', 0, 0, 1, 1, '2016-08-05 06:51:44', '2017-06-12 21:03:02', 1, 0),
(5, 1, 1, 1, 'gl0', 'img5isqru.jpg', 0, 0, 1, 1, '2016-08-05 06:53:02', '2017-06-12 21:02:59', 1, 0),
(6, 1, 1, 1, 'gl0', 'img6edu8a.jpg', 0, 0, 1, 1, '2016-08-05 06:59:54', '2017-06-12 21:02:56', 1, 0),
(7, 1, 1, 1, 'gl0', 'img7trz1l.jpg', 0, 0, 1, 1, '2016-08-05 07:04:30', '2017-06-12 21:02:53', 1, 0),
(8, 1, 1, 1, 'gl0', 'img8t2lbq.jpg', 0, 0, 1, 1, '2016-08-05 07:07:01', '2017-06-12 21:02:50', 1, 0),
(9, 3, 1, 1, 'gl0', 'img95rh3e.jpg', 0, 4, 1, 1, '2016-10-24 22:45:30', '2017-04-23 21:07:42', 1, 0),
(10, 3, 9, 1, 'jmv', 'img101k32g.png', 0, 1, 1, 1, '2016-10-26 22:39:36', '2016-11-10 22:58:40', 1, 0),
(11, 1, 1, 1, 'gl0', 'img11aa8qh.png', 0, 6, 1, 1, '2016-11-11 06:03:23', '2017-06-12 21:02:41', 1, 0),
(12, 1, 1, 1, 'gl0', 'img12z32ux.jpg', 0, 7, 1, 1, '2016-11-11 06:11:44', '2017-06-12 21:02:38', 1, 0),
(13, 1, 1, 1, 'gl0', 'img13ss3vt.jpg', 0, 8, 1, 1, '2016-11-11 06:14:20', '2017-06-12 21:02:34', 1, 0),
(14, 1, 1, 1, 'gl0', 'img14rawcx.png', 0, 9, 1, 1, '2016-11-11 06:20:04', '2017-06-12 21:02:32', 1, 0),
(15, 1, 1, 1, 'gl0', 'img150fen7.png', 0, 10, 1, 1, '2016-11-11 06:20:37', '2017-06-12 21:02:29', 1, 0),
(16, 3, 19, 1, '27u', 'img16jk657.jpg', 0, 1, 1, 1, '2017-04-03 23:59:09', '2017-08-06 23:31:12', 1, 1),
(17, 3, 19, 1, '27u', 'img17jilem.jpg', 0, 2, 1, 1, '2017-04-03 23:59:40', '2017-08-06 23:31:14', 1, 1),
(18, 3, 18, 1, 'ggv', 'img18hz6as.jpg', 0, 1, 1, 1, '2017-06-13 01:56:02', '2017-06-12 20:57:58', 1, 0),
(19, 3, 1, 1, 'gl0', 'img19ugv9a.jpg', 0, 5, 1, 0, '2017-08-03 22:58:34', '2017-08-03 06:28:34', 1, 0),
(20, 3, 1, 1, 'gl0', 'img20s8irf.jpg', 0, 6, 1, 0, '2017-08-03 22:58:44', '2017-08-03 06:28:44', 1, 0),
(21, 3, 1, 1, 'gl0', 'img21ilw6w.jpg', 0, 7, 1, 0, '2017-08-03 23:03:48', '2017-08-03 06:33:48', 1, 0),
(22, 3, 1, 1, 'gl0', 'img22k1ymn.jpg', 0, 8, 1, 0, '2017-08-03 23:04:39', '2017-08-03 06:34:39', 1, 0),
(23, 3, 1, 1, 'gl0', 'img23c68t7.jpg', 0, 9, 1, 0, '2017-08-03 23:06:14', '2017-08-03 06:36:14', 1, 0),
(24, 3, 1, 1, 'gl0', 'img244mhmi.jpg', 0, 10, 1, 0, '2017-08-03 23:06:51', '2017-08-03 06:36:51', 1, 0),
(25, 3, 1, 1, 'gl0', 'img2589ax6.jpg', 0, 11, 1, 0, '2017-08-03 23:09:09', '2017-08-03 06:39:09', 1, 0),
(26, 3, 1, 1, 'gl0', 'img26z764m.jpg', 0, 12, 1, 0, '2017-08-03 23:09:31', '2017-08-03 06:39:31', 1, 0),
(27, 3, 19, 1, '27u', 'img27bmguw.jpeg', 0, 3, 1, 0, '2017-08-07 04:24:48', '2017-08-06 11:54:48', 1, 0),
(28, 3, 19, 1, '27u', 'img28nvnbz.jpeg', 0, 4, 1, 0, '2017-08-07 04:24:56', '2017-08-17 20:25:04', 1, 0),
(29, 3, 19, 1, '27u', 'img29zix5u.jpeg', 0, 5, 1, 0, '2017-08-07 04:24:58', '2017-08-11 21:18:15', 1, 0),
(30, 3, 19, 1, '27u', 'img30ac7xl.jpeg', 1, 6, 1, 0, '2017-08-07 04:25:00', '2017-08-17 20:25:04', 1, 0),
(36, 3, 30, 1, 'c73', 'img3618jgl.jpg', 1, 1, 1, 0, '2018-07-06 06:29:38', '2018-07-06 07:04:06', 1, 0),
(37, 3, 30, 1, 'c73', 'img37iind3.jpg', 0, 2, 1, 0, '2018-07-06 06:31:26', '2018-07-06 07:04:06', 1, 0),
(38, 3, 30, 1, 'c73', 'img38o833l.jpg', 0, 3, 1, 0, '2018-07-06 06:32:15', '2018-07-06 07:04:04', 1, 0),
(39, 3, 30, 1, 'c73', 'img39a2w53.jpg', 0, 4, 1, 0, '2018-07-06 06:33:07', '2018-07-06 01:03:07', 1, 0),
(40, 3, 30, 1, 'c73', 'img40hskls.jpg', 0, 5, 1, 0, '2018-07-06 06:35:47', '2018-07-06 01:05:47', 1, 0),
(41, 3, 30, 1, 'c73', 'img41im84j.jpg', 0, 6, 1, 0, '2018-07-06 06:35:58', '2018-07-06 07:02:54', 1, 0),
(42, 3, 30, 1, 'c73', 'img42uljoz.jpg', 0, 7, 1, 0, '2018-07-06 06:36:07', '2018-07-06 07:02:43', 1, 0),
(43, 3, 30, 1, 'c73', 'img431erdf.jpg', 0, 8, 1, 0, '2018-07-06 06:38:21', '2018-07-06 07:02:17', 1, 0),
(44, 3, 30, 1, 'c73', 'img44ylwk0.jpg', 0, 9, 1, 0, '2018-07-06 07:00:13', '2018-07-06 07:03:55', 1, 0),
(45, 3, 29, 1, 'rph', 'img45jnqon.jpg', 0, 1, 1, 0, '2018-07-06 11:26:03', '2018-07-06 05:56:03', 1, 0),
(46, 3, 29, 1, 'rph', 'img466yd6t.jpg', 0, 2, 1, 0, '2018-07-06 11:26:24', '2018-07-06 05:56:24', 1, 0),
(47, 3, 31, 1, '8da', 'img477t0ay.jpg', 0, 1, 1, 0, '2018-07-06 12:06:50', '2018-07-06 06:36:50', 1, 0),
(48, 3, 31, 1, '8da', 'img4802c8p.jpg', 0, 2, 1, 0, '2018-07-06 12:07:01', '2018-07-06 06:37:01', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `img_display_settings`
--

CREATE TABLE IF NOT EXISTS `img_display_settings` (
  `ids_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `display_size` varchar(255) NOT NULL,
  `width` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'in px',
  `height` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'in px',
  PRIMARY KEY (`ids_id`),
  UNIQUE KEY `display_size` (`display_size`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `img_display_settings`
--

INSERT INTO `img_display_settings` (`ids_id`, `display_size`, `width`, `height`) VALUES
(1, 'product-img-md', 200, 200),
(2, 'product-details', 420, 512),
(3, 'product-details-additional', 100, 122),
(4, 'product-details-zoom', 850, 1036),
(5, 'home-slider-block-featured', 200, 200),
(6, 'home-slider-block-img', 1170, 390),
(7, 'broswe-slider-block-img', 871, 228);

-- --------------------------------------------------------

--
-- Table structure for table `img_filters`
--

CREATE TABLE IF NOT EXISTS `img_filters` (
  `img_filter_id` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `post_type_id` tinyint(2) unsigned NOT NULL,
  `relative_post_id` bigint(20) unsigned NOT NULL,
  `img_id` bigint(20) unsigned NOT NULL,
  `primary_img` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`img_filter_id`),
  KEY `post_type_id` (`post_type_id`),
  KEY `relative_post_id` (`relative_post_id`),
  KEY `img_id` (`img_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `img_filters`
--

INSERT INTO `img_filters` (`img_filter_id`, `post_type_id`, `relative_post_id`, `img_id`, `primary_img`, `sort_order`, `is_deleted`) VALUES
(1, 4, 5, 1, 1, 0, 0),
(2, 4, 6, 2, 1, 0, 0),
(3, 4, 7, 3, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `img_settings`
--

CREATE TABLE IF NOT EXISTS `img_settings` (
  `img_settings_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `post_type_id` tinyint(2) unsigned NOT NULL,
  `img_type_id` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `file_path` varchar(255) DEFAULT NULL,
  `product_image_verification` tinyint(1) unsigned NOT NULL COMMENT '0--auto,1--manual',
  `width` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT ' in PX',
  `height` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'in PX',
  `max_size` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Size in BYTES',
  PRIMARY KEY (`img_settings_id`),
  KEY `post_type_id` (`post_type_id`),
  KEY `img_type_id` (`img_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `img_settings`
--

INSERT INTO `img_settings` (`img_settings_id`, `post_type_id`, `img_type_id`, `file_path`, `product_image_verification`, `width`, `height`, `max_size`) VALUES
(1, 1, 1, 'brands', 0, 200, 200, 2048),
(2, 1, 2, 'brands/logo', 0, 0, 0, 0),
(3, 1, 3, 'brands/banner', 0, 0, 0, 0),
(4, 2, 1, 'categories', 0, 0, 0, 0),
(5, 2, 2, 'categories/logo', 0, 0, 0, 0),
(6, 2, 3, 'categories/banner', 0, 0, 0, 0),
(7, 3, 1, 'products', 0, 0, 0, 0),
(8, 3, 2, 'products/logo', 0, 0, 0, 0),
(9, 3, 3, 'products/banner', 0, 0, 0, 0),
(10, 4, 1, 'product-combinations', 0, 0, 0, 0),
(11, 4, 2, 'product-combinations/logo', 0, 0, 0, 0),
(12, 4, 3, 'product-combinations/banner', 0, 0, 0, 0),
(13, 5, 1, 'supplier', 0, 0, 0, 0),
(14, 5, 2, 'supplier/logo', 0, 0, 0, 0),
(15, 5, 3, 'supplier/banner', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `img_type_settings`
--

CREATE TABLE IF NOT EXISTS `img_type_settings` (
  `img_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `display_type` varchar(255) NOT NULL,
  PRIMARY KEY (`img_type_id`),
  UNIQUE KEY `display_type` (`display_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `img_type_settings`
--

INSERT INTO `img_type_settings` (`img_type_id`, `display_type`) VALUES
(3, 'Banner'),
(1, 'Image'),
(2, 'Logo');

-- --------------------------------------------------------

--
-- Table structure for table `kyc_documents`
--

CREATE TABLE IF NOT EXISTS `kyc_documents` (
  `kyc_id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `post_type` tinyint(1) NOT NULL,
  `relative_post_id` bigint(20) unsigned DEFAULT '0',
  `pan_card_no` varchar(50) NOT NULL,
  `pan_card_name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `pan_card_image` text NOT NULL,
  `vat_no` varchar(50) NOT NULL,
  `cst_no` varchar(50) NOT NULL,
  `gstin` varchar(30) NOT NULL,
  `auth_person_name` varchar(100) NOT NULL,
  `id_proof_document_type_id` tinyint(2) unsigned DEFAULT NULL,
  `auth_person_id_proof` text NOT NULL,
  `updated_on` timestamp NULL DEFAULT NULL,
  `status_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`kyc_id`),
  KEY `supplier_id` (`relative_post_id`),
  KEY `id_proof_document_type_id` (`id_proof_document_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `kyc_documents`
--

INSERT INTO `kyc_documents` (`kyc_id`, `post_type`, `relative_post_id`, `pan_card_no`, `pan_card_name`, `dob`, `pan_card_image`, `vat_no`, `cst_no`, `gstin`, `auth_person_name`, `id_proof_document_type_id`, `auth_person_id_proof`, `updated_on`, `status_id`, `is_deleted`) VALUES
(1, 3, 33, 'AAAAA1231A', 'prakash', '2000-02-22', 'resources/uploads/suppliers/pan_cards/PANCARD_33_Jellyfish.jpg', '4564654', '78979879', '29ABCDE1234F2Z5', 'prakash', 18, 'resources/uploads/suppliers/id_proof/IDPROOF_33_Penguins.jpg', NULL, 0, 0),
(2, 0, 1, 'ABCDE1234A', 'Parthiban Kasi', '1990-01-02', 'assets/uploads/suppliers/pan_cards/PANCARD_27_Chrysanthemum.jpg', '1234567890', '1234567890', '', 'Parthiban K', NULL, 'assets/uploads/suppliers/id_proof/IDPROOF_27_Lighthouse.jpg', NULL, 0, 0),
(3, 0, 1, 'ABCDE1334C', 'Parthiban', '1990-06-02', 'assets/uploads/suppliers/pan_cards/PANCARD_28_Lighthouse.jpg', '1234567890', '1234567890', '', 'Parthiban', 2, 'assets/uploads/suppliers/id_proof/IDPROOF_28_Lighthouse.jpg', NULL, 0, 0),
(4, 0, 1, 'ABCDE4334A', 'Parthiban', '1990-06-06', 'assets/uploads/suppliers/pan_cards/PANCARD_24_Lighthouse.jpg', '1234567890', '1234567890', '', 'Parthiban', 9, 'assets/uploads/suppliers/id_proof/IDPROOF_24_Koala.jpg', NULL, 0, 0),
(5, 3, 35, 'AAAAA1231A', 'prakash', '2018-07-20', 'resources/uploads/suppliers/pan_cards/PANCARD_35_travel2.jpg', '4564654123456', '78979879123', '29ABCDE1234F2Z5', 'prakash', 1, 'resources/uploads/suppliers/id_proof/IDPROOF_35_travel.jpg', NULL, 0, 0),
(6, 3, 51, 'AAAAA1231A', 'prakash', '2018-07-16', 'resources/uploads/suppliers/pan_cards/PANCARD_51_travel.jpg', '4564654', '78979879', '29ABCDE1234F2Z5', 'prakash', 1, 'resources/uploads/suppliers/id_proof/IDPROOF_51_resort.jpg', NULL, 0, 0),
(7, 3, 53, 'AAAAA1231A', 'prakash', '2018-07-13', 'resources/uploads/suppliers/pan_cards/PANCARD_53_resort.jpg', '4564654', '78979879', '29ABCDE1234F2Z5', 'prakash', 1, 'resources/uploads/suppliers/id_proof/IDPROOF_53_travel2.jpg', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `language_lookups`
--

CREATE TABLE IF NOT EXISTS `language_lookups` (
  `language_id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `language_name` varchar(30) DEFAULT NULL,
  `iso_code` varchar(3) NOT NULL,
  PRIMARY KEY (`language_id`),
  UNIQUE KEY `iso_code` (`iso_code`),
  UNIQUE KEY `language_name` (`language_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `language_lookups`
--

INSERT INTO `language_lookups` (`language_id`, `language_name`, `iso_code`) VALUES
(1, 'English', 'eng');

-- --------------------------------------------------------

--
-- Table structure for table `locale_lookups`
--

CREATE TABLE IF NOT EXISTS `locale_lookups` (
  `locale_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `locale_name` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`locale_id`),
  UNIQUE KEY `locale_name` (`locale_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `locale_lookups`
--

INSERT INTO `locale_lookups` (`locale_id`, `locale_name`) VALUES
(1, 'en');

-- --------------------------------------------------------

--
-- Stand-in structure for view `local_shipping_charges`
--
CREATE TABLE IF NOT EXISTS `local_shipping_charges` (
`supplier_product_id` bigint(15) unsigned
,`supplier_id` int(10) unsigned
,`sup_logistic_id` decimal(6,0)
,`mode_id` smallint(4) unsigned
,`pro_weight` decimal(34,13)
,`weight_slab_title` varchar(50)
,`for_each_grams` decimal(10,3) unsigned
,`for_each_grams_delivery_charge` decimal(10,3) unsigned
,`delivery_charge` decimal(10,3)
,`delivery_days` smallint(3) unsigned
);
-- --------------------------------------------------------

--
-- Table structure for table `location_countries`
--

CREATE TABLE IF NOT EXISTS `location_countries` (
  `country_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(80) DEFAULT NULL,
  `phonecode` varchar(15) DEFAULT '',
  `mobile_validation` varchar(100) DEFAULT '' COMMENT 'Regular Expression to validate Mobile number of this country',
  `telephone_validation` varchar(100) DEFAULT '' COMMENT 'Regular Expression to validate telephone no of this country',
  `iso2` varchar(2) DEFAULT '',
  `currency_id` tinyint(3) unsigned DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `time_zone` varchar(75) NOT NULL,
  `operate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`country_id`),
  UNIQUE KEY `country` (`country`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=195 ;

--
-- Dumping data for table `location_countries`
--

INSERT INTO `location_countries` (`country_id`, `country`, `phonecode`, `mobile_validation`, `telephone_validation`, `iso2`, `currency_id`, `status`, `time_zone`, `operate`) VALUES
(1, 'Afghanistan', '+93', NULL, NULL, 'AF', NULL, 0, 'Asia/Kabul', 0),
(2, 'Albania', '+355', NULL, NULL, 'AL', NULL, 0, 'Europe/Tirane', 0),
(3, 'Algeria', '+213', NULL, NULL, 'DZ', NULL, 0, 'Africa/Algiers', 0),
(4, 'Andorra', '+376', NULL, NULL, 'AD', NULL, 0, 'Europe/Andorra', 0),
(5, 'Angola', '+244', NULL, NULL, '', NULL, 0, '', 0),
(6, 'Antigua and barbuda', '+1268', NULL, NULL, '', NULL, 0, '', 0),
(7, 'Argentina', '+54', NULL, NULL, '', NULL, 0, '', 0),
(8, 'Armenia', '+374', NULL, NULL, '', NULL, 0, '', 0),
(9, 'Australia', '+61', NULL, NULL, '', NULL, 0, '', 0),
(10, 'Austria', '+43', NULL, NULL, '', NULL, 0, '', 0),
(11, 'Azerbaijan', '+994', NULL, NULL, '', NULL, 0, '', 0),
(12, 'Bahrain', '+973', NULL, NULL, '', NULL, 0, '', 0),
(13, 'Bangladesh', '+880', NULL, NULL, '', NULL, 0, '', 0),
(14, 'Barbados', '+1', NULL, NULL, '', NULL, 0, '', 0),
(15, 'Belarus', '+375', NULL, NULL, '', NULL, 0, '', 0),
(16, 'Belgium', '+32', NULL, NULL, '', NULL, 0, '', 0),
(17, 'Belize', '+501', NULL, NULL, '', NULL, 0, '', 0),
(18, 'Benin', '+229', NULL, NULL, '', NULL, 0, '', 0),
(19, 'Bhutan', '+975', NULL, NULL, '', NULL, 0, '', 0),
(20, 'Bolivia', '+591', NULL, NULL, '', NULL, 0, '', 0),
(21, 'Bosnia and herzegovina', '+387', NULL, NULL, '', NULL, 0, '', 0),
(22, 'Botswana', '+267', NULL, NULL, '', NULL, 0, '', 0),
(23, 'Brazil', '+55', NULL, NULL, '', NULL, 0, '', 0),
(24, 'Brunei', '+673', NULL, NULL, '', NULL, 0, '', 0),
(25, 'Bulgaria', '+359', NULL, NULL, '', NULL, 0, '', 0),
(26, 'Burkina faso', '+226', NULL, NULL, '', NULL, 0, '', 0),
(27, 'Burma/Myanmar', '+95', NULL, NULL, '', NULL, 0, '', 0),
(28, 'Burundi', '+257', NULL, NULL, '', NULL, 0, '', 0),
(29, 'Cambodia', '+855', NULL, NULL, '', NULL, 0, '', 0),
(30, 'Cameroon', '+237', NULL, NULL, '', NULL, 0, '', 0),
(31, 'Canada', '+1', NULL, NULL, '', NULL, 0, '', 0),
(32, 'Cape verde', '+238', NULL, NULL, '', NULL, 0, '', 0),
(33, 'Central african republic', '+236', NULL, NULL, '', NULL, 0, '', 0),
(34, 'Chad', '+235', NULL, NULL, '', NULL, 0, '', 0),
(35, 'Chile', '+56', NULL, NULL, '', NULL, 0, '', 0),
(36, 'China', '+86', NULL, NULL, '', NULL, 0, '', 0),
(37, 'Colombia', '+855', NULL, NULL, '', NULL, 0, '', 0),
(38, 'Comores', '+269', NULL, NULL, '', NULL, 0, '', 0),
(39, 'Republic of the Congo', '+242', NULL, NULL, '', NULL, 0, '', 0),
(40, 'Democratic Republic of the Congo', '+243', NULL, NULL, '', NULL, 0, '', 0),
(41, 'Costa rica', '+506', NULL, NULL, '', NULL, 0, '', 0),
(42, 'Cote d ivoire', '+225', NULL, NULL, '', NULL, 0, '', 0),
(43, 'Croatia', '+385', NULL, NULL, '', NULL, 0, '', 0),
(44, 'Cuba', '+53', NULL, NULL, '', NULL, 0, '', 0),
(45, 'Cyprus', '+357', NULL, NULL, '', NULL, 0, '', 0),
(46, 'Czech republic', '+420', NULL, NULL, '', NULL, 0, '', 0),
(47, 'Denmark', '+45', NULL, NULL, '', NULL, 0, '', 0),
(48, 'Djibouti', '+253', NULL, NULL, '', NULL, 0, '', 0),
(49, 'Dominica', '+1767', NULL, NULL, '', NULL, 0, '', 0),
(50, 'Dominican republic', '+1', NULL, NULL, '', NULL, 0, '', 0),
(51, 'East Timor', '+670', NULL, NULL, '', NULL, 0, '', 0),
(52, 'Ecuador', '+593', NULL, NULL, '', NULL, 0, '', 0),
(53, 'Egypt', '+20', NULL, NULL, '', NULL, 0, '', 0),
(54, 'El salvador', '+503', NULL, NULL, '', NULL, 0, '', 0),
(55, 'Equatorial guinea', '+240', NULL, NULL, '', NULL, 0, '', 0),
(56, 'Eritrea', '+291', NULL, NULL, '', NULL, 0, '', 0),
(57, 'Estonia', '+372', NULL, NULL, '', NULL, 0, '', 0),
(58, 'Ethiopia', '+251', NULL, NULL, '', NULL, 0, '', 0),
(59, 'Fiji', '+679', NULL, NULL, '', NULL, 0, '', 0),
(60, 'Finland', '+358', NULL, NULL, '', NULL, 0, '', 0),
(61, 'France', '+33', NULL, NULL, '', NULL, 0, '', 0),
(62, 'Gabon', '+241', NULL, NULL, '', NULL, 0, '', 0),
(63, 'Gambia', '+220', NULL, NULL, '', NULL, 0, '', 0),
(64, 'Georgia', '+995', NULL, NULL, '', NULL, 0, '', 0),
(65, 'Germany', '+49', NULL, NULL, '', NULL, 0, '', 0),
(66, 'Ghana', '+233', NULL, NULL, '', NULL, 0, '', 0),
(67, 'Greece', '+30', NULL, NULL, '', NULL, 0, '', 0),
(68, 'Grenada', '+1473', NULL, NULL, '', NULL, 0, '', 0),
(69, 'Guatemala', '+502', NULL, NULL, '', NULL, 0, '', 0),
(70, 'Guinea', '+224', NULL, NULL, '', NULL, 0, '', 0),
(71, 'Guinea-bissau', '+245', NULL, NULL, '', NULL, 0, '', 0),
(72, 'Guyana', '+592', NULL, NULL, '', NULL, 0, '', 0),
(73, 'Haiti', '+509', NULL, NULL, '', NULL, 0, '', 0),
(74, 'Honduras', '+504', NULL, NULL, '', NULL, 0, '', 0),
(75, 'Hungary', '+36', NULL, NULL, '', NULL, 0, '', 0),
(76, 'Iceland', '+354', NULL, NULL, '', NULL, 0, '', 0),
(77, 'India', '+91', '/^[0-9]{10}$/', '[0-9]{6}', 'IN', 2, 1, 'Asia/Calcutta', 1),
(78, 'Indonesia', '+62', NULL, NULL, '', NULL, 0, '', 0),
(79, 'Iran', '+98', NULL, NULL, '', NULL, 0, '', 0),
(80, 'Iraq', '+964', NULL, NULL, '', NULL, 0, '', 0),
(81, 'Ireland', '+353', NULL, NULL, '', NULL, 0, '', 0),
(82, 'Israel', '+972', NULL, NULL, '', NULL, 0, '', 0),
(83, 'Italy', '+39', NULL, NULL, '', NULL, 0, '', 0),
(84, 'Jamaica', '+1876', NULL, NULL, '', NULL, 0, '', 0),
(85, 'Japan', '+81', NULL, NULL, '', NULL, 0, '', 0),
(86, 'Jordan', '+962', NULL, NULL, '', NULL, 0, '', 0),
(87, 'Kazakhstan', '+7', NULL, NULL, '', NULL, 0, '', 0),
(88, 'Kenya', '+254', NULL, NULL, '', NULL, 0, '', 0),
(89, 'Kiribati', '+686', NULL, NULL, '', NULL, 0, '', 0),
(90, 'Kuwait', '+965', NULL, NULL, '', NULL, 0, '', 0),
(91, 'Kyrgyzstan', '+996', NULL, NULL, '', NULL, 0, '', 0),
(92, 'Laos', '+856', NULL, NULL, '', NULL, 0, '', 0),
(93, 'Latvia', '+371', NULL, NULL, '', NULL, 0, '', 0),
(94, 'Lebanon', '+961', NULL, NULL, '', NULL, 0, '', 0),
(95, 'Lesotho', '+266', NULL, NULL, '', NULL, 0, '', 0),
(96, 'Liberia', '+231', NULL, NULL, '', NULL, 0, '', 0),
(97, 'Libya', '+218', NULL, NULL, '', NULL, 0, '', 0),
(98, 'Liechtenstein', '+423', NULL, NULL, '', NULL, 0, '', 0),
(99, 'Lithuania', '+370', NULL, NULL, '', NULL, 0, '', 0),
(100, 'Luxembourg', '+352', NULL, NULL, '', NULL, 0, '', 0),
(101, 'Macedonia', '+389', NULL, NULL, '', NULL, 0, '', 0),
(102, 'Madagascar', '+261', NULL, NULL, '', NULL, 0, '', 0),
(103, 'Malawi', '+265', NULL, NULL, '', NULL, 0, '', 0),
(104, 'Malaysia', '+60', '/^[0-9]{8,9}$/', NULL, 'MY', NULL, 1, '', 1),
(105, 'Maldives', '+960', NULL, NULL, '', NULL, 0, '', 0),
(106, 'Mali', '+223', NULL, NULL, '', NULL, 0, '', 0),
(107, 'Malta', '+356', NULL, NULL, '', NULL, 0, '', 0),
(108, 'Marshall islands', '+692', NULL, NULL, '', NULL, 0, '', 0),
(109, 'Mauritania', '+222', NULL, NULL, '', NULL, 0, '', 0),
(110, 'Mauritius', '+230', NULL, NULL, '', NULL, 0, '', 0),
(111, 'Mexico', '+52', NULL, NULL, '', NULL, 0, '', 0),
(112, 'Micronesia', '+691', NULL, NULL, '', NULL, 0, '', 0),
(113, 'Moldova', '+373', NULL, NULL, '', NULL, 0, '', 0),
(114, 'Monaco', '+377', NULL, NULL, '', NULL, 0, '', 0),
(115, 'Mongolia', '+976', NULL, NULL, '', NULL, 0, '', 0),
(116, 'Morocco', '+212', NULL, NULL, '', NULL, 0, '', 0),
(117, 'Mozambique', '+258', NULL, NULL, '', NULL, 0, '', 0),
(118, 'Namibia', '+264', NULL, NULL, '', NULL, 0, '', 0),
(119, 'Nauru', '+674', NULL, NULL, '', NULL, 0, '', 0),
(120, 'Nepal', '+977', NULL, NULL, '', NULL, 0, '', 0),
(121, 'Netherlands', '+31', NULL, NULL, '', NULL, 0, '', 0),
(122, 'New zealand', '+64', NULL, NULL, '', NULL, 0, '', 0),
(123, 'Nicaragua', '+505', NULL, NULL, '', NULL, 0, '', 0),
(124, 'Niger', '+227', NULL, NULL, '', NULL, 0, '', 0),
(125, 'Nigeria', '+234', NULL, NULL, '', NULL, 0, '', 0),
(126, 'North korea', '+850', NULL, NULL, '', NULL, 0, '', 0),
(127, 'Norway', '+47', NULL, NULL, '', NULL, 0, '', 0),
(128, 'Oman', '+968', NULL, NULL, '', NULL, 0, '', 0),
(129, 'Pakistan', '+92', NULL, NULL, '', NULL, 0, '', 0),
(130, 'Palau', '+680', NULL, NULL, '', NULL, 0, '', 0),
(131, 'Panama', '+507', NULL, NULL, '', NULL, 0, '', 0),
(132, 'Papua new guinea', '+675', NULL, NULL, '', NULL, 0, '', 0),
(133, 'Paraguay', '+595', NULL, NULL, '', NULL, 0, '', 0),
(134, 'Peru', '+51', NULL, NULL, '', NULL, 0, '', 0),
(135, 'Philippines', '+63', NULL, NULL, '', NULL, 0, '', 0),
(136, 'Poland', '+48', NULL, NULL, '', NULL, 0, '', 0),
(137, 'Portugal', '+351', NULL, NULL, '', NULL, 0, '', 0),
(138, 'Qatar', '+974', NULL, NULL, '', NULL, 0, '', 0),
(139, 'Romania', '+40', NULL, NULL, '', NULL, 0, '', 0),
(140, 'Russia', '+7', NULL, NULL, '', NULL, 0, '', 0),
(141, 'Rwanda', '+250', NULL, NULL, '', NULL, 0, '', 0),
(142, 'Saint kitts and nevis', '+1', NULL, NULL, '', NULL, 0, '', 0),
(143, 'Saint lucia', '+1', NULL, NULL, '', NULL, 0, '', 0),
(144, 'Saint vincent and the grenadines', '+1', NULL, NULL, '', NULL, 0, '', 0),
(145, 'Samoa', '+685', NULL, NULL, '', NULL, 0, '', 0),
(146, 'San marino', '+378', NULL, NULL, '', NULL, 0, '', 0),
(147, 'Sao tome and principe', '+239', NULL, NULL, '', NULL, 0, '', 0),
(148, 'Saudi arabia', '+966', NULL, NULL, '', NULL, 0, '', 0),
(149, 'Senegal', '+221', NULL, NULL, '', NULL, 0, '', 0),
(150, 'Seychelles', '+248', NULL, NULL, '', NULL, 0, '', 0),
(151, 'Sierra leone', '+232', NULL, NULL, '', NULL, 0, '', 0),
(152, 'Singapore', '+65', NULL, NULL, '', NULL, 0, '', 0),
(153, 'Slovakia', '+421', NULL, NULL, '', NULL, 0, '', 0),
(154, 'Slovenia', '+386', NULL, NULL, '', NULL, 0, '', 0),
(155, 'Solomon islands', '+677', NULL, NULL, '', NULL, 0, '', 0),
(156, 'Somalia', '+252', NULL, NULL, '', NULL, 0, '', 0),
(157, 'South africa', '+27', NULL, NULL, '', NULL, 0, '', 0),
(158, 'South korea', '+82', NULL, NULL, '', NULL, 0, '', 0),
(159, 'Spain', '+34', NULL, NULL, '', NULL, 0, '', 0),
(160, 'Sri lanka', '+94', NULL, NULL, '', NULL, 0, '', 0),
(161, 'Sudan', '+249', NULL, NULL, '', NULL, 0, '', 0),
(162, 'Suriname', '+597', NULL, NULL, '', NULL, 0, '', 0),
(163, 'Swaziland', '+268', NULL, NULL, '', NULL, 0, '', 0),
(164, 'Sweden', '+46', NULL, NULL, '', NULL, 0, '', 0),
(165, 'Switzerland', '+41', NULL, NULL, '', NULL, 0, '', 0),
(166, 'Syria', '+963', NULL, NULL, '', NULL, 0, '', 0),
(167, 'Taiwan', '+886', NULL, NULL, '', NULL, 0, '', 0),
(168, 'Tajikistan', '+992', NULL, NULL, '', NULL, 0, '', 0),
(169, 'Tanzania', '+255', NULL, NULL, '', NULL, 0, '', 0),
(170, 'Thailand', '+66', NULL, NULL, '', NULL, 0, '', 0),
(171, 'The bahamas', '+1', NULL, NULL, '', NULL, 0, '', 0),
(172, 'Togo', '+228', NULL, NULL, '', NULL, 0, '', 0),
(173, 'Tonga', '+676', NULL, NULL, '', NULL, 0, '', 0),
(174, 'Trinidad and tobago', '+1', NULL, NULL, '', NULL, 0, '', 0),
(175, 'Tunisia', '+216', NULL, NULL, '', NULL, 0, '', 0),
(176, 'Turkey', '+90', NULL, NULL, '', NULL, 0, '', 0),
(177, 'Turkmenistan', '+993', NULL, NULL, '', NULL, 0, '', 0),
(178, 'Tuvalu', '+688', NULL, NULL, '', NULL, 0, '', 0),
(179, 'Uganda', '+256', NULL, NULL, '', NULL, 0, '', 0),
(180, 'Ukraine', '+380', NULL, NULL, '', NULL, 0, '', 0),
(181, 'United arab emirates', '+971', NULL, NULL, '', NULL, 0, '', 0),
(182, 'United kingdom', '+44', NULL, NULL, '', NULL, 0, '', 0),
(183, 'United states', '+1', NULL, NULL, '', NULL, 0, '', 0),
(184, 'Uruguay', '+598', NULL, NULL, '', NULL, 0, '', 0),
(185, 'Uzbekistan', '+998', NULL, NULL, '', NULL, 0, '', 0),
(186, 'Vanuatu', '+678', NULL, NULL, '', NULL, 0, '', 0),
(187, 'Vatican city', '+379', NULL, NULL, '', NULL, 0, '', 0),
(188, 'Venezuela', '+58', NULL, NULL, '', NULL, 0, '', 0),
(189, 'Vietnam', '+84', NULL, NULL, '', NULL, 0, '', 0),
(190, 'Yemen', '+967', NULL, NULL, '', NULL, 0, '', 0),
(191, 'Yugoslavia/Serbia And Montenegro', '+381', NULL, NULL, '', NULL, 0, '', 0),
(192, 'Zambia', '+260', NULL, NULL, '', NULL, 0, '', 0),
(193, 'Zimbabwe', '+263', NULL, NULL, '', NULL, 0, '', 0),
(194, 'Hong Kong', '+852', NULL, NULL, '', NULL, 0, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `location_districts`
--

CREATE TABLE IF NOT EXISTS `location_districts` (
  `district_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `district` varchar(100) NOT NULL,
  `state_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`district_id`),
  UNIQUE KEY `district` (`district`,`state_id`),
  KEY `state_id` (`state_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=616 ;

--
-- Dumping data for table `location_districts`
--

INSERT INTO `location_districts` (`district_id`, `district`, `state_id`) VALUES
(500, 'ADILABAD', 32),
(514, 'AGRA', 34),
(310, 'AHMED NAGAR', 21),
(124, 'AHMEDABAD', 12),
(360, 'AIZAWL', 24),
(432, 'AJMER', 29),
(311, 'AKOLA', 21),
(249, 'ALAPPUZHA', 18),
(515, 'ALIGARH', 34),
(516, 'ALLAHABAD', 34),
(584, 'ALMORA', 35),
(433, 'ALWAR', 29),
(150, 'AMBALA', 13),
(517, 'AMBEDKAR NAGAR', 34),
(312, 'AMRAVATI', 21),
(125, 'AMRELI', 12),
(411, 'AMRITSAR', 28),
(126, 'ANAND', 12),
(4, 'ANANTHAPUR', 2),
(181, 'ANANTHNAG', 15),
(379, 'ANGUL', 26),
(264, 'ANUPPUR', 20),
(54, 'ARARIA', 5),
(468, 'ARIYALUR', 31),
(55, 'ARWAL', 5),
(518, 'AURAIYA', 34),
(313, 'AURANGABAD', 21),
(56, 'AURANGABAD(BH)', 5),
(519, 'AZAMGARH', 34),
(220, 'BAGALKOT', 17),
(585, 'BAGESHWAR', 35),
(520, 'BAGPAT', 34),
(521, 'BAHRAICH', 34),
(265, 'BALAGHAT', 20),
(380, 'BALANGIR', 26),
(381, 'BALESWAR', 26),
(522, 'BALLIA', 34),
(523, 'BALRAMPUR', 34),
(127, 'BANASKANTHA', 12),
(524, 'BANDA', 34),
(182, 'BANDIPUR', 15),
(221, 'BANGALORE', 17),
(222, 'BANGALORE RURAL', 17),
(57, 'BANKA', 5),
(597, 'BANKURA', 36),
(434, 'BANSWARA', 29),
(525, 'BARABANKI', 34),
(183, 'BARAMULLA', 15),
(435, 'BARAN', 29),
(598, 'BARDHAMAN', 36),
(526, 'BAREILLY', 34),
(382, 'BARGARH', 26),
(436, 'BARMER', 29),
(412, 'BARNALA', 28),
(31, 'BARPETA', 4),
(266, 'BARWANI', 20),
(93, 'BASTAR', 7),
(527, 'BASTI', 34),
(413, 'BATHINDA', 28),
(314, 'BEED', 21),
(58, 'BEGUSARAI', 5),
(223, 'BELGAUM', 17),
(224, 'BELLARY', 17),
(267, 'BETUL', 20),
(383, 'BHADRAK', 26),
(59, 'BHAGALPUR', 5),
(315, 'BHANDARA', 21),
(437, 'BHARATPUR', 29),
(128, 'BHARUCH', 12),
(129, 'BHAVNAGAR', 12),
(438, 'BHILWARA', 29),
(268, 'BHIND', 20),
(151, 'BHIWANI', 13),
(60, 'BHOJPUR', 5),
(269, 'BHOPAL', 20),
(225, 'BIDAR', 17),
(94, 'BIJAPUR', 7),
(226, 'BIJAPUR', 17),
(528, 'BIJNOR', 34),
(439, 'BIKANER', 29),
(95, 'BILASPUR', 7),
(169, 'BILASPUR (HP)', 14),
(599, 'BIRBHUM', 36),
(344, 'BISHNUPUR', 22),
(196, 'BOKARO', 16),
(32, 'BONGAIGAON', 4),
(384, 'BOUDH', 26),
(529, 'BUDAUN', 34),
(184, 'BUDGAM', 15),
(530, 'BULANDSHAHR', 34),
(316, 'BULDHANA', 21),
(440, 'BUNDI', 29),
(61, 'BUXAR', 5),
(33, 'CACHAR', 4),
(114, 'CENTRAL DELHI', 10),
(170, 'CHAMBA', 14),
(586, 'CHAMOLI', 35),
(587, 'CHAMPAWAT', 35),
(361, 'CHAMPHAI', 24),
(227, 'CHAMRAJNAGAR', 17),
(531, 'CHANDAULI', 34),
(345, 'CHANDEL', 22),
(92, 'CHANDIGARH', 6),
(317, 'CHANDRAPUR', 21),
(17, 'CHANGLANG', 3),
(197, 'CHATRA', 16),
(469, 'CHENNAI', 31),
(270, 'CHHATARPUR', 20),
(271, 'CHHINDWARA', 20),
(228, 'CHICKMAGALUR', 17),
(229, 'CHIKKABALLAPUR', 17),
(230, 'CHITRADURGA', 17),
(532, 'CHITRAKOOT', 34),
(5, 'CHITTOOR', 2),
(441, 'CHITTORGARH', 29),
(346, 'CHURACHANDPUR', 22),
(442, 'CHURU', 29),
(470, 'COIMBATORE', 31),
(600, 'COOCH BEHAR', 36),
(471, 'CUDDALORE', 31),
(6, 'CUDDAPAH', 2),
(385, 'CUTTACK', 26),
(111, 'DADRA & NAGAR HAVELI', 8),
(130, 'DAHOD', 12),
(231, 'DAKSHINA KANNADA', 17),
(112, 'DAMAN', 9),
(272, 'DAMOH', 20),
(96, 'DANTEWADA', 7),
(62, 'DARBHANGA', 5),
(601, 'DARJILING', 36),
(34, 'DARRANG', 4),
(273, 'DATIA', 20),
(443, 'DAUSA', 29),
(232, 'DAVANGARE', 17),
(386, 'DEBAGARH', 26),
(588, 'DEHRADUN', 35),
(198, 'DEOGHAR', 16),
(533, 'DEORIA', 34),
(274, 'DEWAS', 20),
(510, 'DHALAI', 33),
(97, 'DHAMTARI', 7),
(199, 'DHANBAD', 16),
(275, 'DHAR', 20),
(472, 'DHARMAPURI', 31),
(233, 'DHARWARD', 17),
(35, 'DHEMAJI', 4),
(387, 'DHENKANAL', 26),
(444, 'DHOLPUR', 29),
(36, 'DHUBRI', 4),
(318, 'DHULE', 21),
(18, 'DIBANG VALLEY', 3),
(37, 'DIBRUGARH', 4),
(368, 'DIMAPUR', 25),
(473, 'DINDIGUL', 31),
(276, 'DINDORI', 20),
(113, 'DIU', 9),
(185, 'DODA', 15),
(200, 'DUMKA', 16),
(445, 'DUNGARPUR', 29),
(98, 'DURG', 7),
(63, 'EAST CHAMPARAN', 5),
(353, 'EAST GARO HILLS', 23),
(7, 'EAST GODAVARI', 2),
(19, 'EAST KAMENG', 3),
(354, 'EAST KHASI HILLS', 23),
(602, 'EAST MIDNAPORE', 36),
(277, 'EAST NIMAR', 20),
(20, 'EAST SIANG', 3),
(464, 'EAST SIKKIM', 30),
(201, 'EAST SINGHBHUM', 16),
(250, 'ERNAKULAM', 18),
(474, 'ERODE', 31),
(534, 'ETAH', 34),
(535, 'ETAWAH', 34),
(536, 'FAIZABAD', 34),
(152, 'FARIDABAD', 13),
(414, 'FARIDKOT', 28),
(537, 'FARRUKHABAD', 34),
(153, 'FATEHABAD', 13),
(415, 'FATEHGARH SAHIB', 28),
(538, 'FATEHPUR', 34),
(416, 'Fazilka', 28),
(539, 'FIROZABAD', 34),
(417, 'FIROZPUR', 28),
(234, 'GADAG', 17),
(319, 'GADCHIROLI', 21),
(388, 'GAJAPATI', 26),
(131, 'GANDHI NAGAR', 12),
(446, 'GANGANAGAR', 29),
(389, 'GANJAM', 26),
(202, 'GARHWA', 16),
(540, 'GAUTAM BUDDHA NAGAR', 34),
(64, 'GAYA', 5),
(541, 'GHAZIABAD', 34),
(542, 'GHAZIPUR', 34),
(203, 'GIRIDH', 16),
(38, 'GOALPARA', 4),
(204, 'GODDA', 16),
(39, 'GOLAGHAT', 4),
(543, 'GONDA', 34),
(320, 'GONDIA', 21),
(65, 'GOPALGANJ', 5),
(544, 'GORAKHPUR', 34),
(235, 'GULBARGA', 17),
(205, 'GUMLA', 16),
(278, 'GUNA', 20),
(8, 'GUNTUR', 2),
(418, 'GURDASPUR', 28),
(154, 'GURGAON', 13),
(279, 'GWALIOR', 20),
(40, 'HAILAKANDI', 4),
(545, 'HAMIRPUR', 34),
(171, 'HAMIRPUR(HP)', 14),
(447, 'HANUMANGARH', 29),
(280, 'HARDA', 20),
(546, 'HARDOI', 34),
(589, 'HARIDWAR', 35),
(236, 'HASSAN', 17),
(547, 'HATHRAS', 34),
(237, 'HAVERI', 17),
(206, 'HAZARIBAG', 16),
(321, 'HINGOLI', 21),
(155, 'HISAR', 13),
(603, 'HOOGHLY', 36),
(281, 'HOSHANGABAD', 20),
(419, 'HOSHIARPUR', 28),
(604, 'HOWRAH', 36),
(501, 'HYDERABAD', 32),
(251, 'IDUKKI', 18),
(347, 'IMPHAL EAST', 22),
(348, 'IMPHAL WEST', 22),
(282, 'INDORE', 20),
(283, 'JABALPUR', 20),
(390, 'JAGATSINGHAPUR', 26),
(355, 'JAINTIA HILLS', 23),
(448, 'JAIPUR', 29),
(449, 'JAISALMER', 29),
(391, 'JAJAPUR', 26),
(420, 'JALANDHAR', 28),
(548, 'JALAUN', 34),
(322, 'JALGAON', 21),
(323, 'JALNA', 21),
(450, 'JALOR', 29),
(605, 'JALPAIGURI', 36),
(186, 'JAMMU', 15),
(132, 'JAMNAGAR', 12),
(207, 'JAMTARA', 16),
(66, 'JAMUI', 5),
(99, 'JANJGIR-CHAMPA', 7),
(100, 'JASHPUR', 7),
(549, 'JAUNPUR', 34),
(67, 'JEHANABAD', 5),
(284, 'JHABUA', 20),
(156, 'JHAJJAR', 13),
(451, 'JHALAWAR', 29),
(550, 'JHANSI', 34),
(392, 'JHARSUGUDA', 26),
(452, 'JHUJHUNU', 29),
(157, 'JIND', 13),
(453, 'JODHPUR', 29),
(41, 'JORHAT', 4),
(133, 'JUNAGADH', 12),
(551, 'JYOTIBA PHULE NAGAR', 34),
(502, 'K.V.RANGAREDDY', 32),
(134, 'KACHCHH', 12),
(68, 'KAIMUR (BHABUA)', 5),
(158, 'KAITHAL', 13),
(393, 'KALAHANDI', 26),
(42, 'KAMRUP', 4),
(475, 'KANCHIPURAM', 31),
(394, 'KANDHAMAL', 26),
(172, 'KANGRA', 14),
(101, 'KANKER', 7),
(552, 'KANNAUJ', 34),
(252, 'KANNUR', 18),
(553, 'KANPUR DEHAT', 34),
(554, 'KANPUR NAGAR', 34),
(476, 'KANYAKUMARI', 31),
(421, 'KAPURTHALA', 28),
(409, 'KARAIKAL', 27),
(454, 'KARAULI', 29),
(43, 'KARBI ANGLONG', 4),
(187, 'KARGIL', 15),
(503, 'KARIM NAGAR', 32),
(44, 'KARIMGANJ', 4),
(159, 'KARNAL', 13),
(477, 'KARUR', 31),
(253, 'KASARGOD', 18),
(188, 'KATHUA', 15),
(69, 'KATIHAR', 5),
(285, 'KATNI', 20),
(555, 'KAUSHAMBI', 34),
(102, 'KAWARDHA', 7),
(395, 'KENDRAPARA', 26),
(396, 'KENDUJHAR', 26),
(70, 'KHAGARIA', 5),
(504, 'KHAMMAM', 32),
(135, 'KHEDA', 12),
(556, 'KHERI', 34),
(397, 'KHORDA', 26),
(208, 'KHUNTI', 16),
(173, 'KINNAUR', 14),
(369, 'KIPHIRE', 25),
(71, 'KISHANGANJ', 5),
(238, 'KODAGU', 17),
(209, 'KODERMA', 16),
(370, 'KOHIMA', 25),
(45, 'KOKRAJHAR', 4),
(239, 'KOLAR', 17),
(362, 'KOLASIB', 24),
(324, 'KOLHAPUR', 21),
(606, 'KOLKATA', 36),
(254, 'KOLLAM', 18),
(240, 'KOPPAL', 17),
(398, 'KORAPUT', 26),
(103, 'KORBA', 7),
(104, 'KORIYA', 7),
(455, 'KOTA', 29),
(255, 'KOTTAYAM', 18),
(256, 'KOZHIKODE', 18),
(9, 'KRISHNA', 2),
(478, 'KRISHNAGIRI', 31),
(174, 'KULLU', 14),
(189, 'KUPWARA', 15),
(10, 'KURNOOL', 2),
(160, 'KURUKSHETRA', 13),
(21, 'KURUNG KUMEY', 3),
(557, 'KUSHINAGAR', 34),
(175, 'LAHUL & SPITI', 14),
(46, 'LAKHIMPUR', 4),
(72, 'LAKHISARAI', 5),
(263, 'LAKSHADWEEP', 19),
(558, 'LALITPUR', 34),
(210, 'LATEHAR', 16),
(325, 'LATUR', 21),
(363, 'LAWNGTLAI', 24),
(190, 'LEH', 15),
(211, 'LOHARDAGA', 16),
(22, 'LOHIT', 3),
(371, 'LONGLENG', 25),
(23, 'LOWER SUBANSIRI', 3),
(559, 'LUCKNOW', 34),
(422, 'LUDHIANA', 28),
(364, 'LUNGLEI', 24),
(73, 'MADHEPURA', 5),
(74, 'MADHUBANI', 5),
(479, 'MADURAI', 31),
(505, 'MAHABUB NAGAR', 32),
(560, 'MAHARAJGANJ', 34),
(105, 'MAHASAMUND', 7),
(161, 'MAHENDRAGARH', 13),
(136, 'MAHESANA', 12),
(561, 'MAHOBA', 34),
(562, 'MAINPURI', 34),
(257, 'MALAPPURAM', 18),
(607, 'MALDA', 36),
(399, 'MALKANGIRI', 26),
(365, 'MAMMIT', 24),
(176, 'MANDI', 14),
(286, 'MANDLA', 20),
(287, 'MANDSAUR', 20),
(241, 'MANDYA', 17),
(423, 'MANSA', 28),
(47, 'MARIGAON', 4),
(563, 'MATHURA', 34),
(564, 'MAU', 34),
(400, 'MAYURBHANJ', 26),
(506, 'MEDAK', 32),
(565, 'MEERUT', 34),
(566, 'MIRZAPUR', 34),
(424, 'MOGA', 28),
(372, 'MOKOKCHUNG', 25),
(373, 'MON', 25),
(567, 'MORADABAD', 34),
(137, 'MORBI', 12),
(288, 'MORENA', 20),
(425, 'MUKTSAR', 28),
(326, 'MUMBAI', 21),
(75, 'MUNGER', 5),
(608, 'MURSHIDABAD', 36),
(568, 'MUZAFFARNAGAR', 34),
(76, 'MUZAFFARPUR', 5),
(242, 'Mysuru', 17),
(401, 'NABARANGAPUR', 26),
(609, 'NADIA', 36),
(48, 'NAGAON', 4),
(480, 'NAGAPATTINAM', 31),
(456, 'NAGAUR', 29),
(327, 'NAGPUR', 21),
(590, 'NAINITAL', 35),
(77, 'NALANDA', 5),
(49, 'NALBARI', 4),
(507, 'NALGONDA', 32),
(481, 'NAMAKKAL', 31),
(328, 'NANDED', 21),
(329, 'NANDURBAR', 21),
(106, 'NARAYANPUR', 7),
(138, 'NARMADA', 12),
(289, 'NARSINGHPUR', 20),
(330, 'NASHIK', 21),
(139, 'NAVSARI', 12),
(78, 'NAWADA', 5),
(426, 'NAWANSHAHR', 28),
(402, 'NAYAGARH', 26),
(290, 'NEEMUCH', 20),
(11, 'NELLORE', 2),
(1, 'NICOBAR', 1),
(482, 'NILGIRIS', 31),
(508, 'NIZAMABAD', 32),
(610, 'NORTH 24 PARGANAS', 36),
(2, 'NORTH AND MIDDLE ANDAMAN', 1),
(50, 'NORTH CACHAR HILLS', 4),
(115, 'NORTH DELHI', 10),
(611, 'NORTH DINAJPUR', 36),
(116, 'NORTH EAST DELHI', 10),
(122, 'NORTH GOA', 11),
(465, 'NORTH SIKKIM', 30),
(511, 'NORTH TRIPURA', 33),
(117, 'NORTH WEST DELHI', 10),
(403, 'NUAPADA', 26),
(331, 'OSMANABAD', 21),
(212, 'PAKUR', 16),
(258, 'PALAKKAD', 18),
(213, 'PALAMAU', 16),
(457, 'PALI', 29),
(140, 'PANCH MAHALS', 12),
(162, 'PANCHKULA', 13),
(163, 'PANIPAT', 13),
(291, 'PANNA', 20),
(24, 'PAPUM PARE', 3),
(332, 'PARBHANI', 21),
(141, 'PATAN', 12),
(259, 'PATHANAMTHITTA', 18),
(427, 'Pathankot', 28),
(428, 'PATIALA', 28),
(79, 'PATNA', 5),
(591, 'PAURI GARHWAL', 35),
(483, 'PERAMBALUR', 31),
(374, 'PEREN', 25),
(375, 'PHEK', 25),
(569, 'PILIBHIT', 34),
(592, 'PITHORAGARH', 35),
(410, 'PONDICHERRY', 27),
(191, 'POONCH', 15),
(142, 'PORBANDAR', 12),
(12, 'PRAKASAM', 2),
(570, 'PRATAPGARH', 34),
(484, 'PUDUKKOTTAI', 31),
(192, 'PULWAMA', 15),
(333, 'PUNE', 21),
(404, 'PURI', 26),
(80, 'PURNIA', 5),
(612, 'Purulia', 36),
(571, 'RAEBARELI', 34),
(243, 'RAICHUR', 17),
(107, 'RAIGARH', 7),
(334, 'RAIGARH(MH)', 21),
(108, 'RAIPUR', 7),
(292, 'RAISEN', 20),
(193, 'RAJAURI', 15),
(293, 'RAJGARH', 20),
(143, 'RAJKOT', 12),
(109, 'RAJNANDGAON', 7),
(458, 'RAJSAMAND', 29),
(244, 'RAMANAGAR', 17),
(485, 'RAMANATHAPURAM', 31),
(214, 'RAMGARH', 16),
(572, 'RAMPUR', 34),
(215, 'RANCHI', 16),
(294, 'RATLAM', 20),
(335, 'RATNAGIRI', 21),
(405, 'RAYAGADA', 26),
(295, 'REWA', 20),
(164, 'REWARI', 13),
(356, 'RI BHOI', 23),
(165, 'ROHTAK', 13),
(81, 'ROHTAS', 5),
(593, 'RUDRAPRAYAG', 35),
(429, 'RUPNAGAR', 28),
(144, 'SABARKANTHA', 12),
(296, 'SAGAR', 20),
(573, 'SAHARANPUR', 34),
(82, 'SAHARSA', 5),
(216, 'SAHIBGANJ', 16),
(366, 'SAIHA', 24),
(486, 'SALEM', 31),
(83, 'SAMASTIPUR', 5),
(406, 'SAMBALPUR', 26),
(336, 'SANGLI', 21),
(430, 'SANGRUR', 28),
(574, 'SANT KABIR NAGAR', 34),
(575, 'SANT RAVIDAS NAGAR', 34),
(84, 'SARAN', 5),
(337, 'SATARA', 21),
(297, 'SATNA', 20),
(459, 'SAWAI MADHOPUR', 29),
(298, 'SEHORE', 20),
(349, 'SENAPATI', 22),
(299, 'SEONI', 20),
(217, 'SERAIKELA-KHARSAWAN', 16),
(367, 'SERCHHIP', 24),
(300, 'SHAHDOL', 20),
(576, 'SHAHJAHANPUR', 34),
(301, 'SHAJAPUR', 20),
(85, 'SHEIKHPURA', 5),
(86, 'SHEOHAR', 5),
(302, 'SHEOPUR', 20),
(177, 'SHIMLA', 14),
(245, 'SHIMOGA', 17),
(303, 'SHIVPURI', 20),
(577, 'SHRAWASTI', 34),
(51, 'SIBSAGAR', 4),
(578, 'SIDDHARTHNAGAR', 34),
(304, 'SIDHI', 20),
(460, 'SIKAR', 29),
(218, 'SIMDEGA', 16),
(338, 'SINDHUDURG', 21),
(178, 'SIRMAUR', 14),
(461, 'SIROHI', 29),
(166, 'SIRSA', 13),
(87, 'SITAMARHI', 5),
(579, 'SITAPUR', 34),
(487, 'SIVAGANGA', 31),
(88, 'SIWAN', 5),
(179, 'SOLAN', 14),
(339, 'SOLAPUR', 21),
(407, 'SONAPUR', 26),
(580, 'SONBHADRA', 34),
(167, 'SONIPAT', 13),
(52, 'SONITPUR', 4),
(613, 'SOUTH 24 PARGANAS', 36),
(3, 'SOUTH ANDAMAN', 1),
(118, 'SOUTH DELHI', 10),
(614, 'SOUTH DINAJPUR', 36),
(119, 'SOUTH EAST DELHI', 10),
(357, 'SOUTH GARO HILLS', 23),
(123, 'SOUTH GOA', 11),
(466, 'SOUTH SIKKIM', 30),
(512, 'SOUTH TRIPURA', 33),
(120, 'SOUTH WEST DELHI', 10),
(13, 'SRIKAKULAM', 2),
(194, 'SRINAGAR', 15),
(581, 'SULTANPUR', 34),
(408, 'SUNDERGARH', 26),
(89, 'SUPAUL', 5),
(145, 'SURAT', 12),
(146, 'SURENDRA NAGAR', 12),
(110, 'SURGUJA', 7),
(350, 'TAMENGLONG', 22),
(431, 'TARN TARAN', 28),
(25, 'TAWANG', 3),
(594, 'TEHRI GARHWAL', 35),
(340, 'THANE', 21),
(488, 'THANJAVUR', 31),
(147, 'THE DANGS', 12),
(489, 'THENI', 31),
(260, 'THIRUVANANTHAPURAM', 18),
(351, 'THOUBAL', 22),
(261, 'THRISSUR', 18),
(305, 'TIKAMGARH', 20),
(53, 'TINSUKIA', 4),
(26, 'TIRAP', 3),
(490, 'TIRUCHIRAPPALLI', 31),
(491, 'TIRUNELVELI', 31),
(492, 'TIRUPPUR', 31),
(493, 'TIRUVALLUR', 31),
(494, 'TIRUVANNAMALAI', 31),
(495, 'TIRUVARUR', 31),
(462, 'TONK', 29),
(376, 'TUENSANG', 25),
(246, 'TUMKUR', 17),
(496, 'TUTICORIN', 31),
(463, 'UDAIPUR', 29),
(595, 'UDHAM SINGH NAGAR', 35),
(195, 'UDHAMPUR', 15),
(247, 'UDUPI', 17),
(306, 'UJJAIN', 20),
(352, 'UKHRUL', 22),
(307, 'UMARIA', 20),
(180, 'UNA', 14),
(582, 'UNNAO', 34),
(27, 'UPPER SIANG', 3),
(28, 'UPPER SUBANSIRI', 3),
(248, 'UTTARA KANNADA', 17),
(596, 'UTTARKASHI', 35),
(148, 'VADODARA', 12),
(90, 'VAISHALI', 5),
(149, 'VALSAD', 12),
(583, 'VARANASI', 34),
(497, 'VELLORE', 31),
(308, 'VIDISHA', 20),
(498, 'VILLUPURAM', 31),
(499, 'VIRUDHUNAGAR', 31),
(14, 'VISAKHAPATNAM', 2),
(15, 'VIZIANAGARAM', 2),
(509, 'WARANGAL', 32),
(341, 'WARDHA', 21),
(342, 'WASHIM', 21),
(262, 'WAYANAD', 18),
(91, 'WEST CHAMPARAN', 5),
(121, 'WEST DELHI', 10),
(358, 'WEST GARO HILLS', 23),
(16, 'WEST GODAVARI', 2),
(29, 'WEST KAMENG', 3),
(359, 'WEST KHASI HILLS', 23),
(615, 'WEST MIDNAPORE', 36),
(309, 'WEST NIMAR', 20),
(30, 'WEST SIANG', 3),
(467, 'WEST SIKKIM', 30),
(219, 'WEST SINGHBHUM', 16),
(513, 'WEST TRIPURA', 33),
(377, 'WOKHA', 25),
(168, 'YAMUNA NAGAR', 13),
(343, 'YAVATMAL', 21),
(378, 'ZUNHEBOTTO', 25);

-- --------------------------------------------------------

--
-- Table structure for table `location_regions`
--

CREATE TABLE IF NOT EXISTS `location_regions` (
  `region_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `region` varchar(30) DEFAULT NULL,
  `country_id` tinyint(3) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`region_id`),
  UNIQUE KEY `region` (`region`,`country_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `location_regions`
--

INSERT INTO `location_regions` (`region_id`, `region`, `country_id`, `status`) VALUES
(1, 'East', 77, 0),
(2, 'West', 77, 0),
(3, 'North', 77, 0),
(4, 'South', 77, 0),
(5, 'Center', 77, 0);

-- --------------------------------------------------------

--
-- Table structure for table `location_states`
--

CREATE TABLE IF NOT EXISTS `location_states` (
  `state_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` varchar(100) NOT NULL,
  `region_id` smallint(6) unsigned DEFAULT NULL,
  `country_id` tinyint(3) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-InActive, 1-Active',
  PRIMARY KEY (`state_id`),
  UNIQUE KEY `state` (`state`,`region_id`,`country_id`),
  KEY `region_id` (`region_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `location_states`
--

INSERT INTO `location_states` (`state_id`, `state`, `region_id`, `country_id`, `status`) VALUES
(1, 'ANDAMAN & NICOBAR ISLANDS', NULL, 77, 1),
(2, 'ANDHRA PRADESH', NULL, 77, 1),
(3, 'ARUNACHAL PRADESH', NULL, 77, 1),
(4, 'ASSAM', NULL, 77, 1),
(5, 'BIHAR', NULL, 77, 1),
(6, 'CHANDIGARH', NULL, 77, 1),
(7, 'CHATTISGARH', NULL, 77, 1),
(8, 'DADRA & NAGAR HAVELI', NULL, 77, 1),
(9, 'DAMAN & DIU', NULL, 77, 1),
(10, 'DELHI', NULL, 77, 1),
(11, 'GOA', NULL, 77, 1),
(12, 'GUJARAT', NULL, 77, 1),
(13, 'HARYANA', NULL, 77, 1),
(14, 'HIMACHAL PRADESH', NULL, 77, 1),
(15, 'JAMMU & KASHMIR', NULL, 77, 1),
(16, 'JHARKHAND', NULL, 77, 1),
(17, 'KARNATAKA', NULL, 77, 1),
(18, 'KERALA', NULL, 77, 1),
(19, 'LAKSHADWEEP', NULL, 77, 1),
(20, 'MADHYA PRADESH', NULL, 77, 1),
(21, 'MAHARASHTRA', NULL, 77, 1),
(22, 'MANIPUR', NULL, 77, 1),
(23, 'MEGHALAYA', NULL, 77, 1),
(24, 'MIZORAM', NULL, 77, 1),
(25, 'NAGALAND', NULL, 77, 1),
(26, 'ODISHA', NULL, 77, 1),
(27, 'PONDICHERRY', NULL, 77, 1),
(28, 'PUNJAB', NULL, 77, 1),
(29, 'RAJASTHAN', NULL, 77, 1),
(30, 'SIKKIM', NULL, 77, 1),
(31, 'TAMIL NADU', 4, 77, 1),
(32, 'TELANGANA', NULL, 77, 1),
(33, 'TRIPURA', NULL, 77, 1),
(34, 'UTTAR PRADESH', NULL, 77, 1),
(35, 'UTTARAKHAND', NULL, 77, 1),
(36, 'WEST BENGAL', NULL, 77, 1);

-- --------------------------------------------------------

--
-- Table structure for table `login_status_lookups`
--

CREATE TABLE IF NOT EXISTS `login_status_lookups` (
  `status_id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `status` varchar(100) NOT NULL,
  `status_class` varchar(50) NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `login_status_lookups`
--

INSERT INTO `login_status_lookups` (`status_id`, `status`, `status_class`) VALUES
(1, 'Active', 'label label-success'),
(2, 'Inactive', 'label label-danger');

-- --------------------------------------------------------

--
-- Table structure for table `logistics_handling_pincodes`
--

CREATE TABLE IF NOT EXISTS `logistics_handling_pincodes` (
  `lhp_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `logistic_id` smallint(6) unsigned NOT NULL,
  `pincode_id` bigint(20) unsigned NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` timestamp NOT NULL,
  `is_deleted` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`lhp_id`),
  KEY `logistic_id` (`logistic_id`),
  KEY `pincode_id` (`pincode_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `logistics_handling_pincodes`
--

INSERT INTO `logistics_handling_pincodes` (`lhp_id`, `logistic_id`, `pincode_id`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 1, 1, '2017-07-10 14:51:43', '2017-07-09 22:21:43', 0),
(2, 1, 1, '2017-07-11 01:30:51', '0000-00-00 00:00:00', 0),
(3, 1, 14670, '2017-07-28 01:02:33', '0000-00-00 00:00:00', 0),
(4, 1, 14671, '2017-07-28 01:02:33', '0000-00-00 00:00:00', 0),
(5, 1, 14672, '2017-07-28 01:02:33', '0000-00-00 00:00:00', 0),
(6, 1, 14673, '2017-07-28 01:02:33', '0000-00-00 00:00:00', 0),
(7, 1, 14674, '2017-07-28 01:02:33', '0000-00-00 00:00:00', 0),
(8, 1, 14675, '2017-07-28 01:02:33', '0000-00-00 00:00:00', 0),
(9, 1, 14676, '2017-07-28 01:02:33', '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `logistics_product_avaliablity`
--

CREATE TABLE IF NOT EXISTS `logistics_product_avaliablity` (
  `lpa_id` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `lhp_id` bigint(20) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`lpa_id`),
  KEY `lhp_id` (`lhp_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `logistics_product_avaliablity`
--

INSERT INTO `logistics_product_avaliablity` (`lpa_id`, `lhp_id`, `product_id`, `is_deleted`) VALUES
(1, 1, 1, 0),
(2, 1, 2, 0),
(3, 1, 3, 0),
(4, 1, 4, 0),
(5, 1, 5, 0),
(6, 1, 6, 0),
(7, 1, 7, 0),
(8, 1, 8, 0),
(9, 1, 9, 0),
(10, 1, 11, 0),
(11, 1, 16, 0),
(12, 1, 17, 0),
(13, 1, 18, 0),
(14, 1, 19, 0);

-- --------------------------------------------------------

--
-- Table structure for table `meta_info`
--

CREATE TABLE IF NOT EXISTS `meta_info` (
  `meta_info_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_type_id` tinyint(2) unsigned NOT NULL COMMENT 'product-1,supplier-2',
  `relative_post_id` bigint(20) unsigned NOT NULL,
  `description` text,
  `meta_keys` text,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`meta_info_id`),
  KEY `post_type_id` (`post_type_id`),
  KEY `relative_post_id` (`relative_post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `meta_info`
--

INSERT INTO `meta_info` (`meta_info_id`, `post_type_id`, `relative_post_id`, `description`, `meta_keys`, `created_on`, `updated_on`) VALUES
(1, 1, 2, 'Samsung', 'apple', '2016-07-28 02:08:09', '2016-07-28 00:15:54'),
(2, 1, 1, 'apple', 'apple', '2016-07-28 05:16:18', '2016-07-28 00:16:18'),
(3, 3, 1, 'As enabling as smartphones are, they can be confusing to use too. Many people still prefer the homely comfort of feature phones. If you are one of those people then switch on to the Nokia 130. Loaded with features such as an FM radio, a bright flashlight, a 4.54 cm LCD transmissive screen, and a sturdy body that keeps its color even when scratched, this dual-sim feature phone is always ready for action.\r\nMusic makes work more enjoyable\r\nFeaturing up to 46 hours of playback time and an expandable storage capacity of up to 32GB, this phone lets you play your favourite tracks all day long. So put on your headphones and keep enjoying your favourite music.\r\nWatch more video\r\nThe 1020mAh battery of this phone offers you up to 16 hours of video playback on a single charge. So add as many videos and clips as you want to your mobile’s SD card and enjoy your favourite videos on-the-go.\r\nShare as much as you want\r\nSharing your files with the Nokia 130 is fast and fun. Whether you want to use the Bluetooth or the microUSB cable, this phone lets you share videos, contacts and more with ease.', 'nokia-130-ds', '2016-08-23 01:38:26', '2016-10-25 20:12:02'),
(4, 1, 3, 'GSAdadfgad', 'asdfas', '2016-10-23 23:01:49', '2016-11-03 22:36:25'),
(5, 1, 4, '', '', '2016-10-22 01:16:44', '2016-10-21 20:16:44'),
(6, 1, 5, '', '', '2016-10-22 01:18:17', '2016-10-21 20:18:17'),
(7, 1, 6, '', '', '2016-10-22 01:19:15', '2016-10-21 20:19:15'),
(9, 3, 9, '\r\nWe''ll facilitate the installation and demo through authorized service engineer at your convenience. The installation will be done within 2 to 5 Business days of delivery of the TV. The service engineer will install your new TV, either on wall mount or on table top. Installation and demo are provided free of cost. The engineer will also help you understand your new TV''s features. The process generally covers: Wall-mounted or table-top installation, as requested (Wall mounted mode is recommended for better sound experience), Physical check of all ports, including power and USB ports. Accessories also checked, Demonstration of features and settings, Quick run-through on how to operate the TV.', 'TV, SONY, LED', '2016-10-25 01:06:32', '2016-10-24 20:06:32'),
(10, 3, 10, '', '', '2016-10-25 06:28:29', '2016-10-25 01:28:29'),
(11, 3, 18, '', '', '2016-11-13 23:32:42', '2017-06-28 22:23:03'),
(12, 3, 19, 'pto1', 'pro1', '2017-02-21 01:11:09', '2017-05-17 01:44:39'),
(13, 3, 11, 'OnePlus 3T', 'OnePlus 3T', '2017-05-22 23:01:56', '2017-05-22 18:01:56'),
(14, 3, 16, 'OnePlus 3T', 'OnePlus 3T', '2017-05-30 02:31:08', '2017-05-29 21:31:08'),
(15, 3, 17, 'OnePlus 3T', 'OnePlus 3T', '2017-05-30 02:38:43', '2017-05-29 21:38:43'),
(16, 1, 18, '', '', NULL, '2017-07-23 20:43:07'),
(17, 1, 19, 'pto1', 'pro1', NULL, '2017-08-06 23:01:05'),
(18, 1, 20, 'Meta Description:', 'Meta Keys:', '2018-03-07 21:46:46', '2018-03-07 17:46:46'),
(19, 1, 22, 'meta desc', 'meta keys', NULL, '2018-07-05 11:28:48'),
(20, 1, 24, 'meta desc', 'meta keys', NULL, '2018-07-05 11:52:22'),
(21, 1, 25, 'meta desc', 'meta keys', NULL, '2018-07-05 11:55:45'),
(22, 1, 26, 'meta desc', 'meta keys', NULL, '2018-07-05 11:56:22'),
(23, 1, 27, 'meta desc', 'meta keys', NULL, '2018-07-05 12:01:49'),
(24, 1, 28, 'meta desc', 'meta keys', NULL, '2018-07-05 12:02:42'),
(25, 1, 29, 'meta desc', 'meta keys', NULL, '2018-07-05 12:03:18'),
(26, 1, 30, 'meta desc', 'meta keys', NULL, '2018-07-05 12:06:06'),
(27, 1, 31, 'description', 'meta keys', NULL, '2018-07-06 11:30:34'),
(30, 3, 33, 'description123', 'key1,key2,key3', '2018-07-11 05:39:44', '2018-07-11 05:40:43');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email_id` varchar(70) DEFAULT NULL,
  `un_subscribe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email_id`, `un_subscribe`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 'parthiban.ejugiter@gmail.com', 0, NULL, '2015-10-26 22:32:23', 0),
(2, 'sriram.ejugiter@gmail.com', 0, NULL, '2015-10-26 22:32:23', 0),
(3, 'adsf@g.com', 0, NULL, '2017-04-04 21:53:57', 0),
(4, 'kparthiban@ymail.com', 0, NULL, '2017-08-03 19:40:32', 0),
(5, '', 0, NULL, '2017-08-06 19:28:35', 0),
(6, '', 0, NULL, '2017-08-06 19:28:39', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_code` varchar(30) NOT NULL DEFAULT '0',
  `account_id` bigint(20) unsigned NOT NULL,
  `qty` smallint(6) unsigned NOT NULL DEFAULT '0',
  `currency_id` tinyint(3) unsigned NOT NULL,
  `sub_total` double unsigned DEFAULT '0',
  `tax` double unsigned NOT NULL DEFAULT '0',
  `shipping_charges` double unsigned NOT NULL DEFAULT '0',
  `net_pay` double unsigned NOT NULL DEFAULT '0',
  `payment_type_id` tinyint(3) unsigned DEFAULT NULL,
  `payment_status_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order_status_id` tinyint(2) unsigned NOT NULL,
  `approval_status_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`order_id`),
  KEY `approval_status_id` (`approval_status_id`),
  KEY `order_status_id` (`order_status_id`),
  KEY `account_id` (`account_id`),
  KEY `currency_id` (`currency_id`),
  KEY `payment_type_id` (`payment_type_id`),
  KEY `payment_status_id` (`payment_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_code`, `account_id`, `qty`, `currency_id`, `sub_total`, `tax`, `shipping_charges`, `net_pay`, `payment_type_id`, `payment_status_id`, `order_status_id`, `approval_status_id`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 'OD1', 2, 5, 2, 0, 0, 0, 3960, 18, 0, 3, 1, '2017-04-21 07:17:44', '2018-07-13 07:02:48', 0),
(2, 'OD2', 2, 4, 2, 0, 0, 0, 1430, 18, 0, 1, 1, '2017-04-22 00:35:57', '2018-07-13 12:20:13', 0),
(3, 'OD3', 2, 4, 2, 0, 0, 0, 2030, 18, 0, 1, 1, '2017-04-25 00:31:20', '2018-07-13 12:20:16', 0),
(4, 'OD4', 2, 2, 2, 0, 0, 0, 875, 18, 0, 1, 1, '2017-04-26 05:32:20', '2018-07-13 12:20:18', 0),
(6, 'OD6', 2, 4, 2, 0, 0, 0, 2000, 18, 0, 1, 1, '2017-05-04 01:40:54', '2018-07-13 12:20:21', 0),
(17, 'OD17', 4, 1, 2, 0, 0, 0, 1450, 18, 0, 1, 1, '2017-05-04 04:36:28', '2018-07-13 12:20:23', 0),
(18, 'OD18', 2, 2, 2, 0, 0, 0, 1635, 18, 0, 1, 1, '2017-07-11 06:13:46', '2018-07-13 12:20:26', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_approval_status`
--

CREATE TABLE IF NOT EXISTS `order_approval_status` (
  `approval_status_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(200) NOT NULL,
  `status_key` varchar(200) NOT NULL,
  PRIMARY KEY (`approval_status_id`),
  UNIQUE KEY `partner_status` (`status`),
  UNIQUE KEY `status_key` (`status_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `order_approval_status`
--

INSERT INTO `order_approval_status` (`approval_status_id`, `status`, `status_key`) VALUES
(1, 'Partner Holding', 'PARTNER_HOLDING'),
(2, 'Partner Confirmed', 'PARTNER_CONFIRMED'),
(3, 'Partner Cancelled', 'PARTNER_CANCELLED'),
(4, 'Admin Holding', 'ADMIN_HOLDING'),
(5, 'Admin Confirmed', 'ADMIN_CONFIRMED'),
(6, 'Admin Cancelled', 'ADMIN_CANCELLED'),
(7, 'Supplier Holding', 'SUPPLIER_HOLDING'),
(8, 'Supplier Confirmed', 'SUPPLIER_CONFIRMED'),
(9, 'Supplier Cancelled', 'SUPPLIER_CANCELLED'),
(10, 'Pending', 'PENDING');

-- --------------------------------------------------------

--
-- Table structure for table `order_approval_status_notification`
--

CREATE TABLE IF NOT EXISTS `order_approval_status_notification` (
  `oasn_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `oass_id` smallint(6) unsigned NOT NULL,
  `account_type_id` tinyint(2) unsigned NOT NULL,
  `sms` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `email` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `notification` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  PRIMARY KEY (`oasn_id`),
  UNIQUE KEY `oss_id` (`oass_id`,`account_type_id`),
  KEY `oss_id_2` (`oass_id`),
  KEY `account_type_id` (`account_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;

--
-- Dumping data for table `order_approval_status_notification`
--

INSERT INTO `order_approval_status_notification` (`oasn_id`, `oass_id`, `account_type_id`, `sms`, `email`, `notification`) VALUES
(1, 1, 1, 0, 0, 0),
(2, 1, 2, 0, 0, 0),
(3, 1, 3, 0, 0, 0),
(4, 1, 4, 0, 0, 0),
(5, 2, 1, 0, 0, 0),
(9, 2, 2, 0, 0, 0),
(10, 2, 3, 0, 0, 0),
(11, 2, 4, 0, 0, 0),
(12, 3, 1, 0, 0, 0),
(13, 3, 2, 0, 0, 0),
(14, 3, 3, 0, 0, 0),
(15, 3, 4, 0, 0, 0),
(16, 4, 1, 0, 0, 0),
(17, 4, 2, 0, 0, 0),
(18, 4, 3, 0, 0, 0),
(19, 4, 4, 0, 0, 0),
(29, 5, 1, 0, 0, 0),
(30, 5, 2, 0, 0, 0),
(31, 5, 3, 0, 0, 0),
(32, 5, 4, 0, 0, 0),
(33, 6, 1, 0, 0, 0),
(34, 6, 2, 0, 0, 0),
(35, 6, 3, 0, 0, 0),
(36, 6, 4, 0, 0, 0),
(37, 7, 1, 0, 0, 0),
(38, 7, 2, 0, 0, 0),
(39, 7, 3, 0, 0, 0),
(40, 7, 4, 0, 0, 0),
(41, 8, 1, 0, 0, 0),
(42, 8, 2, 0, 0, 0),
(43, 8, 3, 0, 0, 0),
(44, 8, 4, 0, 0, 0),
(45, 9, 1, 0, 0, 0),
(46, 9, 2, 0, 0, 0),
(47, 9, 3, 0, 0, 0),
(48, 9, 4, 0, 0, 0),
(49, 10, 1, 0, 0, 0),
(50, 10, 2, 0, 0, 0),
(51, 10, 3, 0, 0, 0),
(52, 10, 4, 0, 0, 0),
(53, 11, 1, 0, 0, 0),
(54, 11, 2, 0, 0, 0),
(55, 11, 3, 0, 0, 0),
(56, 11, 4, 0, 0, 0),
(57, 12, 1, 0, 0, 0),
(58, 12, 2, 0, 0, 0),
(59, 12, 3, 0, 0, 0),
(60, 12, 4, 0, 0, 0),
(61, 13, 1, 0, 0, 0),
(62, 13, 2, 0, 0, 0),
(63, 13, 3, 0, 0, 0),
(64, 13, 4, 0, 0, 0),
(65, 14, 1, 0, 0, 0),
(66, 14, 2, 0, 0, 0),
(67, 14, 3, 0, 0, 0),
(68, 14, 4, 0, 0, 0),
(69, 15, 1, 0, 0, 0),
(70, 15, 2, 0, 0, 0),
(71, 15, 3, 0, 0, 0),
(72, 15, 4, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_approval_status_settings`
--

CREATE TABLE IF NOT EXISTS `order_approval_status_settings` (
  `oass_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `from_approval_status_id` tinyint(1) unsigned NOT NULL,
  `to_approval_status_id` tinyint(1) unsigned NOT NULL,
  `account_type_id` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `update_order_status_id_to` tinyint(2) unsigned DEFAULT NULL,
  `is_comment_required` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  PRIMARY KEY (`oass_id`),
  UNIQUE KEY `from_approval_status_id` (`from_approval_status_id`,`to_approval_status_id`,`account_type_id`),
  KEY `account_type_id` (`account_type_id`),
  KEY `to_approval_status_id` (`to_approval_status_id`),
  KEY `update_order_status_id_to` (`update_order_status_id_to`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `order_approval_status_settings`
--

INSERT INTO `order_approval_status_settings` (`oass_id`, `from_approval_status_id`, `to_approval_status_id`, `account_type_id`, `update_order_status_id_to`, `is_comment_required`) VALUES
(1, 0, 1, 4, NULL, 1),
(2, 0, 2, 4, NULL, 0),
(3, 0, 3, 4, 7, 1),
(4, 1, 2, 4, NULL, 0),
(5, 1, 3, 4, 7, 1),
(6, 2, 4, 1, NULL, 1),
(7, 2, 5, 1, NULL, 0),
(8, 2, 6, 1, 7, 1),
(9, 4, 5, 1, NULL, 0),
(10, 4, 6, 1, 7, 1),
(11, 5, 7, 2, NULL, 1),
(12, 5, 8, 2, 1, 0),
(13, 5, 9, 2, 7, 1),
(14, 7, 8, 2, 1, 0),
(15, 7, 9, 2, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE IF NOT EXISTS `order_items` (
  `order_item_id` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `order_item_code` varchar(65) NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `sub_order_id` bigint(20) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `supplier_product_id` bigint(15) unsigned NOT NULL,
  `supplier_id` int(10) unsigned NOT NULL,
  `specification` text NOT NULL COMMENT 'combination key:value',
  `currency_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `mrp_price` double unsigned NOT NULL DEFAULT '0',
  `discount` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `price` double unsigned NOT NULL DEFAULT '0' COMMENT 'Tax Included',
  `qty` smallint(6) unsigned NOT NULL DEFAULT '0',
  `sub_total` double unsigned NOT NULL DEFAULT '0' COMMENT 'Tax Included',
  `mode_id` tinyint(2) unsigned DEFAULT NULL,
  `shipping_charge` double unsigned NOT NULL DEFAULT '0',
  `net_pay` double unsigned NOT NULL DEFAULT '0' COMMENT 'Tax Included',
  `tax` double unsigned NOT NULL DEFAULT '0',
  `expected_delivery_date` date NOT NULL,
  `delivery_days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delivered_in` date DEFAULT NULL,
  `replacement_due_days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `replacement_due_date` datetime NOT NULL,
  `replacement_time` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT 'nth Time Shippment',
  `approval_status_id` tinyint(1) unsigned DEFAULT NULL,
  `order_item_status_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shippment_info` text,
  `discount_info` text,
  `commission_info` text,
  `tax_info` text,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`order_item_id`),
  UNIQUE KEY `order_item_code` (`order_item_code`),
  KEY `order_id` (`order_id`),
  KEY `sub_order_id` (`sub_order_id`),
  KEY `product_id` (`product_id`),
  KEY `supplier_product_id` (`supplier_product_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `currency_id` (`currency_id`),
  KEY `mode_id` (`mode_id`),
  KEY `approval_status_id` (`approval_status_id`),
  KEY `order_item_status_id` (`order_item_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_item_code`, `order_id`, `sub_order_id`, `product_id`, `supplier_product_id`, `supplier_id`, `specification`, `currency_id`, `mrp_price`, `discount`, `price`, `qty`, `sub_total`, `mode_id`, `shipping_charge`, `net_pay`, `tax`, `expected_delivery_date`, `delivery_days`, `delivered_in`, `replacement_due_days`, `replacement_due_date`, `replacement_time`, `approval_status_id`, `order_item_status_id`, `shippment_info`, `discount_info`, `commission_info`, `tax_info`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 'OD1S1I1', 1, 1, 3, 3, 1, '{\\"pro_attr\\":\\"BATTERY CAPACITY: 100 rpm, OPERATING SYSTEM: Android\\"}', 1, 1000, 10, 900, 2, 1800, 0, 0, 1800, 0, '2017-04-21', 0, NULL, 30, '2017-05-21 07:17:44', 0, 2, 3, '[]', '[]', NULL, NULL, '2017-04-21 07:17:44', '2018-07-13 07:02:48', 157, 0),
(2, 'OD1S1I2', 1, 1, 4, 4, 1, '{\\"pro_attr\\":\\"\\"}', 1, 700, 10, 630, 2, 1260, 0, 0, 1260, 0, '2017-04-21', 0, NULL, 30, '2017-05-21 07:17:44', 0, 2, 3, '[]', '[]', NULL, NULL, '2017-04-21 07:17:44', '2018-07-13 07:02:48', 157, 0),
(3, 'OD1S1I3', 1, 1, 3, 3, 1, '{\\"pro_attr\\":\\"BATTERY CAPACITY: 100 rpm, OPERATING SYSTEM: Android\\"}', 1, 1000, 10, 900, 1, 900, 0, 0, 900, 0, '2017-04-21', 0, NULL, 30, '2017-05-21 07:17:44', 0, 2, 3, '[]', '[]', NULL, NULL, '2017-04-21 07:17:44', '2018-07-13 07:02:48', 157, 0),
(4, 'OD2S2I4', 2, 2, 7, 8, 1, '{\\"pro_attr\\":\\"\\"}', 1, 500, 5, 475, 2, 950, 0, 0, 950, 0, '2017-04-22', 0, NULL, 30, '2017-05-22 00:35:57', 0, 2, 1, '[]', '[]', '{"mrp_price":500,"supplier_price":475,"supplier_discount_per":5,"supplier_sold_price":475,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":475,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":950,"site_commission_sub_total":0,"supplier_price_sub_total":950,"partner_commission_sub_total":0,"admin_discount_per":0}', NULL, '2017-04-22 00:35:57', '2017-06-19 07:54:28', 3, 0),
(5, 'OD2S2I5', 2, 2, 6, 6, 1, '{\\"pro_attr\\":\\"\\"}', 1, 300, 20, 240, 2, 480, 0, 0, 480, 0, '2017-04-22', 0, NULL, 30, '2017-05-22 00:35:57', 0, 2, 1, '[]', '[]', '{"mrp_price":300,"supplier_price":240,"supplier_discount_per":20,"supplier_sold_price":240,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":240,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":480,"site_commission_sub_total":0,"supplier_price_sub_total":480,"partner_commission_sub_total":0,"admin_discount_per":0}', NULL, '2017-04-22 00:35:57', '2017-06-19 07:54:28', 3, 0),
(6, 'OD3S3I6', 3, 3, 1, 1, 1, '{\\"pro_attr\\":\\"RAM: 2 mm, RAM: 1 mm, RAM: 512 cm, OPERATING SYSTEM: 256 cm, OPERATING SYSTEM: 4G VOLTE\\"}', 1, 500, 20, 400, 1, 400, 0, 0, 400, 0, '2017-04-25', 0, NULL, 30, '2017-05-25 00:31:19', 0, 5, 2, '[]', '[]', '{"mrp_price":500,"supplier_price":400,"supplier_discount_per":20,"supplier_sold_price":400,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":400,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":400,"site_commission_sub_total":0,"supplier_price_sub_total":400,"partner_commission_sub_total":0,"admin_discount_per":0}', NULL, '2017-04-25 00:31:20', '2017-04-25 14:25:36', 3, 0),
(7, 'OD3S3I7', 3, 3, 5, 5, 1, '{\\"pro_attr\\":\\"\\"}', 1, 800, 5, 760, 1, 760, 0, 0, 760, 0, '2017-04-25', 0, NULL, 30, '2017-05-25 00:31:19', 0, 5, 2, '[]', '[]', '{"mrp_price":800,"supplier_price":760,"supplier_discount_per":5,"supplier_sold_price":760,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":760,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":760,"site_commission_sub_total":0,"supplier_price_sub_total":760,"partner_commission_sub_total":0,"admin_discount_per":0}', NULL, '2017-04-25 00:31:20', '2017-04-25 14:25:36', 3, 0),
(8, 'OD3S3I8', 3, 3, 6, 6, 1, '{\\"pro_attr\\":\\"\\"}', 1, 300, 20, 240, 1, 240, 0, 0, 240, 0, '2017-04-25', 0, NULL, 30, '2017-05-25 00:31:19', 0, 5, 2, '[]', '[]', '{"mrp_price":300,"supplier_price":240,"supplier_discount_per":20,"supplier_sold_price":240,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":240,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":240,"site_commission_sub_total":0,"supplier_price_sub_total":240,"partner_commission_sub_total":0,"admin_discount_per":0}', NULL, '2017-04-25 00:31:20', '2017-04-25 14:25:36', 3, 0),
(9, 'OD3S3I9', 3, 3, 4, 4, 1, '{\\"pro_attr\\":\\"\\"}', 1, 700, 10, 630, 1, 630, 0, 0, 630, 0, '2017-04-25', 0, NULL, 30, '2017-05-25 00:31:19', 0, 5, 2, '[]', '[]', '{"mrp_price":700,"supplier_price":630,"supplier_discount_per":10,"supplier_sold_price":630,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":630,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":630,"site_commission_sub_total":0,"supplier_price_sub_total":630,"partner_commission_sub_total":0,"admin_discount_per":0}', NULL, '2017-04-25 00:31:20', '2017-04-25 14:25:36', 3, 0),
(10, 'OD4S4I10', 4, 4, 7, 8, 1, '{\\"pro_attr\\":\\"\\"}', 1, 500, 5, 475, 1, 475, 0, 0, 475, 0, '2017-04-26', 0, NULL, 30, '2017-05-26 07:07:53', 0, 5, 8, '[]', '[]', '{"mrp_price":500,"supplier_price":475,"supplier_discount_per":5,"supplier_sold_price":475,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":475,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":475,"site_commission_sub_total":0,"supplier_price_sub_total":475,"partner_margin_sub_total":0,"supplier_tax_total":0,"partner_tax_total":0,"partner_commission_sub_total":0,"admin_discount_per":0}', '{"taxes":[],"total_tax_per":0,"total_tax_amount":0,"tax_per":0}', '2017-04-26 05:32:20', '2017-04-25 14:37:53', 1, 0),
(11, 'OD4S4I11', 4, 4, 1, 1, 1, '{\\"pro_attr\\":\\"RAM: 2 mm, RAM: 1 mm, RAM: 512 cm, OPERATING SYSTEM: 256 cm, OPERATING SYSTEM: 4G VOLTE\\"}', 1, 500, 20, 400, 1, 400, 0, 0, 400, 0, '2017-04-26', 0, NULL, 30, '2017-05-26 07:07:53', 0, 5, 8, '[]', '[]', '{"mrp_price":500,"supplier_price":400,"supplier_discount_per":20,"supplier_sold_price":400,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":400,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":400,"site_commission_sub_total":0,"supplier_price_sub_total":400,"partner_margin_sub_total":0,"supplier_tax_total":0,"partner_tax_total":0,"partner_commission_sub_total":0,"admin_discount_per":0}', '{"taxes":[],"total_tax_per":0,"total_tax_amount":0,"tax_per":0}', '2017-04-26 05:32:20', '2017-04-25 14:37:53', 1, 0),
(13, 'OD6S6I13', 6, 6, 1, 1, 1, '{\\"pro_attr\\":\\"RAM: 2 mm, RAM: 1 mm, RAM: 512 cm, OPERATING SYSTEM: 256 cm, AVAILABILITY: 256 cm, OPERATING SYSTEM: 4G VOLTE, AVAILABILITY: Exclude Out of Stock, AVAILABILITY: Include Out of Stock\\"}', 1, 500, 20, 400, 4, 1600, 1, 400, 2000, 196.49, '2017-05-04', 7, NULL, 30, '2017-06-03 01:40:54', 0, 5, 1, '[]', '[]', '{"mrp_price":500,"supplier_price":400,"supplier_discount_per":20,"supplier_sold_price":400,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":400,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":1600,"site_commission_sub_total":0,"supplier_price_sub_total":1600,"partner_margin_sub_total":0,"supplier_tax_total":196.49,"partner_tax_total":0,"partner_commission_sub_total":0}', '{"total_tax_per":14,"total_tax_amount":0,"taxes":[{"tax":"Indian Sales Product Tax","value_type":1,"tax_value":14,"currency_id":1}],"tax_per":14}', '2017-05-04 01:40:54', '2017-06-13 11:52:12', 3, 0),
(14, 'OD17S17I14', 17, 17, 7, 8, 1, '{\\"pro_attr\\":\\"\\"}', 1, 500, 5, 475, 2, 950, 1, 0, 950, 116.67, '2017-05-04', 7, NULL, 30, '2017-06-03 04:36:28', 0, 5, 0, '[]', '[]', '{"mrp_price":500,"supplier_price":475,"supplier_discount_per":5,"supplier_sold_price":475,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":475,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":950,"site_commission_sub_total":0,"shipping_fee":0,"supplier_price_sub_total":950,"collection_fee":0,"fixed_fee":0,"partner_margin_sub_total":0,"supplier_tax_total":116.67,"partner_tax_total":0,"partner_commission_sub_total":0}', '{"total_tax_per":14,"total_tax_amount":0,"taxes":[{"tax":"Indian Sales Product Tax","value_type":1,"tax_value":14,"currency_id":1}],"tax_per":14}', '2017-05-04 04:36:28', '2018-07-13 10:59:54', 2, 0),
(15, 'OD17S17I15', 17, 17, 1, 1, 1, '{\\"pro_attr\\":\\"RAM: 2 mm, RAM: 1 mm, RAM: 512 cm, OPERATING SYSTEM: 256 cm, AVAILABILITY: 256 cm, OPERATING SYSTEM: 4G VOLTE, AVAILABILITY: Exclude Out of Stock, AVAILABILITY: Include Out of Stock\\"}', 1, 500, 20, 400, 1, 400, 1, 100, 500, 49.12, '2017-05-04', 7, NULL, 30, '2017-06-03 04:36:28', 0, 5, 0, '[]', '[]', '{"mrp_price":500,"supplier_price":400,"supplier_discount_per":20,"supplier_sold_price":400,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":400,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":400,"site_commission_sub_total":0,"shipping_fee":0,"supplier_price_sub_total":400,"collection_fee":8,"fixed_fee":1950,"partner_margin_sub_total":0,"supplier_tax_total":49.12,"partner_tax_total":0,"partner_commission_sub_total":0}', '{"total_tax_per":14,"total_tax_amount":0,"taxes":[{"tax":"Indian Sales Product Tax","value_type":1,"tax_value":14,"currency_id":1}],"tax_per":14}', '2017-05-04 04:36:28', '2018-07-13 10:59:48', 3, 0),
(16, 'OD18S18I16', 18, 18, 8, 8, 1, '{\\"pro_attr\\":\\"\\"}', 1, 5000, 94, 285, 1, 285, 1, 0, 285, 35, '2017-07-11', 0, NULL, 30, '2017-08-10 06:13:46', 0, 2, 0, '[]', '[]', '{"mrp_price":5000,"supplier_price":285,"supplier_discount_per":94,"supplier_sold_price":285,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":285,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":285,"site_commission_sub_total":0,"shipping_fee":0,"supplier_price_sub_total":285,"collection_fee":5.7,"fixed_fee":1950,"partner_margin_sub_total":0,"supplier_tax_total":35,"partner_tax_total":0,"partner_commission_sub_total":0}', '{"total_tax_per":14,"total_tax_amount":0,"taxes":[{"tax":"Indian Sales Product Tax","value_type":1,"tax_value":14,"currency_id":1}],"tax_per":14}', '2017-07-11 06:13:47', '2018-07-13 11:35:06', 157, 0),
(17, 'OD18S18I17', 18, 18, 31, 1, 1, '{\\"pro_attr\\":\\"RAM: 512 cm, RAM: 2 mm, RAM: 1 mm, OPERATING SYSTEM: 256 cm, OPERATING SYSTEM: 4G VOLTE, AVAILABILITY: 256 cm, AVAILABILITY: Exclude Out of Stock, AVAILABILITY: Include Out of Stock\\"}', 1, 1000, 0, 1000, 1, 1000, 1, 350, 1350, 122.81, '2017-07-11', 5, NULL, 30, '2017-08-10 06:13:46', 0, 2, 0, '[]', '[]', '{"mrp_price":1000,"supplier_price":1000,"supplier_discount_per":0,"supplier_sold_price":1000,"site_commission_unit":null,"site_commission_value":0,"site_commission_amount":0,"site_margin_price":0,"site_discount_per":0,"site_sold_price":1000,"partner_margin_price":0,"partner_sold_price":0,"partner_commission_unit":null,"partner_commission_value":0,"partner_commission_amount":0,"price_sub_total":1000,"site_commission_sub_total":0,"shipping_fee":0,"supplier_price_sub_total":1000,"collection_fee":20,"fixed_fee":1950,"partner_margin_sub_total":0,"supplier_tax_total":122.81,"partner_tax_total":0,"partner_commission_sub_total":0}', '{"total_tax_per":14,"total_tax_amount":0,"taxes":[{"tax":"Indian Sales Product Tax","value_type":1,"tax_value":14,"currency_id":1}],"tax_per":14}', '2017-07-11 06:13:47', '2018-07-13 11:35:09', 157, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_return_request_mst`
--

CREATE TABLE IF NOT EXISTS `order_return_request_mst` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `return_enquiry_code` varchar(20) DEFAULT NULL,
  `request_type_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `account_id` bigint(20) unsigned NOT NULL,
  `supplier_product_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `order_item_id` text NOT NULL,
  `order_id` bigint(15) unsigned NOT NULL,
  `payout_type_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `comments` text NOT NULL,
  `return_type_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `contact` varchar(60) NOT NULL,
  `address_id` int(11) unsigned DEFAULT '0',
  `contact_no` varchar(15) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`,`supplier_product_id`,`payout_type_id`,`contact`,`is_deleted`),
  KEY `payout_type_id` (`payout_type_id`),
  KEY `address_id` (`address_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `order_return_request_mst`
--

INSERT INTO `order_return_request_mst` (`id`, `return_enquiry_code`, `request_type_id`, `account_id`, `supplier_product_id`, `order_item_id`, `order_id`, `payout_type_id`, `comments`, `return_type_id`, `contact`, `address_id`, `contact_no`, `status`, `created_on`, `updated_by`, `updated_on`, `is_deleted`) VALUES
(1, NULL, 11, 3, 1, '1', 0, 0, 'sds', 2, '', NULL, '', 1, '2016-11-30 00:00:00', 0, '2016-11-17 03:15:00', 0),
(2, NULL, 11, 3, 1, '1', 0, 0, 'sds', 2, '', NULL, '', 1, '2016-11-30 00:00:00', 0, '2016-11-17 03:15:00', 0),
(3, NULL, 9, 0, 1, '1', 2, 0, 'sdsds', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:19:10', 0),
(4, NULL, 9, 0, 1, '1', 2, 0, 'sdsds', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:20:53', 0),
(5, NULL, 9, 0, 1, '1', 2, 0, 'sdsds', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:21:20', 0),
(6, NULL, 9, 0, 1, '1', 2, 0, 'sdsds', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:21:32', 0),
(7, '201611227', 9, 0, 1, '1', 2, 0, 'sdsdsd', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:27:41', 0),
(8, NULL, 8, 3, 1, '1', 2, 0, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:32:23', 0),
(9, NULL, 8, 3, 1, '1', 2, 0, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:32:30', 0),
(10, NULL, 8, 3, 1, '1', 2, 0, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:33:08', 0),
(11, NULL, 8, 3, 1, '1', 2, 0, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:34:37', 0),
(12, NULL, 8, 3, 1, '1', 2, 0, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:37:43', 0),
(13, NULL, 8, 3, 1, '1', 2, 0, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 22:38:15', 0),
(14, '2016112214', 8, 3, 1, '1', 2, 14, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:00:17', 0),
(15, '2016112215', 8, 3, 1, '1', 2, 15, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:00:47', 0),
(16, '2016112216', 8, 3, 1, '1', 2, 16, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:03:03', 0),
(17, '2016112217', 8, 3, 1, '1', 2, 17, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:03:25', 0),
(18, '2016112218', 8, 3, 1, '1', 2, 18, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:03:56', 0),
(19, '2016112219', 8, 3, 1, '1', 2, 19, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:08:02', 0),
(20, '2016112220', 8, 3, 1, '1', 2, 20, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:08:27', 0),
(21, '2016112221', 8, 3, 1, '1', 2, 21, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:09:34', 0),
(22, '2016112222', 8, 3, 1, '1', 2, 22, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:10:06', 0),
(23, '2016112223', 8, 3, 1, '1', 2, 23, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:11:42', 0),
(24, '2016112224', 8, 3, 1, '1', 2, 24, '', 0, ' Parthiban K', 0, '', 0, NULL, 0, '2016-11-21 23:11:57', 0),
(25, '2016112325', 8, 3, 1, '1', 2, 25, 'sdsds', 0, ' Parthiban K', 0, '1234567890', 0, NULL, 0, '2016-11-22 21:16:23', 0),
(26, NULL, 8, 3, 1, '1', 2, 0, '', 0, ' Parthiban K', 0, '1234567890', 0, NULL, 0, '2016-11-22 21:25:29', 0),
(27, NULL, 8, 3, 1, '1', 2, 0, '', 0, ' Parthiban K', 0, '1234567890', 0, NULL, 0, '2016-11-22 21:28:21', 0),
(28, '2016112328', 8, 3, 1, '1', 2, 27, '', 0, ' Parthiban K', 0, '1234567890', 0, NULL, 0, '2016-11-22 21:30:12', 0),
(29, '2016112329', 8, 3, 1, '1', 2, 28, '', 0, ' Parthiban K', 0, '1234567890', 0, NULL, 0, '2016-11-22 22:37:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_sales_commissioin_payments`
--

CREATE TABLE IF NOT EXISTS `order_sales_commissioin_payments` (
  `osc_id` bigint(30) unsigned NOT NULL,
  `supplier_payment_status_id` tinyint(1) unsigned DEFAULT NULL,
  `supplier_status_updated_on` datetime DEFAULT NULL,
  UNIQUE KEY `osc_id` (`osc_id`),
  KEY `supplier_payment_status_id` (`supplier_payment_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order_sales_commissioin_payments`
--

INSERT INTO `order_sales_commissioin_payments` (`osc_id`, `supplier_payment_status_id`, `supplier_status_updated_on`) VALUES
(1, NULL, '2017-04-21 23:30:50'),
(2, NULL, '2017-04-21 23:30:50'),
(3, NULL, '2017-04-21 23:30:51');

-- --------------------------------------------------------

--
-- Table structure for table `order_sales_commission`
--

CREATE TABLE IF NOT EXISTS `order_sales_commission` (
  `osc_id` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint(30) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `sub_order_id` bigint(25) unsigned NOT NULL,
  `supplier_id` int(10) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `mrp_price` double unsigned NOT NULL DEFAULT '0',
  `supplier_price` double unsigned NOT NULL DEFAULT '0',
  `supplier_discount_per` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `supplier_sold_price` double unsigned NOT NULL DEFAULT '0',
  `site_commission_unit` tinyint(2) unsigned DEFAULT NULL,
  `site_commission_value` double unsigned NOT NULL DEFAULT '0',
  `site_commission_amount` double unsigned NOT NULL DEFAULT '0',
  `site_margin_price` double unsigned DEFAULT '0',
  `site_discount_per` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `site_sold_price` double unsigned NOT NULL DEFAULT '0',
  `qty` smallint(6) unsigned NOT NULL DEFAULT '0',
  `price_sub_total` double unsigned NOT NULL DEFAULT '0',
  `site_commission_sub_total` double unsigned NOT NULL DEFAULT '0',
  `supplier_price_sub_total` double unsigned NOT NULL DEFAULT '0',
  `is_shipping_beared_by_supplier` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Shipping Charge by Customer, 1-Shipping Charge By Supplier',
  `shipping_fee` double unsigned NOT NULL DEFAULT '0',
  `collection_fee` double unsigned NOT NULL DEFAULT '0',
  `fixed_fee` double unsigned NOT NULL DEFAULT '0',
  `supplier_tax_total` double unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`osc_id`),
  KEY `order_id` (`order_id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `currency_id` (`currency_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `sub_order_id` (`sub_order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `order_sales_commission`
--

INSERT INTO `order_sales_commission` (`osc_id`, `order_item_id`, `order_id`, `sub_order_id`, `supplier_id`, `currency_id`, `mrp_price`, `supplier_price`, `supplier_discount_per`, `supplier_sold_price`, `site_commission_unit`, `site_commission_value`, `site_commission_amount`, `site_margin_price`, `site_discount_per`, `site_sold_price`, `qty`, `price_sub_total`, `site_commission_sub_total`, `supplier_price_sub_total`, `is_shipping_beared_by_supplier`, `shipping_fee`, `collection_fee`, `fixed_fee`, `supplier_tax_total`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 1, 1, 1, 1, 2, 1000, 900, 10, 900, NULL, 0, 0, 0, 0, 900, 2, 1800, 0, 1800, 0, 0, 0, 0, 0, '2017-04-21 23:30:50', '2018-07-17 07:52:50', 0),
(2, 2, 1, 1, 1, 2, 700, 630, 10, 630, NULL, 0, 0, 0, 0, 630, 2, 1260, 0, 1260, 0, 0, 0, 0, 0, '2017-04-21 23:30:50', '2018-07-17 07:52:53', 0),
(3, 3, 1, 1, 1, 2, 1000, 900, 10, 900, NULL, 0, 0, 0, 0, 900, 1, 900, 0, 900, 0, 0, 0, 0, 0, '2017-04-21 23:30:51', '2018-07-17 07:52:57', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_shipment_courier_log`
--

CREATE TABLE IF NOT EXISTS `order_shipment_courier_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint(30) NOT NULL,
  `replacement_time` tinyint(2) NOT NULL DEFAULT '0',
  `courier_id` smallint(4) NOT NULL,
  `bill_number` varchar(255) NOT NULL,
  `remarks` text,
  `weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `expected_delivery_date` date DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_shipping_details`
--

CREATE TABLE IF NOT EXISTS `order_shipping_details` (
  `shipping_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `address1` varchar(200) NOT NULL,
  `address2` varchar(200) NOT NULL,
  `city_id` bigint(20) unsigned DEFAULT NULL,
  `state_id` int(11) unsigned DEFAULT NULL,
  `country_id` tinyint(3) unsigned DEFAULT NULL,
  `mobile_no` varchar(20) DEFAULT NULL,
  `email_id` varchar(100) NOT NULL,
  `postal_code` varchar(15) NOT NULL,
  `courier_id` smallint(4) unsigned NOT NULL,
  `mode_id` smallint(4) unsigned NOT NULL,
  `bill_number` varchar(255) NOT NULL,
  `remarks` text,
  `weight` decimal(10,3) unsigned NOT NULL DEFAULT '0.000',
  `shipping_status` tinyint(2) unsigned NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dispatch_date` datetime DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`shipping_id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `city_id` (`city_id`),
  KEY `state_id` (`state_id`),
  KEY `country_id` (`country_id`),
  KEY `courier_id` (`courier_id`),
  KEY `mode_id` (`mode_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `order_shipping_details`
--

INSERT INTO `order_shipping_details` (`shipping_id`, `order_id`, `full_name`, `address1`, `address2`, `city_id`, `state_id`, `country_id`, `mobile_no`, `email_id`, `postal_code`, `courier_id`, `mode_id`, `bill_number`, `remarks`, `weight`, `shipping_status`, `created_date`, `updated_date`, `dispatch_date`, `delivery_date`, `is_deleted`) VALUES
(1, 1, ' Parthiban K', 'bs1', 'bs2', NULL, NULL, NULL, '1234567890', 'customer@gmail.com', '11111', 0, 0, '', NULL, '0.000', 0, '2016-10-06 04:13:42', '2016-10-06 04:43:42', NULL, NULL, 0),
(2, 2, ' Parthiban K', 'bs1', 'bs2', NULL, NULL, NULL, '1234567890', 'customer@gmail.com', '11111', 0, 0, '', NULL, '0.000', 0, '2016-10-06 04:23:52', '2016-10-06 04:53:52', NULL, NULL, 0),
(3, 3, 'Parthiban K', 'New Str', 'Main Road ', NULL, NULL, NULL, '926128834', 'customer@gmail.com', '600052', 0, 0, '', NULL, '0.000', 0, '2017-04-25 00:31:20', '2017-04-25 01:01:20', NULL, NULL, 0),
(4, 4, 'Parthiban K', 'New Str', 'Main Road ', NULL, NULL, NULL, '926128834', 'customer@gmail.com', '600052', 0, 0, '', NULL, '0.000', 0, '2017-04-26 05:32:20', '2017-04-26 06:02:20', NULL, NULL, 0),
(6, 6, 'Parthiban', 'new Str,', '', NULL, NULL, NULL, '9626128834', 'parthiban.ejugiter@gmail.com', '632006', 0, 0, '', NULL, '0.000', 0, '2017-05-04 01:40:54', '2017-05-04 02:10:54', NULL, NULL, 0),
(17, 17, 'Parthiban', 'new Str,', '', NULL, NULL, NULL, '9626128834', 'parthiban.ejugiter@gmail.com', '632006', 0, 0, '', NULL, '0.000', 0, '2017-05-04 04:36:28', '2017-05-04 05:06:28', NULL, NULL, 0),
(18, 18, 'Jaayprakash', 'New Str', 'PWD Nag', 796, 31, 77, '9626128834', 'prakash@ymail.com', '632006', 0, 0, '', NULL, '0.000', 0, '2017-07-11 06:13:46', '2017-07-11 06:43:46', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_account_type_messages`
--

CREATE TABLE IF NOT EXISTS `order_status_account_type_messages` (
  `osatm_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `order_status_id` tinyint(2) unsigned NOT NULL,
  `account_type_id` tinyint(2) unsigned NOT NULL,
  `is_visible` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `pending_msg` text,
  `completed_msg` text,
  PRIMARY KEY (`osatm_id`),
  UNIQUE KEY `order_status_id_2` (`order_status_id`,`account_type_id`),
  KEY `order_status_id` (`order_status_id`),
  KEY `account_type_id` (`account_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `order_status_account_type_messages`
--

INSERT INTO `order_status_account_type_messages` (`osatm_id`, `order_status_id`, `account_type_id`, `is_visible`, `pending_msg`, `completed_msg`) VALUES
(1, 0, 2, 1, NULL, 'Your order has been placed'),
(2, 1, 2, 1, 'Seller yet to confirm item', 'Your order has been confirmed'),
(3, 2, 2, 1, 'Seller yet to pack item', 'Your order has been packed'),
(4, 3, 2, 1, 'Seller yet to dispatch item  to courier', 'Your order has been dispatched'),
(5, 4, 2, 1, 'Courier yet to ship item', 'Your order has been shipping'),
(6, 5, 2, 1, 'Shipment yet to reach hub nearest to you', 'Your order has been reached nearest to you'),
(7, 6, 2, 1, 'Shipment yet to be deliver', 'Your Order has been delivered'),
(8, 7, 2, 0, NULL, 'Your order installation/ assembly in progress'),
(9, 8, 2, 0, NULL, 'Your order installation/ assembly has been completed'),
(10, 9, 2, 0, NULL, 'Your order has been cancelled'),
(11, 10, 2, 0, NULL, 'Your order has been returned'),
(12, 11, 2, 0, NULL, 'Your order refund request has been approve'),
(13, 12, 2, 0, NULL, 'Your order refund request has been rejected'),
(14, 13, 2, 0, NULL, 'Your order has been picked to refund'),
(15, 14, 2, 0, NULL, NULL),
(16, 15, 2, 0, NULL, 'Your order has been returned'),
(17, 16, 2, 0, NULL, 'Your order has been requested to replace'),
(18, 17, 2, 0, NULL, 'Your order replace request has been approved'),
(19, 18, 2, 0, NULL, 'Your order replace request has been rejected'),
(20, 19, 2, 0, NULL, 'Your Order has been picked to replace'),
(21, 20, 2, 0, NULL, NULL),
(22, 21, 2, 0, NULL, 'Your order has been replaced'),
(23, 22, 2, 0, NULL, NULL),
(24, 23, 2, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_log`
--

CREATE TABLE IF NOT EXISTS `order_status_log` (
  `status_log_id` bigint(30) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `sub_order_id` bigint(25) unsigned DEFAULT NULL,
  `order_item_id` bigint(30) unsigned DEFAULT NULL,
  `replacement_time` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `from_order_status_id` tinyint(2) unsigned DEFAULT NULL,
  `to_order_status_id` tinyint(2) unsigned DEFAULT NULL,
  `from_approval_status_id` tinyint(1) unsigned DEFAULT NULL,
  `to_approval_status_id` tinyint(1) unsigned DEFAULT NULL,
  `comments` text,
  `updated_by` bigint(20) unsigned NOT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`status_log_id`),
  KEY `order_id` (`order_id`),
  KEY `sub_order_id` (`sub_order_id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `from_order_status_id` (`from_order_status_id`),
  KEY `to_order_status_id` (`to_order_status_id`),
  KEY `from_approval_status_id` (`from_approval_status_id`),
  KEY `to_approval_status_id` (`to_approval_status_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=419 ;

--
-- Dumping data for table `order_status_log`
--

INSERT INTO `order_status_log` (`status_log_id`, `order_id`, `sub_order_id`, `order_item_id`, `replacement_time`, `from_order_status_id`, `to_order_status_id`, `from_approval_status_id`, `to_approval_status_id`, `comments`, `updated_by`, `updated_on`) VALUES
(1, 1, NULL, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(2, 1, 1, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(3, 1, 1, 1, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(4, 1, 1, 2, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(5, 1, 1, 3, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(6, 2, NULL, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(7, 2, 2, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(8, 2, 2, 4, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(9, 2, 2, 5, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(10, 3, NULL, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(11, 3, 3, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(12, 3, 3, 6, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(13, 3, 3, 7, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(14, 3, 3, 8, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(15, 3, 3, 9, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(16, NULL, 2, NULL, 0, NULL, NULL, 2, 5, NULL, 1, NULL),
(17, 2, NULL, NULL, 0, NULL, NULL, 2, 5, NULL, 1, NULL),
(18, 4, NULL, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(19, 4, 4, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(20, 4, 4, 10, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(21, 4, 4, 11, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(29, NULL, NULL, 10, 0, NULL, NULL, 2, 5, NULL, 1, '2017-04-26 01:08:53'),
(30, NULL, NULL, 11, 0, NULL, NULL, 2, 5, NULL, 1, '2017-04-26 01:08:53'),
(31, NULL, 4, NULL, 0, NULL, NULL, 2, 5, NULL, 1, '2017-04-26 01:08:53'),
(32, 4, NULL, NULL, 0, NULL, NULL, 2, 5, NULL, 1, '2017-04-26 01:08:53'),
(33, NULL, NULL, 6, 0, NULL, NULL, 2, 5, NULL, 1, '2017-04-26 01:09:04'),
(34, NULL, NULL, 7, 0, NULL, NULL, 2, 5, NULL, 1, '2017-04-26 01:09:04'),
(35, NULL, NULL, 8, 0, NULL, NULL, 2, 5, NULL, 1, '2017-04-26 01:09:04'),
(36, NULL, NULL, 9, 0, NULL, NULL, 2, 5, NULL, 1, '2017-04-26 01:09:04'),
(37, NULL, 3, NULL, 0, NULL, NULL, 2, 5, NULL, 1, '2017-04-26 01:09:04'),
(38, 3, NULL, NULL, 0, NULL, NULL, 2, 5, NULL, 1, '2017-04-26 01:09:04'),
(41, NULL, NULL, 10, 0, NULL, 1, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(42, 4, NULL, NULL, 0, NULL, NULL, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(43, NULL, NULL, 11, 0, NULL, 1, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(44, NULL, 4, NULL, 0, NULL, 1, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(45, 4, NULL, NULL, 0, NULL, 1, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(49, NULL, NULL, 6, 0, NULL, 1, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(50, 3, NULL, NULL, 0, NULL, NULL, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(51, NULL, NULL, 7, 0, NULL, 1, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(52, NULL, NULL, 8, 0, NULL, 1, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(53, NULL, NULL, 9, 0, NULL, 1, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(54, NULL, 3, NULL, 0, NULL, 1, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(55, 3, NULL, NULL, 0, NULL, 1, NULL, NULL, '', 3, '2017-05-16 01:49:51'),
(56, NULL, NULL, 10, 0, 1, 2, NULL, NULL, '', 3, '2017-04-26 01:47:34'),
(57, 4, NULL, NULL, 0, 1, 1, NULL, NULL, '', 3, '2017-04-26 01:47:34'),
(58, NULL, NULL, 11, 0, 1, 2, NULL, NULL, '', 3, '2017-04-26 01:47:34'),
(59, NULL, 4, NULL, 0, 1, 2, NULL, NULL, '', 3, '2017-04-26 01:47:34'),
(60, 4, NULL, NULL, 0, 1, 2, NULL, NULL, '', 3, '2017-04-26 01:47:34'),
(61, NULL, NULL, 10, 0, 2, 3, NULL, NULL, '', 3, '2017-04-26 01:52:45'),
(62, 4, NULL, NULL, 0, 2, 2, NULL, NULL, '', 3, '2017-04-26 01:52:45'),
(63, NULL, NULL, 11, 0, 2, 3, NULL, NULL, '', 3, '2017-04-26 01:52:45'),
(64, NULL, 4, NULL, 0, 2, 3, NULL, NULL, '', 3, '2017-04-26 01:52:45'),
(65, 4, NULL, NULL, 0, 2, 3, NULL, NULL, '', 3, '2017-04-26 01:52:45'),
(66, NULL, NULL, 6, 0, 1, 2, NULL, NULL, '', 3, '2017-04-26 01:55:36'),
(67, 3, NULL, NULL, 0, 1, 1, NULL, NULL, '', 3, '2017-04-26 01:55:36'),
(68, NULL, NULL, 7, 0, 1, 2, NULL, NULL, '', 3, '2017-04-26 01:55:36'),
(69, NULL, NULL, 8, 0, 1, 2, NULL, NULL, '', 3, '2017-04-26 01:55:36'),
(70, NULL, NULL, 9, 0, 1, 2, NULL, NULL, '', 3, '2017-04-26 01:55:36'),
(71, NULL, 3, NULL, 0, 1, 2, NULL, NULL, '', 3, '2017-04-26 01:55:36'),
(72, 3, NULL, NULL, 0, 1, 2, NULL, NULL, '', 3, '2017-04-26 01:55:36'),
(79, NULL, NULL, 10, 0, 3, 4, NULL, NULL, '', 1, '2017-04-26 02:07:07'),
(80, 4, NULL, NULL, 0, 3, 3, NULL, NULL, '', 1, '2017-04-26 02:07:07'),
(81, NULL, NULL, 11, 0, 3, 4, NULL, NULL, '', 1, '2017-04-26 02:07:07'),
(82, NULL, 4, NULL, 0, 3, 4, NULL, NULL, '', 1, '2017-04-26 02:07:07'),
(83, 4, NULL, NULL, 0, 3, 4, NULL, NULL, '', 1, '2017-04-26 02:07:07'),
(84, NULL, NULL, 10, 0, 4, 5, NULL, NULL, '', 1, '2017-04-26 02:07:13'),
(85, 4, NULL, NULL, 0, 4, 4, NULL, NULL, '', 1, '2017-04-26 02:07:13'),
(86, NULL, NULL, 11, 0, 4, 5, NULL, NULL, '', 1, '2017-04-26 02:07:13'),
(87, NULL, 4, NULL, 0, 4, 5, NULL, NULL, '', 1, '2017-04-26 02:07:13'),
(88, 4, NULL, NULL, 0, 4, 5, NULL, NULL, '', 1, '2017-04-26 02:07:13'),
(89, NULL, NULL, 10, 0, 5, 6, NULL, NULL, '', 1, '2017-04-26 02:07:19'),
(90, 4, NULL, NULL, 0, 5, 5, NULL, NULL, '', 1, '2017-04-26 02:07:20'),
(91, NULL, NULL, 11, 0, 5, 6, NULL, NULL, '', 1, '2017-04-26 02:07:20'),
(92, NULL, 4, NULL, 0, 5, 6, NULL, NULL, '', 1, '2017-04-26 02:07:20'),
(93, 4, NULL, NULL, 0, 5, 6, NULL, NULL, '', 1, '2017-04-26 02:07:20'),
(94, NULL, NULL, 10, 0, 6, 7, NULL, NULL, '', 1, '2017-04-26 02:07:48'),
(95, 4, NULL, NULL, 0, 6, 6, NULL, NULL, '', 1, '2017-04-26 02:07:48'),
(96, NULL, NULL, 11, 0, 6, 7, NULL, NULL, '', 1, '2017-04-26 02:07:48'),
(97, NULL, 4, NULL, 0, 6, 7, NULL, NULL, '', 1, '2017-04-26 02:07:48'),
(98, 4, NULL, NULL, 0, 6, 7, NULL, NULL, '', 1, '2017-04-26 02:07:48'),
(99, NULL, NULL, 10, 0, 7, 8, NULL, NULL, '', 1, '2017-04-26 02:07:53'),
(100, 4, NULL, NULL, 0, 7, 7, NULL, NULL, '', 1, '2017-04-26 02:07:53'),
(101, NULL, NULL, 11, 0, 7, 8, NULL, NULL, '', 1, '2017-04-26 02:07:53'),
(102, NULL, 4, NULL, 0, 7, 8, NULL, NULL, '', 1, '2017-04-26 02:07:53'),
(103, 4, NULL, NULL, 0, 7, 8, NULL, NULL, '', 1, '2017-04-26 02:07:53'),
(106, 6, NULL, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(107, 6, 6, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(108, 6, 6, 13, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(109, NULL, NULL, 13, 0, NULL, NULL, 2, 5, NULL, 1, '2017-05-03 20:44:41'),
(110, NULL, 6, NULL, 0, NULL, NULL, 2, 5, NULL, 1, '2017-05-03 20:44:41'),
(111, 6, NULL, NULL, 0, NULL, NULL, 2, 5, NULL, 1, '2017-05-03 20:44:41'),
(132, 17, NULL, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(133, 17, 17, NULL, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(134, 17, 17, 14, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(135, 17, 17, 15, 0, NULL, NULL, NULL, 2, NULL, 2, '2017-05-16 01:49:51'),
(136, NULL, NULL, 14, 0, NULL, 9, NULL, NULL, '', 2, '2017-05-16 01:49:51'),
(137, NULL, 17, NULL, 0, NULL, NULL, NULL, NULL, '', 2, '2017-05-16 01:49:51'),
(138, 17, NULL, NULL, 0, NULL, NULL, NULL, NULL, '', 2, '2017-05-16 01:49:51'),
(169, NULL, NULL, 14, 0, NULL, NULL, 2, 5, NULL, 1, '2017-06-13 23:16:11'),
(170, NULL, NULL, 15, 0, NULL, NULL, 2, 5, NULL, 1, '2017-06-13 23:16:11'),
(171, NULL, 17, NULL, 0, NULL, NULL, 2, 5, NULL, 1, '2017-06-13 23:16:11'),
(172, 17, NULL, NULL, 0, NULL, NULL, 2, 5, NULL, 1, '2017-06-13 23:16:11'),
(179, NULL, NULL, 15, 0, 0, 1, NULL, NULL, '', 3, '2017-06-13 23:22:03'),
(180, NULL, 17, NULL, 0, 0, 1, NULL, NULL, '', 3, '2017-06-13 23:22:04'),
(181, 17, NULL, NULL, 0, 0, 1, NULL, NULL, '', 3, '2017-06-13 23:22:04'),
(182, NULL, NULL, 13, 0, 0, 1, NULL, NULL, '', 3, '2017-06-13 23:22:12'),
(183, NULL, 6, NULL, 0, 0, 1, NULL, NULL, '', 3, '2017-06-13 23:22:12'),
(184, 6, NULL, NULL, 0, 0, 1, NULL, NULL, '', 3, '2017-06-13 23:22:12'),
(352, NULL, NULL, 4, 0, 0, 1, NULL, NULL, '', 3, '2017-06-19 19:24:28'),
(353, 2, NULL, NULL, 0, 0, 0, NULL, NULL, '', 3, '2017-06-19 19:24:28'),
(354, NULL, NULL, 5, 0, 0, 1, NULL, NULL, '', 3, '2017-06-19 19:24:28'),
(355, NULL, 2, NULL, 0, 0, 1, NULL, NULL, '', 3, '2017-06-19 19:24:28'),
(356, 2, NULL, NULL, 0, 0, 1, NULL, NULL, '', 3, '2017-06-19 19:24:28'),
(357, 18, NULL, NULL, 0, 0, 0, 0, 2, NULL, 2, '2017-07-11 01:13:46'),
(358, 18, 18, NULL, 0, 0, 0, 0, 2, NULL, 2, '2017-07-11 01:13:46'),
(359, 18, 18, 16, 0, 0, 0, 0, 2, NULL, 2, '2017-07-11 01:13:47'),
(360, 18, 18, 17, 0, 0, 0, 0, 2, NULL, 2, '2017-07-11 01:13:47'),
(361, NULL, NULL, 1, 0, 0, 1, NULL, NULL, '', 3, '2017-08-10 22:11:11'),
(362, 1, NULL, NULL, 0, 0, 0, NULL, NULL, '', 3, '2017-08-10 22:11:11'),
(363, NULL, NULL, 2, 0, 0, 1, NULL, NULL, '', 3, '2017-08-10 22:11:11'),
(364, NULL, NULL, 3, 0, 0, 1, NULL, NULL, '', 3, '2017-08-10 22:11:13'),
(365, NULL, 1, NULL, 0, 0, 1, NULL, NULL, '', 3, '2017-08-10 22:11:13'),
(366, 1, NULL, NULL, 0, 0, 1, NULL, NULL, '', 3, '2017-08-10 22:11:13'),
(382, NULL, NULL, 16, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:06:10'),
(383, 18, NULL, NULL, 0, 0, 0, NULL, NULL, '', 157, '2018-07-13 11:06:10'),
(384, NULL, NULL, 17, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:06:10'),
(385, NULL, 18, NULL, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:06:10'),
(386, 18, NULL, NULL, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:06:10'),
(387, NULL, NULL, 16, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:19:03'),
(388, 18, NULL, NULL, 0, 0, 0, NULL, NULL, '', 157, '2018-07-13 11:19:03'),
(389, NULL, NULL, 17, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:19:03'),
(390, NULL, 18, NULL, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:19:03'),
(391, 18, NULL, NULL, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:19:03'),
(392, NULL, NULL, 16, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:34:34'),
(393, 18, NULL, NULL, 0, 1, 0, NULL, NULL, '', 157, '2018-07-13 11:34:34'),
(394, NULL, NULL, 17, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:34:34'),
(395, NULL, 18, NULL, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:34:34'),
(396, 18, NULL, NULL, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 11:34:34'),
(401, NULL, NULL, 1, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 12:31:20'),
(402, 1, NULL, NULL, 0, 1, 0, NULL, NULL, '', 157, '2018-07-13 12:31:20'),
(403, NULL, NULL, 2, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 12:31:20'),
(404, NULL, NULL, 3, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 12:31:20'),
(405, NULL, 1, NULL, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 12:31:20'),
(406, 1, NULL, NULL, 0, 0, 1, NULL, NULL, '', 157, '2018-07-13 12:31:20'),
(407, NULL, NULL, 1, 0, 1, 2, NULL, NULL, '', 157, '2018-07-13 12:32:04'),
(408, 1, NULL, NULL, 0, 1, 1, NULL, NULL, '', 157, '2018-07-13 12:32:04'),
(409, NULL, NULL, 2, 0, 1, 2, NULL, NULL, '', 157, '2018-07-13 12:32:04'),
(410, NULL, NULL, 3, 0, 1, 2, NULL, NULL, '', 157, '2018-07-13 12:32:04'),
(411, NULL, 1, NULL, 0, 1, 2, NULL, NULL, '', 157, '2018-07-13 12:32:04'),
(412, 1, NULL, NULL, 0, 1, 2, NULL, NULL, '', 157, '2018-07-13 12:32:04'),
(413, NULL, NULL, 1, 0, 2, 3, NULL, NULL, '', 157, '2018-07-13 12:32:48'),
(414, 1, NULL, NULL, 0, 2, 2, NULL, NULL, '', 157, '2018-07-13 12:32:48'),
(415, NULL, NULL, 2, 0, 2, 3, NULL, NULL, '', 157, '2018-07-13 12:32:48'),
(416, NULL, NULL, 3, 0, 2, 3, NULL, NULL, '', 157, '2018-07-13 12:32:48'),
(417, NULL, 1, NULL, 0, 2, 3, NULL, NULL, '', 157, '2018-07-13 12:32:48'),
(418, 1, NULL, NULL, 0, 2, 3, NULL, NULL, '', 157, '2018-07-13 12:32:48');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_lookup`
--

CREATE TABLE IF NOT EXISTS `order_status_lookup` (
  `order_status_id` tinyint(2) unsigned NOT NULL,
  `status` varchar(50) NOT NULL,
  `status_key` varchar(200) NOT NULL,
  `order_status_class` varchar(50) NOT NULL,
  PRIMARY KEY (`order_status_id`),
  UNIQUE KEY `order_status_id` (`order_status_id`),
  UNIQUE KEY `status_key` (`status_key`),
  UNIQUE KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order_status_lookup`
--

INSERT INTO `order_status_lookup` (`order_status_id`, `status`, `status_key`, `order_status_class`) VALUES
(0, 'Placed', 'PLACED', 'label label-info'),
(1, 'Approved', 'APPROVED', 'label label-success'),
(2, 'Packed', 'PACKED', 'label label-default'),
(3, 'Dispatched', 'DISPATCHED', 'label label-info'),
(4, 'In Shipping', 'IN_SHIPPING', 'label label-success'),
(5, 'Reached Hub', 'REACHED_HUB', 'label label-danger'),
(6, 'Delivered', 'DELIVERED', 'label label-warning'),
(7, 'Service in Progress', 'SERVICE_IN_PROGRESS', ''),
(8, 'Service Completed', 'SERVICE_COMPLETED', ''),
(9, 'Cancelled', 'CANCELLED', ''),
(10, 'Return & Refund', 'RETURN_REFUND', ''),
(11, 'Refund Approved', 'REFUND_APPROVED', ''),
(12, 'Refund Rejected', 'REFUND_REJECTED', ''),
(13, 'Refund Item Picked', 'REFUND_PICKED', ''),
(14, 'Refund Dispatched', 'REFUND_DISPATCHED', ''),
(15, 'Refunded', 'REFUNDED', ''),
(16, 'Return & Replace', 'RETURN_REPLACE', ''),
(17, 'Replace Approved', 'REPLACE_APPROVED', ''),
(18, 'Replace Declined', 'REPLCAE_REJECTED', ''),
(19, 'Return Replace Picked', 'REPLACE_PICKED', ''),
(20, 'Replace Dispatched', 'REPLACE_DISPATCHED', ''),
(21, 'Replaced', 'REPLACED', ''),
(22, 'Cancel Return Dispatched', 'CANCEL_RETURN_DISPATCHED', ''),
(23, 'Completed', 'COMPLETED', 'label label-success');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_notification`
--

CREATE TABLE IF NOT EXISTS `order_status_notification` (
  `osn_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `oss_id` smallint(6) unsigned NOT NULL,
  `account_type_id` tinyint(2) unsigned NOT NULL,
  `sms` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `email` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `notification` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  PRIMARY KEY (`osn_id`),
  UNIQUE KEY `oss_id` (`oss_id`,`account_type_id`),
  KEY `oss_id_2` (`oss_id`),
  KEY `account_type_id` (`account_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=130 ;

--
-- Dumping data for table `order_status_notification`
--

INSERT INTO `order_status_notification` (`osn_id`, `oss_id`, `account_type_id`, `sms`, `email`, `notification`) VALUES
(1, 1, 1, 1, 1, 1),
(2, 1, 2, 1, 1, 1),
(3, 1, 3, 1, 1, 1),
(5, 1, 4, 1, 1, 1),
(6, 2, 1, 1, 1, 1),
(7, 2, 2, 1, 1, 1),
(8, 2, 3, 1, 1, 1),
(9, 2, 4, 1, 1, 1),
(10, 3, 1, 1, 1, 1),
(11, 3, 2, 1, 1, 1),
(12, 3, 3, 1, 1, 1),
(13, 3, 4, 1, 1, 1),
(14, 4, 1, 1, 1, 1),
(15, 4, 2, 1, 1, 1),
(16, 4, 3, 1, 1, 1),
(17, 4, 4, 1, 1, 1),
(18, 5, 1, 1, 1, 1),
(19, 5, 2, 1, 1, 1),
(20, 5, 3, 1, 1, 1),
(21, 5, 4, 1, 1, 1),
(22, 6, 1, 1, 1, 1),
(23, 6, 2, 1, 1, 1),
(24, 6, 3, 1, 1, 1),
(25, 6, 4, 1, 1, 1),
(26, 7, 1, 1, 1, 1),
(27, 7, 2, 1, 1, 1),
(28, 7, 3, 1, 1, 1),
(29, 7, 4, 1, 1, 1),
(30, 8, 1, 1, 1, 1),
(31, 8, 2, 1, 1, 1),
(32, 8, 3, 1, 1, 1),
(33, 8, 4, 1, 1, 1),
(34, 9, 1, 1, 1, 1),
(35, 9, 2, 1, 1, 1),
(36, 9, 3, 1, 1, 1),
(37, 9, 4, 1, 1, 1),
(38, 10, 1, 1, 1, 1),
(39, 10, 2, 1, 1, 1),
(40, 10, 3, 1, 1, 1),
(41, 10, 4, 1, 1, 1),
(46, 12, 1, 1, 1, 1),
(47, 12, 2, 1, 1, 1),
(48, 12, 3, 1, 1, 1),
(49, 12, 4, 1, 1, 1),
(50, 13, 1, 1, 1, 1),
(51, 13, 2, 1, 1, 1),
(52, 13, 3, 1, 1, 1),
(53, 13, 4, 1, 1, 1),
(54, 14, 1, 1, 1, 1),
(55, 14, 2, 1, 1, 1),
(56, 14, 3, 1, 1, 1),
(57, 14, 4, 1, 1, 1),
(58, 15, 1, 1, 1, 1),
(59, 15, 2, 1, 1, 1),
(60, 15, 3, 1, 1, 1),
(61, 15, 4, 1, 1, 1),
(62, 16, 1, 1, 1, 1),
(63, 16, 2, 1, 1, 1),
(64, 16, 3, 1, 1, 1),
(65, 16, 4, 1, 1, 1),
(66, 17, 1, 1, 1, 1),
(67, 17, 2, 1, 1, 1),
(68, 17, 3, 1, 1, 1),
(69, 17, 4, 1, 1, 1),
(70, 18, 1, 1, 1, 1),
(71, 18, 2, 1, 1, 1),
(72, 18, 3, 1, 1, 1),
(73, 18, 4, 1, 1, 1),
(74, 19, 1, 1, 1, 1),
(75, 19, 2, 1, 1, 1),
(76, 19, 3, 1, 1, 1),
(77, 19, 4, 1, 1, 1),
(78, 20, 1, 1, 1, 1),
(79, 20, 2, 1, 1, 1),
(80, 20, 3, 1, 1, 1),
(81, 20, 4, 1, 1, 1),
(82, 21, 1, 1, 1, 1),
(83, 21, 2, 1, 1, 1),
(84, 21, 3, 1, 1, 1),
(85, 21, 4, 1, 1, 1),
(86, 22, 1, 1, 1, 1),
(87, 22, 2, 1, 1, 1),
(88, 22, 3, 1, 1, 1),
(89, 22, 4, 1, 1, 1),
(90, 23, 1, 1, 1, 1),
(91, 23, 2, 1, 1, 1),
(92, 23, 3, 1, 1, 1),
(93, 23, 4, 1, 1, 1),
(94, 24, 1, 1, 1, 1),
(95, 24, 2, 1, 1, 1),
(96, 24, 3, 1, 1, 1),
(97, 24, 4, 1, 1, 1),
(98, 25, 1, 1, 1, 1),
(99, 25, 2, 1, 1, 1),
(100, 25, 3, 1, 1, 1),
(101, 25, 4, 1, 1, 1),
(102, 26, 1, 1, 1, 1),
(103, 26, 2, 1, 1, 1),
(104, 26, 3, 1, 1, 1),
(105, 26, 4, 1, 1, 1),
(106, 27, 1, 1, 1, 1),
(107, 27, 2, 1, 1, 1),
(108, 27, 3, 1, 1, 1),
(109, 27, 4, 1, 1, 1),
(110, 28, 1, 1, 1, 1),
(111, 28, 2, 1, 1, 1),
(112, 28, 3, 1, 1, 1),
(113, 28, 4, 1, 1, 1),
(114, 29, 1, 1, 1, 1),
(115, 29, 2, 1, 1, 1),
(116, 29, 3, 1, 1, 1),
(117, 29, 4, 1, 1, 1),
(118, 30, 1, 1, 1, 1),
(119, 30, 2, 1, 1, 1),
(120, 30, 3, 1, 1, 1),
(121, 30, 4, 1, 1, 1),
(122, 31, 1, 1, 1, 1),
(123, 31, 2, 1, 1, 1),
(124, 31, 3, 1, 1, 1),
(125, 31, 4, 1, 1, 1),
(126, 32, 1, 1, 1, 1),
(127, 32, 2, 1, 1, 1),
(128, 32, 3, 1, 1, 1),
(129, 32, 4, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_settings`
--

CREATE TABLE IF NOT EXISTS `order_status_settings` (
  `oss_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `from_order_status_id` tinyint(2) unsigned NOT NULL,
  `to_order_status_id` tinyint(2) unsigned NOT NULL,
  `account_type_id` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `is_comment_required` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  PRIMARY KEY (`oss_id`),
  UNIQUE KEY `from_approval_status_id` (`from_order_status_id`,`to_order_status_id`,`account_type_id`),
  KEY `to_order_status_id` (`to_order_status_id`),
  KEY `account_type_id` (`account_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `order_status_settings`
--

INSERT INTO `order_status_settings` (`oss_id`, `from_order_status_id`, `to_order_status_id`, `account_type_id`, `is_comment_required`) VALUES
(1, 0, 1, 3, 0),
(2, 1, 2, 3, 0),
(3, 2, 3, 3, 0),
(4, 3, 4, 1, 0),
(5, 4, 5, 1, 0),
(6, 5, 6, 1, 0),
(7, 6, 7, 1, 0),
(8, 7, 8, 1, 0),
(9, 0, 9, 1, 0),
(10, 0, 9, 3, 0),
(12, 0, 9, 2, 0),
(13, 1, 9, 2, 0),
(14, 2, 9, 2, 0),
(15, 3, 9, 2, 0),
(16, 4, 9, 2, 0),
(17, 5, 9, 2, 0),
(18, 6, 10, 2, 0),
(19, 6, 16, 2, 0),
(20, 10, 11, 2, 0),
(21, 10, 11, 3, 0),
(22, 10, 12, 1, 0),
(23, 10, 12, 3, 0),
(24, 11, 13, 3, 0),
(25, 13, 14, 3, 0),
(26, 14, 15, 3, 0),
(27, 16, 18, 1, 0),
(28, 16, 18, 3, 0),
(29, 17, 19, 3, 0),
(30, 19, 20, 3, 0),
(31, 20, 21, 3, 0),
(32, 9, 22, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(100) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`page_id`, `page`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 'home', '2016-10-15 04:30:35', '2017-05-03 01:50:11', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pay`
--

CREATE TABLE IF NOT EXISTS `pay` (
  `pay_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `payment_id` bigint(20) unsigned DEFAULT NULL,
  `payment_type_id` tinyint(2) unsigned DEFAULT NULL,
  `to_wallet_id` tinyint(2) unsigned DEFAULT NULL,
  `from_currency_id` tinyint(3) unsigned DEFAULT NULL,
  `from_amount` double unsigned NOT NULL DEFAULT '0',
  `to_currency_id` tinyint(3) unsigned DEFAULT NULL,
  `to_amount` double unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Pending,1-Confirmed, 2-Cancelled, 3-Failed',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pay_id`),
  KEY `order_id` (`order_id`),
  KEY `payment_type_id` (`payment_type_id`),
  KEY `to_wallet_id` (`to_wallet_id`),
  KEY `from_currency_id` (`from_currency_id`),
  KEY `to_currency_id` (`to_currency_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `pay`
--

INSERT INTO `pay` (`pay_id`, `order_id`, `payment_id`, `payment_type_id`, `to_wallet_id`, `from_currency_id`, `from_amount`, `to_currency_id`, `to_amount`, `status`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 47, NULL, 21, 1, 2, 500, 2, 500, 1, '2018-07-09 10:18:00', NULL, 155, 0),
(2, 48, NULL, 21, 1, 2, 250, 2, 250, 1, '2018-07-09 10:22:40', NULL, 155, 0),
(3, 49, NULL, 21, 1, 2, 250, 2, 250, 1, '2018-07-09 10:26:20', NULL, 155, 0),
(4, 50, NULL, 21, 1, 2, 250, 2, 250, 1, '2018-07-09 10:34:40', NULL, 155, 0),
(5, 51, NULL, 21, 1, 2, 250, 2, 250, 1, '2018-07-09 10:35:17', NULL, 155, 0),
(6, 52, NULL, 21, 1, 2, 250, 2, 250, 1, '2018-07-09 10:55:40', NULL, 155, 0),
(7, 53, NULL, 21, 1, 2, 250, 2, 250, 1, '2018-07-09 10:56:08', NULL, 155, 0),
(8, 54, NULL, 21, 1, 2, 100, 2, 100, 1, '2018-07-09 10:58:24', NULL, 155, 0),
(9, 55, NULL, 21, 1, 2, 100, 2, 100, 1, '2018-07-09 10:59:03', NULL, 155, 0),
(10, 56, NULL, 21, 1, 2, 111, 2, 111, 1, '2018-07-09 10:59:21', NULL, 155, 0),
(11, 57, NULL, 21, 1, 2, 100, 2, 100, 1, '2018-07-09 11:06:01', NULL, 155, 0),
(12, 58, NULL, 21, 1, 2, 100, 2, 100, 1, '2018-07-09 11:06:50', NULL, 155, 0),
(13, 59, NULL, 21, 1, 2, 100, 2, 100, 1, '2018-07-09 11:10:30', NULL, 155, 0),
(14, 60, NULL, 21, 1, 2, 100, 2, 100, 1, '2018-07-09 11:11:16', NULL, 155, 0),
(15, 61, NULL, 21, 1, 2, 1111, 2, 1111, 1, '2018-07-09 11:12:22', NULL, 155, 0),
(16, 62, NULL, 21, 1, 2, 1111, 2, 1111, 1, '2018-07-09 11:12:38', NULL, 155, 0),
(17, 63, NULL, 21, 1, 2, 1111, 2, 1111, 1, '2018-07-09 11:13:47', NULL, 155, 0),
(18, 64, NULL, 21, 1, 2, 1111, 2, 1111, 1, '2018-07-09 11:14:06', NULL, 155, 0),
(19, 65, NULL, 21, 1, 2, 1111, 2, 1111, 1, '2018-07-09 11:14:53', NULL, 155, 0),
(20, 66, NULL, 21, 1, 2, 1111, 2, 1111, 1, '2018-07-09 11:15:03', NULL, 155, 0),
(21, 67, NULL, 21, 1, 2, 1111, 2, 1111, 1, '2018-07-09 11:16:11', NULL, 155, 0),
(22, 68, NULL, 21, 1, 2, 2222, 2, 2222, 1, '2018-07-09 11:17:22', NULL, 155, 0),
(23, 69, NULL, 21, 1, 2, 333, 2, 333, 1, '2018-07-09 11:18:34', NULL, 155, 0),
(24, 70, NULL, 21, 1, 2, 333, 2, 333, 1, '2018-07-09 11:19:26', NULL, 155, 0),
(25, 71, NULL, 21, 1, 2, 100, 2, 100, 1, '2018-07-09 11:20:29', NULL, 155, 0);

-- --------------------------------------------------------

--
-- Table structure for table `payment_modes_lookups`
--

CREATE TABLE IF NOT EXISTS `payment_modes_lookups` (
  `paymode_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(250) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`paymode_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `payment_modes_lookups`
--

INSERT INTO `payment_modes_lookups` (`paymode_id`, `mode_name`, `status`, `is_deleted`) VALUES
(1, 'Credit Card', 1, 0),
(2, 'Net Banking', 1, 0),
(3, 'EMI', 1, 0),
(4, 'Debit Card', 1, 0),
(5, 'Cash On Delivery', 1, 0),
(6, 'Gift Card', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `payment_status_lookups`
--

CREATE TABLE IF NOT EXISTS `payment_status_lookups` (
  `payment_status_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `payment_status` varchar(100) NOT NULL,
  `payment_status_class` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`payment_status_id`),
  UNIQUE KEY `paymrnt_status` (`payment_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `payment_status_lookups`
--

INSERT INTO `payment_status_lookups` (`payment_status_id`, `payment_status`, `payment_status_class`) VALUES
(1, 'Confirmed', 'label label-success'),
(2, 'Cancelled', 'label label-danger'),
(3, 'Refunded', 'label label-info'),
(4, 'Pending', 'label label-warning');

-- --------------------------------------------------------

--
-- Table structure for table `payment_types`
--

CREATE TABLE IF NOT EXISTS `payment_types` (
  `payment_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(100) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL,
  `payment_key` varchar(200) NOT NULL,
  `status` tinyint(1) unsigned DEFAULT '1',
  `buy_package` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `image_name` varchar(150) DEFAULT NULL,
  `priority` tinyint(3) unsigned DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `test_url` varchar(255) DEFAULT NULL,
  `check_kyc_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `api_settings` text,
  `payment_modes` text NOT NULL,
  `handling_currencies` text COMMENT 'Currency IDs',
  `transaction_settings` text,
  `auto_credit` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-Enabled, 1-Disabled',
  `mode` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Test, 1-Live',
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_type_id`),
  KEY `type_status` (`status`),
  KEY `buy_package` (`buy_package`),
  KEY `check_kyc_status` (`check_kyc_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `payment_types`
--

INSERT INTO `payment_types` (`payment_type_id`, `payment_type`, `description`, `payment_key`, `status`, `buy_package`, `image_name`, `priority`, `url`, `test_url`, `check_kyc_status`, `api_settings`, `payment_modes`, `handling_currencies`, `transaction_settings`, `auto_credit`, `mode`, `created_date`) VALUES
(1, 'Paypal', NULL, 'paypal', 0, 0, 'assets/imgs/payments/PayPal.png', 6, NULL, NULL, 0, NULL, '4', '1,5,6,9,10,11,12', NULL, 1, 0, '2018-07-06 10:22:39'),
(2, 'Indian Credit/Debit Cards', NULL, 'indian-credit-debit-cards', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', '', NULL, 1, 0, '2018-03-17 00:30:50'),
(3, 'Indian Netbanking', NULL, 'indian-net-banking', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', '', NULL, 1, 0, '2018-03-17 00:30:50'),
(4, 'Solid Trust Pay', NULL, 'solid-trust-pay', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', '1,9,10,11', NULL, 1, 0, '2018-03-17 00:30:50'),
(5, 'Bitcoin', NULL, 'bitcoin', 0, 0, 'assets/imgs/payments/Bitcoin.png', 0, NULL, NULL, 0, NULL, '', '', NULL, 1, 0, '2018-03-17 00:30:50'),
(6, 'Perfect Money', NULL, 'perfect-money', 0, 0, 'assets/imgs/payments/PayPal.png', 3, NULL, NULL, 0, NULL, '', '', NULL, 1, 0, '2018-03-17 00:30:50'),
(7, 'Payza', NULL, 'payza', 0, 0, 'assets/imgs/payments/Payza.png', 0, NULL, NULL, 0, NULL, '', '', NULL, 1, 0, '2018-03-17 00:30:50'),
(9, 'eWallet', NULL, 'ewallet', 1, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', '1,2,3,4,5,6,7,8,9,10', NULL, 1, 0, '2018-03-17 00:31:20'),
(10, 'Credit/Debit Card', NULL, 'credit-debit-cards', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', '', NULL, 1, 0, '2018-03-17 00:30:50'),
(11, 'PayUmoney', NULL, 'payumoney', 0, 0, 'assets/imgs/payments/PayPal.png', 4, NULL, NULL, 0, NULL, '', '2', NULL, 1, 0, '2018-03-17 00:30:50'),
(12, 'Bank', NULL, 'bank', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', '1,2,3,4,5,6,7,8,9', NULL, 1, 0, '2018-03-17 00:30:50'),
(13, 'Express Withdrawal', NULL, 'express-withdrawal', 0, 0, 'assets/imgs/payments/PayPal.png', 2, NULL, NULL, 0, NULL, '', NULL, NULL, 1, 0, '2018-03-17 00:30:50'),
(14, 'Wire Transfer', NULL, 'wire-transfer', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', '', NULL, 1, 0, '2018-03-17 00:30:50'),
(15, 'OS Debit Card', NULL, 'os-debit-card', 0, 0, 'assets/imgs/payments/PayPal.png', 1, NULL, NULL, 0, NULL, '', NULL, NULL, 1, 0, '2018-03-17 00:30:50'),
(16, 'Ko-Kard Credit/Debit Card', NULL, 'ko-kart', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', '1', NULL, 1, 0, '2018-03-17 00:30:50'),
(17, 'Wallet System', NULL, 'wallet-system', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', '', NULL, 1, 0, '2018-03-17 00:30:50'),
(18, 'COD', NULL, 'cod', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', NULL, NULL, 1, 0, '2018-03-17 00:30:50'),
(19, 'Local Money Transfer', NULL, 'local-money-transfer', 0, 0, 'assets/imgs/payments/PayPal.png', NULL, NULL, NULL, 0, NULL, '', NULL, NULL, 1, 0, '2018-03-17 00:30:50'),
(20, 'CashFree', NULL, 'cashfree', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', NULL, NULL, 1, 0, '2018-03-17 00:30:50'),
(21, 'Cash', NULL, 'cash', 0, 0, 'assets/imgs/payments/PayPal.png', 0, NULL, NULL, 0, NULL, '', NULL, NULL, 1, 0, '2018-03-17 00:30:50');

-- --------------------------------------------------------

--
-- Table structure for table `personal_commission`
--

CREATE TABLE IF NOT EXISTS `personal_commission` (
  `p_id` int(11) NOT NULL,
  `confirm_date` date NOT NULL,
  `direct_cv` int(11) NOT NULL,
  `self_cv` int(11) NOT NULL,
  `slab` float NOT NULL,
  `total_cv` int(11) NOT NULL,
  `earnings` decimal(10,2) NOT NULL DEFAULT '0.00',
  `commission` double NOT NULL,
  `tax` double NOT NULL,
  `ngo_wallet` double NOT NULL,
  `net_pay` double NOT NULL,
  `status` smallint(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `post_type_lookups`
--

CREATE TABLE IF NOT EXISTS `post_type_lookups` (
  `post_type_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `post_type` varchar(50) DEFAULT NULL,
  `post_type_key` varchar(75) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `discount_priority` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `admin_discountable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `supplier_discountable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `img_uploadable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  PRIMARY KEY (`post_type_id`),
  UNIQUE KEY `post_type` (`post_type`),
  UNIQUE KEY `post_type_key` (`post_type_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `post_type_lookups`
--

INSERT INTO `post_type_lookups` (`post_type_id`, `post_type`, `post_type_key`, `discount_priority`, `admin_discountable`, `supplier_discountable`, `img_uploadable`, `status`) VALUES
(1, 'Brands', 'brands', 3, 1, 0, 1, 1),
(2, 'Categories', 'categories', 4, 1, 0, 1, 1),
(3, 'Products', 'products', 2, 1, 0, 1, 1),
(4, 'Product Combinations', 'product-combinations', 1, 1, 0, 0, 1),
(5, 'Supplier', 'supplier', 8, 1, 0, 1, 1),
(6, 'Payment Types', 'payments-types', 9, 0, 0, 0, 1),
(7, 'Supplier Products', 'supplier-products', 5, 0, 1, 0, 1),
(8, 'Supplier Category', 'supplier-category', 7, 0, 1, 0, 1),
(9, 'Supplier Brand', 'supplier-brand', 6, 0, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `category_id` smallint(6) unsigned NOT NULL,
  `assoc_category_id` text,
  `brand_id` smallint(6) unsigned NOT NULL,
  `is_combinations` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Combination Not Exist, 1-Combinations exist',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`product_id`),
  KEY `brand_id` (`brand_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `category_id`, `assoc_category_id`, `brand_id`, `is_combinations`, `is_deleted`) VALUES
(1, 'Pro 1', 2, NULL, 1, 0, 0),
(2, 'Pro 2', 3, NULL, 3, 1, 0),
(3, 'Pro 3', 4, '', 1, 0, 0),
(4, 'Pro 4 ', 5, NULL, 2, 0, 0),
(5, 'Pro 5', 6, NULL, 2, 0, 0),
(6, 'Pro 6', 7, NULL, 2, 0, 0),
(7, 'Pro 7', 8, NULL, 1, 0, 0),
(8, 'Pro 8', 9, NULL, 1, 0, 0),
(9, 'Pro 9', 10, NULL, 1, 0, 0),
(11, 'OnePlus 3T', 7, NULL, 1, 0, 0),
(16, 'OnePlus 3T', 12, NULL, 3, 0, 0),
(17, 'OnePlus 3T', 12, NULL, 3, 0, 0),
(18, 'OnePlus 3T', 12, NULL, 3, 0, 0),
(19, 'Moto G5 Plus', 203, NULL, 5, 0, 0),
(24, 'product name', 571, NULL, 14, 0, 0),
(25, 'product name', 571, NULL, 14, 0, 0),
(26, 'product name', 571, NULL, 14, 0, 0),
(27, 'product name', 571, NULL, 14, 0, 0),
(28, 'product name', 571, NULL, 14, 0, 0),
(29, 'product name', 571, NULL, 14, 0, 0),
(30, 'product name', 571, NULL, 14, 0, 0),
(31, 'Nokia Mobiles', 1, NULL, 14, 0, 0);

-- --------------------------------------------------------

--
-- Stand-in structure for view `products_list`
--
CREATE TABLE IF NOT EXISTS `products_list` (
`product_id` int(10) unsigned
,`product_cmb_id` int(11) unsigned
,`product_code` varchar(20)
,`eanbarcode` varchar(20)
,`upcbarcode` varchar(20)
,`category_id` smallint(6) unsigned
,`category_code` varchar(3)
,`category` varchar(100)
,`category_url_str` varchar(200)
,`replacement_service_policy_id` tinyint(3) unsigned
,`category_status` tinyint(1) unsigned
,`assoc_category_id` text
,`brand_id` smallint(6) unsigned
,`brand_name` varchar(100)
,`brand_url_str` varchar(200)
,`brand_sku` varchar(255)
,`brand_status` tinyint(1) unsigned
,`product_name` text
,`product_cmb` varchar(255)
,`product_slug` varchar(255)
,`sku` varchar(255)
,`description` text
,`currency_id` tinyint(3) unsigned
,`mrp_price` double unsigned
,`rating_1` int(10) unsigned
,`rating_2` int(10) unsigned
,`rating_3` int(10) unsigned
,`rating_4` int(10) unsigned
,`rating_5` int(10) unsigned
,`avg_rating` int(10) unsigned
,`rating_count` int(10) unsigned
,`is_combinations` tinyint(1) unsigned
,`visiblity_id` tinyint(1) unsigned
,`redirect_id` tinyint(2) unsigned
,`is_verified` tinyint(1) unsigned
,`is_exclusive` tinyint(1) unsigned
,`width` decimal(10,3) unsigned
,`height` decimal(10,3) unsigned
,`length` decimal(10,3) unsigned
,`weight` decimal(10,3) unsigned
,`volumetric_weight` decimal(10,3)
,`status` tinyint(1) unsigned
,`created_on` datetime
,`created_by` bigint(20) unsigned
,`updated_on` datetime
,`updated_by` bigint(20) unsigned
,`verified_on` datetime
,`verified_by` bigint(20) unsigned
,`is_deleted` decimal(3,0)
);
-- --------------------------------------------------------

--
-- Table structure for table `product_brands`
--

CREATE TABLE IF NOT EXISTS `product_brands` (
  `brand_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(100) DEFAULT NULL,
  `url_str` varchar(200) NOT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `is_exclusive_for_supplier` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`brand_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=105 ;

--
-- Dumping data for table `product_brands`
--

INSERT INTO `product_brands` (`brand_id`, `brand_name`, `url_str`, `sku`, `status`, `is_verified`, `is_exclusive_for_supplier`, `created_by`, `created_on`, `updated_by`, `updated_on`, `is_deleted`) VALUES
(1, 'Apple', 'apple', 'apple', 1, 0, 1, 1, '2017-06-30 23:23:26', 0, '2017-07-22 00:24:44', 0),
(2, 'Samsung', 'samsung', 'samsung', 1, 0, 0, 1, '2017-06-30 23:23:42', 0, '2017-06-30 23:53:42', 0),
(3, 'HTC', 'htc', 'htc', 1, 0, 0, 1, '2017-06-30 23:23:53', 0, '2017-06-30 23:53:53', 0),
(4, 'Sony', 'sony', 'sony', 1, 0, 0, 1, '2017-06-30 23:24:06', 0, '2017-06-30 23:54:06', 0),
(5, 'Nokia', 'nokia', 'nokia', 1, 0, 0, 1, '2017-06-30 23:24:15', 0, '2017-06-30 23:54:15', 0),
(6, 'LG', 'lg', 'lg', 1, 0, 0, 1, '2017-06-30 23:24:24', 1, '2017-07-20 21:36:43', 0),
(7, 'Micromax', 'micromax', 'micromax', 1, 0, 0, 1, '2017-06-30 23:24:33', 0, '2017-06-30 23:54:33', 0),
(8, 'Karbonn', 'karbonn', 'karbonn', 1, 0, 0, 1, '2017-06-30 23:24:41', 0, '2017-06-30 23:54:41', 0),
(9, 'Lenovo', 'lenovo', 'lenovo', 1, 1, 0, 1, '2017-06-30 23:24:50', 1, '2017-07-20 21:23:53', 0),
(10, 'Xolo', 'xolo', 'xolo', 1, 0, 0, 1, '2017-06-30 23:25:01', 0, '2017-06-30 23:55:01', 0),
(11, 'HP', 'hp', 'hp', 1, 0, 0, 1, '2017-06-30 23:25:19', 0, '2017-06-30 23:55:19', 0),
(12, 'Dell', 'dell', 'dell', 1, 0, 0, 1, '2017-06-30 23:25:29', 0, '2017-06-30 23:55:29', 0),
(13, 'Asus', 'asus', 'asus', 1, 0, 0, 1, '2017-06-30 23:25:38', 0, '2017-06-30 23:55:38', 0),
(14, 'Acer', 'acer', 'acer', 1, 0, 0, 1, '2017-06-30 23:25:50', 0, '2017-06-30 23:55:50', 0),
(15, 'Philips', 'philips', 'philips', 1, 0, 0, 1, '2017-06-30 23:26:00', 0, '2017-06-30 23:56:00', 0),
(16, 'Panasonic', 'panasonic', 'panasonic', 1, 0, 0, 1, '2017-06-30 23:26:15', 0, '2017-06-30 23:56:15', 0),
(17, 'Toshiba', 'toshiba', 'toshiba', 1, 0, 0, 1, '2017-06-30 23:26:25', 0, '2017-06-30 23:56:25', 0),
(18, 'Canon', 'canon', 'canon', 1, 0, 0, 1, '2017-06-30 23:26:34', 0, '2017-06-30 23:56:34', 0),
(19, 'Nikon', 'nikon', 'nikon', 1, 0, 0, 1, '2017-06-30 23:26:42', 0, '2017-06-30 23:56:42', 0),
(20, 'L''Oreal', 'l-oreal', 'l-oreal', 1, 1, 0, 1, '2017-06-30 23:27:41', 1, '2017-07-20 21:23:46', 0),
(21, 'Garnier', 'garnier', 'garnier', 1, 0, 0, 1, '2017-06-30 23:28:12', 0, '2017-06-30 23:58:12', 0),
(22, 'Maybelline', 'maybelline', 'maybelline', 1, 0, 0, 1, '2017-06-30 23:28:24', 0, '2017-06-30 23:58:24', 0),
(23, 'Pantene', 'pantene', 'pantene', 1, 0, 0, 1, '2017-06-30 23:31:06', 0, '2017-07-01 00:01:06', 0),
(24, 'Calvin Klein', 'calvin-klein', 'calvin-klein', 1, 0, 0, 1, '2017-06-30 23:31:30', 0, '2017-07-01 00:01:30', 0),
(25, 'Davidoff', 'davidoff', 'davidoff', 1, 0, 0, 1, '2017-06-30 23:31:43', 0, '2017-07-01 00:01:43', 0),
(26, 'Gillette', 'gillette', 'gillette', 1, 0, 0, 1, '2017-06-30 23:31:52', 0, '2017-07-01 00:01:52', 0),
(27, 'Whisper', 'whisper', 'whisper', 1, 0, 0, 1, '2017-06-30 23:32:01', 0, '2017-07-01 00:02:01', 0),
(28, 'Himalaya', 'himalaya', 'himalaya', 1, 0, 0, 1, '2017-06-30 23:32:12', 0, '2017-07-01 00:02:12', 0),
(29, 'Pampers', 'pampers', 'pampers', 1, 0, 0, 1, '2017-06-30 23:32:21', 0, '2017-07-01 00:02:21', 0),
(30, 'Disney', 'disney', 'disney', 1, 0, 0, 1, '2017-06-30 23:32:30', 0, '2017-07-01 00:02:30', 0),
(31, 'Apollo Pharmacy', 'apollo-pharmacy', 'apollo-pharmacy', 1, 0, 0, 1, '2017-06-30 23:32:51', 0, '2017-07-01 00:02:51', 0),
(32, 'Pigeon', 'pigeon', 'pigeon', 1, 0, 0, 1, '2017-06-30 23:33:02', 0, '2017-07-01 00:03:02', 0),
(33, 'Biotique', 'biotique', 'biotique', 1, 0, 0, 1, '2017-06-30 23:33:11', 0, '2017-07-01 00:03:11', 0),
(34, 'Ariel', 'ariel', 'ariel', 1, 0, 0, 1, '2017-06-30 23:33:21', 0, '2017-07-01 00:03:21', 0),
(35, 'Tide', 'tide', 'tide', 1, 0, 0, 1, '2017-06-30 23:33:32', 0, '2017-07-01 00:03:32', 0),
(36, 'Ambipur', 'ambipur', 'ambipur', 1, 0, 0, 1, '2017-06-30 23:33:42', 0, '2017-07-01 00:03:42', 0),
(37, 'Jaipan', 'jaipan', 'jaipan', 1, 0, 0, 1, '2017-06-30 23:33:51', 0, '2017-07-01 00:03:51', 0),
(38, 'Philips', 'philips', 'philips', 1, 0, 0, 1, '2017-06-30 23:34:02', 0, '2017-07-01 00:04:02', 0),
(39, 'Kent RO', 'kent-ro', 'kent-ro', 1, 0, 0, 1, '2017-06-30 23:34:15', 0, '2017-07-01 00:04:15', 0),
(40, 'Coffee Day', 'coffee-day', 'coffee-day', 1, 0, 0, 1, '2017-06-30 23:34:27', 0, '2017-07-01 00:04:27', 0),
(41, 'Sharp', 'sharp', 'sharp', 1, 0, 0, 1, '2017-06-30 23:34:38', 0, '2017-07-01 00:04:38', 0),
(42, 'Vola', 'vola', 'vola', 1, 0, 0, 1, '2017-06-30 23:34:48', 0, '2017-07-01 00:04:48', 0),
(43, 'Nova', 'nova', 'nova', 1, 0, 0, 1, '2017-06-30 23:34:57', 0, '2017-07-01 00:04:57', 0),
(44, 'Clearline', 'clearline', 'clearline', 1, 0, 0, 1, '2017-06-30 23:35:07', 0, '2017-07-01 00:05:07', 0),
(45, 'Accu Chek', 'accu-chek', 'accu-chek', 1, 0, 0, 1, '2017-06-30 23:35:21', 1, '2017-07-20 21:40:52', 0),
(46, 'Dr. Morepen', 'dr-morepen', 'dr-morepen', 1, 0, 0, 1, '2017-06-30 23:35:34', 0, '2017-07-01 00:05:34', 0),
(47, 'Health Aid', 'health-aid', 'health-aid', 1, 0, 0, 1, '2017-06-30 23:35:47', 0, '2017-07-01 00:05:47', 0),
(48, 'Johnson & Johnson', 'johnson-johnson', 'johnson-johnson', 1, 0, 0, 1, '2017-06-30 23:36:01', 0, '2017-07-01 00:06:01', 0),
(49, 'Equinox', 'equinox', 'equinox', 1, 0, 0, 1, '2017-06-30 23:36:12', 0, '2017-07-01 00:06:12', 0),
(50, 'Kamasutra', 'kamasutra', 'kamasutra', 1, 0, 0, 1, '2017-06-30 23:36:23', 0, '2017-07-01 00:06:23', 0),
(51, 'Moods', 'moods', 'moods', 1, 0, 0, 1, '2017-06-30 23:36:33', 0, '2017-07-01 00:06:33', 0),
(52, 'Dr. Gene', 'dr-gene', 'dr-gene', 1, 0, 0, 1, '2017-06-30 23:36:45', 0, '2017-07-01 00:06:45', 0),
(53, 'Adidas', 'adidas', 'adidas', 1, 0, 0, 1, '2017-06-30 23:36:54', 0, '2017-07-01 00:06:54', 0),
(54, 'GNC', 'gnc', 'gnc', 1, 0, 0, 1, '2017-06-30 23:37:07', 0, '2017-07-01 00:07:07', 0),
(55, 'Reebok', 'reebok', 'reebok', 1, 0, 0, 1, '2017-06-30 23:37:17', 0, '2017-07-01 00:07:17', 0),
(56, 'Puma', 'puma', 'puma', 1, 0, 0, 1, '2017-06-30 23:37:28', 0, '2017-07-01 00:07:28', 0),
(57, 'ESN', 'esn', 'esn', 1, 0, 0, 1, '2017-06-30 23:37:37', 0, '2017-07-01 00:07:37', 0),
(58, 'Nike', 'nike', 'nike', 1, 0, 0, 1, '2017-06-30 23:37:46', 0, '2017-07-01 00:07:46', 0),
(59, 'Lotto', 'lotto', 'lotto', 1, 0, 0, 1, '2017-06-30 23:37:56', 0, '2017-07-01 00:07:56', 0),
(60, 'Deemark', 'deemark', 'deemark', 1, 0, 0, 1, '2017-06-30 23:38:08', 0, '2017-07-01 00:08:08', 0),
(61, 'Unistar', 'unistar', 'unistar', 1, 0, 0, 1, '2017-06-30 23:38:17', 0, '2017-07-01 00:08:17', 0),
(62, 'Optimum Nutrition', 'optimum-nutrition', 'optimum-nutrition', 1, 0, 0, 1, '2017-06-30 23:38:44', 0, '2017-07-01 00:08:44', 0),
(63, 'RiteBite', 'ritebite', 'ritebite', 1, 0, 0, 1, '2017-06-30 23:39:04', 0, '2017-07-01 00:09:04', 0),
(64, 'Organic India', 'organic-india', 'organic-india', 1, 0, 0, 1, '2017-06-30 23:39:27', 0, '2017-07-01 00:09:27', 0),
(65, 'Organica', 'organica', 'organica', 1, 0, 0, 1, '2017-06-30 23:39:37', 0, '2017-07-01 00:09:37', 0),
(66, 'Down To Earth', 'down-to-earth', 'down-to-earth', 1, 0, 0, 1, '2017-06-30 23:39:50', 0, '2017-07-01 00:09:50', 0),
(67, 'DiSano', 'disano', 'disano', 1, 0, 0, 1, '2017-06-30 23:40:02', 0, '2017-07-01 00:10:02', 0),
(68, 'Chamong', 'chamong', 'chamong', 1, 0, 0, 1, '2017-06-30 23:40:15', 0, '2017-07-01 00:10:15', 0),
(69, 'Sugar Free', 'sugar-free', 'sugar-free', 1, 0, 0, 1, '2017-06-30 23:40:29', 0, '2017-07-01 00:10:29', 0),
(70, 'Kenny Delights', 'kenny-delights', 'kenny-delights', 1, 0, 0, 1, '2017-06-30 23:40:43', 0, '2017-07-01 00:10:43', 0),
(71, 'Organo', 'organo', 'organo', 1, 0, 0, 1, '2017-06-30 23:40:54', 0, '2017-07-01 00:10:54', 0),
(72, 'HealthAid', 'healthaid', 'healthaid', 1, 0, 0, 1, '2017-06-30 23:41:04', 0, '2017-07-01 00:11:04', 0),
(73, 'Growmax', 'growmax', 'growmax', 1, 0, 0, 1, '2017-06-30 23:41:15', 0, '2017-07-01 00:11:15', 0),
(74, 'Baidyanath', 'baidyanath', 'baidyanath', 1, 0, 0, 1, '2017-06-30 23:41:26', 0, '2017-07-01 00:11:26', 0),
(75, 'Inlife', 'inlife', 'inlife', 1, 0, 0, 1, '2017-06-30 23:41:39', 0, '2017-07-01 00:11:39', 0),
(76, 'Cangard', 'cangard', 'cangard', 1, 0, 0, 1, '2017-06-30 23:41:49', 0, '2017-07-01 00:11:49', 0),
(77, 'Herbal Hills', 'herbal-hills', 'herbal-hills', 1, 0, 0, 1, '2017-06-30 23:42:03', 0, '2017-07-01 00:12:03', 0),
(78, 'Biosys', 'biosys', 'biosys', 1, 0, 0, 1, '2017-06-30 23:42:14', 0, '2017-07-01 00:12:14', 0),
(79, 'Mothercare', 'mothercare', 'mothercare', 1, 0, 0, 1, '2017-06-30 23:42:26', 0, '2017-07-01 00:12:26', 0),
(80, 'Gini and Jony', 'gini-and-jony', 'gini-and-jony', 1, 0, 0, 1, '2017-06-30 23:42:48', 0, '2017-07-01 00:12:48', 0),
(81, 'The Children''s Place', 'the-children-s-place', 'the-children-s-place', 1, 0, 0, 1, '2017-06-30 23:43:05', 0, '2017-07-01 00:13:05', 0),
(82, 'United Colors of Benetton', 'united-colors-of-benetton', 'united-colors-of-benetton', 1, 0, 0, 1, '2017-06-30 23:43:18', 0, '2017-07-01 00:13:18', 0),
(83, 'YK', 'yk', 'yk', 1, 0, 0, 1, '2017-06-30 23:43:28', 0, '2017-07-01 00:13:28', 0),
(84, 'Allen Solly Junior', 'allen-solly-junior', 'allen-solly-junior', 1, 0, 0, 1, '2017-06-30 23:43:41', 0, '2017-07-01 00:13:41', 0),
(85, 'Mango Kids', 'mango-kids', 'mango-kids', 1, 0, 0, 1, '2017-06-30 23:43:53', 0, '2017-07-01 00:13:53', 0),
(86, 'Marks & Spencer', 'marks-spencer', 'marks-spencer', 1, 0, 0, 1, '2017-06-30 23:44:08', 0, '2017-07-01 00:14:08', 0),
(87, 'Tommy Hilfiger', 'tommy-hilfiger', 'tommy-hilfiger', 1, 0, 0, 1, '2017-06-30 23:44:23', 0, '2017-07-01 00:14:23', 0),
(88, 'People', 'people', 'people', 1, 0, 0, 1, '2017-06-30 23:44:33', 0, '2017-07-01 00:14:33', 0),
(89, 'Bombay Dyeing', 'bombay-dyeing', 'bombay-dyeing', 1, 0, 0, 1, '2017-06-30 23:44:46', 0, '2017-07-01 00:14:46', 0),
(90, 'Spaces', 'spaces', 'spaces', 1, 0, 0, 1, '2017-06-30 23:44:55', 0, '2017-07-01 00:14:55', 0),
(91, 'Portico New York', 'portico-new-york', 'portico-new-york', 1, 0, 0, 1, '2017-06-30 23:45:08', 0, '2017-07-01 00:15:08', 0),
(92, 'Swayam', 'swayam', 'swayam', 1, 0, 0, 1, '2017-06-30 23:45:19', 0, '2017-07-01 00:15:19', 0),
(93, 'Raymond Home', 'raymond-home', 'raymond-home', 1, 0, 0, 1, '2017-06-30 23:45:35', 0, '2017-07-01 00:15:35', 0),
(94, 'Trident', 'trident', 'trident', 1, 0, 0, 1, '2017-06-30 23:45:44', 0, '2017-07-01 00:15:44', 0),
(95, 'Cortina', 'cortina', 'cortina', 1, 0, 0, 1, '2017-06-30 23:45:55', 0, '2017-07-01 00:15:55', 0),
(96, 'Athome by Nilkamal', 'athome-by-nilkamal', 'athome-by-nilkamal', 1, 0, 0, 1, '2017-06-30 23:46:12', 0, '2017-07-01 00:16:12', 0),
(97, 'WELHOME', 'welhome', 'welhome', 1, 1, 0, 1, '2017-06-30 23:46:25', 1, '2017-08-20 19:35:10', 0),
(98, 'Tangerine', 'tangerine', 'tangerine', 1, 1, 0, 1, '2017-06-30 23:46:34', 1, '2017-08-20 19:32:59', 0),
(99, 'Sej by Nisha Gupta', 'sej-by-nisha-gupta', 'sej-by-nisha-gupta', 1, 1, 0, 1, '2017-06-30 23:46:47', 1, '2017-07-20 21:20:53', 0),
(100, 'House This', 'house-this', 'house-this', 1, 1, 0, 1, '2017-06-30 23:47:01', 1, '2017-07-20 21:19:14', 0),
(101, 'Tijo', 'tijo', 'tijo', 0, 0, 0, 1, '2018-03-08 04:25:53', 1, '2018-03-07 17:25:53', 0),
(104, 'test', 'test', 'test', 0, 0, 0, 1, '2018-07-11 13:02:21', 1, '2018-07-11 07:32:21', 0);

-- --------------------------------------------------------

--
-- Stand-in structure for view `product_brand_categories`
--
CREATE TABLE IF NOT EXISTS `product_brand_categories` (
`category_id` smallint(6) unsigned
,`brand_id` smallint(6) unsigned
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `product_browser_path_by_categories`
--
CREATE TABLE IF NOT EXISTS `product_browser_path_by_categories` (
`category_id` smallint(6) unsigned
,`url` varchar(354)
);
-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE IF NOT EXISTS `product_categories` (
  `category_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `category_code` varchar(3) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `url_str` varchar(200) NOT NULL,
  `replacement_service_policy_id` tinyint(3) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `url_str` (`url_str`),
  UNIQUE KEY `category_code` (`category_code`),
  UNIQUE KEY `category` (`category`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1412 ;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`category_id`, `category_code`, `category`, `url_str`, `replacement_service_policy_id`, `status`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 'all', 'All', 'all', 1, 1, '2017-07-04 15:12:07', '2017-07-12 01:04:20', 1, 0),
(2, '90z', 'Audio & Video', 'audio-video', 1, 1, '2017-07-06 00:27:37', '2017-07-20 08:08:49', NULL, 0),
(3, 'fxa', 'Automation & Robotics', 'automation-robotics', 1, 1, '2017-07-06 00:27:37', '2017-07-20 08:08:57', NULL, 0),
(4, 'b0n', 'Automotive', 'automotive', 1, 1, '2017-07-06 00:27:37', '2017-07-20 08:08:57', NULL, 0),
(5, '0lc', 'Baby Care', 'baby-care', 1, 1, '2017-07-06 00:27:37', '2017-07-20 08:08:57', NULL, 0),
(6, 'omy', 'Bags, Wallets & Belts', 'bags-wallets-belts', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(7, 'vcz', 'Beauty and Personal Care', 'beauty-and-personal-care', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(8, 'kmx', 'Books', 'books', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(9, 'rnp', 'Cameras & Accessories', 'cameras-accessories', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(10, 'bvu', 'Clothing', 'clothing', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(11, 'izg', 'Computers', 'computers', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(12, 'cvw', 'E-Learning', 'e-learning', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(13, '3w5', 'Eyewear', 'eyewear', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(14, 'jsq', 'Food & Nutrition', 'food-nutrition', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(15, '5v0', 'Footwear', 'footwear', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(16, 'j8c', 'Furniture', 'furniture', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(17, 'j8x', 'Gaming', 'gaming', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(18, '9h5', 'Grocery', 'grocery', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(19, 'fjv', 'Health & Personal Care Appliances', 'health-personal-care-appliances', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(20, 'vqh', 'Home & Kitchen', 'home-kitchen', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(21, 'qn9', 'Home Decor & Festive Needs', 'home-decor-festive-needs', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(22, 'hwf', 'Home Entertainment', 'home-entertainment', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(23, 'i0p', 'Home Furnishing', 'home-furnishing', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(24, 'o3z', 'Home Improvement', 'home-improvement', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(25, 'cbm', 'Household Supplies', 'household-supplies', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(26, '5xi', 'Jewellery', 'jewellery', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(27, '5xe', 'Kids Accessories', 'kids-accessories', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(28, 'vaq', 'Kitchen & Dining', 'kitchen-dining', 1, 1, '2017-07-06 00:27:38', '2017-07-20 08:08:57', NULL, 0),
(29, '8hb', 'Mobiles & Accessories', 'mobiles-accessories', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(30, 'fby', 'MP3 Downloads', 'mp3-downloads', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(31, 'zc8', 'Music', 'music', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(32, 'dha', 'Music, Movies & Posters', 'music-movies-posters', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(33, 'uci', 'Musical Instruments', 'musical-instruments', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(34, 'mox', 'Packaging', 'packaging', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(35, 'a9q', 'Pens & Stationery', 'pens-stationery', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(36, 'qke', 'Pet Supplies', 'pet-supplies', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(37, 'fuj', 'Special', 'special', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(38, '1qv', 'Sports & Fitness', 'sports-fitness', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(39, 'yd6', 'Tools & Hardware', 'tools-hardware', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(40, 'xzd', 'Toys', 'toys', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(41, 'mkd', 'Vas', 'vas', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(42, 'blw', 'Watches', 'watches', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(43, 'c34', 'Wearable Smart Devices', 'wearable-smart-devices', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(44, 'jmy', 'Audio Accessories', 'audio-accessories', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(45, 'kje', 'Audio Players & Recorders', 'audio-players-recorders', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(46, 'uyi', 'Professional Audio Systems', 'professional-audio-systems', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(47, 'e3r', 'Speakers', 'speakers', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(48, 'kvg', 'Television', 'television', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(49, 'y0h', 'Video Accessories', 'video-accessories', 1, 1, '2017-07-06 00:27:39', '2017-07-20 08:08:57', NULL, 0),
(50, 'dyq', 'Video Players & Recorders', 'video-players-recorders', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(51, 'ins', 'Controllers', 'controllers', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(52, 'jnw', 'Drones', 'drones', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(53, 't48', 'Sensor Security Systems', 'sensor-security-systems', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(54, '04l', 'Sensors & Alarms', 'sensors-alarms', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(55, 'nbj', 'Smart Door Locks', 'smart-door-locks', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(56, 'ct3', 'Smart Lighting', 'smart-lighting', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(57, 'o4z', 'Smart Monitoring Systems', 'smart-monitoring-systems', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(58, 'n2a', 'Smart Pens', 'smart-pens', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(59, '2wr', 'Smart Switches', 'smart-switches', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(60, 'nxc', 'Surveillance Devices', 'surveillance-devices', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(61, 'uws', 'Automobile', 'automobile', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(62, 'd34', 'Automotive Combo', 'automotive-combo', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(63, 'p02', 'Automotive Services', 'automotive-services', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(64, 'gpy', 'Car & Bike Accessories', 'car-bike-accessories', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(65, 'w87', 'Baby Bath & Skin', 'baby-bath-skin', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(66, '39o', 'Baby Bedding', 'baby-bedding', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(67, 'i45', 'Baby Toys', 'baby-toys', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(68, '5hz', 'Diapering & Potty Training', 'diapering-potty-training', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(69, '78i', 'Strollers & Activity Gear', 'strollers-activity-gear', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(70, '9h8', 'Bags & Backpacks', 'bags-backpacks', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(71, 'drm', 'Bags, Belts & Wallets Combo', 'bags-belts-wallets-combo', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(72, 'sh4', 'Belts', 'belts', 1, 1, '2017-07-06 00:27:40', '2017-07-20 08:08:57', NULL, 0),
(73, 'q8m', 'Carabiners', 'carabiners', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(74, '7d7', 'Garment Covers', 'garment-covers', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(75, '6gn', 'Handbags & Clutches', 'handbags-clutches', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(76, 'x65', 'Key Chains', 'key-chains', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(77, 'a3y', 'Luggage & Travel', 'luggage-travel', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(78, '23t', 'Tie Pins', 'tie-pins', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(79, 'odm', 'Wallets & Clutches', 'wallets-clutches', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(80, 'oec', 'Body and Skin Care', 'body-and-skin-care', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(81, 'tkq', 'Fragrances', 'fragrances', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(82, 'oy1', 'Hair Care', 'hair-care', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(83, 'rlv', 'Makeup', 'makeup', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(84, 'y3s', 'Men''s Grooming', 'men-s-grooming', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(85, 'yur', 'Educational and Professional Books', 'educational-and-professional-books', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(86, 'i2o', 'Fiction & Non-Fiction Books', 'fiction-non-fiction-books', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(87, 'fgx', 'Indian Writing Books', 'indian-writing-books', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(88, '7u2', 'Philosophy Books', 'philosophy-books', 1, 1, '2017-07-06 00:27:41', '2017-07-20 08:08:57', NULL, 0),
(89, 'jkf', 'Camera Accessories', 'camera-accessories', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(90, '6zp', 'Cameras', 'cameras', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(91, 'dm0', 'Kids'' Clothing', 'kids-clothing', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(92, '8gs', 'Men''s Clothing', 'men-s-clothing', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(93, 's6u', 'Women''s Clothing', 'women-s-clothing', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(94, 'il6', 'Laptop Accessories', 'laptop-accessories', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(95, 'a64', 'Laptops', 'laptops', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(96, '0zi', 'Certification & Professional Courses', 'certification-professional-courses', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(97, 'a4n', 'Educational Media', 'educational-media', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(98, 'znr', 'Online Courses', 'online-courses', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(99, 'jd5', 'Online Tests', 'online-tests', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(100, 'tc1', 'Reference', 'reference', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(101, '7ea', 'Contact Lenses', 'contact-lenses', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(102, 'st3', 'Frames', 'frames', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(103, '41g', 'Nose Pads', 'nose-pads', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(104, 'xyj', 'Baby Food', 'baby-food', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(105, '5lm', 'Bakery & Baking Essentials', 'bakery-baking-essentials', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(106, 'p95', 'Beverages', 'beverages', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(107, 'bes', 'Canned Food', 'canned-food', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(108, '6qw', 'Confectioneries & Sweets', 'confectioneries-sweets', 1, 1, '2017-07-06 00:27:42', '2017-07-20 08:08:57', NULL, 0),
(109, '9p4', 'Dairy Products', 'dairy-products', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(110, '3kg', 'Edible Oils & Ghee', 'edible-oils-ghee', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(111, '60g', 'Food & Nutrition Combo', 'food-nutrition-combo', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(112, '3c9', 'Fruits & Vegetables', 'fruits-vegetables', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(113, 'al1', 'Jams, Spreads & Honey', 'jams-spreads-honey', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(114, '4yx', 'Nuts, Dry Fruits & Combos', 'nuts-dry-fruits-combos', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(115, 'ifv', 'Poultry, Meat & Seafood', 'poultry-meat-seafood', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(116, 'n9d', 'Ready to Cook & Eat', 'ready-to-cook-eat', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(117, 'jm6', 'Spices, Condiments & Sauces', 'spices-condiments-sauces', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(118, 'ko7', 'Kids'' & Infant Footwear', 'kids-infant-footwear', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(119, 'q3j', 'Men''s Footwear', 'men-s-footwear', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(120, '6wh', 'Shoe Accessories', 'shoe-accessories', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(121, 'tw8', 'Women''s Footwear', 'women-s-footwear', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(122, 'wq0', 'Bedroom Furniture', 'bedroom-furniture', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(123, 'jwz', 'Living Room Furniture', 'living-room-furniture', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(124, 'kem', 'Seating Furniture', 'seating-furniture', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(125, 'ups', 'Storage Furniture', 'storage-furniture', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(126, 'mz5', 'Study & Home Office Furniture', 'study-home-office-furniture', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(127, 'zm9', 'Bean Bags', 'bean-bags', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(128, 'eci', 'Beds', 'beds', 1, 1, '2017-07-06 00:27:43', '2017-07-20 08:08:57', NULL, 0),
(129, 'u3r', 'Cabinets', 'cabinets', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(130, 'ul5', 'Furniture Accessories', 'furniture-accessories', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(131, 'udw', 'Shoe Rack', 'shoe-rack', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(132, '1p0', 'Showcases', 'showcases', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(133, 'kpv', 'Sofa Beds', 'sofa-beds', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(134, '0xp', 'Sofas', 'sofas', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(135, 'ilu', 'Tables', 'tables', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(136, 'x7y', 'TV Units & Cabinets', 'tv-units-cabinets', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(137, 'ovz', 'Wardrobes', 'wardrobes', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(138, 'l9i', 'Games', 'games', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(139, 'i8v', 'Gaming Accessories', 'gaming-accessories', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(140, 'av2', 'Gaming Components', 'gaming-components', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(141, 'f8m', 'Gaming Consoles', 'gaming-consoles', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(142, 'sap', 'Gaming Laptops', 'gaming-laptops', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(143, 'vn9', 'Membership Cards', 'membership-cards', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(144, 'r9e', 'Household Care', 'household-care', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(145, 'nvs', 'Packaged Food', 'packaged-food', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(146, '8dy', 'Personal & Baby Care', 'personal-baby-care', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(147, '6tq', 'Snacks & Beverages', 'snacks-beverages', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(148, 'idf', 'Staples', 'staples', 1, 1, '2017-07-06 00:27:44', '2017-07-20 08:08:57', NULL, 0),
(149, 'li3', 'Health Care', 'health-care', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(150, 's0e', 'Personal Care Appliances', 'personal-care-appliances', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(151, 'b4n', 'Home Appliances', 'home-appliances', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(152, '7r3', 'Kitchen Appliances', 'kitchen-appliances', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(153, '2af', 'Decorative Lighting & Lamps', 'decorative-lighting-lamps', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(154, 'xap', 'Garden & Leisure', 'garden-leisure', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(155, 'zeq', 'Home Decor Combo', 'home-decor-combo', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(156, 'wgp', 'Photo Frames & Albums', 'photo-frames-albums', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(157, '0ug', 'Showpiece', 'showpiece', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(158, 'j71', 'Wall Decor & Clocks', 'wall-decor-clocks', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(159, '46b', 'Audio Players', 'audio-players', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(160, 'dz0', 'Home Audio', 'home-audio', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(161, '3d6', 'MP3 players/Ipods Accessories', 'mp3-players-ipods-accessories', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(162, '291', 'Televisions', 'televisions', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(163, '06q', 'Video Players & Accessories', 'video-players-accessories', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(164, 'd4p', 'Bath Linen', 'bath-linen', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(165, 'i3a', 'Bed Linen', 'bed-linen', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(166, '8th', 'Building & Construction', 'building-construction', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(167, '4jm', 'Curtains & Accessories', 'curtains-accessories', 1, 1, '2017-07-06 00:27:45', '2017-07-20 08:08:57', NULL, 0),
(168, '6ze', 'Cushions, Pillows & Covers', 'cushions-pillows-covers', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(169, '9rf', 'Floor Coverings', 'floor-coverings', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(170, '50k', 'Living Room Furnishing', 'living-room-furnishing', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(171, '89d', 'Pillows & Pillow Covers', 'pillows-pillow-covers', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(172, 'u7p', 'Upholstery Fabric', 'upholstery-fabric', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(173, '6an', 'Alternate Energy', 'alternate-energy', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(174, '2o5', 'Electricals', 'electricals', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(175, 'i7k', 'Hardware', 'hardware', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(176, '3lv', 'Home Automation & Safety', 'home-automation-safety', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(177, 'c70', 'Home Storage & Organization', 'home-storage-organization', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(178, '9n7', 'Paint Equipment & Supplies', 'paint-equipment-supplies', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(179, 'nq8', 'Plumbing Supplies', 'plumbing-supplies', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(180, 'xqx', 'Sanitaryware', 'sanitaryware', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(181, 'sry', 'Tools', 'tools', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(182, 'yji', 'Home Cleaning', 'home-cleaning', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(183, 'ae8', 'Kitchen Supplies', 'kitchen-supplies', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(184, 'j9d', 'Laundry Supplies', 'laundry-supplies', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(185, '2oq', 'Artificial Jewellery', 'artificial-jewellery', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(186, 'epa', 'Digitally Crafted Jewellery', 'digitally-crafted-jewellery', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(187, '0ng', 'Gemstones, Coins & Bars', 'gemstones-coins-bars', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(188, '7so', 'Gift Vouchers', 'gift-vouchers', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(189, '1qs', 'Precious Articles', 'precious-articles', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(190, 'lks', 'Precious Jewellery', 'precious-jewellery', 1, 1, '2017-07-06 00:27:46', '2017-07-20 08:08:57', NULL, 0),
(191, '8zu', 'Silver Jewellery', 'silver-jewellery', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(192, 'njx', 'Kids Bags, Belts & Wallets', 'kids-bags-belts-wallets', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(193, '48r', 'School Supplies', 'school-supplies', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(194, '21s', 'Bakeware', 'bakeware', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(195, 'bft', 'Containers & Bottles', 'containers-bottles', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(196, '97n', 'Cookware', 'cookware', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(197, '9vt', 'Kitchen Tools', 'kitchen-tools', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(198, 'sgl', 'Lighting', 'lighting', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(199, '7i9', 'Outdoor Cooking', 'outdoor-cooking', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(200, 'osu', 'Pressure Cookers & Pans', 'pressure-cookers-pans', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(201, 's1l', 'Stoves & Hobs', 'stoves-hobs', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(202, 'ety', 'Mobile Accessories', 'mobile-accessories', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(203, '20j', 'Mobiles', 'mobiles', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(204, 'a43', 'Power Bank Skins', 'power-bank-skins', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(205, 'of0', 'Tablets', 'tablets', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(206, 'ug6', 'Albums', 'albums', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(207, 'g9m', 'Tracks', 'tracks', 1, 1, '2017-07-06 00:27:47', '2017-07-20 08:08:57', NULL, 0),
(208, 'u8b', 'Blues', 'blues', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(209, 'bcg', 'Children & Teenagers', 'children-teenagers', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(210, 'pc1', 'MP3', 'mp3', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(211, 'ogv', 'Qawwali', 'qawwali', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(212, 'mt7', 'Sufi', 'sufi', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(213, 'mld', 'VCD', 'vcd', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(214, 'zur', 'World Music', 'world-music', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(215, 'ag9', 'Movies & TV Show', 'movies-tv-show', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(216, 'f3n', 'Posters', 'posters', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(217, '1gn', 'Keys & Synthesizers', 'keys-synthesizers', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(218, '9v8', 'String Instruments', 'string-instruments', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(219, 'i93', 'Wind Instruments', 'wind-instruments', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(220, 'hma', 'Flipkart Carton Box', 'flipkart-carton-box', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(221, 'bof', 'Flipkart Security Bag', 'flipkart-security-bag', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(222, 'gum', 'Flipkart Tapes', 'flipkart-tapes', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(223, 'xcn', 'Diaries & Notebooks', 'diaries-notebooks', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(224, 'l32', 'Office Equipments', 'office-equipments', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(225, 'le8', 'Cleaning Accessories', 'cleaning-accessories', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(226, 'nsr', 'Grooming', 'grooming', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(227, 'ue5', 'Habitat', 'habitat', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(228, 'pk1', 'Ornamental Accessories', 'ornamental-accessories', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(229, 'xoi', 'Pet Food & Health Supplies', 'pet-food-health-supplies', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(230, 'p1m', 'Pet Gear', 'pet-gear', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(231, '97r', 'Festivals', 'festivals', 1, 1, '2017-07-06 00:27:48', '2017-07-20 08:08:57', NULL, 0),
(232, '3to', 'Cycling', 'cycling', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(233, 'zxw', 'Exercise & Fitness', 'exercise-fitness', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(234, 'w2p', 'Racquet Sports', 'racquet-sports', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(235, 'j4s', 'Sports & Fitness Combo', 'sports-fitness-combo', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(236, 'cyj', 'Team Sports', 'team-sports', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(237, 'ceo', 'Action Figures', 'action-figures', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(238, '7mb', 'Art & Craft Toys', 'art-craft-toys', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(239, '21p', 'Baggo Boards', 'baggo-boards', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(240, '68w', 'Board Games', 'board-games', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(241, 'jx8', 'Card Games', 'card-games', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(242, 'keq', 'Dolls & Doll Houses', 'dolls-doll-houses', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(243, 'std', 'Gag Toys', 'gag-toys', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(244, 'uod', 'Hobby Kits', 'hobby-kits', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(245, '2d7', 'Learning & Educational Toys ', 'learning-educational-toys', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(246, 'u5o', 'Magic Springs', 'magic-springs', 1, 1, '2017-07-06 00:27:49', '2017-07-20 08:08:57', NULL, 0),
(247, 'sbu', 'Musical Instruments & Toys', 'musical-instruments-toys', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(248, 'lfe', 'Outdoor Toys', 'outdoor-toys', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(249, 'xhm', 'Party Supplies', 'party-supplies', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(250, '7k7', 'Puppets', 'puppets', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(251, 'a9j', 'Puzzles', 'puzzles', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(252, '1yo', 'Puzzles & Board Games', 'puzzles-board-games', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(253, 'zuj', 'Remote Control Toys', 'remote-control-toys', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(254, '4pz', 'Slime & Putty Toys', 'slime-putty-toys', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(255, 'jlu', 'Soft Toys', 'soft-toys', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(256, 'bwq', 'Toy Cars, Trains & Bikes', 'toy-cars-trains-bikes', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(257, '674', 'Toy Sport', 'toy-sport', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(258, 'nve', 'Wind Spinner Toys', 'wind-spinner-toys', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(259, '5wo', 'Camera Rental Services', 'camera-rental-services', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(260, 'cel', 'Digital', 'digital', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(261, 'tyb', 'Photography Workshops', 'photography-workshops', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(262, '187', 'Photoshoot Services', 'photoshoot-services', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(263, 'gb6', 'Physical Gift Voucher', 'physical-gift-voucher', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(264, 'rwj', 'Clocks', 'clocks', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(265, '07j', 'Pocket Watches', 'pocket-watches', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(266, 'nhz', 'Watch Accessories', 'watch-accessories', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(267, '43a', 'Weather Stations', 'weather-stations', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(268, 'brk', 'Wrist Watches', 'wrist-watches', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(269, 'oqb', 'Bluetooth Hats', 'bluetooth-hats', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(270, 'k3d', 'Bluetooth Item Finders', 'bluetooth-item-finders', 1, 1, '2017-07-06 00:27:50', '2017-07-20 08:08:57', NULL, 0),
(271, 'yuh', 'Smart Bands', 'smart-bands', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(272, 'rdr', 'Smart Footwears', 'smart-footwears', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(273, 'c2t', 'Smart Glasses', 'smart-glasses', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(274, '7nt', 'Smart Gloves', 'smart-gloves', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(275, 'zfu', 'Smart Headphones', 'smart-headphones', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(276, '64d', 'Smart Helmets', 'smart-helmets', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(277, 'xmv', 'Smart Jackets', 'smart-jackets', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(278, 'bdk', 'Smart Pendants', 'smart-pendants', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(279, '54z', 'Smart Rings', 'smart-rings', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(280, 'u6l', 'Smart Trackers', 'smart-trackers', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(281, 'iy9', 'Smart Watches', 'smart-watches', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(282, 'vlp', 'VR Camera', 'vr-camera', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(283, 'ca1', 'VR Controllers', 'vr-controllers', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(284, 'f19', 'Wearable Accessories', 'wearable-accessories', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(285, 'xpb', 'Cables', 'cables', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(286, 'hjg', 'Remote Controllers', 'remote-controllers', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(287, '0nj', 'Amplifier Receivers', 'amplifier-receivers', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(288, 'ybt', 'Boom Box', 'boom-box', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(289, '83e', 'DVD/Blueray Players', 'dvd-blueray-players', 1, 1, '2017-07-06 00:27:51', '2017-07-20 08:08:57', NULL, 0),
(290, 'k95', 'FM Radio', 'fm-radio', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(291, '12t', 'Karaoke Players', 'karaoke-players', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(292, '16s', 'Media Streaming Devices', 'media-streaming-devices', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(293, 'p19', 'Voice Recorder', 'voice-recorder', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(294, 'x52', 'DJ Controllers', 'dj-controllers', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(295, '01r', 'Public Address Systems', 'public-address-systems', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(296, 'do8', 'Sound Mixers', 'sound-mixers', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(297, 'uwh', '2.0 Speakers', '2-0-speakers', 1, 1, '2017-07-06 00:27:52', '2017-07-12 01:08:51', NULL, 0),
(298, '24y', '2.1 Speakers', '2-1-speakers', 1, 1, '2017-07-06 00:27:52', '2017-07-12 01:08:58', NULL, 0),
(299, '5mq', '4.1 Speakers', '4-1-speakers', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(300, 'ckh', '5.1 Speakers', '5-1-speakers', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(301, 'hbx', 'Home Theater', 'home-theater', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(302, 'k6a', 'Mobile & Tablet Speakers', 'mobile-tablet-speakers', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(303, 'dpz', 'Soundbar', 'soundbar', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(304, 'w41', 'Video Glasses', 'video-glasses', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(305, 'd7g', 'Beacon', 'beacon', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(306, 'ewa', 'Door & Window Alarms', 'door-window-alarms', 1, 1, '2017-07-06 00:27:52', '2017-07-20 08:08:57', NULL, 0),
(307, '1qk', 'Garage Parking Sensors', 'garage-parking-sensors', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(308, 'sib', 'Gas Leak Detector', 'gas-leak-detector', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(309, 'ts6', 'Leak & Water Sensor', 'leak-water-sensor', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(310, '2mw', 'Metal & Voltage Detectors', 'metal-voltage-detectors', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(311, 'm8h', 'Moisture Meters', 'moisture-meters', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(312, '8nb', 'Motion Sensors', 'motion-sensors', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(313, 'bks', 'Smoke & Fire Alarm', 'smoke-fire-alarm', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(314, 'max', 'Baby Monitors', 'baby-monitors', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(315, 'dq2', 'Security Camera System Accessories', 'security-camera-system-accessories', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(316, 't1q', 'Security Cameras', 'security-cameras', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(317, '4xj', 'Video Door Phone', 'video-door-phone', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(318, 'xt3', 'Electric Scooter Boards', 'electric-scooter-boards', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(319, 'ecf', 'Four Wheeler', 'four-wheeler', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(320, '3kn', 'Two Wheeler', 'two-wheeler', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(321, 'nai', 'Vehicle Care & Repair Codes', 'vehicle-care-repair-codes', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(322, 'vpk', 'Bike Essentials', 'bike-essentials', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(323, '8ma', 'Car & Bike Breakdown Equipments', 'car-bike-breakdown-equipments', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(324, '852', 'Car & Bike Care', 'car-bike-care', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(325, 'n59', 'Car & Bike Lighting', 'car-bike-lighting', 1, 1, '2017-07-06 00:27:53', '2017-07-20 08:08:57', NULL, 0),
(326, 'nhl', 'Car & Bike Styling', 'car-bike-styling', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(327, 'zl1', 'Car Electronics & Accessories', 'car-electronics-accessories', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(328, 'z8u', 'Car Interior & Exterior', 'car-interior-exterior', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(329, 'aki', 'Helmets & Riding Gear', 'helmets-riding-gear', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(330, 'dqj', 'Lubricants & Oils', 'lubricants-oils', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(331, 'rfw', 'Spares & Performance Parts', 'spares-performance-parts', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(332, '3tz', 'Tyres', 'tyres', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(333, 'cw2', 'Baby Bath Towels', 'baby-bath-towels', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(334, 'i69', 'Baby Bath Tubs', 'baby-bath-tubs', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(335, '9yx', 'Baby Body Wash', 'baby-body-wash', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(336, 'tru', 'Baby Powder', 'baby-powder', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(337, 'suh', 'Baby Powder Puffs', 'baby-powder-puffs', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(338, '3b2', 'Baby Shampoo', 'baby-shampoo', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(339, 'p6l', 'Baby Shower Caps', 'baby-shower-caps', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(340, 'mb2', 'Baby Soaps', 'baby-soaps', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(341, '9am', 'Bath Essential Oils', 'bath-essential-oils', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(342, '8ab', 'Bath Robes', 'bath-robes', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(343, 'gme', 'Bath Seats', 'bath-seats', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(344, 'd9r', 'Bath Sponges', 'bath-sponges', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(345, 'npl', 'Soap Cases', 'soap-cases', 1, 1, '2017-07-06 00:27:54', '2017-07-20 08:08:57', NULL, 0),
(346, 'l1a', 'Baby Bedding Sets', 'baby-bedding-sets', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(347, 'kei', 'Baby Beds', 'baby-beds', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(348, 'tnl', 'Baby Bedsheets', 'baby-bedsheets', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(349, 'ylc', 'Baby Blankets', 'baby-blankets', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(350, 'aih', 'Baby Mats', 'baby-mats', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(351, 'l3x', 'Baby Mattresses', 'baby-mattresses', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(352, 'uv3', 'Baby Mosquito Nets', 'baby-mosquito-nets', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(353, 'fh6', 'Baby Pillows', 'baby-pillows', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(354, 'uqs', 'Baby Sleeping Bags', 'baby-sleeping-bags', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(355, 'wi2', 'Bunting Bags', 'bunting-bags', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(356, 'l3n', 'Baby Rattles', 'baby-rattles', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(357, '486', 'Bath Toys', 'bath-toys', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(358, 'c4i', 'Crib Toys & Play Gyms', 'crib-toys-play-gyms', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(359, '58q', 'Early Development Toys', 'early-development-toys', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(360, 'ufc', 'Musical Toys', 'musical-toys', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(361, 'a21', 'Push & Pull Along', 'push-pull-along', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(362, 'bgf', 'Rideons', 'rideons', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(363, 'e9f', 'Stacking Toys', 'stacking-toys', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(364, 'vjo', 'Diaper Bag Dispensers', 'diaper-bag-dispensers', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(365, 'zqw', 'Baby Car Seats', 'baby-car-seats', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(366, 'r0t', 'Baby Chair Belts', 'baby-chair-belts', 1, 1, '2017-07-06 00:27:55', '2017-07-20 08:08:57', NULL, 0),
(367, 'g32', 'Baby Chairs', 'baby-chairs', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(368, 'hmo', 'Baby Cots & Bassinets', 'baby-cots-bassinets', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(369, 'ilg', 'Baby Walkers', 'baby-walkers', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(370, '9fg', 'Bouncers, Rockers & Swings', 'bouncers-rockers-swings', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(371, 'dry', 'Carriers & Carry Cots', 'carriers-carry-cots', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(372, 'ybx', 'Cribs & Cradles', 'cribs-cradles', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(373, 'uet', 'Play Gyms & Crib Toys', 'play-gyms-crib-toys', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(374, 'rbn', 'Shopping Cart Covers', 'shopping-cart-covers', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(375, 'tv3', 'Stroller Pram Pads', 'stroller-pram-pads', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(376, '2sd', 'Stroller Rain Covers', 'stroller-rain-covers', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(377, 'cfo', 'Strollers & Prams', 'strollers-prams', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(378, 'uv6', 'Backpacks', 'backpacks', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(379, 'zet', 'Cross Body Bags', 'cross-body-bags', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(380, '2nm', 'Gym Bags', 'gym-bags', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(381, 'yg7', 'Laptop Bags', 'laptop-bags', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(382, 'kh0', 'Messenger Bags', 'messenger-bags', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(383, 'iw5', 'Waist Bags', 'waist-bags', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(384, 'q5n', 'Wallets', 'wallets', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(385, '3sy', 'Backpack Handbags', 'backpack-handbags', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(386, 'g3q', 'Clutches', 'clutches', 1, 1, '2017-07-06 00:27:56', '2017-07-20 08:08:57', NULL, 0),
(387, 'jdb', 'Clutches & Wallets', 'clutches-wallets', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(388, 'fs1', 'Coin Purses', 'coin-purses', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(389, 'k78', 'Cosmetic Bags', 'cosmetic-bags', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(390, 'h1v', 'Handbags', 'handbags', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(391, 'qdu', 'Potlis', 'potlis', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(392, 'vrw', 'Pouches and Potlis', 'pouches-and-potlis', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(393, 'xk0', 'Shoulder Bags', 'shoulder-bags', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(394, '0ga', 'Sling Bags', 'sling-bags', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(395, 'xob', 'Soiled Garment Bags', 'soiled-garment-bags', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(396, 'zex', 'Totes', 'totes', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(397, 's7q', 'Vanity Bags', 'vanity-bags', 1, 1, '2017-07-06 00:27:57', '2017-07-20 08:08:57', NULL, 0),
(398, '7sk', 'Wristlets', 'wristlets', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(399, '6lb', 'Briefcases', 'briefcases', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(400, 'y34', 'Duffel Bags', 'duffel-bags', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(401, 'pio', 'Luggage Covers', 'luggage-covers', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(402, 'lgb', 'Rucksacks', 'rucksacks', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(403, '7zh', 'Small Travel Bags', 'small-travel-bags', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(404, 'z0k', 'Suitcases', 'suitcases', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(405, 'bk7', 'Travel Accessories', 'travel-accessories', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(406, '8wp', 'Money Clip', 'money-clip', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(407, 'n9o', 'Travel Document Holders & Card Holders', 'travel-document-holders-card-holders', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(408, 'hvi', 'Wallet Emblems', 'wallet-emblems', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(409, 'das', 'Body and Skin Accessories', 'body-and-skin-accessories', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(410, '1oj', 'Body and Skin Treatments', 'body-and-skin-treatments', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(411, '1uq', 'Body Care', 'body-care', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(412, 'w0b', 'Combo & Kits', 'combo-kits', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(413, 'ea4', 'Eye Care', 'eye-care', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(414, '629', 'Face Care', 'face-care', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(415, 'lkh', 'Foot Care', 'foot-care', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(416, 'nf0', 'Hand Care', 'hand-care', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(417, '3mh', 'Lip Care', 'lip-care', 1, 1, '2017-07-06 00:27:58', '2017-07-20 08:08:57', NULL, 0),
(418, 'mer', 'Oral Care', 'oral-care', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(419, 'xj6', 'Air Fresheners', 'air-fresheners', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(420, '1tm', 'Deodorants', 'deodorants', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(421, '0w9', 'Gift Sets', 'gift-sets', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(422, '8kc', 'Perfumes', 'perfumes', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(423, '8zk', 'Conditioners', 'conditioners', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(424, 'xv1', 'Hair Care Accessories', 'hair-care-accessories', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(425, '1t5', 'Hair Colors', 'hair-colors', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(426, 'bhq', 'Hair Fragrances', 'hair-fragrances', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(427, '0b4', 'Hair Oils', 'hair-oils', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(428, 'y51', 'Hair Serums', 'hair-serums', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(429, 'g53', 'Hair Styling', 'hair-styling', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(430, 'vli', 'Hair Treatments', 'hair-treatments', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(431, 'yu7', 'Hair Volumizers', 'hair-volumizers', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(432, 't4e', 'Henna', 'henna', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(433, '90d', 'Shampoos', 'shampoos', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(434, 'jfk', 'Bindis', 'bindis', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(435, '4os', 'Body', 'body', 1, 1, '2017-07-06 00:27:59', '2017-07-20 08:08:57', NULL, 0),
(436, 'x5u', 'Eyes', 'eyes', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(437, 'x4t', 'Face', 'face', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(438, 'gza', 'Lips', 'lips', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(439, 'zih', 'Makeup Accessories', 'makeup-accessories', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(440, '8zg', 'Makeup Combos', 'makeup-combos', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(441, 'k3n', 'Makeup Kits', 'makeup-kits', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(442, 'atm', 'Makeup Removers', 'makeup-removers', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(443, '2xy', 'Nails', 'nails', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(444, 'n3p', 'Sindoor', 'sindoor', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(445, '8xj', 'Vanity Boxes', 'vanity-boxes', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(446, '9rc', 'Aftershave', 'aftershave', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(447, 'rg8', 'Bath and Spa', 'bath-and-spa', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(448, 'w60', 'Men''s Grooming Accessories', 'men-s-grooming-accessories', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(449, 'xwi', 'Razors and Blades', 'razors-and-blades', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(450, 'qmt', 'Shaving Creams, Foams, and Gels', 'shaving-creams-foams-and-gels', 1, 1, '2017-07-06 00:28:00', '2017-07-20 08:08:57', NULL, 0),
(451, 'azf', 'Academic Texts Books', 'academic-texts-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(452, 'qpu', 'Computers & Internet Books', 'computers-internet-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0);
INSERT INTO `product_categories` (`category_id`, `category_code`, `category`, `url_str`, `replacement_service_policy_id`, `status`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(453, '0go', 'Entrance Exams Preparation Books', 'entrance-exams-preparation-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(454, 'mgt', 'Medical Books Books', 'medical-books-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(455, 'aiy', 'School Books', 'school-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(456, 'g3c', 'Art, Architecture & Design Books', 'art-architecture-design-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(457, 'ult', 'Biographies & Autobiographies Books', 'biographies-autobiographies-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(458, '5pz', 'Business, Investing and Management Books', 'business-investing-and-management-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(459, 'xam', 'Children Books', 'children-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(460, 't8u', 'Coffee Table Books', 'coffee-table-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(461, '8tz', 'Comics & Graphic Novels Books', 'comics-graphic-novels-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(462, '9j9', 'Health & Fitness Books', 'health-fitness-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(463, '6f9', 'History and Politics Books', 'history-and-politics-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(464, 'q1x', 'Literature & Fiction Books', 'literature-fiction-books', 1, 1, '2017-07-06 00:28:01', '2017-07-20 08:08:57', NULL, 0),
(465, 'dkf', 'Teens Books', 'teens-books', 1, 1, '2017-07-06 00:28:02', '2017-07-20 08:08:57', NULL, 0),
(466, 'njf', 'Academic and Professional Books', 'academic-and-professional-books', 1, 1, '2017-07-06 00:28:02', '2017-07-20 08:08:57', NULL, 0),
(467, 'r4u', 'Children & Teens Books', 'children-teens-books', 1, 1, '2017-07-06 00:28:02', '2017-07-20 08:08:57', NULL, 0),
(468, 'kt9', 'Others', 'others', 1, 1, '2017-07-06 00:28:02', '2017-07-20 08:08:57', NULL, 0),
(469, 'voy', 'Perinatology & Neonatology', 'perinatology-neonatology', 1, 1, '2017-07-06 00:28:02', '2017-07-20 08:08:57', NULL, 0),
(470, 'ip1', 'Religion & Spirituality Books', 'religion-spirituality-books', 1, 1, '2017-07-06 00:28:02', '2017-07-20 08:08:57', NULL, 0),
(471, '0dx', 'Travel Books', 'travel-books', 1, 1, '2017-07-06 00:28:02', '2017-07-20 08:08:57', NULL, 0),
(472, 'hlv', 'Aesthetics', 'aesthetics', 1, 1, '2017-07-06 00:28:02', '2017-07-20 08:08:57', NULL, 0),
(473, 'q4n', 'Criticism', 'criticism', 1, 1, '2017-07-06 00:28:02', '2017-07-20 08:08:57', NULL, 0),
(474, '7yo', 'Eastern Philosophy', 'eastern-philosophy', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(475, 'hat', 'Epistemology', 'epistemology', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(476, 'q8e', 'Essays', 'essays', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(477, '79e', 'Ethics & Moral Philosophy', 'ethics-moral-philosophy', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(478, '0dm', 'Free Will & Determinism', 'free-will-determinism', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(479, '739', 'Good & Evil', 'good-evil', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(480, 'v4j', 'Hermeneutics', 'hermeneutics', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(481, '5ha', 'History & Surveys', 'history-surveys', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(482, 'wvf', 'Language', 'language', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(483, '5ao', 'Logic', 'logic', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(484, 'ukt', 'Metaphysics', 'metaphysics', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(485, '109', 'Methodology', 'methodology', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(486, 'y5x', 'Mind & Body', 'mind-body', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(487, '4bp', 'Movements', 'movements', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(488, '6l1', 'Political', 'political', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(489, '1tq', 'Religious', 'religious', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(490, 'lp0', 'Social Philosophy', 'social-philosophy', 1, 1, '2017-07-06 00:28:03', '2017-07-20 08:08:57', NULL, 0),
(491, 'x83', 'Binocular Gauges', 'binocular-gauges', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(492, 'a9f', 'Binoculars & Optics', 'binoculars-optics', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(493, 'rb2', 'Camera Bags', 'camera-bags', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(494, 'b25', 'Camera Battery', 'camera-battery', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(495, 'cim', 'Camera Battery Grips', 'camera-battery-grips', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(496, '3m3', 'Camera Housings', 'camera-housings', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(497, 'q2t', 'Camera Lenses', 'camera-lenses', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(498, 'm2b', 'Camera Microphones', 'camera-microphones', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(499, 'g9t', 'Camera Remote Controls', 'camera-remote-controls', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(500, 'rfh', 'Cleaning tapes', 'cleaning-tapes', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(501, 'r0i', 'Collapsible Reflectors', 'collapsible-reflectors', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(502, '4wj', 'Diffusers', 'diffusers', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(503, '6tp', 'Digital Photo frames', 'digital-photo-frames', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(504, 'arz', 'Eyecups', 'eyecups', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(505, 'k50', 'Filters', 'filters', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(506, 'vk7', 'Flash Shoe Adapters', 'flash-shoe-adapters', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(507, 'iga', 'Flashes', 'flashes', 1, 1, '2017-07-06 00:28:04', '2017-07-20 08:08:57', NULL, 0),
(508, '9u7', 'Hidden Camera Detectors', 'hidden-camera-detectors', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(509, 'ti4', 'LED Lights', 'led-lights', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(510, '1ro', 'Lens Cap Holders', 'lens-cap-holders', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(511, 'mg8', 'Lens Caps', 'lens-caps', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(512, '1ag', 'Lens Cleaners', 'lens-cleaners', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(513, 'r7d', 'Lens Hoods', 'lens-hoods', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(514, 'hfs', 'Levelers', 'levelers', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(515, 'i1h', 'Matte Box', 'matte-box', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(516, 'u1f', 'Photo Printer', 'photo-printer', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(517, 'jdx', 'Photographer Vests', 'photographer-vests', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(518, 'br3', 'Quick Release Plates', 'quick-release-plates', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(519, 'the', 'Reflector Umbrellas', 'reflector-umbrellas', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(520, '0st', 'Selfie Flash', 'selfie-flash', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(521, 'vzo', 'Selfie Sticks & Monopods', 'selfie-sticks-monopods', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(522, 'z1n', 'Straps', 'straps', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(523, 'f7w', 'Studio Flash Lights', 'studio-flash-lights', 1, 1, '2017-07-06 00:28:05', '2017-07-20 08:08:57', NULL, 0),
(524, '1or', 'Sync Terminal Adapters', 'sync-terminal-adapters', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(525, 'maq', 'Telescopes', 'telescopes', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(526, '029', 'Underwater Camera Housing', 'underwater-camera-housing', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(527, '5iw', '3D Cameras', '3d-cameras', 1, 1, '2017-07-06 00:28:06', '2017-07-12 01:09:03', NULL, 0),
(528, 'te6', 'Advanced Point & Shoot', 'advanced-point-shoot', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(529, 'igp', 'Bird Cameras', 'bird-cameras', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(530, 'fr7', 'Camcorders', 'camcorders', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(531, 'l3y', 'DSLR & Mirrorless', 'dslr-mirrorless', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(532, 'f7a', 'Instant Cameras', 'instant-cameras', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(533, 'y72', 'Point and Shoot', 'point-and-shoot', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(534, 't4w', 'Sports & Action', 'sports-action', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(535, 'au9', 'Boys Wear', 'boys-wear', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(536, 'j4p', 'Girls Wear', 'girls-wear', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(537, 'ltc', 'Infants Wear', 'infants-wear', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(538, '1sp', ' Inner Wear & Sleep Wear', 'inner-wear-sleep-wear', 1, 1, '2017-07-06 00:28:06', '2017-07-12 01:08:40', NULL, 0),
(539, 'iai', 'Accessories & Combo Sets', 'accessories-combo-sets', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(540, 'y2d', 'Cargos, Shorts & 3/4ths', 'cargos-shorts-3-4ths', 1, 1, '2017-07-06 00:28:06', '2017-07-20 08:08:57', NULL, 0),
(541, 'kca', 'Ethnic Wear', 'ethnic-wear', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(542, 'q32', 'Fabrics', 'fabrics', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(543, '4bm', 'Jeans', 'jeans', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(544, 'sna', 'Shirts', 'shirts', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(545, 't8n', 'Sports Wear', 'sports-wear', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(546, 'ptq', 'Suits & Blazers', 'suits-blazers', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(547, 'tx1', 'Trousers', 'trousers', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(548, '74j', 'T-Shirts', 't-shirts', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(549, '5wz', 'Winter & Seasonal Wear', 'winter-seasonal-wear', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(550, 'odq', 'Accessories', 'accessories', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(551, 'fxh', 'Combo Sets', 'combo-sets', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(552, '1wc', 'Formal Wear', 'formal-wear', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(553, 'olc', 'Fusion Wear', 'fusion-wear', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(554, 'knu', 'Halloween Costumes', 'halloween-costumes', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(555, 'aon', 'Leggings & Jeggings', 'leggings-jeggings', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(556, 'kn9', 'Lingerie, Sleep & Swimwear', 'lingerie-sleep-swimwear', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(557, 'd6x', 'Maternity Wear', 'maternity-wear', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(558, 'd3d', 'Sports & Gym Wear', 'sports-gym-wear', 1, 1, '2017-07-06 00:28:07', '2017-07-20 08:08:57', NULL, 0),
(559, '3oe', 'Western Wear', 'western-wear', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(560, 'nj9', 'Anti Dust Plugs', 'anti-dust-plugs', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(561, 'nrc', 'Batteries', 'batteries', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(562, 'ni5', 'Blank Media', 'blank-media', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(563, 'x17', 'Cooling Pads/Cooling Stands', 'cooling-pads-cooling-stands', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(564, 'i0w', 'Digital Pens', 'digital-pens', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(565, 'eas', 'Docking Stations', 'docking-stations', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(566, 'edb', 'Extended Warranty', 'extended-warranty', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(567, 'i6u', 'External Hard Disks', 'external-hard-disks', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(568, 'xw8', 'Hard Disk Cases', 'hard-disk-cases', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(569, '1yw', 'Hard Disk Skins', 'hard-disk-skins', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(570, '8lr', 'Hard Drive Enclosures', 'hard-drive-enclosures', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(571, 'z7t', 'Headphones', 'headphones', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(572, '86r', 'Heat Sinks', 'heat-sinks', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(573, 'elu', 'Hinges', 'hinges', 1, 1, '2017-07-06 00:28:08', '2017-07-20 08:08:57', NULL, 0),
(574, '2jr', 'Internal Sound Cards', 'internal-sound-cards', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(575, '1lz', 'Keyboard Replacement Keys', 'keyboard-replacement-keys', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(576, '47j', 'Keyboard Skins', 'keyboard-skins', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(577, 'yf0', 'Keyboards', 'keyboards', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(578, 'g64', 'Laptop Adapters', 'laptop-adapters', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(579, 'rft', 'Laptop Bag Covers', 'laptop-bag-covers', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(580, 'xm3', 'Laptop Displays', 'laptop-displays', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(581, 'mab', 'Laptop Skins & Decals', 'laptop-skins-decals', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(582, 'hu1', 'Laser Pointers', 'laser-pointers', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(583, 'u96', 'Memory Cards', 'memory-cards', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(584, 'fmf', 'Monitor & TV Covers', 'monitor-tv-covers', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(585, 'sfo', 'Mouse', 'mouse', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(586, 'nha', 'Mouse Pads', 'mouse-pads', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(587, 'f2f', 'Number Pads', 'number-pads', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(588, 'xv5', 'OTG Adapters', 'otg-adapters', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(589, 'dmh', 'Pen Drives', 'pen-drives', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(590, '1h0', 'Power Banks', 'power-banks', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(591, '8cj', 'Presentation Remotes', 'presentation-remotes', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(592, '6i6', 'Printer Covers', 'printer-covers', 1, 1, '2017-07-06 00:28:09', '2017-07-20 08:08:57', NULL, 0),
(593, 'q7d', 'Projector Mounts', 'projector-mounts', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(594, '4sn', 'Projector Screens', 'projector-screens', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(595, 'a8x', 'Replacement Screens', 'replacement-screens', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(596, 'f75', 'Screen Guards', 'screen-guards', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(597, 'uvk', 'Secure Access Keys', 'secure-access-keys', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(598, 'wrs', 'Spike Busters & Surge Protectors', 'spike-busters-surge-protectors', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(599, 'd6f', 'Stands', 'stands', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(600, 'j3z', 'Touchpads', 'touchpads', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(601, '3yn', 'TV Tuners', 'tv-tuners', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(602, 'zce', 'UPS', 'ups', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(603, 'k9i', 'USB Gadgets', 'usb-gadgets', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(604, 'qtb', 'Wearable Mice', 'wearable-mice', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(605, 'b0t', 'Webcams', 'webcams', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(606, 'cxt', 'Academic', 'academic', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(607, 'lx3', 'Bakery Rolls', 'bakery-rolls', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(608, 'sk6', 'Baking Ingredients & Decoratives', 'baking-ingredients-decoratives', 1, 1, '2017-07-06 00:28:10', '2017-07-20 08:08:57', NULL, 0),
(609, 'z1q', 'Bread Crumbs', 'bread-crumbs', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(610, 'ket', 'Breads', 'breads', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(611, 'jbh', 'Buns', 'buns', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(612, '2z1', 'Cakes & Pastries', 'cakes-pastries', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(613, '2in', 'Food Additives', 'food-additives', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(614, '7n5', 'Rusks', 'rusks', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(615, 'xp9', 'Aerated Drinks', 'aerated-drinks', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(616, '9vs', 'Buttermilk', 'buttermilk', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(617, 'lv5', 'Coffee', 'coffee', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(618, '75g', 'Concentrates', 'concentrates', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(619, 'r4m', 'Energy & Sports Drinks', 'energy-sports-drinks', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(620, 'zd8', 'Fruit Drinks', 'fruit-drinks', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(621, 'hw2', 'Health Drink Mixes', 'health-drink-mixes', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(622, 'th8', 'Ice Cubes', 'ice-cubes', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(623, 'dk2', 'Lassi', 'lassi', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(624, 'cgp', 'Milk & Milk Powder', 'milk-milk-powder', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(625, 'yaw', 'Tea', 'tea', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(626, 'ykj', 'Water', 'water', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(627, 'sjq', 'Candies & Mouth Fresheners', 'candies-mouth-fresheners', 1, 1, '2017-07-06 00:28:11', '2017-07-20 08:08:57', NULL, 0),
(628, '1p6', 'Chewing Gums', 'chewing-gums', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(629, 't8r', 'Chocolates', 'chocolates', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(630, '2m4', 'Sweets & Mithai', 'sweets-mithai', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(631, 'jxi', 'Edible Oils', 'edible-oils', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(632, '5zb', 'Ghee', 'ghee', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(633, 'a5z', 'Honey', 'honey', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(634, 'f2n', 'Jams & Spreads', 'jams-spreads', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(635, '5mc', 'Nuts & Dry Fruits', 'nuts-dry-fruits', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(636, '04a', 'Nuts & Dry Fruits Combo', 'nuts-dry-fruits-combo', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(637, 'o37', 'Condiments', 'condiments', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(638, '02n', 'Dips, Sauces & Pastes', 'dips-sauces-pastes', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(639, '1vu', 'Edible Seeds', 'edible-seeds', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(640, '3ix', 'Pickles & Chutneys', 'pickles-chutneys', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(641, '9l3', 'Salt', 'salt', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(642, 'j90', 'Spices & Masala', 'spices-masala', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(643, 'a7l', 'For Boys', 'for-boys', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(644, '8eo', 'For Girls', 'for-girls', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(645, 'az5', 'Infants', 'infants', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(646, 'dqp', 'Casual Shoes', 'casual-shoes', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(647, 'n6x', 'Ethnic Shoes', 'ethnic-shoes', 1, 1, '2017-07-06 00:28:12', '2017-07-20 08:08:57', NULL, 0),
(648, 'zrb', 'Formal Shoes', 'formal-shoes', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(649, 'cvm', 'Sandals & Floaters', 'sandals-floaters', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(650, 'pyz', 'Shoe Care', 'shoe-care', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(651, 'yp8', 'Slippers & Flip Flops', 'slippers-flip-flops', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(652, 'fz5', 'Sports Shoes', 'sports-shoes', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(653, '3sg', 'Socks', 'socks', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(654, 'a25', 'Ballerinas', 'ballerinas', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(655, 'j41', 'Boots', 'boots', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(656, 'unq', 'Flats', 'flats', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(657, '2fp', 'Formals', 'formals', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(658, 'od5', 'Heels', 'heels', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(659, 'qz8', 'Sandals', 'sandals', 1, 1, '2017-07-06 00:28:13', '2017-07-20 08:08:57', NULL, 0),
(660, 'toa', 'Sports Sandals', 'sports-sandals', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(661, 'ns3', 'Wedges', 'wedges', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(662, 'soh', 'Bedroom Sets', 'bedroom-sets', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(663, 'sdg', 'Coat & Umbrella Stands', 'coat-umbrella-stands', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(664, 'kwr', 'Dressing Mirrors', 'dressing-mirrors', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(665, 'krp', 'Dressing Tables', 'dressing-tables', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(666, 'f6a', 'Mattresses', 'mattresses', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(667, '3cr', 'Room Divider & Partitions', 'room-divider-partitions', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(668, 'ubz', 'Side & End Tables', 'side-end-tables', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(669, 'hob', 'Trunk Boxes', 'trunk-boxes', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(670, 'byh', 'TV Entertainment Units', 'tv-entertainment-units', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(671, 'es9', 'Wardrobes, Drawers & Cupboards', 'wardrobes-drawers-cupboards', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(672, 'hed', 'Centre Table', 'centre-table', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(673, 'xfo', 'Display Units', 'display-units', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(674, 'k5h', 'Diwan & Settees', 'diwan-settees', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(675, '3bf', 'Footstools', 'footstools', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(676, 'ex4', 'Living Room Chairs', 'living-room-chairs', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(677, 'xe4', 'Nesting Tables', 'nesting-tables', 1, 1, '2017-07-06 00:28:14', '2017-07-20 08:08:57', NULL, 0),
(678, 'v8v', 'Outdoor Swings', 'outdoor-swings', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(679, '5c1', 'Recliners', 'recliners', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(680, '7sz', 'Sofa Beds & Futons', 'sofa-beds-futons', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(681, 'kad', 'Sofa Sets', 'sofa-sets', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(682, '824', 'Sofas & Sectionals', 'sofas-sectionals', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(683, 'uk8', 'Auditorium Chairs', 'auditorium-chairs', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(684, 'xm7', 'Bean Bag Covers', 'bean-bag-covers', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(685, '0mq', 'Bean Bag Fillers', 'bean-bag-fillers', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(686, 'nxi', 'Bean Bag with Beans', 'bean-bag-with-beans', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(687, 'm3e', 'Chairs, Benches & Stools', 'chairs-benches-stools', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(688, 'h01', 'Footstools & Ottomans', 'footstools-ottomans', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(689, 'ft5', 'Indoor Swings', 'indoor-swings', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(690, 'l9e', 'Kids Seating', 'kids-seating', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(691, '6ei', 'Outdoor & Cafeteria Chairs', 'outdoor-cafeteria-chairs', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(692, 'luj', 'Sofa, Loungers & Diwans', 'sofa-loungers-diwans', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(693, 'h7k', 'Benches', 'benches', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(694, 'vkm', 'Bookshelves', 'bookshelves', 1, 1, '2017-07-06 00:28:15', '2017-07-20 08:08:57', NULL, 0),
(695, 'xj2', 'Cabinets & Drawers', 'cabinets-drawers', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(696, '0bh', 'Computer Tables', 'computer-tables', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(697, '2la', 'File Cabinets', 'file-cabinets', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(698, '9j8', 'Keyboard Trays', 'keyboard-trays', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(699, 'j61', 'Office & Study Chairs', 'office-study-chairs', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(700, '9b2', 'Office & Study Tables', 'office-study-tables', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(701, 'e8c', 'Portable Laptop Tables', 'portable-laptop-tables', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(702, '980', 'Bedroom Set', 'bedroom-set', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(703, 'snd', 'Bar Cabinets', 'bar-cabinets', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(704, 'yfk', 'Filing Cabinets', 'filing-cabinets', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(705, 'efg', 'Kitchen Cabinets', 'kitchen-cabinets', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(706, 'u9k', 'Blood Donor Chairs', 'blood-donor-chairs', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(707, 'uch', 'Bottle Rack', 'bottle-rack', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(708, '2eo', 'Coffin Stands', 'coffin-stands', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(709, 'sjt', 'Crowd Barriers', 'crowd-barriers', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(710, 'r8a', 'Furniture Combo', 'furniture-combo', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(711, '1id', 'Hammock Swings', 'hammock-swings', 1, 1, '2017-07-06 00:28:16', '2017-07-20 08:08:57', NULL, 0),
(712, 'czh', 'Kitchen Grease Filters', 'kitchen-grease-filters', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(713, 'gpu', 'Mannequin Bag & Trunks', 'mannequin-bag-trunks', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(714, 'x4l', 'Massage Beds', 'massage-beds', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(715, 'uia', 'Medicine Cabinets', 'medicine-cabinets', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(716, 'lpk', 'Display Racks', 'display-racks', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(717, 'hrn', 'Diwans & Settees', 'diwans-settees', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(718, 'wpq', 'Inflatable Sofas', 'inflatable-sofas', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(719, 'ewe', 'Coffee Tables', 'coffee-tables', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(720, 'l6d', 'Office Study Table', 'office-study-table', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(721, 'iwm', 'Outdoor Tables', 'outdoor-tables', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(722, 'ojs', 'Side Tables', 'side-tables', 1, 1, '2017-07-06 00:28:17', '2017-07-20 08:08:57', NULL, 0),
(723, '2of', 'Collapsible Wardrobes', 'collapsible-wardrobes', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(724, 'u6p', '3DS Games', '3ds-games', 1, 1, '2017-07-06 00:28:18', '2017-07-12 01:09:09', NULL, 0),
(725, 'n65', 'DS Games', 'ds-games', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(726, 'k8b', 'PC Games', 'pc-games', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(727, 'qnj', 'PS Vita Games', 'ps-vita-games', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(728, 'f6s', 'PS2 Games', 'ps2-games', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(729, 'l35', 'PS3 Games', 'ps3-games', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(730, 'cwc', 'PS4 Games', 'ps4-games', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(731, 'u5d', 'Wii Games', 'wii-games', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(732, 't3h', 'Wii U Games', 'wii-u-games', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(733, 'inz', 'Xbox 360 Games', 'xbox-360-games', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(734, '0db', 'Xbox One Games', 'xbox-one-games', 1, 1, '2017-07-06 00:28:18', '2017-07-20 08:08:57', NULL, 0),
(735, 't4a', 'Accessory kits', 'accessory-kits', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(736, 'z1f', 'Anti Suppression Patches', 'anti-suppression-patches', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(737, 'm5g', 'Audio Gaming Panels', 'audio-gaming-panels', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(738, 'gj3', 'Batteries & Chargers', 'batteries-chargers', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(739, 'drq', 'Cables & Adapters', 'cables-adapters', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(740, '6ag', 'Cases & Covers', 'cases-covers', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(741, 'g95', 'Charging Stations', 'charging-stations', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(742, '5em', 'Game Control Mounts', 'game-control-mounts', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(743, 'cus', 'Gaming Accessories Combo', 'gaming-accessories-combo', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(744, '8z0', 'Gaming Chairs', 'gaming-chairs', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(745, 'dbf', 'Gaming Speakers', 'gaming-speakers', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(746, 'ebs', 'Gaming Vests', 'gaming-vests', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(747, 'owe', 'Headphones (With Mic)', 'headphones-with-mic', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(748, 'z9u', 'Mousepads', 'mousepads', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(749, 'sp2', 'Processor', 'processor', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(750, 'u8c', 'Storage Devices', 'storage-devices', 1, 1, '2017-07-06 00:28:19', '2017-07-20 08:08:57', NULL, 0),
(751, 'jyx', 'TV-Out Cable', 'tv-out-cable', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(752, 'pqs', 'Fans & Heatsinks', 'fans-heatsinks', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(753, 'l74', 'Handheld Consoles', 'handheld-consoles', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(754, 'ghj', 'PS Vista', 'ps-vista', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(755, 'kia', 'PS2', 'ps2', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(756, '8m4', 'PS3', 'ps3', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(757, '9z9', 'PS4', 'ps4', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(758, 'r28', 'PSP', 'psp', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(759, 'wmw', 'Wii', 'wii', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(760, 'qja', 'Wii U', 'wii-u', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(761, 'kwo', 'Xbox 360', 'xbox-360', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(762, 't0k', 'Xbox One', 'xbox-one', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(763, '9to', 'Basic Electricals', 'basic-electricals', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(764, '201', 'Detergents & Laundry', 'detergents-laundry', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(765, '63g', 'Floor & Other Cleaners', 'floor-other-cleaners', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(766, 'o61', 'Paper & Disposables', 'paper-disposables', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(767, 'ioz', 'Pet Food', 'pet-food', 1, 1, '2017-07-06 00:28:20', '2017-07-20 08:08:57', NULL, 0),
(768, 'jsf', 'Pooja Needs', 'pooja-needs', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(769, 'sw5', 'Repellants & Fresheners', 'repellants-fresheners', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(770, 'y4t', 'Utensil Cleaners', 'utensil-cleaners', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(771, 'uo3', 'Baking', 'baking', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(772, 'htl', 'Breakfast Cereals', 'breakfast-cereals', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(773, '9md', 'Chocolates & Sweets', 'chocolates-sweets', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(774, '6qh', 'Cooking Sauces & Vinegar', 'cooking-sauces-vinegar', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(775, 'c6r', 'Jams & Honey', 'jams-honey', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(776, '4mk', 'Ketchups & Spreads', 'ketchups-spreads', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(777, 'ngz', 'Noodles & Pasta', 'noodles-pasta', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(778, 'lxr', 'Pickles & Chutney', 'pickles-chutney', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(779, 'ubs', 'Ready To Cook', 'ready-to-cook', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(780, 'w9b', 'Baby Bath & Skin Care', 'baby-bath-skin-care', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(781, 'x6k', 'Baby Foods', 'baby-foods', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(782, 'okf', 'Creams, Lotions, Skin Care', 'creams-lotions-skin-care', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(783, 'u2g', 'Deo''s, Perfumes & Talc', 'deo-s-perfumes-talc', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(784, '1ig', 'Diapers & Wipes', 'diapers-wipes', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(785, 'skj', 'Kajal & Makeup', 'kajal-makeup', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(786, 'a4g', 'Sanitary Needs', 'sanitary-needs', 1, 1, '2017-07-06 00:28:21', '2017-07-20 08:08:57', NULL, 0),
(787, 'd8q', 'Shaving Needs', 'shaving-needs', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(788, '6k8', 'Soaps & Body Wash', 'soaps-body-wash', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(789, '568', 'Wellness & Common Pharma', 'wellness-common-pharma', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(790, 'i2d', 'Biscuits', 'biscuits', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(791, 'vwu', 'Chips,Namkeen & Snacks', 'chips-namkeen-snacks', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(792, 'boj', 'Health Drink Mix', 'health-drink-mix', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(793, 'ey8', 'Juices', 'juices', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(794, '62q', 'Milk & Milk Products', 'milk-milk-products', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(795, 'jzm', 'Soft Drinks', 'soft-drinks', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(796, 'nbp', 'Squash & Syrups', 'squash-syrups', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(797, 'g6k', 'Atta & Flours', 'atta-flours', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(798, 'lf8', 'Dals & Pulses', 'dals-pulses', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(799, 'xcu', 'Dry Fruits, Nuts & Seeds', 'dry-fruits-nuts-seeds', 1, 1, '2017-07-06 00:28:22', '2017-07-20 08:08:57', NULL, 0),
(800, 'ah6', 'Ghee & Oils', 'ghee-oils', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(801, 'rxy', 'Masalas & Spices', 'masalas-spices', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(802, '2dr', 'Rice & Rice Products', 'rice-rice-products', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(803, 'xqg', 'Sugar, Jaggery & Salt', 'sugar-jaggery-salt', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(804, 'u12', 'Acupuncture Devices & Accessories', 'acupuncture-devices-accessories', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(805, 'tlf', 'Appliance Combos', 'appliance-combos', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(806, '18p', 'BP Monitor Bulbs', 'bp-monitor-bulbs', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(807, '5vh', 'Cholestrol Analyzers', 'cholestrol-analyzers', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(808, '6nc', 'Elderly Care', 'elderly-care', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(809, 'xc5', 'Electronic Cigarettes', 'electronic-cigarettes', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(810, 'nga', 'Health Care Accessories', 'health-care-accessories', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(811, '6hd', 'Health Care Devices', 'health-care-devices', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(812, 'gs4', 'Maternity Care', 'maternity-care', 1, 1, '2017-07-06 00:28:23', '2017-07-20 08:08:57', NULL, 0),
(813, 'itn', 'Medical Equipment & Accessories', 'medical-equipment-accessories', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(814, '79t', 'Medicines & Treatment', 'medicines-treatment', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(815, 'uze', 'Oxygen Concentrators', 'oxygen-concentrators', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(816, 'fy7', 'Slimming Machines', 'slimming-machines', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(817, 'c63', 'Anti Wrinkle Machines', 'anti-wrinkle-machines', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(818, '2wx', 'Electric Tanners', 'electric-tanners', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(819, '061', 'Electrical Hand Warmers', 'electrical-hand-warmers', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(820, 'p3u', 'Electronic Facial Cleansers', 'electronic-facial-cleansers', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(821, '47u', 'Epilators', 'epilators', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(822, 'wom', 'Hair Curlers', 'hair-curlers', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(823, 'jyi', 'Hair Dryers', 'hair-dryers', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(824, 'en3', 'Hair Rejuvenator Helmets', 'hair-rejuvenator-helmets', 1, 1, '2017-07-06 00:28:24', '2017-07-20 08:08:57', NULL, 0),
(825, 'sd6', 'Hair Removers', 'hair-removers', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(826, 'u9u', 'Hair Straighteners', 'hair-straighteners', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(827, 'hd3', 'Hair Stylers', 'hair-stylers', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(828, 'pm9', 'Hair Styling Tools', 'hair-styling-tools', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(829, '102', 'Salon Hood Dryers', 'salon-hood-dryers', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(830, 'k0d', 'Shavers', 'shavers', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(831, 'pxl', 'Shaving & Hair Removal', 'shaving-hair-removal', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(832, 'd4e', 'Tattoo Ink', 'tattoo-ink', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(833, '656', 'Tattoo Machines', 'tattoo-machines', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(834, 'mpx', 'Trimmers', 'trimmers', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(835, 'enh', 'Vaping Devices', 'vaping-devices', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(836, 'pq4', 'Water Flosser', 'water-flosser', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(837, '6qo', 'Air Conditioners', 'air-conditioners', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(838, '2yv', 'Air Coolers', 'air-coolers', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(839, '74n', 'Air Purifiers', 'air-purifiers', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(840, 'hpc', 'Appliance Parts & Accessories', 'appliance-parts-accessories', 1, 1, '2017-07-06 00:28:25', '2017-07-20 08:08:57', NULL, 0),
(841, 'ed9', 'Compact Refrigerators', 'compact-refrigerators', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(842, 'dx9', 'Dimmers', 'dimmers', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(843, 'aiz', 'Dryer', 'dryer', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(844, 'nsu', 'Electric Shoe Dryers', 'electric-shoe-dryers', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(845, 'zm2', 'Emergency Lights', 'emergency-lights', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(846, 'e8q', 'Fans', 'fans', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(847, 'yzq', 'Freezer Chests', 'freezer-chests', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(848, '4rf', 'Immersion Rods', 'immersion-rods', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(849, 'lb6', 'Inverters', 'inverters', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(850, '39h', 'Irons', 'irons', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(851, 'erm', 'Landline Phones', 'landline-phones', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(852, '7v9', 'Lightings', 'lightings', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(853, 'ip8', 'Popcorn Makers', 'popcorn-makers', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(854, '14q', 'Refrigerators', 'refrigerators', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(855, '5gy', 'Room Heaters', 'room-heaters', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(856, 'hjq', 'Sewing Machines', 'sewing-machines', 1, 1, '2017-07-06 00:28:26', '2017-07-20 08:08:57', NULL, 0),
(857, 'mlo', 'Spike Guards & Surge Protectors', 'spike-guards-surge-protectors', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(858, 'jec', 'Telephones', 'telephones', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(859, 'wxj', 'Thermostats', 'thermostats', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(860, '2n1', 'Transformer Converters', 'transformer-converters', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(861, '0jw', 'Ultrasonic Cleaners', 'ultrasonic-cleaners', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(862, 'ea7', 'Vacuum Cleaners', 'vacuum-cleaners', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(863, '56t', 'Voltage Stabilizers', 'voltage-stabilizers', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(864, 't0v', 'Washing Machines', 'washing-machines', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(865, 'kst', 'Water Geysers', 'water-geysers', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(866, 'osx', 'Water Heaters', 'water-heaters', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(867, 'q57', 'Water purifiers', 'water-purifiers', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(868, 'mic', 'Dish washers', 'dish-washers', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(869, 'xn4', 'Donut Makers', 'donut-makers', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(870, 'wte', 'Electric Bottle Openers', 'electric-bottle-openers', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(871, 'yx1', 'Electric Cookers', 'electric-cookers', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(872, 'o2y', 'Electric Cooking Heaters', 'electric-cooking-heaters', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(873, '0v6', 'Electric Deep Fryers', 'electric-deep-fryers', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(874, '20t', 'Electric Woks', 'electric-woks', 1, 1, '2017-07-06 00:28:27', '2017-07-20 08:08:57', NULL, 0),
(875, '2ry', 'Flourmills', 'flourmills', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(876, 'rtw', 'Food Makers', 'food-makers', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(877, 'lz8', 'Hot Dog Machines', 'hot-dog-machines', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(878, 'jcb', 'Ice Makers', 'ice-makers', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(879, '60y', 'Induction Cooktops', 'induction-cooktops', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(880, '548', 'Mixer,juicer,grinders', 'mixer-juicer-grinders', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(881, 'n83', 'Sandwich Makers', 'sandwich-makers', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(882, 'w5x', 'Sausage Makers', 'sausage-makers', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(883, 'im7', 'Soda Makers', 'soda-makers', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(884, 'dlu', ' Lamp Shade and Lamp base', 'lamp-shade-and-lamp-base', 1, 1, '2017-07-06 00:28:28', '2017-07-12 01:08:45', NULL, 0),
(885, '969', 'Candles', 'candles', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(886, '2zs', 'Ceiling Lights', 'ceiling-lights', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(887, '2vq', 'Chandeliers', 'chandeliers', 1, 1, '2017-07-06 00:28:28', '2017-07-20 08:08:57', NULL, 0),
(888, 'd3z', 'Disco Lights', 'disco-lights', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(889, 'y86', 'Floor Lamp', 'floor-lamp', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(890, 'u64', 'Lanterns', 'lanterns', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(891, 'y9u', 'Light fixtures', 'light-fixtures', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(892, 'd10', 'Night Lamps', 'night-lamps', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(893, 'cw9', 'Outdoor Lamps', 'outdoor-lamps', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(894, 'ia1', 'Series Lights', 'series-lights', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(895, '53y', 'Table Lamps', 'table-lamps', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(896, 'kmj', 'Wall Lamps', 'wall-lamps', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(897, '2v8', 'Photo Albums', 'photo-albums', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(898, '1sv', 'Photo Frames', 'photo-frames', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(899, '4uo', 'Animal Figurines', 'animal-figurines', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(900, '3vi', 'Contemporary Showpiece', 'contemporary-showpiece', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(901, 'dqx', 'Couples Showpiece', 'couples-showpiece', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(902, 'g8e', 'Ethnic Showpiece', 'ethnic-showpiece', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(903, 'dx1', 'Feng Shui Products', 'feng-shui-products', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(904, 'zsm', 'Gramophones', 'gramophones', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0),
(905, '7ya', 'Human Figurines', 'human-figurines', 1, 1, '2017-07-06 00:28:29', '2017-07-20 08:08:57', NULL, 0);
INSERT INTO `product_categories` (`category_id`, `category_code`, `category`, `url_str`, `replacement_service_policy_id`, `status`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(906, 'f4d', 'Match Box Holders', 'match-box-holders', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(907, 'jbt', 'Religious Idols', 'religious-idols', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(908, 'ljk', 'Telephone Mouthpieces', 'telephone-mouthpieces', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(909, 'miu', 'Barn Signs', 'barn-signs', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(910, '0r1', 'Decorative Mask', 'decorative-mask', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(911, 'e1y', 'Fridge Magnets', 'fridge-magnets', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(912, 'env', 'Jharokha', 'jharokha', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(913, 'eil', 'Key Holders', 'key-holders', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(914, 'gup', 'Mirrors', 'mirrors', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(915, 'ovg', 'Name Plates', 'name-plates', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(916, 'uxo', 'Paintings', 'paintings', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(917, '10q', 'Rainbow Makers', 'rainbow-makers', 1, 1, '2017-07-06 00:28:30', '2017-07-20 08:08:57', NULL, 0),
(918, '1pr', 'Tapestry', 'tapestry', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(919, '4f0', 'Tin & Metal Signs', 'tin-metal-signs', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(920, 'qi2', 'Trophies & Medals', 'trophies-medals', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(921, 'w2w', 'Wall Decals & Stickers', 'wall-decals-stickers', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(922, 'gby', 'Wall Hangings', 'wall-hangings', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(923, 'sqd', 'Wall Photo Frames', 'wall-photo-frames', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(924, '4iw', 'Wall Shelves', 'wall-shelves', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(925, 'mat', 'Wallpapers', 'wallpapers', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(926, '2jd', 'Audio Player Accessories', 'audio-player-accessories', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(927, 'bti', 'iPods', 'ipods', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(928, 's3h', 'MP3 Players', 'mp3-players', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(929, 'bjn', 'MP4 / Video MP3 Players', 'mp4-video-mp3-players', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(930, 'sc2', 'Amplifiers & AV Receivers', 'amplifiers-av-receivers', 1, 1, '2017-07-06 00:28:31', '2017-07-20 08:08:57', NULL, 0),
(931, '8vf', 'Hi-Fi Systems', 'hi-fi-systems', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(932, 'baj', 'Satellite Radio', 'satellite-radio', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(933, 'wsz', 'Sound Mixer', 'sound-mixer', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(934, 'ylg', 'Soundbars', 'soundbars', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(935, 'xvw', 'Turntables', 'turntables', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(936, 'r7g', 'Voice Recorders', 'voice-recorders', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(937, 'mpb', 'Bluetooth Music Receivers', 'bluetooth-music-receivers', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(938, 'h5q', 'FM Tuners', 'fm-tuners', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(939, 'u07', 'Ipod Chargers', 'ipod-chargers', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(940, 'bgq', 'Ipod Guards/Covers/Cases', 'ipod-guards-covers-cases', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(941, 'r7k', 'Ipod Screen guards', 'ipod-screen-guards', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(942, 'hn8', 'Antenna Rotators', 'antenna-rotators', 1, 1, '2017-07-06 00:28:32', '2017-07-20 08:08:57', NULL, 0),
(943, 'ol2', 'DTH', 'dth', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(944, '9op', 'Headsets', 'headsets', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(945, 'ok5', 'Home Theaters', 'home-theaters', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(946, 'vr4', 'Video Players', 'video-players', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(947, 'xp2', 'Bath Linen Sets', 'bath-linen-sets', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(948, '5kb', 'Bath Mats', 'bath-mats', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(949, 'oj1', 'Bathroom Accessories', 'bathroom-accessories', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(950, '9fn', 'Laundry Baskets', 'laundry-baskets', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(951, 'qmi', 'Shower Curtains', 'shower-curtains', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(952, 'cq2', 'Towels', 'towels', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(953, 'mwf', 'Bed Covers', 'bed-covers', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(954, 'l18', 'Bed Skirts', 'bed-skirts', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(955, 'tve', 'Bedding Sets', 'bedding-sets', 1, 1, '2017-07-06 00:28:33', '2017-07-20 08:08:57', NULL, 0),
(956, 'lcz', 'Bedsheets', 'bedsheets', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(957, 'hgi', 'Blankets, Quilts & Dohars', 'blankets-quilts-dohars', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(958, '0h4', 'Duvet Covers', 'duvet-covers', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(959, '83l', 'Mattress Protectors', 'mattress-protectors', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(960, 'kdw', 'Mosquito Nets', 'mosquito-nets', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(961, 'w5u', 'Quilt Battings', 'quilt-battings', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(962, 'rzh', 'Curtain Accessories', 'curtain-accessories', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(963, 'y3z', 'Curtain Fabric', 'curtain-fabric', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(964, 'vks', 'Curtains', 'curtains', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(965, 'fy1', 'Bolster Covers', 'bolster-covers', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(966, 'gf1', 'Bolsters', 'bolsters', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(967, '5ym', 'Cushion Covers', 'cushion-covers', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(968, '2d0', 'Cushions', 'cushions', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(969, 'zbl', 'Pillow Covers', 'pillow-covers', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(970, 'hyk', 'Pillows', 'pillows', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(971, 'mqn', 'Carpets & Rugs', 'carpets-rugs', 1, 1, '2017-07-06 00:28:34', '2017-07-20 08:08:57', NULL, 0),
(972, 'b9e', 'Chair Carpet Mats', 'chair-carpet-mats', 1, 1, '2017-07-06 00:28:35', '2017-07-20 08:08:57', NULL, 0),
(973, 'kpl', 'Mats', 'mats', 1, 1, '2017-07-06 00:28:35', '2017-07-20 08:08:57', NULL, 0),
(974, 'boc', 'Door Blinds', 'door-blinds', 1, 1, '2017-07-06 00:28:35', '2017-07-20 08:08:57', NULL, 0),
(975, 'fsf', 'Draft Stoppers', 'draft-stoppers', 1, 1, '2017-07-06 00:28:35', '2017-07-20 08:08:57', NULL, 0),
(976, '0yp', 'Pillow Protectors', 'pillow-protectors', 1, 1, '2017-07-06 00:28:35', '2017-07-20 08:08:57', NULL, 0),
(977, 's5x', 'Sofa Covers', 'sofa-covers', 1, 1, '2017-07-06 00:28:35', '2017-07-20 08:08:57', NULL, 0),
(978, 'e0z', 'Window Valance', 'window-valance', 1, 1, '2017-07-06 00:28:35', '2017-07-20 08:08:57', NULL, 0),
(979, 'ios', 'Sofa Fabric', 'sofa-fabric', 1, 1, '2017-07-06 00:28:35', '2017-07-20 08:08:57', NULL, 0),
(980, 'fhy', 'Power Backup', 'power-backup', 1, 1, '2017-07-06 00:28:35', '2017-07-20 08:08:57', NULL, 0),
(981, 'vl1', 'Solar', 'solar', 1, 1, '2017-07-06 00:28:35', '2017-07-20 08:08:57', NULL, 0),
(982, '1f1', 'Adapters & Plugs', 'adapters-plugs', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(983, 'u9y', 'Circuit Breaker', 'circuit-breaker', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(984, 'u7f', 'Electrical Hardware', 'electrical-hardware', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(985, 'emq', 'Switches & Accessories', 'switches-accessories', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(986, 'rni', 'Wire, Joints & Connectors', 'wire-joints-connectors', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(987, 'laj', 'Adhesives', 'adhesives', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(988, 'hw9', 'Bathroom & Kitchen Fixtures', 'bathroom-kitchen-fixtures', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(989, 'snz', 'Chords & Ties', 'chords-ties', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(990, '0n3', 'Door & Windows Fittings', 'door-windows-fittings', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(991, '4be', 'Ladders & Step ladders', 'ladders-step-ladders', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(992, '8bn', 'Nails, Screws & Fastners', 'nails-screws-fastners', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(993, 'w1a', 'Sprays', 'sprays', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(994, 'yhz', 'Appliance Safety & Outlet Controls', 'appliance-safety-outlet-controls', 1, 1, '2017-07-06 00:28:36', '2017-07-20 08:08:57', NULL, 0),
(995, '3gz', 'Fire Safety', 'fire-safety', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(996, '40v', 'Garage Convenience', 'garage-convenience', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(997, 'rsy', 'Locks', 'locks', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(998, 'go2', 'Personal Safety', 'personal-safety', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(999, 'rv1', 'Safes', 'safes', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(1000, 'brd', 'Bathroom', 'bathroom', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(1001, 'olj', 'Bedroom', 'bedroom', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(1002, 'ozc', 'Clothing & Closet', 'clothing-closet', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(1003, '8zr', 'Garage', 'garage', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(1004, 'ic2', 'Kitchen Storage', 'kitchen-storage', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(1005, 'el2', 'Laundry', 'laundry', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(1006, '1pk', 'Living Room', 'living-room', 1, 1, '2017-07-06 00:28:37', '2017-07-20 08:08:57', NULL, 0),
(1007, 'wo2', 'Paint Tools & Accessories', 'paint-tools-accessories', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1008, 'u29', 'Paint, Stains & Solvent', 'paint-stains-solvent', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1009, '8gz', 'Sealants & Putty', 'sealants-putty', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1010, 'emu', 'Wallpaper & Borders', 'wallpaper-borders', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1011, 'wd3', 'Pipes & Fittings', 'pipes-fittings', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1012, 'zdw', 'Pumps', 'pumps', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1013, 'xcg', 'Water Tank', 'water-tank', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1014, '81v', 'Watermeter', 'watermeter', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1015, 'rzo', 'Bathroom Fixtures', 'bathroom-fixtures', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1016, '3dg', 'Hand Tools', 'hand-tools', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1017, 'swx', 'Measuring & Layout Tools', 'measuring-layout-tools', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1018, 'cns', 'Tool Accessories', 'tool-accessories', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1019, 'gw6', 'Bathroom Floor Cleaners', 'bathroom-floor-cleaners', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1020, 'rls', 'Carpet & Upholstery Cleaners', 'carpet-upholstery-cleaners', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1021, 'l0f', 'Drain Openers', 'drain-openers', 1, 1, '2017-07-06 00:28:38', '2017-07-20 08:08:57', NULL, 0),
(1022, '0u0', 'Kitchen Surface Cleaners', 'kitchen-surface-cleaners', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1023, 'k19', 'Naphthalene Balls', 'naphthalene-balls', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1024, '93a', 'Toilet Brushes', 'toilet-brushes', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1025, 'm5j', 'Toilet Cleaners', 'toilet-cleaners', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1026, 'tcf', 'Dish Cleaning Gels', 'dish-cleaning-gels', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1027, '4r9', 'Dish Detergents', 'dish-detergents', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1028, '4aw', 'Dish Washer Detergents', 'dish-washer-detergents', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1029, 'nm0', 'Dish Washing Bars', 'dish-washing-bars', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1030, 'fyl', 'Detergent Pods', 'detergent-pods', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1031, '2xj', 'Fabric Deodorizers', 'fabric-deodorizers', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1032, 'c1z', 'Fabric Softeners', 'fabric-softeners', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1033, 'e51', 'Fabric Stiffeners', 'fabric-stiffeners', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1034, 'xz3', 'Fabric Whiteners', 'fabric-whiteners', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1035, '0l9', 'Liquid Detergents', 'liquid-detergents', 1, 1, '2017-07-06 00:28:39', '2017-07-20 08:08:57', NULL, 0),
(1036, 'pid', 'Machine Washing Powders', 'machine-washing-powders', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1037, 'n7y', 'Stain Removers', 'stain-removers', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1038, '373', 'Washing Bars', 'washing-bars', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1039, 'b2b', 'Washing Powders', 'washing-powders', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1040, 'gt5', 'Anklets', 'anklets', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1041, '1x7', 'Bangles Bracelets & Armlets', 'bangles-bracelets-armlets', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1042, 'npm', 'Body Chains', 'body-chains', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1043, 'mif', 'Earrings', 'earrings', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1044, '4uz', 'Horse Shoe Rings', 'horse-shoe-rings', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1045, 'mf4', 'Jewellery Sets', 'jewellery-sets', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1046, 'y90', 'Kamarband', 'kamarband', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1047, '4sr', 'Mangalsutras & Tanmaniyas', 'mangalsutras-tanmaniyas', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1048, 'n10', 'Necklaces & Chains', 'necklaces-chains', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1049, '68m', 'Nose Rings & Studs', 'nose-rings-studs', 1, 1, '2017-07-06 00:28:40', '2017-07-20 08:08:57', NULL, 0),
(1050, 'sv1', 'Pendants & Lockets', 'pendants-lockets', 1, 1, '2017-07-06 00:28:41', '2017-07-20 08:08:57', NULL, 0),
(1051, '47x', 'Rings', 'rings', 1, 1, '2017-07-06 00:28:41', '2017-07-20 08:08:57', NULL, 0),
(1052, 'o8r', 'Chains', 'chains', 1, 1, '2017-07-06 00:28:41', '2017-07-20 08:08:57', NULL, 0),
(1053, 'r05', 'Necklaces', 'necklaces', 1, 1, '2017-07-06 00:28:41', '2017-07-20 08:08:57', NULL, 0),
(1054, 'ebe', 'Coins & Bars', 'coins-bars', 1, 1, '2017-07-06 00:28:41', '2017-07-20 08:08:57', NULL, 0),
(1055, 'lf5', 'Gemstones', 'gemstones', 1, 1, '2017-07-06 00:28:41', '2017-07-20 08:08:57', NULL, 0),
(1056, '7tp', 'Pooja Thali Sets', 'pooja-thali-sets', 1, 1, '2017-07-06 00:28:41', '2017-07-20 08:08:57', NULL, 0),
(1057, 'kqh', 'Showpieces & Figurines', 'showpieces-figurines', 1, 1, '2017-07-06 00:28:41', '2017-07-20 08:08:57', NULL, 0),
(1058, 'yuz', 'Couple Rings', 'couple-rings', 1, 1, '2017-07-06 00:28:41', '2017-07-20 08:08:57', NULL, 0),
(1059, 'psx', 'Maang Tikkas', 'maang-tikkas', 1, 1, '2017-07-06 00:28:42', '2017-07-20 08:08:57', NULL, 0),
(1060, 'bx0', 'Kamarbands', 'kamarbands', 1, 1, '2017-07-06 00:28:42', '2017-07-20 08:08:57', NULL, 0),
(1061, 'lqr', 'Backpacks & Shoulder Bags', 'backpacks-shoulder-bags', 1, 1, '2017-07-06 00:28:42', '2017-07-20 08:08:57', NULL, 0),
(1062, '49o', 'Daypacks', 'daypacks', 1, 1, '2017-07-06 00:28:42', '2017-07-20 08:08:57', NULL, 0),
(1063, 'ain', 'Haversacks', 'haversacks', 1, 1, '2017-07-06 00:28:42', '2017-07-20 08:08:57', NULL, 0),
(1064, '489', 'Multipurpose Bags', 'multipurpose-bags', 1, 1, '2017-07-06 00:28:43', '2017-07-20 08:08:57', NULL, 0),
(1065, 'iaf', 'Trolley Bags', 'trolley-bags', 1, 1, '2017-07-06 00:28:43', '2017-07-20 08:08:57', NULL, 0),
(1066, 'tug', 'Weekender Bags', 'weekender-bags', 1, 1, '2017-07-06 00:28:43', '2017-07-20 08:08:57', NULL, 0),
(1067, 'vqk', 'Badges', 'badges', 1, 1, '2017-07-06 00:28:43', '2017-07-20 08:08:57', NULL, 0),
(1068, 'prt', 'Coin Banks', 'coin-banks', 1, 1, '2017-07-06 00:28:43', '2017-07-20 08:08:57', NULL, 0),
(1069, 'olu', 'Examination Pads', 'examination-pads', 1, 1, '2017-07-06 00:28:43', '2017-07-20 08:08:57', NULL, 0),
(1070, 'fn9', 'Geometry & Pencil Boxes', 'geometry-pencil-boxes', 1, 1, '2017-07-06 00:28:43', '2017-07-20 08:08:57', NULL, 0),
(1071, 'ae0', 'Lunch Bags', 'lunch-bags', 1, 1, '2017-07-06 00:28:43', '2017-07-20 08:08:57', NULL, 0),
(1072, 'u3g', 'Lunch Boxes', 'lunch-boxes', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1073, 'pqb', 'School Accessories', 'school-accessories', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1074, 'q7o', 'School Bags', 'school-bags', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1075, 'frs', 'School Sets', 'school-sets', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1076, 'vbg', 'Stickers', 'stickers', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1077, '530', 'Umbrellas', 'umbrellas', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1078, 'ag6', 'Uniform', 'uniform', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1079, '0ur', 'Water Bottles', 'water-bottles', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1080, 'aux', 'Bakeware Moulds', 'bakeware-moulds', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1081, '8ky', 'Baking Cutters', 'baking-cutters', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1082, '4ha', 'Baking Dishes & Pans', 'baking-dishes-pans', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1083, '5hv', 'Bowls', 'bowls', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1084, 'b0m', 'Cookie & Biscuit Presses', 'cookie-biscuit-presses', 1, 1, '2017-07-06 00:28:44', '2017-07-20 08:08:57', NULL, 0),
(1085, '21w', 'Decoration & Accessories', 'decoration-accessories', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1086, 'n97', 'Pastry Brushes', 'pastry-brushes', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1087, 'y76', 'Pastry Frames', 'pastry-frames', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1088, '7w0', 'Roast Injectors', 'roast-injectors', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1089, 'hjw', 'Roasters', 'roasters', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1090, 'z5e', 'Weights & Measures', 'weights-measures', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1091, 'c1w', 'Whisks', 'whisks', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1092, 'nmv', 'Bottles & Sippers', 'bottles-sippers', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1093, 'pog', 'Closet/ Drawer Dividers', 'closet-drawer-dividers', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1094, 'axw', 'Containers & Jars', 'containers-jars', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1095, 'wge', 'General Coolers', 'general-coolers', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1096, 'bl4', 'Kitchen Racks & Trolleys', 'kitchen-racks-trolleys', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1097, 'gdl', 'Multipurpose Storage Cabinets', 'multipurpose-storage-cabinets', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1098, 'gx3', 'Racks & Shelves', 'racks-shelves', 1, 1, '2017-07-06 00:28:45', '2017-07-20 08:08:57', NULL, 0),
(1099, 'i97', 'Rain Barrels', 'rain-barrels', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1100, 'ufm', 'Shoe Racks', 'shoe-racks', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1101, '2px', 'Storage Baskets', 'storage-baskets', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1102, 'ev3', 'Storage Boxes', 'storage-boxes', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1103, 'xnd', 'Storage Organizers', 'storage-organizers', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1104, '0sj', 'Vegetable Bags & Baskets', 'vegetable-bags-baskets', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1105, 'j1j', 'Casserole & Sets', 'casserole-sets', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1106, 'dx5', 'Cookware Sets', 'cookware-sets', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1107, 'qar', 'Lids', 'lids', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1108, 'daw', 'Pots & Pans', 'pots-pans', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1109, 'r7o', 'Steamers & Idli Makers', 'steamers-idli-makers', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1110, '96c', 'Dough Presses', 'dough-presses', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1111, 'vxo', 'Dumpling Presses', 'dumpling-presses', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1112, '8c2', 'Kitchen Tool Sets', 'kitchen-tool-sets', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1113, 'hm4', 'Bulbs', 'bulbs', 1, 1, '2017-07-06 00:28:46', '2017-07-20 08:08:57', NULL, 0),
(1114, '9dy', 'Torches', 'torches', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1115, 'ji1', 'Tube Lights', 'tube-lights', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1116, 'ojm', 'Barbecue, Grills & Skewers', 'barbecue-grills-skewers', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1117, '6ye', 'Chimneys', 'chimneys', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1118, 'y8d', 'Gas Stoves', 'gas-stoves', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1119, 'n7k', 'Anti Radiation Stickers & Chips', 'anti-radiation-stickers-chips', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1120, 'dek', 'Bluetooth Headphones (With Mic)', 'bluetooth-headphones-with-mic', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1121, '1x0', 'Bluetooth Shutter Remotes', 'bluetooth-shutter-remotes', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1122, 'txi', 'Car Accessories', 'car-accessories', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1123, 'b56', 'Charging Pad Receivers', 'charging-pad-receivers', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1124, '579', 'Charging Pads', 'charging-pads', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1125, '17w', 'Earphone Cable Organizers', 'earphone-cable-organizers', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1126, 'cdn', 'Headphone Amplifiers', 'headphone-amplifiers', 1, 1, '2017-07-06 00:28:47', '2017-07-20 08:08:57', NULL, 0),
(1127, '48k', 'Headphone Cushions & Earbuds', 'headphone-cushions-earbuds', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1128, '3u1', 'Headphone Splitters', 'headphone-splitters', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1129, 'fbs', 'Memory Cards & Readers', 'memory-cards-readers', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1130, 'yu3', 'Mobile Battery', 'mobile-battery', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1131, 'u1t', 'Mobile Body Panels', 'mobile-body-panels', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1132, '2kz', 'Mobile Cables', 'mobile-cables', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1133, 'm21', 'Mobile Displays', 'mobile-displays', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1134, 'hyg', 'Mobile Enhancements', 'mobile-enhancements', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1135, 'on3', 'Mobile Flashes', 'mobile-flashes', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1136, '4i1', 'Mobile Holders', 'mobile-holders', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1137, 'nc2', 'Mobile Portable Projectors', 'mobile-portable-projectors', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1138, 'py1', 'Mobile Pouches', 'mobile-pouches', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1139, 'fdw', 'Mobile Sim & SD Card Trays', 'mobile-sim-sd-card-trays', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1140, '1hn', 'Mods', 'mods', 1, 1, '2017-07-06 00:28:48', '2017-07-20 08:08:57', NULL, 0),
(1141, '4pi', 'On the Go Pen Drives', 'on-the-go-pen-drives', 1, 1, '2017-07-06 00:28:49', '2017-07-20 08:08:57', NULL, 0),
(1142, 'h1z', 'Photo Printers', 'photo-printers', 1, 1, '2017-07-06 00:28:49', '2017-07-20 08:08:57', NULL, 0),
(1143, 'bs7', 'Power Bank Pouches', 'power-bank-pouches', 1, 1, '2017-07-06 00:28:49', '2017-07-20 08:08:57', NULL, 0),
(1144, 'k03', 'Radiation Monitors', 'radiation-monitors', 1, 1, '2017-07-06 00:28:49', '2017-07-20 08:08:57', NULL, 0),
(1145, 'rn8', 'Satellite Phone Converters', 'satellite-phone-converters', 1, 1, '2017-07-06 00:28:49', '2017-07-20 08:08:57', NULL, 0),
(1146, '0az', 'Screen Expander For Phones', 'screen-expander-for-phones', 1, 1, '2017-07-06 00:28:49', '2017-07-20 08:08:57', NULL, 0),
(1147, 'ivw', 'Screen Protector Applicator', 'screen-protector-applicator', 1, 1, '2017-07-06 00:28:49', '2017-07-20 08:08:57', NULL, 0),
(1148, 'ryv', 'Security Software', 'security-software', 1, 1, '2017-07-06 00:28:49', '2017-07-20 08:08:57', NULL, 0),
(1149, '7px', 'Selfie Sticks', 'selfie-sticks', 1, 1, '2017-07-06 00:28:49', '2017-07-20 08:08:57', NULL, 0),
(1150, 'p82', 'SIM Adapters', 'sim-adapters', 1, 1, '2017-07-06 00:28:50', '2017-07-20 08:08:57', NULL, 0),
(1151, 'acl', 'Sim Cutters', 'sim-cutters', 1, 1, '2017-07-06 00:28:50', '2017-07-20 08:08:57', NULL, 0),
(1152, 'hc0', 'Smart Keys', 'smart-keys', 1, 1, '2017-07-06 00:28:50', '2017-07-20 08:08:57', NULL, 0),
(1153, 'e9y', 'Styling & Maintenance', 'styling-maintenance', 1, 1, '2017-07-06 00:28:50', '2017-07-20 08:08:57', NULL, 0),
(1154, 'f02', 'Stylus Pens', 'stylus-pens', 1, 1, '2017-07-06 00:28:50', '2017-07-20 08:08:57', NULL, 0),
(1155, 'gb0', 'Tablets with Call Facility', 'tablets-with-call-facility', 1, 1, '2017-07-06 00:28:50', '2017-07-20 08:08:57', NULL, 0),
(1156, '5pv', 'Classical', 'classical', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1157, 'lhg', 'Country & Western', 'country-western', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1158, 'agf', 'Dance Styles', 'dance-styles', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1159, 'mtw', 'Devotional & Spiritual', 'devotional-spiritual', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1160, 'wxg', 'Electronic/Dance', 'electronic-dance', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1161, 'wf5', 'Festivals & Occasions', 'festivals-occasions', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1162, 'kx7', 'Film / OST', 'film-ost', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1163, 'dzy', 'Folk', 'folk', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1164, 'pe1', 'Ghazal', 'ghazal', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1165, 'g5g', 'Indi Pop (Indian Artists)', 'indi-pop-indian-artists', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1166, 'kd1', 'Instrumental', 'instrumental', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1167, '0y8', 'Jazz', 'jazz', 1, 1, '2017-07-06 00:28:51', '2017-07-20 08:08:57', NULL, 0),
(1168, 'g1z', 'Live', 'live', 1, 1, '2017-07-06 00:28:52', '2017-07-20 08:08:57', NULL, 0),
(1169, 'w3f', 'Lounge / Fusion', 'lounge-fusion', 1, 1, '2017-07-06 00:28:52', '2017-07-20 08:08:57', NULL, 0),
(1170, '3yp', 'Pop', 'pop', 1, 1, '2017-07-06 00:28:52', '2017-07-20 08:08:57', NULL, 0),
(1171, '1bo', 'Rap & Hip-Hop', 'rap-hip-hop', 1, 1, '2017-07-06 00:28:52', '2017-07-20 08:08:57', NULL, 0),
(1172, 'm6g', 'Reggae', 'reggae', 1, 1, '2017-07-06 00:28:52', '2017-07-20 08:08:57', NULL, 0),
(1173, 'cdc', 'Rock', 'rock', 1, 1, '2017-07-06 00:28:52', '2017-07-20 08:08:57', NULL, 0),
(1174, 'jny', 'Soul / R&B', 'soul-r-b', 1, 1, '2017-07-06 00:28:52', '2017-07-20 08:08:57', NULL, 0),
(1175, 'gdo', 'Special Interest', 'special-interest', 1, 1, '2017-07-06 00:28:52', '2017-07-20 08:08:57', NULL, 0),
(1176, 'tqf', 'Acoustic Blues', 'acoustic-blues', 1, 1, '2017-07-06 00:28:53', '2017-07-20 08:08:57', NULL, 0),
(1177, '89o', 'Blues Revival', 'blues-revival', 1, 1, '2017-07-06 00:28:53', '2017-07-20 08:08:57', NULL, 0),
(1178, 'sf3', 'Boogie Woogie', 'boogie-woogie', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1179, '04p', 'Chicago Blues', 'chicago-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1180, 'wdx', 'Classic Female Vocal Blues', 'classic-female-vocal-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1181, 'm01', 'Contemporary Blues', 'contemporary-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1182, '5v9', 'Country Blues', 'country-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1183, 'rac', 'Delta Blues', 'delta-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1184, 'zsf', 'Dirty Blues', 'dirty-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1185, 'z82', 'Electric Blues', 'electric-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1186, 'v2i', 'Harmonica Blues', 'harmonica-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1187, 'rjg', 'Jazz Blues', 'jazz-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1188, 'hmp', 'Jump Blues', 'jump-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1189, 'q7w', 'Modern Blues', 'modern-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1190, '9t0', 'Rock n roll', 'rock-n-roll', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1191, 'rza', 'Traditional Blues', 'traditional-blues', 1, 1, '2017-07-06 00:28:54', '2017-07-20 08:08:57', NULL, 0),
(1192, 'rbq', 'Cartoon Music', 'cartoon-music', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1193, 'z4z', 'Children Songs', 'children-songs', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1194, 'qe4', 'Disney', 'disney', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1195, '0lx', 'Educational', 'educational', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1196, 'dj9', 'Fairytales', 'fairytales', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1197, '34f', 'Kindergarten Music', 'kindergarten-music', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1198, 'dbw', 'Lullabyes', 'lullabyes', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1199, 'ulf', 'Nursery Rhymes', 'nursery-rhymes', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1200, '8b2', 'Sing-A-Longs', 'sing-a-longs', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1201, 'ms0', 'Stories', 'stories', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1202, 'l2q', 'Afro-Cuban', 'afro-cuban', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1203, 'clr', 'Argentinean', 'argentinean', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1204, '4xb', 'Bachata', 'bachata', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1205, 'bd3', 'Basque', 'basque', 1, 1, '2017-07-06 00:28:55', '2017-07-20 08:08:57', NULL, 0),
(1206, '1ki', 'Brazilian', 'brazilian', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1207, 'dcu', 'Central Europe', 'central-europe', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1208, 'zxl', 'Chinese', 'chinese', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1209, 'ysc', 'Dominican Republic', 'dominican-republic', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1210, 'fad', 'Far Eastern', 'far-eastern', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1211, 'o5o', 'Greek', 'greek', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1212, 'okb', 'Romanian', 'romanian', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1213, 'h94', 'Russian', 'russian', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1214, 'biy', 'South Pacific', 'south-pacific', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1215, 'ur5', 'Spanish', 'spanish', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1216, 'cwx', 'Swiss', 'swiss', 1, 1, '2017-07-06 00:28:56', '2017-07-20 08:08:57', NULL, 0),
(1217, '6cv', 'Turkish', 'turkish', 1, 1, '2017-07-06 00:28:57', '2017-07-20 08:08:57', NULL, 0),
(1218, 'iyf', 'Western Europe', 'western-europe', 1, 1, '2017-07-06 00:28:57', '2017-07-20 08:08:57', NULL, 0),
(1219, 'vqe', 'Movies', 'movies', 1, 1, '2017-07-06 00:28:57', '2017-07-20 08:08:57', NULL, 0),
(1220, 'lfb', 'TV Show', 'tv-show', 1, 1, '2017-07-06 00:28:57', '2017-07-20 08:08:57', NULL, 0),
(1221, 'rap', 'Harmoniums', 'harmoniums', 1, 1, '2017-07-06 00:28:57', '2017-07-20 08:08:57', NULL, 0),
(1222, 'axa', 'Shruti Boxes', 'shruti-boxes', 1, 1, '2017-07-06 00:28:57', '2017-07-20 08:08:57', NULL, 0),
(1223, 'm8w', 'Acoustic Guitars', 'acoustic-guitars', 1, 1, '2017-07-06 00:28:57', '2017-07-20 08:08:57', NULL, 0),
(1224, 'mp5', 'Bass Guitars', 'bass-guitars', 1, 1, '2017-07-06 00:28:57', '2017-07-20 08:08:57', NULL, 0),
(1225, '407', 'Double Bass', 'double-bass', 1, 1, '2017-07-06 00:28:57', '2017-07-20 08:08:57', NULL, 0),
(1226, '5r4', 'Electric Guitars', 'electric-guitars', 1, 1, '2017-07-06 00:28:57', '2017-07-20 08:08:57', NULL, 0),
(1227, '2rk', 'Lyre', 'lyre', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1228, 'gr7', 'Mandolins', 'mandolins', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1229, 'r70', 'Sanshin', 'sanshin', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1230, 'x4m', 'Santoors', 'santoors', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1231, '35q', 'Sitars', 'sitars', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1232, 'l4c', 'Tanpuras', 'tanpuras', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1233, 'fhv', 'Taus', 'taus', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1234, 'pd4', 'Tonkori', 'tonkori', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1235, 'bsa', 'Ukuleles', 'ukuleles', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1236, 'twp', 'Veenas', 'veenas', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1237, 'fm2', 'Violins', 'violins', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1238, 'hcy', 'Alto Horns', 'alto-horns', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1239, 'tsq', 'Bagpipes', 'bagpipes', 1, 1, '2017-07-06 00:28:58', '2017-07-20 08:08:57', NULL, 0),
(1240, 'uqw', 'Baritones', 'baritones', 1, 1, '2017-07-06 00:28:59', '2017-07-20 08:08:57', NULL, 0),
(1241, 'ums', 'Bassoons', 'bassoons', 1, 1, '2017-07-06 00:28:59', '2017-07-20 08:08:57', NULL, 0),
(1242, 's21', 'Bugles', 'bugles', 1, 1, '2017-07-06 00:28:59', '2017-07-20 08:08:57', NULL, 0),
(1243, 'lpy', 'Clarinets', 'clarinets', 1, 1, '2017-07-06 00:28:59', '2017-07-20 08:08:57', NULL, 0),
(1244, 'sud', 'Concertinas', 'concertinas', 1, 1, '2017-07-06 00:28:59', '2017-07-20 08:08:57', NULL, 0),
(1245, 'z64', 'Conches', 'conches', 1, 1, '2017-07-06 00:28:59', '2017-07-20 08:08:57', NULL, 0),
(1246, 'nal', 'Cornets', 'cornets', 1, 1, '2017-07-06 00:29:00', '2017-07-20 08:08:57', NULL, 0),
(1247, '1wg', 'Euphoniums', 'euphoniums', 1, 1, '2017-07-06 00:29:00', '2017-07-20 08:08:57', NULL, 0),
(1248, 'w01', 'Flugelhorns', 'flugelhorns', 1, 1, '2017-07-06 00:29:01', '2017-07-20 08:08:57', NULL, 0),
(1249, '9f0', 'Flutes', 'flutes', 1, 1, '2017-07-06 00:29:01', '2017-07-20 08:08:57', NULL, 0),
(1250, '2u1', 'Harmonicas', 'harmonicas', 1, 1, '2017-07-06 00:29:01', '2017-07-20 08:08:57', NULL, 0),
(1251, '2m1', 'Mellophones', 'mellophones', 1, 1, '2017-07-06 00:29:02', '2017-07-20 08:08:57', NULL, 0),
(1252, 'z1t', 'Oboes', 'oboes', 1, 1, '2017-07-06 00:29:02', '2017-07-20 08:08:57', NULL, 0),
(1253, 'x87', 'Pungis', 'pungis', 1, 1, '2017-07-06 00:29:02', '2017-07-20 08:08:57', NULL, 0),
(1254, '0b1', 'Recorders', 'recorders', 1, 1, '2017-07-06 00:29:02', '2017-07-20 08:08:57', NULL, 0),
(1255, '9fo', 'Saxaphones', 'saxaphones', 1, 1, '2017-07-06 00:29:02', '2017-07-20 08:08:57', NULL, 0),
(1256, 'y9y', 'Shehanais', 'shehanais', 1, 1, '2017-07-06 00:29:02', '2017-07-20 08:08:57', NULL, 0),
(1257, 'c5u', 'Sousaphones', 'sousaphones', 1, 1, '2017-07-06 00:29:02', '2017-07-20 08:08:57', NULL, 0),
(1258, 'kme', 'Tibetan Horns', 'tibetan-horns', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1259, 'okq', 'Trombones', 'trombones', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1260, 'sat', 'Trumpets', 'trumpets', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1261, '5bw', 'Tubas', 'tubas', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1262, 'g1t', 'Calendars', 'calendars', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1263, 'e26', 'Diaries', 'diaries', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1264, 'ncq', 'Journals', 'journals', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1265, 'qon', 'Memo Pads', 'memo-pads', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1266, '3i9', 'Notebooks', 'notebooks', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1267, 's6n', 'Notepads', 'notepads', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1268, '5ql', 'Planners & Organisers', 'planners-organisers', 1, 1, '2017-07-06 00:29:03', '2017-07-20 08:08:57', NULL, 0),
(1269, 'wry', 'Record Keeping Books', 'record-keeping-books', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1270, 'hxg', 'Scrapbook Kits', 'scrapbook-kits', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1271, 'e4z', 'Utility Pads', 'utility-pads', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1272, 'sj8', 'Analytical Scales', 'analytical-scales', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1273, 'qtx', 'Floor Cleaning Machines', 'floor-cleaning-machines', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1274, 'lhc', 'Home Security Cameras', 'home-security-cameras', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1275, '2ts', 'Label Printers', 'label-printers', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1276, 'y2h', 'Lamination Machines', 'lamination-machines', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1277, 'gcy', 'Metal Detectors', 'metal-detectors', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1278, '30t', 'Paper Jogger Machines', 'paper-jogger-machines', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1279, 'u6i', 'Paper Shredders', 'paper-shredders', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1280, 'rf4', 'Spiral Binders', 'spiral-binders', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1281, 'naz', 'Tagging Guns', 'tagging-guns', 1, 1, '2017-07-06 00:29:04', '2017-07-20 08:08:57', NULL, 0),
(1282, 'poy', 'Vending Machines', 'vending-machines', 1, 1, '2017-07-06 00:29:05', '2017-07-20 08:08:57', NULL, 0),
(1283, 'wcv', 'Alphabet Letters', 'alphabet-letters', 1, 1, '2017-07-06 00:29:05', '2017-07-20 08:08:57', NULL, 0),
(1284, 'po3', 'Art & Craft', 'art-craft', 1, 1, '2017-07-06 00:29:05', '2017-07-20 08:08:57', NULL, 0),
(1285, 'k7e', 'Boards & Dusters', 'boards-dusters', 1, 1, '2017-07-06 00:29:05', '2017-07-20 08:08:57', NULL, 0),
(1286, 'lcd', 'Book Wraps & Covers', 'book-wraps-covers', 1, 1, '2017-07-06 00:29:06', '2017-07-20 08:08:57', NULL, 0),
(1287, 'npi', 'Canvases', 'canvases', 1, 1, '2017-07-06 00:29:06', '2017-07-20 08:08:57', NULL, 0),
(1288, 'wdo', 'Chalks', 'chalks', 1, 1, '2017-07-06 00:29:06', '2017-07-20 08:08:57', NULL, 0),
(1289, 'wn5', 'Geometry, Pen & Pencil boxes', 'geometry-pen-pencil-boxes', 1, 1, '2017-07-06 00:29:06', '2017-07-20 08:08:57', NULL, 0),
(1290, 'xai', 'Globes', 'globes', 1, 1, '2017-07-06 00:29:06', '2017-07-20 08:08:57', NULL, 0),
(1291, 'geh', 'Pencil Grips', 'pencil-grips', 1, 1, '2017-07-06 00:29:06', '2017-07-20 08:08:57', NULL, 0),
(1292, 'hth', 'Pencils, Pen & Pencil Boxes', 'pencils-pen-pencil-boxes', 1, 1, '2017-07-06 00:29:06', '2017-07-20 08:08:57', NULL, 0),
(1293, 'ais', 'Pens & Notebooks', 'pens-notebooks', 1, 1, '2017-07-06 00:29:06', '2017-07-20 08:08:57', NULL, 0),
(1294, 'y23', 'Pocket Charts', 'pocket-charts', 1, 1, '2017-07-06 00:29:07', '2017-07-20 08:08:57', NULL, 0),
(1295, 'fzl', 'Project Kits', 'project-kits', 1, 1, '2017-07-06 00:29:07', '2017-07-20 08:08:57', NULL, 0),
(1296, '6da', 'School Bells', 'school-bells', 1, 1, '2017-07-06 00:29:07', '2017-07-20 08:08:57', NULL, 0),
(1297, 'b6o', 'Sketch & Paint Markers', 'sketch-paint-markers', 1, 1, '2017-07-06 00:29:07', '2017-07-20 08:08:57', NULL, 0),
(1298, '615', 'Wooden Geometric Objects', 'wooden-geometric-objects', 1, 1, '2017-07-06 00:29:07', '2017-07-20 08:08:57', NULL, 0),
(1299, 'hbq', 'Dog Waste Pickup Bags', 'dog-waste-pickup-bags', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1300, '09c', 'Horse Hoof Picks', 'horse-hoof-picks', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1301, 'fzt', 'Litter Scoops', 'litter-scoops', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1302, '5i4', 'Odor Removers', 'odor-removers', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1303, '80p', 'Pet Cage Cleaner', 'pet-cage-cleaner', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1304, 'wvt', 'Pet Litter Tray Refills', 'pet-litter-tray-refills', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1305, '90s', 'Bird Play Stands', 'bird-play-stands', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1306, 'fsi', 'Brushes & Combs', 'brushes-combs', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1307, 'f7m', 'Clipper Blade Cases', 'clipper-blade-cases', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1308, 'iqn', 'Clipper Oils', 'clipper-oils', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1309, 'fk8', 'Horse Grooming Kits', 'horse-grooming-kits', 1, 1, '2017-07-06 00:29:08', '2017-07-20 08:08:57', NULL, 0),
(1310, 'xfl', 'Horseshoes', 'horseshoes', 1, 1, '2017-07-06 00:29:09', '2017-07-20 08:08:57', NULL, 0),
(1311, 'tmc', 'Odor Control', 'odor-control', 1, 1, '2017-07-06 00:29:09', '2017-07-20 08:08:57', NULL, 0),
(1312, 'v1t', 'Paw Care', 'paw-care', 1, 1, '2017-07-06 00:29:09', '2017-07-20 08:08:57', NULL, 0),
(1313, 'f0r', 'Pet Bath Mats', 'pet-bath-mats', 1, 1, '2017-07-06 00:29:09', '2017-07-20 08:08:57', NULL, 0),
(1314, '5c5', 'Pet Bath Towels', 'pet-bath-towels', 1, 1, '2017-07-06 00:29:09', '2017-07-20 08:08:57', NULL, 0),
(1315, 'jlc', 'Pet Bathtubs', 'pet-bathtubs', 1, 1, '2017-07-06 00:29:09', '2017-07-20 08:08:57', NULL, 0),
(1316, '0bv', 'Pet Hair Styling', 'pet-hair-styling', 1, 1, '2017-07-06 00:29:09', '2017-07-20 08:08:57', NULL, 0),
(1317, '63j', 'Pet Hair Trimmers', 'pet-hair-trimmers', 1, 1, '2017-07-06 00:29:09', '2017-07-20 08:08:57', NULL, 0),
(1318, '2yd', 'Pet Spa Kits', 'pet-spa-kits', 1, 1, '2017-07-06 00:29:09', '2017-07-20 08:08:57', NULL, 0),
(1319, '9oh', 'Skin & Coat Care', 'skin-coat-care', 1, 1, '2017-07-06 00:29:10', '2017-07-20 08:08:57', NULL, 0),
(1320, 'htv', 'Sweat Scrapers', 'sweat-scrapers', 1, 1, '2017-07-06 00:29:10', '2017-07-20 08:08:57', NULL, 0),
(1321, 'za0', 'Aquarium Reef Glue', 'aquarium-reef-glue', 1, 1, '2017-07-06 00:29:10', '2017-07-20 08:08:57', NULL, 0),
(1322, '065', 'Habitat Essentials', 'habitat-essentials', 1, 1, '2017-07-06 00:29:10', '2017-07-20 08:08:57', NULL, 0),
(1323, '3v8', 'Collar Charms', 'collar-charms', 1, 1, '2017-07-06 00:29:10', '2017-07-20 08:08:57', NULL, 0),
(1324, 't7c', 'Medical Supplies', 'medical-supplies', 1, 1, '2017-07-06 00:29:10', '2017-07-20 08:08:57', NULL, 0),
(1325, 'vp3', 'Pet Health Supplements', 'pet-health-supplements', 1, 1, '2017-07-06 00:29:10', '2017-07-20 08:08:57', NULL, 0),
(1326, 'rcn', 'Pet Hygiene', 'pet-hygiene', 1, 1, '2017-07-06 00:29:10', '2017-07-20 08:08:57', NULL, 0),
(1327, 'pd1', 'Pet Medicines', 'pet-medicines', 1, 1, '2017-07-06 00:29:10', '2017-07-20 08:08:57', NULL, 0),
(1328, 'ka7', 'Pet Treats & Chews', 'pet-treats-chews', 1, 1, '2017-07-06 00:29:10', '2017-07-20 08:08:57', NULL, 0),
(1329, 'l4g', 'Car Pet Steps', 'car-pet-steps', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1330, 'ra9', 'Horse Boots', 'horse-boots', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1331, 'fdl', 'Horse Tail Wraps', 'horse-tail-wraps', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1332, '0tq', 'Jump Cups', 'jump-cups', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1333, '1au', 'Pet Perch', 'pet-perch', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1334, 'qhy', 'Sheath Cleaner', 'sheath-cleaner', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1335, '0oh', 'Snake Sticks', 'snake-sticks', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1336, 'zwh', 'Raksha Bandhan', 'raksha-bandhan', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1337, 'bfw', 'Ab Exercisers', 'ab-exercisers', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1338, '08u', 'Bars', 'bars', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1339, 'w0f', 'Bottles & Shakers', 'bottles-shakers', 1, 1, '2017-07-06 00:29:11', '2017-07-20 08:08:57', NULL, 0),
(1340, 'nxq', 'Cardio Equipments', 'cardio-equipments', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1341, 'tnd', 'Cross Trainers', 'cross-trainers', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1342, 'ru5', 'Dumbbells', 'dumbbells', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1343, 'e2n', 'Exercise Bikes', 'exercise-bikes', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1344, 'xi2', 'Finger Grips', 'finger-grips', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1345, 'r7z', 'Fitness Bags', 'fitness-bags', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1346, 'g18', 'Fitness Balls', 'fitness-balls', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1347, 'w98', 'Fitness Bands', 'fitness-bands', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1348, 'gv1', 'Free Weights', 'free-weights', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1349, 'xnw', 'Gloves', 'gloves', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1350, 'es5', 'Hand Grips', 'hand-grips', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1351, 'jas', 'Heart Rate Monitors', 'heart-rate-monitors', 1, 1, '2017-07-06 00:29:12', '2017-07-20 08:08:57', NULL, 0),
(1352, 'pan', 'Home Gym Combos', 'home-gym-combos', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0),
(1353, 'rxg', 'Home Gym Equipment', 'home-gym-equipment', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0),
(1354, 'l46', 'Kits', 'kits', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0),
(1355, 'fb4', 'Resistance Tubes', 'resistance-tubes', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0),
(1356, 't03', 'Skipping Ropes', 'skipping-ropes', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0),
(1357, 'kyl', 'Steppers', 'steppers', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0),
(1358, 'kvy', 'Strength', 'strength', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0),
(1359, 'hzv', 'Supports', 'supports', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0),
(1360, 'vj4', 'Treadmills', 'treadmills', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0),
(1361, 'v4u', 'Whistles', 'whistles', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0),
(1362, '3rn', 'Yoga', 'yoga', 1, 1, '2017-07-06 00:29:13', '2017-07-20 08:08:57', NULL, 0);
INSERT INTO `product_categories` (`category_id`, `category_code`, `category`, `url_str`, `replacement_service_policy_id`, `status`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1363, '1ou', 'Badminton', 'badminton', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1364, 'z54', 'Pickleball', 'pickleball', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1365, 'z2r', 'Squash', 'squash', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1366, '3ic', 'Table Tennis', 'table-tennis', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1367, '1sa', 'Tennis', 'tennis', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1368, 'ti1', 'Baseball', 'baseball', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1369, 'nyb', 'Basketball', 'basketball', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1370, 'xgp', 'Broomball', 'broomball', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1371, 'js9', 'Cricket', 'cricket', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1372, '7g5', 'Football', 'football', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1373, 'kbv', 'Handball', 'handball', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1374, '6g0', 'Hockey', 'hockey', 1, 1, '2017-07-06 00:29:14', '2017-07-20 08:08:57', NULL, 0),
(1375, 'tgo', 'Lacrosse', 'lacrosse', 1, 1, '2017-07-06 00:29:15', '2017-07-20 08:08:57', NULL, 0),
(1376, 'cp6', 'Other Ball Sports', 'other-ball-sports', 1, 1, '2017-07-06 00:29:15', '2017-07-20 08:08:57', NULL, 0),
(1377, 'tct', 'Rugby', 'rugby', 1, 1, '2017-07-06 00:29:15', '2017-07-20 08:08:57', NULL, 0),
(1378, 'exa', 'Volleyball', 'volleyball', 1, 1, '2017-07-06 00:29:15', '2017-07-20 08:08:57', NULL, 0),
(1379, 'iv4', 'Gardening Tools', 'gardening-tools', 1, 1, '2017-07-06 00:29:15', '2017-07-20 08:08:57', NULL, 0),
(1380, '0z5', 'Hardware & Electricals', 'hardware-electricals', 1, 1, '2017-07-06 00:29:15', '2017-07-20 08:08:57', NULL, 0),
(1381, 'lb7', 'Home Organizers & Storage', 'home-organizers-storage', 1, 1, '2017-07-06 00:29:15', '2017-07-20 08:08:57', NULL, 0),
(1382, 'ojp', 'Home Utilities', 'home-utilities', 1, 1, '2017-07-06 00:29:16', '2017-07-20 08:08:57', NULL, 0),
(1383, 'wgi', 'Paint Equipments & Supplies', 'paint-equipments-supplies', 1, 1, '2017-07-06 00:29:16', '2017-07-20 08:08:57', NULL, 0),
(1384, '6jz', 'Power Tools', 'power-tools', 1, 1, '2017-07-06 00:29:16', '2017-07-20 08:08:57', NULL, 0),
(1385, 'muw', 'Tools Combo', 'tools-combo', 3, 1, '2017-07-06 00:29:16', '2018-03-07 16:53:30', NULL, 0),
(1386, '7hj', 'Teethers & Soothers', 'teethers-soothers', 1, 1, '2017-07-06 00:29:17', '2017-07-20 08:08:57', NULL, 0),
(1387, 'ci5', 'Beanbag Toss Boards', 'beanbag-toss-boards', 1, 1, '2017-07-06 00:29:17', '2017-07-20 08:08:57', NULL, 0),
(1388, 'fqz', 'Doll Accessories', 'doll-accessories', 1, 1, '2017-07-06 00:29:17', '2017-07-20 08:08:57', NULL, 0),
(1389, '89e', 'Doll Houses & Play Sets', 'doll-houses-play-sets', 1, 1, '2017-07-06 00:29:17', '2017-07-20 08:08:57', NULL, 0),
(1390, '3r7', 'Dollhouse Accessories', 'dollhouse-accessories', 1, 1, '2017-07-06 00:29:17', '2017-07-20 08:08:57', NULL, 0),
(1391, '6og', 'Dolls & Accessories', 'dolls-accessories', 1, 1, '2017-07-06 00:29:17', '2017-07-20 08:08:57', NULL, 0),
(1392, 'yec', 'Smart Watch Screen Guards', 'smart-watch-screen-guards', 1, 1, '2017-07-06 00:29:18', '2017-07-20 08:08:57', NULL, 0),
(1393, 'bux', 'Knitting Design Stations', 'knitting-design-stations', 1, 1, '2017-07-06 00:29:18', '2017-07-20 08:08:57', NULL, 0),
(1394, 'zi4', 'Music Boxes', 'music-boxes', 1, 1, '2017-07-06 00:29:18', '2017-07-20 08:08:57', NULL, 0),
(1395, '4i8', 'Balloons & Decoration', 'balloons-decoration', 1, 1, '2017-07-06 00:29:18', '2017-07-20 08:08:57', NULL, 0),
(1396, 'm05', 'Cards', 'cards', 1, 1, '2017-07-06 00:29:18', '2017-07-20 08:08:57', NULL, 0),
(1397, '4zg', 'Equipment & Supplies', 'equipment-supplies', 1, 1, '2017-07-06 00:29:18', '2017-07-20 08:08:57', NULL, 0),
(1398, 'gu1', 'Party & Gift Bags', 'party-gift-bags', 1, 1, '2017-07-06 00:29:18', '2017-07-20 08:08:57', NULL, 0),
(1399, 'n5u', 'Plates & Tableware', 'plates-tableware', 1, 1, '2017-07-06 00:29:18', '2017-07-20 08:08:57', NULL, 0),
(1400, 'yp5', 'Posts and Ropes', 'posts-and-ropes', 1, 1, '2017-07-06 00:29:18', '2017-07-20 08:08:57', NULL, 0),
(1401, 'ohz', 'Damage Protection Plan', 'damage-protection-plan', 1, 1, '2017-07-06 00:29:19', '2018-03-07 16:52:30', NULL, 0),
(1402, 'vgu', 'Digital Voucher Codes', 'digital-voucher-codes', 1, 1, '2017-07-06 00:29:19', '2017-07-20 08:08:57', NULL, 0),
(1403, 'bds', 'Table Clocks', 'table-clocks', 1, 1, '2017-07-06 00:29:19', '2018-03-07 16:53:08', NULL, 0),
(1404, 'q25', 'Wall Clocks', 'wall-clocks', 1, 1, '2017-07-06 00:29:19', '2017-07-20 08:08:57', NULL, 0),
(1405, 're1', 'Pocket Watch Chains', 'pocket-watch-chains', 1, 1, '2017-07-06 00:29:19', '2018-03-07 16:54:05', NULL, 0),
(1406, '2xk', 'Watch Boxes', 'watch-boxes', 1, 1, '2017-07-06 00:29:19', '2017-07-20 08:08:57', NULL, 0),
(1407, 'jlf', 'Watch Protective Cases & Covers', 'watch-protective-cases-covers', 1, 1, '2017-07-06 00:29:19', '2017-07-20 08:08:57', NULL, 0),
(1408, 'fis', 'Watch Repair Kits', 'watch-repair-kits', 1, 1, '2017-07-06 00:29:19', '2017-07-20 08:08:57', NULL, 0),
(1409, 'uo0', 'Watch Straps', 'watch-straps', 3, 1, '2017-07-06 00:29:19', '2018-03-07 16:52:23', NULL, 0),
(1410, '30l', 'Watch Winders', 'watch-winders', 1, 1, '2017-07-06 00:29:20', '2017-07-20 08:08:57', NULL, 0),
(1411, 'mfa', 'Wrist Bands', 'wrist-bands', 1, 1, '2017-07-06 00:29:20', '2017-08-20 19:42:23', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories_parents`
--

CREATE TABLE IF NOT EXISTS `product_categories_parents` (
  `pcp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` smallint(6) unsigned NOT NULL,
  `parent_category_id` smallint(6) unsigned DEFAULT NULL,
  `parents` text,
  `cat_lftnode` int(11) unsigned DEFAULT NULL,
  `cat_rgtnode` int(11) unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1- Deleted',
  PRIMARY KEY (`pcp_id`),
  UNIQUE KEY `category_id_2` (`category_id`,`parent_category_id`),
  KEY `category_id` (`category_id`),
  KEY `parent_category_id` (`parent_category_id`),
  KEY `cat_rgtnode` (`cat_rgtnode`),
  KEY `cat_rgtnode_2` (`cat_rgtnode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1605 ;

--
-- Dumping data for table `product_categories_parents`
--

INSERT INTO `product_categories_parents` (`pcp_id`, `category_id`, `parent_category_id`, `parents`, `cat_lftnode`, `cat_rgtnode`, `is_deleted`) VALUES
(1, 1, NULL, NULL, 1, 3208, 0),
(2, 2, 1, '1', 2, 109, 0),
(3, 3, 1, '1', 110, 149, 0),
(4, 4, 1, '1', 150, 175, 0),
(5, 5, 1, '1', 176, 275, 0),
(6, 6, 1, '1', 276, 393, 0),
(7, 7, 1, '1', 394, 489, 0),
(8, 8, 1, '1', 490, 565, 0),
(9, 9, 1, '1', 566, 685, 0),
(10, 10, 1, '1', 686, 739, 0),
(11, 11, 1, '1', 740, 865, 0),
(12, 12, 1, '1', 866, 881, 0),
(13, 13, 1, '1', 882, 889, 0),
(14, 14, 1, '1', 890, 979, 0),
(15, 15, 1, '1', 980, 1025, 0),
(16, 16, 1, '1', 1026, 1131, 0),
(17, 17, 1, '1', 1132, 1249, 0),
(18, 18, 1, '1', 1250, 1353, 0),
(19, 19, 1, '1', 1354, 1441, 0),
(20, 20, 1, '1', 1442, 1489, 0),
(21, 21, 1, '1', 1490, 1601, 0),
(22, 22, 1, '1', 1602, 1697, 0),
(23, 23, 1, '1', 1698, 1795, 0),
(24, 24, 1, '1', 1796, 1905, 0),
(25, 25, 1, '1', 1906, 2005, 0),
(26, 26, 1, '1', 2006, 2041, 0),
(27, 27, 1, '1', 2042, 2137, 0),
(28, 28, 1, '1', 2138, 2189, 0),
(29, 29, 1, '1', 2190, 2313, 0),
(30, 30, 1, '1', 2314, 2417, 0),
(31, 31, 1, '1', 2418, 2477, 0),
(32, 32, 1, '1', 2478, 2589, 0),
(33, 33, 1, '1', 2590, 2633, 0),
(34, 34, 1, '1', 2634, 2679, 0),
(35, 35, 1, '1', 2680, 2737, 0),
(36, 36, 1, '1', 2738, 2765, 0),
(37, 37, 1, '1', 2766, 2905, 0),
(38, 38, 1, '1', 2906, 2921, 0),
(39, 39, 1, '1', 2922, 3017, 0),
(40, 40, 1, '1', 3018, 3021, 0),
(41, 41, 1, '1', 3022, 3173, 0),
(42, 42, 1, '1', 3174, 3185, 0),
(43, 43, 1, '1', 3186, 3207, 0),
(44, 44, 2, '2', 3, 50, 0),
(45, 45, 2, '2', 51, 56, 0),
(46, 46, 2, '2', 57, 74, 0),
(47, 47, 2, '2', 75, 82, 0),
(48, 48, 2, '2', 83, 98, 0),
(49, 49, 2, '2', 99, 100, 0),
(50, 50, 2, '2', 101, 108, 0),
(51, 51, 3, '3', 111, 112, 0),
(52, 52, 3, '3', 113, 114, 0),
(53, 53, 3, '3', 115, 116, 0),
(54, 54, 3, '3', 117, 118, 0),
(55, 55, 3, '3', 119, 138, 0),
(56, 56, 3, '3', 139, 140, 0),
(57, 57, 3, '3', 141, 142, 0),
(58, 58, 3, '3', 143, 144, 0),
(59, 59, 3, '3', 145, 146, 0),
(60, 60, 3, '3', 147, 148, 0),
(61, 61, 4, '4', 151, 160, 0),
(62, 62, 4, '4', 161, 168, 0),
(63, 63, 4, '4', 169, 170, 0),
(64, 64, 4, '4', 171, 174, 0),
(65, 65, 5, '5', 177, 202, 0),
(66, 66, 5, '5', 203, 230, 0),
(67, 67, 5, '5', 231, 252, 0),
(68, 68, 5, '5', 253, 270, 0),
(69, 69, 5, '5', 271, 274, 0),
(70, 70, 6, '6', 277, 304, 0),
(71, 71, 6, '6', 305, 320, 0),
(72, 72, 6, '6', 321, 322, 0),
(73, 73, 6, '6', 323, 324, 0),
(74, 74, 6, '6', 325, 326, 0),
(75, 75, 6, '6', 327, 328, 0),
(76, 76, 6, '6', 329, 370, 0),
(77, 77, 6, '6', 371, 372, 0),
(78, 78, 6, '6', 373, 390, 0),
(79, 79, 6, '6', 391, 392, 0),
(80, 80, 7, '7', 395, 406, 0),
(81, 81, 7, '7', 407, 428, 0),
(82, 82, 7, '7', 429, 438, 0),
(83, 83, 7, '7', 439, 462, 0),
(84, 84, 7, '7', 463, 488, 0),
(85, 85, 8, '8', 491, 508, 0),
(86, 86, 8, '8', 509, 520, 0),
(87, 87, 8, '8', 521, 542, 0),
(88, 88, 8, '8', 543, 564, 0),
(89, 89, 9, '9', 567, 610, 0),
(90, 90, 9, '9', 611, 684, 0),
(91, 91, 10, '10', 687, 704, 0),
(92, 92, 10, '10', 705, 712, 0),
(93, 93, 10, '10', 713, 738, 0),
(94, 94, 11, '11', 741, 768, 0),
(95, 95, 11, '11', 769, 864, 0),
(96, 96, 12, '12', 867, 868, 0),
(97, 97, 12, '12', 869, 870, 0),
(98, 98, 12, '12', 871, 876, 0),
(99, 99, 12, '12', 877, 878, 0),
(100, 100, 12, '12', 879, 880, 0),
(101, 101, 13, '13', 883, 884, 0),
(102, 102, 13, '13', 885, 886, 0),
(103, 103, 13, '13', 887, 888, 0),
(104, 104, 14, '14', 891, 892, 0),
(105, 105, 14, '14', 893, 894, 0),
(106, 106, 14, '14', 895, 912, 0),
(107, 107, 14, '14', 913, 938, 0),
(108, 108, 14, '14', 939, 940, 0),
(109, 109, 14, '14', 941, 950, 0),
(110, 110, 14, '14', 951, 952, 0),
(111, 111, 14, '14', 953, 958, 0),
(112, 112, 14, '14', 959, 960, 0),
(113, 113, 14, '14', 961, 962, 0),
(114, 114, 14, '14', 963, 968, 0),
(115, 115, 14, '14', 969, 974, 0),
(116, 116, 14, '14', 975, 976, 0),
(117, 117, 14, '14', 977, 978, 0),
(118, 118, 15, '15', 981, 994, 0),
(119, 119, 15, '15', 995, 1002, 0),
(120, 120, 15, '15', 1003, 1020, 0),
(121, 121, 15, '15', 1021, 1024, 0),
(122, 122, 16, '16', 1027, 1052, 0),
(123, 123, 16, '16', 1053, 1076, 0),
(124, 124, 16, '16', 1077, 1106, 0),
(125, 125, 16, '16', 1107, 1128, 0),
(126, 126, 16, '16', 1129, 1130, 0),
(127, 127, 17, '17', 1133, 1154, 0),
(128, 128, 17, '17', 1155, 1162, 0),
(129, 129, 17, '17', 1163, 1168, 0),
(130, 130, 17, '17', 1169, 1176, 0),
(131, 131, 17, '17', 1177, 1202, 0),
(132, 132, 17, '17', 1203, 1206, 0),
(133, 133, 17, '17', 1207, 1212, 0),
(134, 134, 17, '17', 1213, 1216, 0),
(135, 135, 17, '17', 1217, 1228, 0),
(136, 136, 17, '17', 1229, 1244, 0),
(137, 137, 17, '17', 1245, 1248, 0),
(138, 138, 18, '18', 1251, 1256, 0),
(139, 139, 18, '18', 1257, 1280, 0),
(140, 140, 18, '18', 1281, 1322, 0),
(141, 141, 18, '18', 1323, 1328, 0),
(142, 142, 18, '18', 1329, 1350, 0),
(143, 143, 18, '18', 1351, 1352, 0),
(144, 144, 19, '19', 1355, 1356, 0),
(145, 145, 19, '19', 1357, 1374, 0),
(146, 146, 19, '19', 1375, 1394, 0),
(147, 147, 19, '19', 1395, 1420, 0),
(148, 148, 19, '19', 1421, 1440, 0),
(149, 149, 20, '20', 1443, 1458, 0),
(150, 150, 20, '20', 1459, 1488, 0),
(151, 151, 21, '21', 1491, 1534, 0),
(152, 152, 21, '21', 1535, 1600, 0),
(153, 153, 22, '22', 1603, 1636, 0),
(154, 154, 22, '22', 1637, 1664, 0),
(155, 155, 22, '22', 1665, 1666, 0),
(156, 156, 22, '22', 1667, 1668, 0),
(157, 157, 22, '22', 1669, 1674, 0),
(158, 158, 22, '22', 1675, 1696, 0),
(159, 159, 23, '23', 1699, 1738, 0),
(160, 160, 23, '23', 1739, 1750, 0),
(161, 161, 23, '23', 1751, 1776, 0),
(162, 162, 23, '23', 1777, 1792, 0),
(163, 163, 23, '23', 1793, 1794, 0),
(164, 164, 24, '24', 1797, 1812, 0),
(165, 165, 24, '24', 1813, 1828, 0),
(166, 166, 24, '24', 1829, 1848, 0),
(167, 167, 24, '24', 1849, 1850, 0),
(168, 168, 24, '24', 1851, 1858, 0),
(169, 169, 24, '24', 1859, 1872, 0),
(170, 170, 24, '24', 1873, 1880, 0),
(171, 171, 24, '24', 1881, 1896, 0),
(172, 172, 24, '24', 1897, 1904, 0),
(173, 173, 25, '25', 1907, 1912, 0),
(174, 174, 25, '25', 1913, 1918, 0),
(175, 175, 25, '25', 1919, 1930, 0),
(176, 176, 25, '25', 1931, 1946, 0),
(177, 177, 25, '25', 1947, 1964, 0),
(178, 178, 25, '25', 1965, 1980, 0),
(179, 179, 25, '25', 1981, 1990, 0),
(180, 180, 25, '25', 1991, 2000, 0),
(181, 181, 25, '25', 2001, 2004, 0),
(182, 182, 26, '26', 2007, 2014, 0),
(183, 183, 26, '26', 2015, 2030, 0),
(184, 184, 26, '26', 2031, 2040, 0),
(185, 185, 27, '27', 2043, 2064, 0),
(186, 186, 27, '27', 2065, 2092, 0),
(187, 187, 27, '27', 2093, 2106, 0),
(188, 188, 27, '27', 2107, 2112, 0),
(189, 189, 27, '27', 2113, 2114, 0),
(190, 190, 27, '27', 2115, 2120, 0),
(191, 191, 27, '27', 2121, 2136, 0),
(192, 192, 28, '28', 2139, 2166, 0),
(193, 193, 28, '28', 2167, 2188, 0),
(194, 194, 29, '29', 2191, 2218, 0),
(195, 195, 29, '29', 2219, 2244, 0),
(196, 196, 29, '29', 2245, 2274, 0),
(197, 197, 29, '29', 2275, 2286, 0),
(198, 198, 29, '29', 2287, 2296, 0),
(199, 199, 29, '29', 2297, 2306, 0),
(200, 200, 29, '29', 2307, 2310, 0),
(201, 201, 29, '29', 2311, 2312, 0),
(202, 202, 30, '30', 2315, 2320, 0),
(203, 203, 30, '30', 2321, 2412, 0),
(204, 204, 30, '30', 2413, 2414, 0),
(205, 205, 30, '30', 2415, 2416, 0),
(206, 206, 31, '31', 2419, 2422, 0),
(207, 207, 31, '31', 2423, 2476, 0),
(208, 208, 32, '32', 2479, 2524, 0),
(209, 209, 32, '32', 2525, 2558, 0),
(210, 210, 32, '32', 2559, 2580, 0),
(211, 211, 32, '32', 2581, 2582, 0),
(212, 212, 32, '32', 2583, 2584, 0),
(213, 213, 32, '32', 2585, 2586, 0),
(214, 214, 32, '32', 2587, 2588, 0),
(215, 215, 33, '33', 2591, 2626, 0),
(216, 216, 33, '33', 2627, 2632, 0),
(217, 217, 34, '34', 2635, 2636, 0),
(218, 218, 34, '34', 2637, 2644, 0),
(219, 219, 34, '34', 2645, 2678, 0),
(220, 220, 35, '35', 2681, 2732, 0),
(221, 221, 35, '35', 2733, 2734, 0),
(222, 222, 35, '35', 2735, 2736, 0),
(223, 223, 36, '36', 2739, 2740, 0),
(224, 224, 36, '36', 2741, 2762, 0),
(225, 193, 36, '36', 2763, 2764, 0),
(226, 225, 37, '37', 2767, 2790, 0),
(227, 226, 37, '37', 2791, 2844, 0),
(228, 227, 37, '37', 2845, 2858, 0),
(229, 228, 37, '37', 2859, 2894, 0),
(230, 229, 37, '37', 2895, 2900, 0),
(231, 230, 37, '37', 2901, 2904, 0),
(232, 231, 38, '38', 2907, 2920, 0),
(233, 232, 39, '39', 2923, 2938, 0),
(234, 233, 39, '39', 2939, 2942, 0),
(235, 234, 39, '39', 2943, 2944, 0),
(236, 235, 39, '39', 2945, 3004, 0),
(237, 236, 39, '39', 3005, 3016, 0),
(238, 181, 40, '40', 3019, 3020, 0),
(239, 237, 41, '41', 3023, 3024, 0),
(240, 238, 41, '41', 3025, 3048, 0),
(241, 67, 41, '41', 3049, 3050, 0),
(242, 239, 41, '41', 3051, 3080, 0),
(243, 240, 41, '41', 3081, 3082, 0),
(244, 241, 41, '41', 3083, 3084, 0),
(245, 242, 41, '41', 3085, 3104, 0),
(246, 243, 41, '41', 3105, 3106, 0),
(247, 244, 41, '41', 3107, 3110, 0),
(248, 245, 41, '41', 3111, 3112, 0),
(249, 246, 41, '41', 3113, 3124, 0),
(250, 247, 41, '41', 3125, 3126, 0),
(251, 248, 41, '41', 3127, 3130, 0),
(252, 249, 41, '41', 3131, 3132, 0),
(253, 250, 41, '41', 3133, 3134, 0),
(254, 251, 41, '41', 3135, 3138, 0),
(255, 252, 41, '41', 3139, 3140, 0),
(256, 253, 41, '41', 3141, 3156, 0),
(257, 254, 41, '41', 3157, 3158, 0),
(258, 255, 41, '41', 3159, 3160, 0),
(259, 256, 41, '41', 3161, 3168, 0),
(260, 257, 41, '41', 3169, 3170, 0),
(261, 258, 41, '41', 3171, 3172, 0),
(262, 259, 42, '42', 3175, 3176, 0),
(263, 260, 42, '42', 3177, 3178, 0),
(264, 261, 42, '42', 3179, 3180, 0),
(265, 262, 42, '42', 3181, 3182, 0),
(266, 263, 42, '42', 3183, 3184, 0),
(267, 264, 43, '43', 3187, 3194, 0),
(268, 265, 43, '43', 3195, 3196, 0),
(269, 266, 43, '43', 3197, 3198, 0),
(270, 267, 43, '43', 3199, 3200, 0),
(271, 268, 43, '43', 3201, 3206, 0),
(272, 269, 44, '44', 4, 5, 0),
(273, 270, 44, '44', 6, 21, 0),
(274, 271, 44, '44', 22, 23, 0),
(275, 272, 44, '44', 24, 25, 0),
(276, 273, 44, '44', 26, 27, 0),
(277, 274, 44, '44', 28, 29, 0),
(278, 275, 44, '44', 30, 31, 0),
(279, 276, 44, '44', 32, 33, 0),
(280, 277, 44, '44', 34, 35, 0),
(281, 278, 44, '44', 36, 37, 0),
(282, 279, 44, '44', 38, 39, 0),
(283, 280, 44, '44', 40, 41, 0),
(284, 281, 44, '44', 42, 43, 0),
(285, 282, 44, '44', 44, 45, 0),
(286, 283, 44, '44', 46, 47, 0),
(287, 284, 44, '44', 48, 49, 0),
(288, 285, 45, '45', 52, 53, 0),
(289, 286, 45, '45', 54, 55, 0),
(290, 287, 46, '46', 58, 59, 0),
(291, 288, 46, '46', 60, 63, 0),
(292, 289, 46, '46', 64, 65, 0),
(293, 290, 46, '46', 66, 67, 0),
(294, 291, 46, '46', 68, 69, 0),
(295, 292, 46, '46', 70, 71, 0),
(296, 293, 46, '46', 72, 73, 0),
(297, 294, 47, '47', 76, 77, 0),
(298, 295, 47, '47', 78, 79, 0),
(299, 296, 47, '47', 80, 81, 0),
(300, 297, 48, '48', 84, 85, 0),
(301, 298, 48, '48', 86, 87, 0),
(302, 299, 48, '48', 88, 89, 0),
(303, 300, 48, '48', 90, 91, 0),
(304, 301, 48, '48', 92, 93, 0),
(305, 302, 48, '48', 94, 95, 0),
(306, 303, 48, '48', 96, 97, 0),
(307, 285, 50, '50', 102, 103, 0),
(308, 286, 50, '50', 104, 105, 0),
(309, 304, 50, '50', 106, 107, 0),
(310, 305, 55, '55', 120, 121, 0),
(311, 306, 55, '55', 122, 123, 0),
(312, 307, 55, '55', 124, 125, 0),
(313, 308, 55, '55', 126, 127, 0),
(314, 309, 55, '55', 128, 129, 0),
(315, 310, 55, '55', 130, 131, 0),
(316, 311, 55, '55', 132, 133, 0),
(317, 312, 55, '55', 134, 135, 0),
(318, 313, 55, '55', 136, 137, 0),
(319, 314, 61, '61', 152, 153, 0),
(320, 315, 61, '61', 154, 155, 0),
(321, 316, 61, '61', 156, 157, 0),
(322, 317, 61, '61', 158, 159, 0),
(323, 318, 62, '62', 162, 163, 0),
(324, 319, 62, '62', 164, 165, 0),
(325, 320, 62, '62', 166, 167, 0),
(326, 321, 64, '64', 172, 173, 0),
(327, 62, 65, '65', 178, 179, 0),
(328, 322, 65, '65', 180, 181, 0),
(329, 323, 65, '65', 182, 183, 0),
(330, 324, 65, '65', 184, 185, 0),
(331, 325, 65, '65', 186, 187, 0),
(332, 326, 65, '65', 188, 189, 0),
(333, 327, 65, '65', 190, 191, 0),
(334, 328, 65, '65', 192, 193, 0),
(335, 329, 65, '65', 194, 195, 0),
(336, 330, 65, '65', 196, 197, 0),
(337, 331, 65, '65', 198, 199, 0),
(338, 332, 65, '65', 200, 201, 0),
(339, 333, 66, '66', 204, 205, 0),
(340, 334, 66, '66', 206, 207, 0),
(341, 335, 66, '66', 208, 209, 0),
(342, 336, 66, '66', 210, 211, 0),
(343, 337, 66, '66', 212, 213, 0),
(344, 338, 66, '66', 214, 215, 0),
(345, 339, 66, '66', 216, 217, 0),
(346, 340, 66, '66', 218, 219, 0),
(347, 341, 66, '66', 220, 221, 0),
(348, 342, 66, '66', 222, 223, 0),
(349, 343, 66, '66', 224, 225, 0),
(350, 344, 66, '66', 226, 227, 0),
(351, 345, 66, '66', 228, 229, 0),
(352, 346, 67, '67', 232, 233, 0),
(353, 347, 67, '67', 234, 235, 0),
(354, 348, 67, '67', 236, 237, 0),
(355, 349, 67, '67', 238, 239, 0),
(356, 350, 67, '67', 240, 241, 0),
(357, 351, 67, '67', 242, 243, 0),
(358, 352, 67, '67', 244, 245, 0),
(359, 353, 67, '67', 246, 247, 0),
(360, 354, 67, '67', 248, 249, 0),
(361, 355, 67, '67', 250, 251, 0),
(362, 356, 68, '68', 254, 255, 0),
(363, 357, 68, '68', 256, 257, 0),
(364, 358, 68, '68', 258, 259, 0),
(365, 359, 68, '68', 260, 261, 0),
(366, 360, 68, '68', 262, 263, 0),
(367, 361, 68, '68', 264, 265, 0),
(368, 362, 68, '68', 266, 267, 0),
(369, 363, 68, '68', 268, 269, 0),
(370, 364, 69, '69', 272, 273, 0),
(371, 365, 70, '70', 278, 279, 0),
(372, 366, 70, '70', 280, 281, 0),
(373, 367, 70, '70', 282, 283, 0),
(374, 368, 70, '70', 284, 285, 0),
(375, 369, 70, '70', 286, 287, 0),
(376, 370, 70, '70', 288, 289, 0),
(377, 371, 70, '70', 290, 291, 0),
(378, 372, 70, '70', 292, 293, 0),
(379, 373, 70, '70', 294, 295, 0),
(380, 374, 70, '70', 296, 297, 0),
(381, 375, 70, '70', 298, 299, 0),
(382, 376, 70, '70', 300, 301, 0),
(383, 377, 70, '70', 302, 303, 0),
(384, 378, 71, '71', 306, 307, 0),
(385, 379, 71, '71', 308, 309, 0),
(386, 380, 71, '71', 310, 311, 0),
(387, 381, 71, '71', 312, 313, 0),
(388, 382, 71, '71', 314, 315, 0),
(389, 383, 71, '71', 316, 317, 0),
(390, 384, 71, '71', 318, 319, 0),
(391, 385, 76, '76', 330, 331, 0),
(392, 378, 76, '76', 332, 333, 0),
(393, 386, 76, '76', 334, 335, 0),
(394, 387, 76, '76', 336, 337, 0),
(395, 388, 76, '76', 338, 339, 0),
(396, 389, 76, '76', 340, 341, 0),
(397, 379, 76, '76', 342, 343, 0),
(398, 380, 76, '76', 344, 345, 0),
(399, 390, 76, '76', 346, 347, 0),
(400, 381, 76, '76', 348, 349, 0),
(401, 382, 76, '76', 350, 351, 0),
(402, 391, 76, '76', 352, 353, 0),
(403, 392, 76, '76', 354, 355, 0),
(404, 393, 76, '76', 356, 357, 0),
(405, 394, 76, '76', 358, 359, 0),
(406, 395, 76, '76', 360, 361, 0),
(407, 396, 76, '76', 362, 363, 0),
(408, 397, 76, '76', 364, 365, 0),
(409, 383, 76, '76', 366, 367, 0),
(410, 398, 76, '76', 368, 369, 0),
(411, 399, 78, '78', 374, 375, 0),
(412, 400, 78, '78', 376, 377, 0),
(413, 401, 78, '78', 378, 379, 0),
(414, 402, 78, '78', 380, 381, 0),
(415, 403, 78, '78', 382, 383, 0),
(416, 404, 78, '78', 384, 385, 0),
(417, 405, 78, '78', 386, 387, 0),
(418, 383, 78, '78', 388, 389, 0),
(419, 386, 80, '80', 396, 397, 0),
(420, 406, 80, '80', 398, 399, 0),
(421, 407, 80, '80', 400, 401, 0),
(422, 408, 80, '80', 402, 403, 0),
(423, 384, 80, '80', 404, 405, 0),
(424, 409, 81, '81', 408, 409, 0),
(425, 410, 81, '81', 410, 411, 0),
(426, 411, 81, '81', 412, 413, 0),
(427, 412, 81, '81', 414, 415, 0),
(428, 413, 81, '81', 416, 417, 0),
(429, 414, 81, '81', 418, 419, 0),
(430, 415, 81, '81', 420, 421, 0),
(431, 416, 81, '81', 422, 423, 0),
(432, 417, 81, '81', 424, 425, 0),
(433, 418, 81, '81', 426, 427, 0),
(434, 419, 82, '82', 430, 431, 0),
(435, 420, 82, '82', 432, 433, 0),
(436, 421, 82, '82', 434, 435, 0),
(437, 422, 82, '82', 436, 437, 0),
(438, 423, 83, '83', 440, 441, 0),
(439, 424, 83, '83', 442, 443, 0),
(440, 425, 83, '83', 444, 445, 0),
(441, 426, 83, '83', 446, 447, 0),
(442, 427, 83, '83', 448, 449, 0),
(443, 428, 83, '83', 450, 451, 0),
(444, 429, 83, '83', 452, 453, 0),
(445, 430, 83, '83', 454, 455, 0),
(446, 431, 83, '83', 456, 457, 0),
(447, 432, 83, '83', 458, 459, 0),
(448, 433, 83, '83', 460, 461, 0),
(449, 434, 84, '84', 464, 465, 0),
(450, 435, 84, '84', 466, 467, 0),
(451, 436, 84, '84', 468, 469, 0),
(452, 437, 84, '84', 470, 471, 0),
(453, 438, 84, '84', 472, 473, 0),
(454, 439, 84, '84', 474, 475, 0),
(455, 440, 84, '84', 476, 477, 0),
(456, 441, 84, '84', 478, 479, 0),
(457, 442, 84, '84', 480, 481, 0),
(458, 443, 84, '84', 482, 483, 0),
(459, 444, 84, '84', 484, 485, 0),
(460, 445, 84, '84', 486, 487, 0),
(461, 446, 85, '85', 492, 493, 0),
(462, 447, 85, '85', 494, 495, 0),
(463, 80, 85, '85', 496, 497, 0),
(464, 412, 85, '85', 498, 499, 0),
(465, 82, 85, '85', 500, 501, 0),
(466, 448, 85, '85', 502, 503, 0),
(467, 449, 85, '85', 504, 505, 0),
(468, 450, 85, '85', 506, 507, 0),
(469, 451, 86, '86', 510, 511, 0),
(470, 452, 86, '86', 512, 513, 0),
(471, 453, 86, '86', 514, 515, 0),
(472, 454, 86, '86', 516, 517, 0),
(473, 455, 86, '86', 518, 519, 0),
(474, 456, 87, '87', 522, 523, 0),
(475, 457, 87, '87', 524, 525, 0),
(476, 458, 87, '87', 526, 527, 0),
(477, 459, 87, '87', 528, 529, 0),
(478, 460, 87, '87', 530, 531, 0),
(479, 461, 87, '87', 532, 533, 0),
(480, 462, 87, '87', 534, 535, 0),
(481, 463, 87, '87', 536, 537, 0),
(482, 464, 87, '87', 538, 539, 0),
(483, 465, 87, '87', 540, 541, 0),
(484, 466, 88, '88', 544, 545, 0),
(485, 457, 88, '88', 546, 547, 0),
(486, 458, 88, '88', 548, 549, 0),
(487, 467, 88, '88', 550, 551, 0),
(488, 463, 88, '88', 552, 553, 0),
(489, 464, 88, '88', 554, 555, 0),
(490, 468, 88, '88', 556, 557, 0),
(491, 469, 88, '88', 558, 559, 0),
(492, 470, 88, '88', 560, 561, 0),
(493, 471, 88, '88', 562, 563, 0),
(494, 472, 89, '89', 568, 569, 0),
(495, 473, 89, '89', 570, 571, 0),
(496, 474, 89, '89', 572, 573, 0),
(497, 475, 89, '89', 574, 575, 0),
(498, 476, 89, '89', 576, 577, 0),
(499, 477, 89, '89', 578, 579, 0),
(500, 478, 89, '89', 580, 581, 0),
(501, 479, 89, '89', 582, 583, 0),
(502, 480, 89, '89', 584, 585, 0),
(503, 481, 89, '89', 586, 587, 0),
(504, 482, 89, '89', 588, 589, 0),
(505, 483, 89, '89', 590, 591, 0),
(506, 484, 89, '89', 592, 593, 0),
(507, 485, 89, '89', 594, 595, 0),
(508, 486, 89, '89', 596, 597, 0),
(509, 487, 89, '89', 598, 599, 0),
(510, 468, 89, '89', 600, 601, 0),
(511, 488, 89, '89', 602, 603, 0),
(512, 100, 89, '89', 604, 605, 0),
(513, 489, 89, '89', 606, 607, 0),
(514, 490, 89, '89', 608, 609, 0),
(515, 491, 90, '90', 612, 613, 0),
(516, 492, 90, '90', 614, 615, 0),
(517, 493, 90, '90', 616, 617, 0),
(518, 494, 90, '90', 618, 619, 0),
(519, 495, 90, '90', 620, 621, 0),
(520, 496, 90, '90', 622, 623, 0),
(521, 497, 90, '90', 624, 625, 0),
(522, 498, 90, '90', 626, 627, 0),
(523, 499, 90, '90', 628, 629, 0),
(524, 500, 90, '90', 630, 631, 0),
(525, 501, 90, '90', 632, 633, 0),
(526, 502, 90, '90', 634, 635, 0),
(527, 503, 90, '90', 636, 637, 0),
(528, 504, 90, '90', 638, 639, 0),
(529, 505, 90, '90', 640, 641, 0),
(530, 506, 90, '90', 642, 643, 0),
(531, 507, 90, '90', 644, 645, 0),
(532, 508, 90, '90', 646, 647, 0),
(533, 509, 90, '90', 648, 649, 0),
(534, 510, 90, '90', 650, 651, 0),
(535, 511, 90, '90', 652, 653, 0),
(536, 512, 90, '90', 654, 655, 0),
(537, 513, 90, '90', 656, 657, 0),
(538, 514, 90, '90', 658, 659, 0),
(539, 515, 90, '90', 660, 661, 0),
(540, 516, 90, '90', 662, 663, 0),
(541, 517, 90, '90', 664, 665, 0),
(542, 518, 90, '90', 666, 667, 0),
(543, 519, 90, '90', 668, 669, 0),
(544, 520, 90, '90', 670, 671, 0),
(545, 521, 90, '90', 672, 673, 0),
(546, 522, 90, '90', 674, 675, 0),
(547, 523, 90, '90', 676, 677, 0),
(548, 524, 90, '90', 678, 679, 0),
(549, 525, 90, '90', 680, 681, 0),
(550, 526, 90, '90', 682, 683, 0),
(551, 527, 91, '91', 688, 689, 0),
(552, 528, 91, '91', 690, 691, 0),
(553, 529, 91, '91', 692, 693, 0),
(554, 530, 91, '91', 694, 695, 0),
(555, 531, 91, '91', 696, 697, 0),
(556, 532, 91, '91', 698, 699, 0),
(557, 533, 91, '91', 700, 701, 0),
(558, 534, 91, '91', 702, 703, 0),
(559, 535, 92, '92', 706, 707, 0),
(560, 536, 92, '92', 708, 709, 0),
(561, 537, 92, '92', 710, 711, 0),
(562, 538, 93, '93', 714, 715, 0),
(563, 539, 93, '93', 716, 717, 0),
(564, 540, 93, '93', 718, 719, 0),
(565, 541, 93, '93', 720, 721, 0),
(566, 542, 93, '93', 722, 723, 0),
(567, 543, 93, '93', 724, 725, 0),
(568, 544, 93, '93', 726, 727, 0),
(569, 545, 93, '93', 728, 729, 0),
(570, 546, 93, '93', 730, 731, 0),
(571, 547, 93, '93', 732, 733, 0),
(572, 548, 93, '93', 734, 735, 0),
(573, 549, 93, '93', 736, 737, 0),
(574, 550, 94, '94', 742, 743, 0),
(575, 551, 94, '94', 744, 745, 0),
(576, 541, 94, '94', 746, 747, 0),
(577, 542, 94, '94', 748, 749, 0),
(578, 552, 94, '94', 750, 751, 0),
(579, 553, 94, '94', 752, 753, 0),
(580, 554, 94, '94', 754, 755, 0),
(581, 555, 94, '94', 756, 757, 0),
(582, 556, 94, '94', 758, 759, 0),
(583, 557, 94, '94', 760, 761, 0),
(584, 558, 94, '94', 762, 763, 0),
(585, 559, 94, '94', 764, 765, 0),
(586, 549, 94, '94', 766, 767, 0),
(587, 560, 95, '95', 770, 771, 0),
(588, 561, 95, '95', 772, 773, 0),
(589, 562, 95, '95', 774, 775, 0),
(590, 563, 95, '95', 776, 777, 0),
(591, 564, 95, '95', 778, 779, 0),
(592, 565, 95, '95', 780, 781, 0),
(593, 566, 95, '95', 782, 783, 0),
(594, 567, 95, '95', 784, 785, 0),
(595, 568, 95, '95', 786, 787, 0),
(596, 569, 95, '95', 788, 789, 0),
(597, 570, 95, '95', 790, 791, 0),
(598, 571, 95, '95', 792, 793, 0),
(599, 572, 95, '95', 794, 795, 0),
(600, 573, 95, '95', 796, 797, 0),
(601, 574, 95, '95', 798, 799, 0),
(602, 575, 95, '95', 800, 801, 0),
(603, 576, 95, '95', 802, 803, 0),
(604, 577, 95, '95', 804, 805, 0),
(605, 578, 95, '95', 806, 807, 0),
(606, 579, 95, '95', 808, 809, 0),
(607, 381, 95, '95', 810, 811, 0),
(608, 580, 95, '95', 812, 813, 0),
(609, 581, 95, '95', 814, 815, 0),
(610, 582, 95, '95', 816, 817, 0),
(611, 583, 95, '95', 818, 819, 0),
(612, 584, 95, '95', 820, 821, 0),
(613, 585, 95, '95', 822, 823, 0),
(614, 586, 95, '95', 824, 825, 0),
(615, 587, 95, '95', 826, 827, 0),
(616, 588, 95, '95', 828, 829, 0),
(617, 589, 95, '95', 830, 831, 0),
(618, 590, 95, '95', 832, 833, 0),
(619, 591, 95, '95', 834, 835, 0),
(620, 592, 95, '95', 836, 837, 0),
(621, 593, 95, '95', 838, 839, 0),
(622, 594, 95, '95', 840, 841, 0),
(623, 595, 95, '95', 842, 843, 0),
(624, 596, 95, '95', 844, 845, 0),
(625, 597, 95, '95', 846, 847, 0),
(626, 598, 95, '95', 848, 849, 0),
(627, 599, 95, '95', 850, 851, 0),
(628, 600, 95, '95', 852, 853, 0),
(629, 601, 95, '95', 854, 855, 0),
(630, 602, 95, '95', 856, 857, 0),
(631, 603, 95, '95', 858, 859, 0),
(632, 604, 95, '95', 860, 861, 0),
(633, 605, 95, '95', 862, 863, 0),
(634, 606, 98, '98', 872, 873, 0),
(635, 100, 98, '98', 874, 875, 0),
(636, 607, 106, '106', 896, 897, 0),
(637, 608, 106, '106', 898, 899, 0),
(638, 609, 106, '106', 900, 901, 0),
(639, 610, 106, '106', 902, 903, 0),
(640, 611, 106, '106', 904, 905, 0),
(641, 612, 106, '106', 906, 907, 0),
(642, 613, 106, '106', 908, 909, 0),
(643, 614, 106, '106', 910, 911, 0),
(644, 615, 107, '107', 914, 915, 0),
(645, 616, 107, '107', 916, 917, 0),
(646, 617, 107, '107', 918, 919, 0),
(647, 618, 107, '107', 920, 921, 0),
(648, 619, 107, '107', 922, 923, 0),
(649, 620, 107, '107', 924, 925, 0),
(650, 621, 107, '107', 926, 927, 0),
(651, 622, 107, '107', 928, 929, 0),
(652, 623, 107, '107', 930, 931, 0),
(653, 624, 107, '107', 932, 933, 0),
(654, 625, 107, '107', 934, 935, 0),
(655, 626, 107, '107', 936, 937, 0),
(656, 627, 109, '109', 942, 943, 0),
(657, 628, 109, '109', 944, 945, 0),
(658, 629, 109, '109', 946, 947, 0),
(659, 630, 109, '109', 948, 949, 0),
(660, 631, 111, '111', 954, 955, 0),
(661, 632, 111, '111', 956, 957, 0),
(662, 633, 114, '114', 964, 965, 0),
(663, 634, 114, '114', 966, 967, 0),
(664, 635, 115, '115', 970, 971, 0),
(665, 636, 115, '115', 972, 973, 0),
(666, 637, 118, '118', 982, 983, 0),
(667, 638, 118, '118', 984, 985, 0),
(668, 639, 118, '118', 986, 987, 0),
(669, 640, 118, '118', 988, 989, 0),
(670, 641, 118, '118', 990, 991, 0),
(671, 642, 118, '118', 992, 993, 0),
(672, 643, 119, '119', 996, 997, 0),
(673, 644, 119, '119', 998, 999, 0),
(674, 645, 119, '119', 1000, 1001, 0),
(675, 646, 120, '120', 1004, 1005, 0),
(676, 647, 120, '120', 1006, 1007, 0),
(677, 648, 120, '120', 1008, 1009, 0),
(678, 649, 120, '120', 1010, 1011, 0),
(679, 120, 120, '120', 1012, 1013, 0),
(680, 650, 120, '120', 1014, 1015, 0),
(681, 651, 120, '120', 1016, 1017, 0),
(682, 652, 120, '120', 1018, 1019, 0),
(683, 653, 121, '121', 1022, 1023, 0),
(684, 654, 122, '122', 1028, 1029, 0),
(685, 655, 122, '122', 1030, 1031, 0),
(686, 646, 122, '122', 1032, 1033, 0),
(687, 647, 122, '122', 1034, 1035, 0),
(688, 656, 122, '122', 1036, 1037, 0),
(689, 657, 122, '122', 1038, 1039, 0),
(690, 658, 122, '122', 1040, 1041, 0),
(691, 659, 122, '122', 1042, 1043, 0),
(692, 651, 122, '122', 1044, 1045, 0),
(693, 660, 122, '122', 1046, 1047, 0),
(694, 652, 122, '122', 1048, 1049, 0),
(695, 661, 122, '122', 1050, 1051, 0),
(696, 662, 123, '123', 1054, 1055, 0),
(697, 128, 123, '123', 1056, 1057, 0),
(698, 663, 123, '123', 1058, 1059, 0),
(699, 664, 123, '123', 1060, 1061, 0),
(700, 665, 123, '123', 1062, 1063, 0),
(701, 666, 123, '123', 1064, 1065, 0),
(702, 667, 123, '123', 1066, 1067, 0),
(703, 668, 123, '123', 1068, 1069, 0),
(704, 669, 123, '123', 1070, 1071, 0),
(705, 670, 123, '123', 1072, 1073, 0),
(706, 671, 123, '123', 1074, 1075, 0),
(707, 672, 124, '124', 1078, 1079, 0),
(708, 663, 124, '124', 1080, 1081, 0),
(709, 673, 124, '124', 1082, 1083, 0),
(710, 674, 124, '124', 1084, 1085, 0),
(711, 675, 124, '124', 1086, 1087, 0),
(712, 676, 124, '124', 1088, 1089, 0),
(713, 677, 124, '124', 1090, 1091, 0),
(714, 678, 124, '124', 1092, 1093, 0),
(715, 679, 124, '124', 1094, 1095, 0),
(716, 668, 124, '124', 1096, 1097, 0),
(717, 680, 124, '124', 1098, 1099, 0),
(718, 681, 124, '124', 1100, 1101, 0),
(719, 682, 124, '124', 1102, 1103, 0),
(720, 670, 124, '124', 1104, 1105, 0),
(721, 683, 125, '125', 1108, 1109, 0),
(722, 684, 125, '125', 1110, 1111, 0),
(723, 685, 125, '125', 1112, 1113, 0),
(724, 686, 125, '125', 1114, 1115, 0),
(725, 687, 125, '125', 1116, 1117, 0),
(726, 688, 125, '125', 1118, 1119, 0),
(727, 689, 125, '125', 1120, 1121, 0),
(728, 690, 125, '125', 1122, 1123, 0),
(729, 691, 125, '125', 1124, 1125, 0),
(730, 692, 125, '125', 1126, 1127, 0),
(731, 693, 127, '127', 1134, 1135, 0),
(732, 694, 127, '127', 1136, 1137, 0),
(733, 695, 127, '127', 1138, 1139, 0),
(734, 696, 127, '127', 1140, 1141, 0),
(735, 697, 127, '127', 1142, 1143, 0),
(736, 698, 127, '127', 1144, 1145, 0),
(737, 699, 127, '127', 1146, 1147, 0),
(738, 700, 127, '127', 1148, 1149, 0),
(739, 701, 127, '127', 1150, 1151, 0),
(740, 667, 127, '127', 1152, 1153, 0),
(741, 684, 128, '128', 1156, 1157, 0),
(742, 685, 128, '128', 1158, 1159, 0),
(743, 127, 128, '128', 1160, 1161, 0),
(744, 702, 129, '129', 1164, 1165, 0),
(745, 128, 129, '129', 1166, 1167, 0),
(746, 703, 130, '130', 1170, 1171, 0),
(747, 704, 130, '130', 1172, 1173, 0),
(748, 705, 130, '130', 1174, 1175, 0),
(749, 706, 131, '131', 1178, 1179, 0),
(750, 707, 131, '131', 1180, 1181, 0),
(751, 708, 131, '131', 1182, 1183, 0),
(752, 709, 131, '131', 1184, 1185, 0),
(753, 710, 131, '131', 1186, 1187, 0),
(754, 711, 131, '131', 1188, 1189, 0),
(755, 698, 131, '131', 1190, 1191, 0),
(756, 712, 131, '131', 1192, 1193, 0),
(757, 713, 131, '131', 1194, 1195, 0),
(758, 714, 131, '131', 1196, 1197, 0),
(759, 715, 131, '131', 1198, 1199, 0),
(760, 669, 131, '131', 1200, 1201, 0),
(761, 131, 132, '132', 1204, 1205, 0),
(762, 716, 133, '133', 1208, 1209, 0),
(763, 673, 133, '133', 1210, 1211, 0),
(764, 133, 134, '134', 1214, 1215, 0),
(765, 717, 135, '135', 1218, 1219, 0),
(766, 718, 135, '135', 1220, 1221, 0),
(767, 679, 135, '135', 1222, 1223, 0),
(768, 681, 135, '135', 1224, 1225, 0),
(769, 682, 135, '135', 1226, 1227, 0),
(770, 719, 136, '136', 1230, 1231, 0),
(771, 665, 136, '136', 1232, 1233, 0),
(772, 677, 136, '136', 1234, 1235, 0),
(773, 720, 136, '136', 1236, 1237, 0),
(774, 721, 136, '136', 1238, 1239, 0),
(775, 701, 136, '136', 1240, 1241, 0),
(776, 722, 136, '136', 1242, 1243, 0),
(777, 670, 137, '137', 1246, 1247, 0),
(778, 723, 138, '138', 1252, 1253, 0),
(779, 137, 138, '138', 1254, 1255, 0),
(780, 724, 139, '139', 1258, 1259, 0),
(781, 725, 139, '139', 1260, 1261, 0),
(782, 726, 139, '139', 1262, 1263, 0),
(783, 727, 139, '139', 1264, 1265, 0),
(784, 728, 139, '139', 1266, 1267, 0),
(785, 729, 139, '139', 1268, 1269, 0),
(786, 730, 139, '139', 1270, 1271, 0),
(787, 731, 139, '139', 1272, 1273, 0),
(788, 732, 139, '139', 1274, 1275, 0),
(789, 733, 139, '139', 1276, 1277, 0),
(790, 734, 139, '139', 1278, 1279, 0),
(791, 735, 140, '140', 1282, 1283, 0),
(792, 736, 140, '140', 1284, 1285, 0),
(793, 737, 140, '140', 1286, 1287, 0),
(794, 738, 140, '140', 1288, 1289, 0),
(795, 739, 140, '140', 1290, 1291, 0),
(796, 740, 140, '140', 1292, 1293, 0),
(797, 741, 140, '140', 1294, 1295, 0),
(798, 51, 140, '140', 1296, 1297, 0),
(799, 742, 140, '140', 1298, 1299, 0),
(800, 743, 140, '140', 1300, 1301, 0),
(801, 744, 140, '140', 1302, 1303, 0),
(802, 745, 140, '140', 1304, 1305, 0),
(803, 746, 140, '140', 1306, 1307, 0),
(804, 747, 140, '140', 1308, 1309, 0),
(805, 577, 140, '140', 1310, 1311, 0),
(806, 585, 140, '140', 1312, 1313, 0),
(807, 748, 140, '140', 1314, 1315, 0),
(808, 749, 140, '140', 1316, 1317, 0),
(809, 750, 140, '140', 1318, 1319, 0),
(810, 751, 140, '140', 1320, 1321, 0),
(811, 129, 141, '141', 1324, 1325, 0),
(812, 752, 141, '141', 1326, 1327, 0),
(813, 753, 142, '142', 1330, 1331, 0),
(814, 754, 142, '142', 1332, 1333, 0),
(815, 755, 142, '142', 1334, 1335, 0),
(816, 756, 142, '142', 1336, 1337, 0),
(817, 757, 142, '142', 1338, 1339, 0),
(818, 758, 142, '142', 1340, 1341, 0),
(819, 759, 142, '142', 1342, 1343, 0),
(820, 760, 142, '142', 1344, 1345, 0),
(821, 761, 142, '142', 1346, 1347, 0),
(822, 762, 142, '142', 1348, 1349, 0),
(823, 763, 145, '145', 1358, 1359, 0),
(824, 764, 145, '145', 1360, 1361, 0),
(825, 765, 145, '145', 1362, 1363, 0),
(826, 766, 145, '145', 1364, 1365, 0),
(827, 767, 145, '145', 1366, 1367, 0),
(828, 768, 145, '145', 1368, 1369, 0),
(829, 769, 145, '145', 1370, 1371, 0),
(830, 770, 145, '145', 1372, 1373, 0),
(831, 771, 146, '146', 1376, 1377, 0),
(832, 772, 146, '146', 1378, 1379, 0),
(833, 773, 146, '146', 1380, 1381, 0),
(834, 774, 146, '146', 1382, 1383, 0),
(835, 775, 146, '146', 1384, 1385, 0),
(836, 776, 146, '146', 1386, 1387, 0),
(837, 777, 146, '146', 1388, 1389, 0),
(838, 778, 146, '146', 1390, 1391, 0),
(839, 779, 146, '146', 1392, 1393, 0),
(840, 780, 147, '147', 1396, 1397, 0),
(841, 781, 147, '147', 1398, 1399, 0),
(842, 782, 147, '147', 1400, 1401, 0),
(843, 783, 147, '147', 1402, 1403, 0),
(844, 784, 147, '147', 1404, 1405, 0),
(845, 82, 147, '147', 1406, 1407, 0),
(846, 785, 147, '147', 1408, 1409, 0),
(847, 418, 147, '147', 1410, 1411, 0),
(848, 786, 147, '147', 1412, 1413, 0),
(849, 787, 147, '147', 1414, 1415, 0),
(850, 788, 147, '147', 1416, 1417, 0),
(851, 789, 147, '147', 1418, 1419, 0),
(852, 790, 148, '148', 1422, 1423, 0),
(853, 791, 148, '148', 1424, 1425, 0),
(854, 617, 148, '148', 1426, 1427, 0),
(855, 792, 148, '148', 1428, 1429, 0),
(856, 793, 148, '148', 1430, 1431, 0),
(857, 794, 148, '148', 1432, 1433, 0),
(858, 795, 148, '148', 1434, 1435, 0),
(859, 796, 148, '148', 1436, 1437, 0),
(860, 625, 148, '148', 1438, 1439, 0),
(861, 797, 149, '149', 1444, 1445, 0),
(862, 798, 149, '149', 1446, 1447, 0),
(863, 799, 149, '149', 1448, 1449, 0),
(864, 800, 149, '149', 1450, 1451, 0),
(865, 801, 149, '149', 1452, 1453, 0),
(866, 802, 149, '149', 1454, 1455, 0),
(867, 803, 149, '149', 1456, 1457, 0),
(868, 804, 150, '150', 1460, 1461, 0),
(869, 805, 150, '150', 1462, 1463, 0),
(870, 806, 150, '150', 1464, 1465, 0),
(871, 807, 150, '150', 1466, 1467, 0),
(872, 412, 150, '150', 1468, 1469, 0),
(873, 808, 150, '150', 1470, 1471, 0),
(874, 809, 150, '150', 1472, 1473, 0),
(875, 810, 150, '150', 1474, 1475, 0),
(876, 811, 150, '150', 1476, 1477, 0),
(877, 812, 150, '150', 1478, 1479, 0),
(878, 813, 150, '150', 1480, 1481, 0),
(879, 814, 150, '150', 1482, 1483, 0),
(880, 815, 150, '150', 1484, 1485, 0),
(881, 816, 150, '150', 1486, 1487, 0),
(882, 817, 151, '151', 1492, 1493, 0),
(883, 805, 151, '151', 1494, 1495, 0),
(884, 818, 151, '151', 1496, 1497, 0),
(885, 819, 151, '151', 1498, 1499, 0),
(886, 820, 151, '151', 1500, 1501, 0),
(887, 821, 151, '151', 1502, 1503, 0),
(888, 822, 151, '151', 1504, 1505, 0),
(889, 823, 151, '151', 1506, 1507, 0),
(890, 824, 151, '151', 1508, 1509, 0),
(891, 825, 151, '151', 1510, 1511, 0),
(892, 826, 151, '151', 1512, 1513, 0),
(893, 827, 151, '151', 1514, 1515, 0),
(894, 828, 151, '151', 1516, 1517, 0),
(895, 829, 151, '151', 1518, 1519, 0),
(896, 830, 151, '151', 1520, 1521, 0),
(897, 831, 151, '151', 1522, 1523, 0),
(898, 832, 151, '151', 1524, 1525, 0),
(899, 833, 151, '151', 1526, 1527, 0),
(900, 834, 151, '151', 1528, 1529, 0),
(901, 835, 151, '151', 1530, 1531, 0),
(902, 836, 151, '151', 1532, 1533, 0),
(903, 837, 152, '152', 1536, 1537, 0),
(904, 838, 152, '152', 1538, 1539, 0),
(905, 839, 152, '152', 1540, 1541, 0),
(906, 840, 152, '152', 1542, 1543, 0),
(907, 841, 152, '152', 1544, 1545, 0),
(908, 842, 152, '152', 1546, 1547, 0),
(909, 843, 152, '152', 1548, 1549, 0),
(910, 844, 152, '152', 1550, 1551, 0),
(911, 845, 152, '152', 1552, 1553, 0),
(912, 846, 152, '152', 1554, 1555, 0),
(913, 847, 152, '152', 1556, 1557, 0),
(914, 848, 152, '152', 1558, 1559, 0),
(915, 849, 152, '152', 1560, 1561, 0),
(916, 850, 152, '152', 1562, 1563, 0),
(917, 851, 152, '152', 1564, 1565, 0),
(918, 852, 152, '152', 1566, 1567, 0),
(919, 853, 152, '152', 1568, 1569, 0),
(920, 854, 152, '152', 1570, 1571, 0),
(921, 855, 152, '152', 1572, 1573, 0),
(922, 53, 152, '152', 1574, 1575, 0),
(923, 856, 152, '152', 1576, 1577, 0),
(924, 857, 152, '152', 1578, 1579, 0),
(925, 858, 152, '152', 1580, 1581, 0),
(926, 859, 152, '152', 1582, 1583, 0),
(927, 860, 152, '152', 1584, 1585, 0),
(928, 861, 152, '152', 1586, 1587, 0),
(929, 862, 152, '152', 1588, 1589, 0),
(930, 863, 152, '152', 1590, 1591, 0),
(931, 864, 152, '152', 1592, 1593, 0),
(932, 865, 152, '152', 1594, 1595, 0),
(933, 866, 152, '152', 1596, 1597, 0),
(934, 867, 152, '152', 1598, 1599, 0),
(935, 868, 153, '153', 1604, 1605, 0),
(936, 869, 153, '153', 1606, 1607, 0),
(937, 870, 153, '153', 1608, 1609, 0),
(938, 871, 153, '153', 1610, 1611, 0),
(939, 872, 153, '153', 1612, 1613, 0),
(940, 873, 153, '153', 1614, 1615, 0),
(941, 874, 153, '153', 1616, 1617, 0),
(942, 875, 153, '153', 1618, 1619, 0),
(943, 876, 153, '153', 1620, 1621, 0),
(944, 877, 153, '153', 1622, 1623, 0),
(945, 878, 153, '153', 1624, 1625, 0),
(946, 879, 153, '153', 1626, 1627, 0),
(947, 880, 153, '153', 1628, 1629, 0),
(948, 881, 153, '153', 1630, 1631, 0),
(949, 882, 153, '153', 1632, 1633, 0),
(950, 883, 153, '153', 1634, 1635, 0),
(951, 884, 154, '154', 1638, 1639, 0),
(952, 885, 154, '154', 1640, 1641, 0),
(953, 886, 154, '154', 1642, 1643, 0),
(954, 887, 154, '154', 1644, 1645, 0),
(955, 888, 154, '154', 1646, 1647, 0),
(956, 889, 154, '154', 1648, 1649, 0),
(957, 890, 154, '154', 1650, 1651, 0),
(958, 891, 154, '154', 1652, 1653, 0),
(959, 892, 154, '154', 1654, 1655, 0),
(960, 893, 154, '154', 1656, 1657, 0),
(961, 894, 154, '154', 1658, 1659, 0),
(962, 895, 154, '154', 1660, 1661, 0),
(963, 896, 154, '154', 1662, 1663, 0),
(964, 897, 157, '157', 1670, 1671, 0),
(965, 898, 157, '157', 1672, 1673, 0),
(966, 899, 158, '158', 1676, 1677, 0),
(967, 900, 158, '158', 1678, 1679, 0),
(968, 901, 158, '158', 1680, 1681, 0),
(969, 902, 158, '158', 1682, 1683, 0),
(970, 903, 158, '158', 1684, 1685, 0),
(971, 904, 158, '158', 1686, 1687, 0),
(972, 905, 158, '158', 1688, 1689, 0),
(973, 906, 158, '158', 1690, 1691, 0),
(974, 907, 158, '158', 1692, 1693, 0),
(975, 908, 158, '158', 1694, 1695, 0),
(976, 909, 159, '159', 1700, 1701, 0),
(977, 264, 159, '159', 1702, 1703, 0),
(978, 910, 159, '159', 1704, 1705, 0),
(979, 911, 159, '159', 1706, 1707, 0),
(980, 912, 159, '159', 1708, 1709, 0),
(981, 913, 159, '159', 1710, 1711, 0),
(982, 914, 159, '159', 1712, 1713, 0),
(983, 915, 159, '159', 1714, 1715, 0),
(984, 916, 159, '159', 1716, 1717, 0),
(985, 216, 159, '159', 1718, 1719, 0),
(986, 917, 159, '159', 1720, 1721, 0),
(987, 918, 159, '159', 1722, 1723, 0),
(988, 919, 159, '159', 1724, 1725, 0),
(989, 920, 159, '159', 1726, 1727, 0),
(990, 921, 159, '159', 1728, 1729, 0),
(991, 922, 159, '159', 1730, 1731, 0),
(992, 923, 159, '159', 1732, 1733, 0),
(993, 924, 159, '159', 1734, 1735, 0),
(994, 925, 159, '159', 1736, 1737, 0),
(995, 926, 160, '160', 1740, 1741, 0),
(996, 160, 160, '160', 1742, 1743, 0),
(997, 927, 160, '160', 1744, 1745, 0),
(998, 928, 160, '160', 1746, 1747, 0),
(999, 929, 160, '160', 1748, 1749, 0),
(1000, 930, 161, '161', 1752, 1753, 0),
(1001, 288, 161, '161', 1754, 1755, 0),
(1002, 294, 161, '161', 1756, 1757, 0),
(1003, 290, 161, '161', 1758, 1759, 0),
(1004, 931, 161, '161', 1760, 1761, 0),
(1005, 291, 161, '161', 1762, 1763, 0),
(1006, 932, 161, '161', 1764, 1765, 0),
(1007, 933, 161, '161', 1766, 1767, 0),
(1008, 934, 161, '161', 1768, 1769, 0),
(1009, 47, 161, '161', 1770, 1771, 0),
(1010, 935, 161, '161', 1772, 1773, 0),
(1011, 936, 161, '161', 1774, 1775, 0),
(1012, 937, 162, '162', 1778, 1779, 0),
(1013, 938, 162, '162', 1780, 1781, 0),
(1014, 571, 162, '162', 1782, 1783, 0),
(1015, 939, 162, '162', 1784, 1785, 0),
(1016, 940, 162, '162', 1786, 1787, 0),
(1017, 941, 162, '162', 1788, 1789, 0),
(1018, 47, 162, '162', 1790, 1791, 0),
(1019, 942, 164, '164', 1798, 1799, 0),
(1020, 943, 164, '164', 1800, 1801, 0),
(1021, 944, 164, '164', 1802, 1803, 0),
(1022, 945, 164, '164', 1804, 1805, 0),
(1023, 588, 164, '164', 1806, 1807, 0),
(1024, 49, 164, '164', 1808, 1809, 0),
(1025, 946, 164, '164', 1810, 1811, 0),
(1026, 947, 165, '165', 1814, 1815, 0),
(1027, 948, 165, '165', 1816, 1817, 0),
(1028, 342, 165, '165', 1818, 1819, 0),
(1029, 949, 165, '165', 1820, 1821, 0),
(1030, 950, 165, '165', 1822, 1823, 0),
(1031, 951, 165, '165', 1824, 1825, 0),
(1032, 952, 165, '165', 1826, 1827, 0),
(1033, 953, 166, '166', 1830, 1831, 0),
(1034, 954, 166, '166', 1832, 1833, 0),
(1035, 955, 166, '166', 1834, 1835, 0),
(1036, 956, 166, '166', 1836, 1837, 0),
(1037, 957, 166, '166', 1838, 1839, 0),
(1038, 958, 166, '166', 1840, 1841, 0),
(1039, 959, 166, '166', 1842, 1843, 0),
(1040, 960, 166, '166', 1844, 1845, 0),
(1041, 961, 166, '166', 1846, 1847, 0),
(1042, 962, 168, '168', 1852, 1853, 0),
(1043, 963, 168, '168', 1854, 1855, 0),
(1044, 964, 168, '168', 1856, 1857, 0),
(1045, 965, 169, '169', 1860, 1861, 0),
(1046, 966, 169, '169', 1862, 1863, 0),
(1047, 967, 169, '169', 1864, 1865, 0),
(1048, 968, 169, '169', 1866, 1867, 0),
(1049, 969, 169, '169', 1868, 1869, 0),
(1050, 970, 169, '169', 1870, 1871, 0),
(1051, 971, 170, '170', 1874, 1875, 0),
(1052, 972, 170, '170', 1876, 1877, 0),
(1053, 973, 170, '170', 1878, 1879, 0),
(1054, 967, 171, '171', 1882, 1883, 0),
(1055, 968, 171, '171', 1884, 1885, 0),
(1056, 974, 171, '171', 1886, 1887, 0),
(1057, 975, 171, '171', 1888, 1889, 0),
(1058, 976, 171, '171', 1890, 1891, 0),
(1059, 977, 171, '171', 1892, 1893, 0),
(1060, 978, 171, '171', 1894, 1895, 0),
(1061, 965, 172, '172', 1898, 1899, 0),
(1062, 967, 172, '172', 1900, 1901, 0),
(1063, 969, 172, '172', 1902, 1903, 0),
(1064, 963, 173, '173', 1908, 1909, 0),
(1065, 979, 173, '173', 1910, 1911, 0),
(1066, 980, 174, '174', 1914, 1915, 0),
(1067, 981, 174, '174', 1916, 1917, 0),
(1068, 982, 175, '175', 1920, 1921, 0),
(1069, 983, 175, '175', 1922, 1923, 0),
(1070, 984, 175, '175', 1924, 1925, 0),
(1071, 985, 175, '175', 1926, 1927, 0),
(1072, 986, 175, '175', 1928, 1929, 0),
(1073, 987, 176, '176', 1932, 1933, 0),
(1074, 988, 176, '176', 1934, 1935, 0),
(1075, 989, 176, '176', 1936, 1937, 0),
(1076, 990, 176, '176', 1938, 1939, 0),
(1077, 991, 176, '176', 1940, 1941, 0),
(1078, 992, 176, '176', 1942, 1943, 0),
(1079, 993, 176, '176', 1944, 1945, 0),
(1080, 994, 177, '177', 1948, 1949, 0),
(1081, 995, 177, '177', 1950, 1951, 0),
(1082, 996, 177, '177', 1952, 1953, 0),
(1083, 997, 177, '177', 1954, 1955, 0),
(1084, 998, 177, '177', 1956, 1957, 0),
(1085, 999, 177, '177', 1958, 1959, 0),
(1086, 54, 177, '177', 1960, 1961, 0),
(1087, 60, 177, '177', 1962, 1963, 0),
(1088, 1000, 178, '178', 1966, 1967, 0),
(1089, 1001, 178, '178', 1968, 1969, 0),
(1090, 1002, 178, '178', 1970, 1971, 0),
(1091, 1003, 178, '178', 1972, 1973, 0),
(1092, 1004, 178, '178', 1974, 1975, 0),
(1093, 1005, 178, '178', 1976, 1977, 0),
(1094, 1006, 178, '178', 1978, 1979, 0),
(1095, 1007, 179, '179', 1982, 1983, 0),
(1096, 1008, 179, '179', 1984, 1985, 0),
(1097, 1009, 179, '179', 1986, 1987, 0),
(1098, 1010, 179, '179', 1988, 1989, 0),
(1099, 1011, 180, '180', 1992, 1993, 0),
(1100, 1012, 180, '180', 1994, 1995, 0),
(1101, 1013, 180, '180', 1996, 1997, 0),
(1102, 1014, 180, '180', 1998, 1999, 0),
(1103, 1015, 181, '181', 2002, 2003, 0),
(1104, 1016, 182, '182', 2008, 2009, 0),
(1105, 1017, 182, '182', 2010, 2011, 0),
(1106, 1018, 182, '182', 2012, 2013, 0),
(1107, 1019, 183, '183', 2016, 2017, 0),
(1108, 1020, 183, '183', 2018, 2019, 0),
(1109, 1021, 183, '183', 2020, 2021, 0),
(1110, 1022, 183, '183', 2022, 2023, 0),
(1111, 1023, 183, '183', 2024, 2025, 0),
(1112, 1024, 183, '183', 2026, 2027, 0),
(1113, 1025, 183, '183', 2028, 2029, 0),
(1114, 1026, 184, '184', 2032, 2033, 0),
(1115, 1027, 184, '184', 2034, 2035, 0),
(1116, 1028, 184, '184', 2036, 2037, 0),
(1117, 1029, 184, '184', 2038, 2039, 0),
(1118, 1030, 185, '185', 2044, 2045, 0),
(1119, 1031, 185, '185', 2046, 2047, 0),
(1120, 1032, 185, '185', 2048, 2049, 0),
(1121, 1033, 185, '185', 2050, 2051, 0),
(1122, 1034, 185, '185', 2052, 2053, 0),
(1123, 1035, 185, '185', 2054, 2055, 0),
(1124, 1036, 185, '185', 2056, 2057, 0),
(1125, 1037, 185, '185', 2058, 2059, 0),
(1126, 1038, 185, '185', 2060, 2061, 0),
(1127, 1039, 185, '185', 2062, 2063, 0),
(1128, 550, 186, '186', 2066, 2067, 0),
(1129, 1040, 186, '186', 2068, 2069, 0),
(1130, 1041, 186, '186', 2070, 2071, 0),
(1131, 1042, 186, '186', 2072, 2073, 0),
(1132, 1043, 186, '186', 2074, 2075, 0),
(1133, 1044, 186, '186', 2076, 2077, 0),
(1134, 1045, 186, '186', 2078, 2079, 0),
(1135, 1046, 186, '186', 2080, 2081, 0),
(1136, 1047, 186, '186', 2082, 2083, 0),
(1137, 1048, 186, '186', 2084, 2085, 0),
(1138, 1049, 186, '186', 2086, 2087, 0),
(1139, 1050, 186, '186', 2088, 2089, 0),
(1140, 1051, 186, '186', 2090, 2091, 0),
(1141, 1041, 187, '187', 2094, 2095, 0),
(1142, 1052, 187, '187', 2096, 2097, 0),
(1143, 1043, 187, '187', 2098, 2099, 0),
(1144, 1053, 187, '187', 2100, 2101, 0),
(1145, 1050, 187, '187', 2102, 2103, 0),
(1146, 1051, 187, '187', 2104, 2105, 0),
(1147, 1054, 188, '188', 2108, 2109, 0),
(1148, 1055, 188, '188', 2110, 2111, 0),
(1149, 1056, 190, '190', 2116, 2117, 0),
(1150, 1057, 190, '190', 2118, 2119, 0),
(1151, 1040, 191, '191', 2122, 2123, 0),
(1152, 1041, 191, '191', 2124, 2125, 0),
(1153, 1058, 191, '191', 2126, 2127, 0),
(1154, 1045, 191, '191', 2128, 2129, 0),
(1155, 1046, 191, '191', 2130, 2131, 0),
(1156, 1059, 191, '191', 2132, 2133, 0),
(1157, 1051, 191, '191', 2134, 2135, 0),
(1158, 550, 192, '192', 2140, 2141, 0),
(1159, 1040, 192, '192', 2142, 2143, 0),
(1160, 1041, 192, '192', 2144, 2145, 0),
(1161, 1042, 192, '192', 2146, 2147, 0),
(1162, 1043, 192, '192', 2148, 2149, 0),
(1163, 1045, 192, '192', 2150, 2151, 0),
(1164, 1060, 192, '192', 2152, 2153, 0),
(1165, 1059, 192, '192', 2154, 2155, 0),
(1166, 1047, 192, '192', 2156, 2157, 0),
(1167, 1048, 192, '192', 2158, 2159, 0),
(1168, 1049, 192, '192', 2160, 2161, 0),
(1169, 1050, 192, '192', 2162, 2163, 0),
(1170, 1051, 192, '192', 2164, 2165, 0),
(1171, 1061, 193, '193', 2168, 2169, 0),
(1172, 72, 193, '193', 2170, 2171, 0),
(1173, 1062, 193, '193', 2172, 2173, 0),
(1174, 1063, 193, '193', 2174, 2175, 0),
(1175, 382, 193, '193', 2176, 2177, 0),
(1176, 1064, 193, '193', 2178, 2179, 0),
(1177, 394, 193, '193', 2180, 2181, 0),
(1178, 1065, 193, '193', 2182, 2183, 0),
(1179, 384, 193, '193', 2184, 2185, 0),
(1180, 1066, 193, '193', 2186, 2187, 0),
(1181, 1067, 194, '194', 2192, 2193, 0),
(1182, 1068, 194, '194', 2194, 2195, 0),
(1183, 1069, 194, '194', 2196, 2197, 0),
(1184, 1070, 194, '194', 2198, 2199, 0),
(1185, 1071, 194, '194', 2200, 2201, 0),
(1186, 1072, 194, '194', 2202, 2203, 0),
(1187, 1073, 194, '194', 2204, 2205, 0),
(1188, 1074, 194, '194', 2206, 2207, 0),
(1189, 1075, 194, '194', 2208, 2209, 0),
(1190, 1076, 194, '194', 2210, 2211, 0),
(1191, 1077, 194, '194', 2212, 2213, 0),
(1192, 1078, 194, '194', 2214, 2215, 0),
(1193, 1079, 194, '194', 2216, 2217, 0),
(1194, 1080, 195, '195', 2220, 2221, 0),
(1195, 1081, 195, '195', 2222, 2223, 0),
(1196, 1082, 195, '195', 2224, 2225, 0),
(1197, 1083, 195, '195', 2226, 2227, 0),
(1198, 1084, 195, '195', 2228, 2229, 0),
(1199, 1085, 195, '195', 2230, 2231, 0),
(1200, 1086, 195, '195', 2232, 2233, 0),
(1201, 1087, 195, '195', 2234, 2235, 0),
(1202, 1088, 195, '195', 2236, 2237, 0),
(1203, 1089, 195, '195', 2238, 2239, 0),
(1204, 1090, 195, '195', 2240, 2241, 0),
(1205, 1091, 195, '195', 2242, 2243, 0),
(1206, 1092, 196, '196', 2246, 2247, 0),
(1207, 1093, 196, '196', 2248, 2249, 0),
(1208, 1094, 196, '196', 2250, 2251, 0),
(1209, 1095, 196, '196', 2252, 2253, 0),
(1210, 1096, 196, '196', 2254, 2255, 0),
(1211, 1072, 196, '196', 2256, 2257, 0),
(1212, 1097, 196, '196', 2258, 2259, 0),
(1213, 1098, 196, '196', 2260, 2261, 0),
(1214, 1099, 196, '196', 2262, 2263, 0),
(1215, 1100, 196, '196', 2264, 2265, 0),
(1216, 1101, 196, '196', 2266, 2267, 0),
(1217, 1102, 196, '196', 2268, 2269, 0),
(1218, 1103, 196, '196', 2270, 2271, 0),
(1219, 1104, 196, '196', 2272, 2273, 0),
(1220, 1105, 197, '197', 2276, 2277, 0),
(1221, 1106, 197, '197', 2278, 2279, 0),
(1222, 1107, 197, '197', 2280, 2281, 0),
(1223, 1108, 197, '197', 2282, 2283, 0),
(1224, 1109, 197, '197', 2284, 2285, 0),
(1225, 1110, 198, '198', 2288, 2289, 0),
(1226, 1111, 198, '198', 2290, 2291, 0),
(1227, 1112, 198, '198', 2292, 2293, 0),
(1228, 882, 198, '198', 2294, 2295, 0),
(1229, 1113, 199, '199', 2298, 2299, 0),
(1230, 845, 199, '199', 2300, 2301, 0),
(1231, 1114, 199, '199', 2302, 2303, 0),
(1232, 1115, 199, '199', 2304, 2305, 0),
(1233, 1116, 200, '200', 2308, 2309, 0),
(1234, 1117, 202, '202', 2316, 2317, 0),
(1235, 1118, 202, '202', 2318, 2319, 0),
(1236, 560, 203, '203', 2322, 2323, 0),
(1237, 1119, 203, '203', 2324, 2325, 0),
(1238, 561, 203, '203', 2326, 2327, 0),
(1239, 1120, 203, '203', 2328, 2329, 0),
(1240, 1121, 203, '203', 2330, 2331, 0),
(1241, 1122, 203, '203', 2332, 2333, 0),
(1242, 740, 203, '203', 2334, 2335, 0),
(1243, 1123, 203, '203', 2336, 2337, 0),
(1244, 1124, 203, '203', 2338, 2339, 0),
(1245, 1125, 203, '203', 2340, 2341, 0),
(1246, 1126, 203, '203', 2342, 2343, 0),
(1247, 1127, 203, '203', 2344, 2345, 0),
(1248, 1128, 203, '203', 2346, 2347, 0),
(1249, 571, 203, '203', 2348, 2349, 0),
(1250, 747, 203, '203', 2350, 2351, 0),
(1251, 1129, 203, '203', 2352, 2353, 0),
(1252, 1130, 203, '203', 2354, 2355, 0),
(1253, 1131, 203, '203', 2356, 2357, 0),
(1254, 1132, 203, '203', 2358, 2359, 0),
(1255, 1133, 203, '203', 2360, 2361, 0),
(1256, 1134, 203, '203', 2362, 2363, 0),
(1257, 1135, 203, '203', 2364, 2365, 0),
(1258, 1136, 203, '203', 2366, 2367, 0),
(1259, 1137, 203, '203', 2368, 2369, 0),
(1260, 1138, 203, '203', 2370, 2371, 0),
(1261, 1139, 203, '203', 2372, 2373, 0),
(1262, 1140, 203, '203', 2374, 2375, 0),
(1263, 1141, 203, '203', 2376, 2377, 0),
(1264, 1142, 203, '203', 2378, 2379, 0),
(1265, 1143, 203, '203', 2380, 2381, 0),
(1266, 590, 203, '203', 2382, 2383, 0),
(1267, 1144, 203, '203', 2384, 2385, 0),
(1268, 1145, 203, '203', 2386, 2387, 0),
(1269, 1146, 203, '203', 2388, 2389, 0),
(1270, 596, 203, '203', 2390, 2391, 0),
(1271, 1147, 203, '203', 2392, 2393, 0),
(1272, 1148, 203, '203', 2394, 2395, 0),
(1273, 1149, 203, '203', 2396, 2397, 0),
(1274, 1150, 203, '203', 2398, 2399, 0),
(1275, 1151, 203, '203', 2400, 2401, 0),
(1276, 1152, 203, '203', 2402, 2403, 0),
(1277, 281, 203, '203', 2404, 2405, 0),
(1278, 47, 203, '203', 2406, 2407, 0),
(1279, 1153, 203, '203', 2408, 2409, 0),
(1280, 1154, 203, '203', 2410, 2411, 0),
(1281, 1155, 206, '206', 2420, 2421, 0),
(1282, 208, 207, '207', 2424, 2425, 0),
(1283, 209, 207, '207', 2426, 2427, 0),
(1284, 1156, 207, '207', 2428, 2429, 0),
(1285, 1157, 207, '207', 2430, 2431, 0),
(1286, 1158, 207, '207', 2432, 2433, 0),
(1287, 1159, 207, '207', 2434, 2435, 0),
(1288, 1160, 207, '207', 2436, 2437, 0),
(1289, 233, 207, '207', 2438, 2439, 0),
(1290, 1161, 207, '207', 2440, 2441, 0),
(1291, 1162, 207, '207', 2442, 2443, 0),
(1292, 1163, 207, '207', 2444, 2445, 0),
(1293, 1164, 207, '207', 2446, 2447, 0),
(1294, 1165, 207, '207', 2448, 2449, 0),
(1295, 1166, 207, '207', 2450, 2451, 0),
(1296, 1167, 207, '207', 2452, 2453, 0),
(1297, 1168, 207, '207', 2454, 2455, 0),
(1298, 1169, 207, '207', 2456, 2457, 0),
(1299, 1170, 207, '207', 2458, 2459, 0),
(1300, 211, 207, '207', 2460, 2461, 0),
(1301, 1171, 207, '207', 2462, 2463, 0),
(1302, 1172, 207, '207', 2464, 2465, 0),
(1303, 1173, 207, '207', 2466, 2467, 0),
(1304, 1174, 207, '207', 2468, 2469, 0),
(1305, 1175, 207, '207', 2470, 2471, 0),
(1306, 212, 207, '207', 2472, 2473, 0),
(1307, 214, 207, '207', 2474, 2475, 0),
(1308, 208, 208, '208', 2480, 2481, 0),
(1309, 209, 208, '208', 2482, 2483, 0),
(1310, 1156, 208, '208', 2484, 2485, 0),
(1311, 1157, 208, '208', 2486, 2487, 0),
(1312, 1158, 208, '208', 2488, 2489, 0),
(1313, 1159, 208, '208', 2490, 2491, 0),
(1314, 1160, 208, '208', 2492, 2493, 0),
(1315, 233, 208, '208', 2494, 2495, 0),
(1316, 1161, 208, '208', 2496, 2497, 0),
(1317, 1164, 208, '208', 2498, 2499, 0),
(1318, 1165, 208, '208', 2500, 2501, 0),
(1319, 1167, 208, '208', 2502, 2503, 0),
(1320, 1168, 208, '208', 2504, 2505, 0),
(1321, 1169, 208, '208', 2506, 2507, 0),
(1322, 1170, 208, '208', 2508, 2509, 0),
(1323, 211, 208, '208', 2510, 2511, 0),
(1324, 1171, 208, '208', 2512, 2513, 0),
(1325, 1172, 208, '208', 2514, 2515, 0),
(1326, 1174, 208, '208', 2516, 2517, 0),
(1327, 1175, 208, '208', 2518, 2519, 0),
(1328, 212, 208, '208', 2520, 2521, 0),
(1329, 214, 208, '208', 2522, 2523, 0),
(1330, 1176, 209, '209', 2526, 2527, 0),
(1331, 1177, 209, '209', 2528, 2529, 0),
(1332, 1178, 209, '209', 2530, 2531, 0),
(1333, 1179, 209, '209', 2532, 2533, 0),
(1334, 1180, 209, '209', 2534, 2535, 0),
(1335, 1181, 209, '209', 2536, 2537, 0),
(1336, 1182, 209, '209', 2538, 2539, 0),
(1337, 1183, 209, '209', 2540, 2541, 0),
(1338, 1184, 209, '209', 2542, 2543, 0),
(1339, 1185, 209, '209', 2544, 2545, 0),
(1340, 1186, 209, '209', 2546, 2547, 0),
(1341, 1187, 209, '209', 2548, 2549, 0),
(1342, 1188, 209, '209', 2550, 2551, 0),
(1343, 1189, 209, '209', 2552, 2553, 0),
(1344, 1190, 209, '209', 2554, 2555, 0),
(1345, 1191, 209, '209', 2556, 2557, 0),
(1346, 1192, 210, '210', 2560, 2561, 0),
(1347, 1193, 210, '210', 2562, 2563, 0),
(1348, 1194, 210, '210', 2564, 2565, 0),
(1349, 1195, 210, '210', 2566, 2567, 0),
(1350, 1196, 210, '210', 2568, 2569, 0),
(1351, 1197, 210, '210', 2570, 2571, 0),
(1352, 1198, 210, '210', 2572, 2573, 0),
(1353, 1199, 210, '210', 2574, 2575, 0),
(1354, 1200, 210, '210', 2576, 2577, 0),
(1355, 1201, 210, '210', 2578, 2579, 0),
(1356, 1202, 215, '215', 2592, 2593, 0),
(1357, 1203, 215, '215', 2594, 2595, 0),
(1358, 1204, 215, '215', 2596, 2597, 0),
(1359, 1205, 215, '215', 2598, 2599, 0),
(1360, 1206, 215, '215', 2600, 2601, 0),
(1361, 1207, 215, '215', 2602, 2603, 0),
(1362, 1208, 215, '215', 2604, 2605, 0),
(1363, 1209, 215, '215', 2606, 2607, 0),
(1364, 1210, 215, '215', 2608, 2609, 0),
(1365, 1211, 215, '215', 2610, 2611, 0),
(1366, 1212, 215, '215', 2612, 2613, 0),
(1367, 1213, 215, '215', 2614, 2615, 0),
(1368, 1214, 215, '215', 2616, 2617, 0),
(1369, 1215, 215, '215', 2618, 2619, 0),
(1370, 1216, 215, '215', 2620, 2621, 0),
(1371, 1217, 215, '215', 2622, 2623, 0),
(1372, 1218, 215, '215', 2624, 2625, 0),
(1373, 1219, 216, '216', 2628, 2629, 0),
(1374, 1220, 216, '216', 2630, 2631, 0),
(1375, 550, 218, '218', 2638, 2639, 0),
(1376, 1221, 218, '218', 2640, 2641, 0),
(1377, 1222, 218, '218', 2642, 2643, 0),
(1378, 550, 219, '219', 2646, 2647, 0),
(1379, 1223, 219, '219', 2648, 2649, 0),
(1380, 1224, 219, '219', 2650, 2651, 0),
(1381, 1225, 219, '219', 2652, 2653, 0),
(1382, 1226, 219, '219', 2654, 2655, 0),
(1383, 1227, 219, '219', 2656, 2657, 0),
(1384, 1228, 219, '219', 2658, 2659, 0),
(1385, 1229, 219, '219', 2660, 2661, 0),
(1386, 1230, 219, '219', 2662, 2663, 0),
(1387, 1231, 219, '219', 2664, 2665, 0),
(1388, 1232, 219, '219', 2666, 2667, 0),
(1389, 1233, 219, '219', 2668, 2669, 0),
(1390, 1234, 219, '219', 2670, 2671, 0),
(1391, 1235, 219, '219', 2672, 2673, 0),
(1392, 1236, 219, '219', 2674, 2675, 0),
(1393, 1237, 219, '219', 2676, 2677, 0),
(1394, 550, 220, '220', 2682, 2683, 0),
(1395, 1238, 220, '220', 2684, 2685, 0),
(1396, 1239, 220, '220', 2686, 2687, 0),
(1397, 1240, 220, '220', 2688, 2689, 0),
(1398, 1241, 220, '220', 2690, 2691, 0),
(1399, 1242, 220, '220', 2692, 2693, 0),
(1400, 1243, 220, '220', 2694, 2695, 0),
(1401, 1244, 220, '220', 2696, 2697, 0),
(1402, 1245, 220, '220', 2698, 2699, 0);
INSERT INTO `product_categories_parents` (`pcp_id`, `category_id`, `parent_category_id`, `parents`, `cat_lftnode`, `cat_rgtnode`, `is_deleted`) VALUES
(1403, 1246, 220, '220', 2700, 2701, 0),
(1404, 1247, 220, '220', 2702, 2703, 0),
(1405, 1248, 220, '220', 2704, 2705, 0),
(1406, 1249, 220, '220', 2706, 2707, 0),
(1407, 1250, 220, '220', 2708, 2709, 0),
(1408, 1251, 220, '220', 2710, 2711, 0),
(1409, 1252, 220, '220', 2712, 2713, 0),
(1410, 1253, 220, '220', 2714, 2715, 0),
(1411, 1254, 220, '220', 2716, 2717, 0),
(1412, 1255, 220, '220', 2718, 2719, 0),
(1413, 1256, 220, '220', 2720, 2721, 0),
(1414, 1257, 220, '220', 2722, 2723, 0),
(1415, 1258, 220, '220', 2724, 2725, 0),
(1416, 1259, 220, '220', 2726, 2727, 0),
(1417, 1260, 220, '220', 2728, 2729, 0),
(1418, 1261, 220, '220', 2730, 2731, 0),
(1419, 1262, 224, '224', 2742, 2743, 0),
(1420, 1263, 224, '224', 2744, 2745, 0),
(1421, 1264, 224, '224', 2746, 2747, 0),
(1422, 1265, 224, '224', 2748, 2749, 0),
(1423, 1266, 224, '224', 2750, 2751, 0),
(1424, 1267, 224, '224', 2752, 2753, 0),
(1425, 1268, 224, '224', 2754, 2755, 0),
(1426, 1269, 224, '224', 2756, 2757, 0),
(1427, 1270, 224, '224', 2758, 2759, 0),
(1428, 1271, 224, '224', 2760, 2761, 0),
(1429, 1272, 225, '225', 2768, 2769, 0),
(1430, 1273, 225, '225', 2770, 2771, 0),
(1431, 1274, 225, '225', 2772, 2773, 0),
(1432, 1275, 225, '225', 2774, 2775, 0),
(1433, 1276, 225, '225', 2776, 2777, 0),
(1434, 1277, 225, '225', 2778, 2779, 0),
(1435, 1278, 225, '225', 2780, 2781, 0),
(1436, 1279, 225, '225', 2782, 2783, 0),
(1437, 1280, 225, '225', 2784, 2785, 0),
(1438, 1281, 225, '225', 2786, 2787, 0),
(1439, 1282, 225, '225', 2788, 2789, 0),
(1440, 1283, 226, '226', 2792, 2793, 0),
(1441, 1284, 226, '226', 2794, 2795, 0),
(1442, 1067, 226, '226', 2796, 2797, 0),
(1443, 1285, 226, '226', 2798, 2799, 0),
(1444, 1286, 226, '226', 2800, 2801, 0),
(1445, 1287, 226, '226', 2802, 2803, 0),
(1446, 1288, 226, '226', 2804, 2805, 0),
(1447, 1068, 226, '226', 2806, 2807, 0),
(1448, 1069, 226, '226', 2808, 2809, 0),
(1449, 1289, 226, '226', 2810, 2811, 0),
(1450, 1290, 226, '226', 2812, 2813, 0),
(1451, 1071, 226, '226', 2814, 2815, 0),
(1452, 1072, 226, '226', 2816, 2817, 0),
(1453, 1291, 226, '226', 2818, 2819, 0),
(1454, 1292, 226, '226', 2820, 2821, 0),
(1455, 1293, 226, '226', 2822, 2823, 0),
(1456, 1294, 226, '226', 2824, 2825, 0),
(1457, 1295, 226, '226', 2826, 2827, 0),
(1458, 1074, 226, '226', 2828, 2829, 0),
(1459, 1296, 226, '226', 2830, 2831, 0),
(1460, 1075, 226, '226', 2832, 2833, 0),
(1461, 1297, 226, '226', 2834, 2835, 0),
(1462, 1076, 226, '226', 2836, 2837, 0),
(1463, 1077, 226, '226', 2838, 2839, 0),
(1464, 1079, 226, '226', 2840, 2841, 0),
(1465, 1298, 226, '226', 2842, 2843, 0),
(1466, 1299, 227, '227', 2846, 2847, 0),
(1467, 1300, 227, '227', 2848, 2849, 0),
(1468, 1301, 227, '227', 2850, 2851, 0),
(1469, 1302, 227, '227', 2852, 2853, 0),
(1470, 1303, 227, '227', 2854, 2855, 0),
(1471, 1304, 227, '227', 2856, 2857, 0),
(1472, 1305, 228, '228', 2860, 2861, 0),
(1473, 1306, 228, '228', 2862, 2863, 0),
(1474, 1307, 228, '228', 2864, 2865, 0),
(1475, 1308, 228, '228', 2866, 2867, 0),
(1476, 1309, 228, '228', 2868, 2869, 0),
(1477, 1310, 228, '228', 2870, 2871, 0),
(1478, 1311, 228, '228', 2872, 2873, 0),
(1479, 418, 228, '228', 2874, 2875, 0),
(1480, 1312, 228, '228', 2876, 2877, 0),
(1481, 1313, 228, '228', 2878, 2879, 0),
(1482, 1314, 228, '228', 2880, 2881, 0),
(1483, 1315, 228, '228', 2882, 2883, 0),
(1484, 1316, 228, '228', 2884, 2885, 0),
(1485, 1317, 228, '228', 2886, 2887, 0),
(1486, 1318, 228, '228', 2888, 2889, 0),
(1487, 1319, 228, '228', 2890, 2891, 0),
(1488, 1320, 228, '228', 2892, 2893, 0),
(1489, 1321, 229, '229', 2896, 2897, 0),
(1490, 1322, 229, '229', 2898, 2899, 0),
(1491, 1323, 230, '230', 2902, 2903, 0),
(1492, 1324, 231, '231', 2908, 2909, 0),
(1493, 767, 231, '231', 2910, 2911, 0),
(1494, 1325, 231, '231', 2912, 2913, 0),
(1495, 1326, 231, '231', 2914, 2915, 0),
(1496, 1327, 231, '231', 2916, 2917, 0),
(1497, 1328, 231, '231', 2918, 2919, 0),
(1498, 1329, 232, '232', 2924, 2925, 0),
(1499, 1330, 232, '232', 2926, 2927, 0),
(1500, 1331, 232, '232', 2928, 2929, 0),
(1501, 1332, 232, '232', 2930, 2931, 0),
(1502, 1333, 232, '232', 2932, 2933, 0),
(1503, 1334, 232, '232', 2934, 2935, 0),
(1504, 1335, 232, '232', 2936, 2937, 0),
(1505, 1336, 233, '233', 2940, 2941, 0),
(1506, 1337, 235, '235', 2946, 2947, 0),
(1507, 550, 235, '235', 2948, 2949, 0),
(1508, 1338, 235, '235', 2950, 2951, 0),
(1509, 1339, 235, '235', 2952, 2953, 0),
(1510, 1340, 235, '235', 2954, 2955, 0),
(1511, 1341, 235, '235', 2956, 2957, 0),
(1512, 1342, 235, '235', 2958, 2959, 0),
(1513, 1343, 235, '235', 2960, 2961, 0),
(1514, 1344, 235, '235', 2962, 2963, 0),
(1515, 1345, 235, '235', 2964, 2965, 0),
(1516, 1346, 235, '235', 2966, 2967, 0),
(1517, 1347, 235, '235', 2968, 2969, 0),
(1518, 1348, 235, '235', 2970, 2971, 0),
(1519, 1349, 235, '235', 2972, 2973, 0),
(1520, 1350, 235, '235', 2974, 2975, 0),
(1521, 1351, 235, '235', 2976, 2977, 0),
(1522, 1352, 235, '235', 2978, 2979, 0),
(1523, 1353, 235, '235', 2980, 2981, 0),
(1524, 1354, 235, '235', 2982, 2983, 0),
(1525, 973, 235, '235', 2984, 2985, 0),
(1526, 1355, 235, '235', 2986, 2987, 0),
(1527, 1356, 235, '235', 2988, 2989, 0),
(1528, 1357, 235, '235', 2990, 2991, 0),
(1529, 1358, 235, '235', 2992, 2993, 0),
(1530, 1359, 235, '235', 2994, 2995, 0),
(1531, 1360, 235, '235', 2996, 2997, 0),
(1532, 42, 235, '235', 2998, 2999, 0),
(1533, 1361, 235, '235', 3000, 3001, 0),
(1534, 1362, 235, '235', 3002, 3003, 0),
(1535, 1363, 236, '236', 3006, 3007, 0),
(1536, 1364, 236, '236', 3008, 3009, 0),
(1537, 1365, 236, '236', 3010, 3011, 0),
(1538, 1366, 236, '236', 3012, 3013, 0),
(1539, 1367, 236, '236', 3014, 3015, 0),
(1540, 1368, 238, '238', 3026, 3027, 0),
(1541, 1369, 238, '238', 3028, 3029, 0),
(1542, 1370, 238, '238', 3030, 3031, 0),
(1543, 1371, 238, '238', 3032, 3033, 0),
(1544, 1372, 238, '238', 3034, 3035, 0),
(1545, 1373, 238, '238', 3036, 3037, 0),
(1546, 1374, 238, '238', 3038, 3039, 0),
(1547, 1375, 238, '238', 3040, 3041, 0),
(1548, 1376, 238, '238', 3042, 3043, 0),
(1549, 1377, 238, '238', 3044, 3045, 0),
(1550, 1378, 238, '238', 3046, 3047, 0),
(1551, 173, 239, '239', 3052, 3053, 0),
(1552, 166, 239, '239', 3054, 3055, 0),
(1553, 1379, 239, '239', 3056, 3057, 0),
(1554, 1016, 239, '239', 3058, 3059, 0),
(1555, 1380, 239, '239', 3060, 3061, 0),
(1556, 176, 239, '239', 3062, 3063, 0),
(1557, 1381, 239, '239', 3064, 3065, 0),
(1558, 1382, 239, '239', 3066, 3067, 0),
(1559, 891, 239, '239', 3068, 3069, 0),
(1560, 1017, 239, '239', 3070, 3071, 0),
(1561, 1383, 239, '239', 3072, 3073, 0),
(1562, 1384, 239, '239', 3074, 3075, 0),
(1563, 1018, 239, '239', 3076, 3077, 0),
(1564, 1385, 239, '41,239', 3078, 3079, 0),
(1565, 356, 242, '242', 3086, 3087, 0),
(1566, 357, 242, '242', 3088, 3089, 0),
(1567, 358, 242, '242', 3090, 3091, 0),
(1568, 359, 242, '242', 3092, 3093, 0),
(1569, 360, 242, '242', 3094, 3095, 0),
(1570, 361, 242, '242', 3096, 3097, 0),
(1571, 362, 242, '242', 3098, 3099, 0),
(1572, 363, 242, '242', 3100, 3101, 0),
(1573, 1386, 242, '242', 3102, 3103, 0),
(1574, 1387, 244, '244', 3108, 3109, 0),
(1575, 1388, 246, '246', 3114, 3115, 0),
(1576, 1389, 246, '246', 3116, 3117, 0),
(1577, 1390, 246, '246', 3118, 3119, 0),
(1578, 1391, 246, '246', 3120, 3121, 0),
(1579, 1392, 246, '246', 3122, 3123, 0),
(1580, 1393, 248, '248', 3128, 3129, 0),
(1581, 1394, 251, '251', 3136, 3137, 0),
(1582, 1395, 253, '253', 3142, 3143, 0),
(1583, 1396, 253, '253', 3144, 3145, 0),
(1584, 1397, 253, '253', 3146, 3147, 0),
(1585, 138, 253, '253', 3148, 3149, 0),
(1586, 1398, 253, '253', 3150, 3151, 0),
(1587, 1399, 253, '253', 3152, 3153, 0),
(1588, 1400, 253, '253', 3154, 3155, 0),
(1589, 240, 256, '256', 3162, 3163, 0),
(1590, 241, 256, '256', 3164, 3165, 0),
(1591, 251, 256, '256', 3166, 3167, 0),
(1592, 1401, 264, '43,264', 3188, 3189, 0),
(1593, 1402, 264, '264', 3190, 3191, 0),
(1594, 566, 264, '264', 3192, 3193, 0),
(1595, 1403, 268, '43,268', 3202, 3203, 0),
(1596, 1404, 268, '268', 3204, 3205, 0),
(1597, 1405, 270, '44,270', 7, 8, 0),
(1598, 1406, 270, '270', 9, 10, 0),
(1599, 1407, 270, '270', 11, 12, 0),
(1600, 1408, 270, '270', 13, 14, 0),
(1601, 1409, 270, '44,270', 15, 16, 0),
(1602, 1410, 270, '270', 17, 18, 0),
(1603, 1411, 270, '44,270', 19, 20, 0),
(1604, 1140, 288, '288', 61, 62, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_category_assoc`
--

CREATE TABLE IF NOT EXISTS `product_category_assoc` (
  `pro_ca_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `category_id` smallint(6) unsigned NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`pro_ca_id`),
  KEY `product_id` (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_category_properties`
--

CREATE TABLE IF NOT EXISTS `product_category_properties` (
  `category_property_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` smallint(6) unsigned NOT NULL,
  `property_id` int(10) unsigned NOT NULL DEFAULT '0',
  `filterable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`category_property_id`),
  KEY `category_id` (`category_id`),
  KEY `property_id` (`property_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=90 ;

--
-- Dumping data for table `product_category_properties`
--

INSERT INTO `product_category_properties` (`category_property_id`, `category_id`, `property_id`, `filterable`, `is_deleted`) VALUES
(1, 2, 11, 1, 0),
(2, 2, 35, 1, 0),
(3, 2, 4, 1, 0),
(4, 7, 60, 0, 0),
(5, 7, 11, 1, 0),
(6, 7, 5, 0, 0),
(7, 7, 69, 0, 0),
(8, 7, 56, 0, 0),
(9, 7, 43, 0, 0),
(10, 7, 28, 0, 0),
(11, 7, 41, 0, 0),
(12, 7, 48, 0, 0),
(13, 7, 42, 1, 0),
(14, 7, 14, 0, 0),
(15, 7, 36, 1, 0),
(16, 7, 73, 0, 0),
(17, 7, 34, 1, 0),
(18, 7, 33, 0, 0),
(19, 7, 45, 0, 0),
(20, 7, 64, 0, 0),
(21, 7, 21, 0, 0),
(22, 7, 29, 0, 0),
(23, 7, 50, 1, 0),
(24, 7, 54, 0, 0),
(25, 7, 9, 0, 0),
(26, 7, 72, 0, 0),
(27, 7, 46, 0, 0),
(28, 7, 58, 0, 0),
(29, 7, 53, 0, 0),
(30, 7, 26, 0, 0),
(31, 7, 23, 0, 0),
(32, 7, 40, 0, 0),
(33, 7, 38, 0, 0),
(34, 7, 44, 0, 0),
(35, 7, 47, 0, 0),
(36, 7, 81, 0, 0),
(37, 7, 8, 0, 0),
(38, 7, 76, 0, 0),
(39, 7, 71, 0, 0),
(40, 7, 22, 0, 0),
(41, 7, 49, 0, 0),
(42, 7, 35, 0, 0),
(43, 7, 77, 0, 0),
(44, 7, 79, 0, 0),
(45, 7, 68, 1, 0),
(46, 7, 63, 0, 0),
(47, 7, 2, 0, 0),
(48, 7, 59, 1, 0),
(49, 7, 16, 0, 0),
(50, 7, 65, 0, 0),
(51, 7, 66, 0, 0),
(52, 7, 6, 1, 0),
(53, 7, 12, 0, 0),
(54, 7, 15, 1, 0),
(55, 7, 75, 0, 0),
(56, 7, 52, 0, 0),
(57, 7, 51, 0, 0),
(58, 7, 17, 0, 0),
(59, 7, 19, 0, 0),
(60, 7, 82, 0, 0),
(61, 7, 1, 1, 0),
(62, 7, 10, 0, 0),
(63, 7, 55, 1, 0),
(64, 7, 3, 0, 0),
(65, 7, 67, 0, 0),
(66, 7, 7, 0, 0),
(67, 7, 70, 0, 0),
(68, 7, 13, 1, 0),
(69, 7, 25, 0, 0),
(70, 7, 24, 0, 0),
(71, 7, 32, 0, 0),
(72, 7, 74, 0, 0),
(73, 7, 18, 0, 0),
(74, 7, 37, 0, 0),
(75, 7, 20, 0, 0),
(76, 7, 31, 0, 0),
(77, 7, 61, 0, 0),
(78, 7, 4, 1, 0),
(79, 7, 30, 0, 0),
(80, 7, 80, 0, 0),
(81, 7, 62, 0, 0),
(82, 7, 27, 0, 0),
(83, 7, 39, 0, 0),
(84, 7, 78, 0, 0),
(85, 7, 57, 0, 0),
(86, 1, 11, 1, 0),
(87, 571, 77, 0, 0),
(88, 571, 57, 0, 0),
(89, 571, 78, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_category_property_values`
--

CREATE TABLE IF NOT EXISTS `product_category_property_values` (
  `cpv_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_property_id` bigint(20) unsigned NOT NULL,
  `value_id` bigint(20) unsigned NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`cpv_id`),
  KEY `value_id` (`value_id`),
  KEY `category_property_id` (`category_property_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `product_category_property_values`
--

INSERT INTO `product_category_property_values` (`cpv_id`, `category_property_id`, `value_id`, `is_deleted`) VALUES
(1, 7, 4, 0),
(2, 7, 5, 0),
(3, 9, 6, 0),
(4, 9, 2, 0),
(5, 9, 1, 0),
(6, 7, 11, 0),
(7, 9, 9, 0),
(8, 10, 6, 0),
(9, 10, 2, 0),
(10, 10, 1, 0),
(11, 10, 9, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_cmb_properties`
--

CREATE TABLE IF NOT EXISTS `product_cmb_properties` (
  `cmb_ppt_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_cmb_id` int(11) unsigned NOT NULL,
  `property_id` int(10) unsigned NOT NULL,
  `value_id` bigint(20) unsigned NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`cmb_ppt_id`),
  KEY `property_id` (`property_id`),
  KEY `value_id` (`value_id`),
  KEY `product_cmb_id` (`product_cmb_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `product_cmb_properties`
--

INSERT INTO `product_cmb_properties` (`cmb_ppt_id`, `product_cmb_id`, `property_id`, `value_id`, `is_deleted`) VALUES
(1, 1, 6, 5, 0),
(2, 1, 1, 6, 0),
(3, 1, 1, 2, 0),
(4, 1, 1, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_combinations`
--

CREATE TABLE IF NOT EXISTS `product_combinations` (
  `product_cmb_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `product_cmb` varchar(255) DEFAULT NULL,
  `pro_com_value_id` varchar(30) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`product_cmb_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `product_combinations`
--

INSERT INTO `product_combinations` (`product_cmb_id`, `product_id`, `product_cmb`, `pro_com_value_id`, `is_deleted`) VALUES
(1, 2, 'Color : #48ee40,Size : X', '0', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_condition_lookups`
--

CREATE TABLE IF NOT EXISTS `product_condition_lookups` (
  `condition_id` smallint(2) NOT NULL AUTO_INCREMENT,
  `condition_desc` varchar(100) DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`condition_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `product_condition_lookups`
--

INSERT INTO `product_condition_lookups` (`condition_id`, `condition_desc`, `status`) VALUES
(1, 'New', 1),
(2, 'Used', 1),
(3, 'Refurbished', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_countries`
--

CREATE TABLE IF NOT EXISTS `product_countries` (
  `pc_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `country_id` tinyint(3) unsigned NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `log` text,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`pc_id`),
  UNIQUE KEY `product_id` (`product_id`,`country_id`),
  KEY `updated_by` (`updated_by`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `product_countries`
--

INSERT INTO `product_countries` (`pc_id`, `product_id`, `country_id`, `created_on`, `updated_on`, `updated_by`, `log`, `is_deleted`) VALUES
(1, 1, 77, NULL, '2017-08-03 23:25:08', 3, 'concat(log,'',{"date":"2017-08-03 22:52:10","updated_by":"3","action":"DELETED"}'')', 0),
(2, 2, 77, '2016-09-07 04:21:14', '2017-07-20 06:55:45', 1, NULL, 0),
(3, 3, 77, '2016-09-07 04:54:36', '2017-07-20 06:35:13', 1, NULL, 0),
(4, 4, 77, '2016-09-07 05:24:25', '2017-07-20 06:35:22', 1, NULL, 0),
(5, 5, 77, '2016-09-07 05:34:13', '2017-07-20 06:35:17', 1, NULL, 0),
(6, 6, 77, '2016-10-25 06:33:11', '2017-07-20 06:35:02', 1, NULL, 0),
(7, 7, 77, '2017-07-20 17:57:00', '2017-07-20 06:57:00', 1, NULL, 0),
(8, 8, 77, '2017-07-20 17:57:00', '2017-07-20 06:57:00', 1, NULL, 0),
(9, 9, 77, '2017-07-20 17:57:00', '2017-07-20 06:57:00', 1, NULL, 0),
(10, 11, 77, '2017-07-20 17:57:00', '2017-07-20 06:57:00', 1, NULL, 0),
(11, 30, 77, '2018-07-06 10:59:26', '2018-07-06 05:29:54', 155, 'concat(log,'',{"date":"2018-07-06 10:59:54","updated_by":155,"action":"ADDED"}'')', 0),
(12, 31, 77, '2018-07-06 12:12:16', '2018-07-06 12:12:16', 155, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_details`
--

CREATE TABLE IF NOT EXISTS `product_details` (
  `product_details_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `product_cmb_id` int(11) unsigned DEFAULT NULL,
  `product_code` varchar(20) NOT NULL,
  `eanbarcode` varchar(20) NOT NULL,
  `upcbarcode` varchar(20) NOT NULL,
  `product_slug` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `description` text,
  `visiblity_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `redirect_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_exclusive` tinyint(1) unsigned NOT NULL,
  `width` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT 'Units in CM (Centimeter)',
  `height` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT 'Units in CM (Centimeter)',
  `length` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT 'Units in CM (Centimeter)',
  `weight` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT 'Units in KiloGrams(KG)',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `verified_on` datetime DEFAULT NULL,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`product_details_id`),
  UNIQUE KEY `eanbarcode` (`eanbarcode`),
  UNIQUE KEY `upcbarcode` (`upcbarcode`),
  KEY `product_id` (`product_id`),
  KEY `product_cmb_id` (`product_cmb_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  KEY `verified_by` (`verified_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `product_details`
--

INSERT INTO `product_details` (`product_details_id`, `product_id`, `product_cmb_id`, `product_code`, `eanbarcode`, `upcbarcode`, `product_slug`, `sku`, `description`, `visiblity_id`, `redirect_id`, `is_verified`, `is_exclusive`, `width`, `height`, `length`, `weight`, `status`, `created_on`, `created_by`, `updated_on`, `updated_by`, `verified_on`, `verified_by`) VALUES
(1, 1, NULL, 'PRO1', '123456798123', '123456798123', 'Mobile', 'Mobile', 'Mobile', 0, 0, 1, 0, '20.000', '5.000', '100.000', '5.000', 1, '2016-07-13 03:43:44', 1, '2017-05-30 13:52:14', 1, '2016-11-14 00:43:10', 1),
(2, 2, 1, 'PRO2CMB1', '123456798124', '123456798124', 'nokia-130-ds', 'nokia-130-dsx-48ee40', 'As enabling as smartphones are, they can be confusing to use too. Many people still prefer the homely comfort of feature phones. If you are one of those people then switch on to the Nokia 130. Loaded with features such as an FM radio, a bright flashlight, a 4.54 cm LCD transmissive screen, and a sturdy body that keeps its color even when scratched, this dual-sim feature phone is always ready for action.\r\nMusic makes work more enjoyable\r\nFeaturing up to 46 hours of playback time and an expandable storage capacity of up to 32GB, this phone lets you play your favourite tracks all day long. So put on your headphones and keep enjoying your favourite music.\r\nWatch more video\r\nThe 1020mAh battery of this phone offers you up to 16 hours of video playback on a single charge. So add as many videos and clips as you want to your mobile’s SD card and enjoy your favourite videos on-the-go.\r\nShare as much as you want\r\nSharing your files with the Nokia 130 is fast and fun. Whether you want to use the Bluetooth or the microUSB cable, this phone lets you share videos, contacts and more with ease.', 0, 0, 1, 0, '50.000', '10.000', '150.000', '150.000', 1, '2016-08-23 01:36:59', 1, '2017-05-30 13:52:19', 2, NULL, 1),
(3, 3, NULL, 'PRO3', '123456798125', '123456798125', 'test', 'test', 'test product', 1, 0, 1, 0, '0.000', '0.000', '0.000', '0.000', 1, '2016-10-22 01:19:15', 1, '2017-05-30 13:52:23', 2, '2016-11-15 03:31:37', 1),
(4, 4, NULL, 'PRO4', '123456798126', '123456798126', 'BRAVIA KLV-32R302D', 'BRAVIA KLV-32R302D', '<br />\r\nWe&#39;ll facilitate the installation and demo through authorized service engineer at your convenience. The installation will be done within 2 to 5 Business days of delivery of the TV. The service engineer will install your new TV, either on wall mount or on table top. Installation and demo are provided free of cost. The engineer will also help you understand your new TV&#39;s features. The process generally covers: Wall-mounted or table-top installation, as requested (Wall mounted mode is recommended for better sound experience), Physical check of all ports, including power and USB ports. Accessories also checked, Demonstration of features and settings, Quick run-through on how to operate the TV.', 1, 0, 1, 0, '0.000', '0.000', '0.000', '0.000', 1, '2016-10-25 00:57:23', 1, '2017-05-30 13:52:29', 1, '2016-11-13 22:40:52', 1),
(5, 5, NULL, 'PRO5', '123456798127', '123456798127', 'BRAVIA KLV-32R302D', 'BRAVIA KLV-32R302D', '<br />\r\nWe&#39;ll facilitate the installation and demo through authorized service engineer at your convenience. The installation will be done within 2 to 5 Business days of delivery of the TV. The service engineer will install your new TV, either on wall mount or on table top. Installation and demo are provided free of cost. The engineer will also help you understand your new TV&#39;s features. The process generally covers: Wall-mounted or table-top installation, as requested (Wall mounted mode is recommended for better sound experience), Physical check of all ports, including power and USB ports. Accessories also checked, Demonstration of features and settings, Quick run-through on how to operate the TV.', 0, 0, 1, 0, '1.000', '1.000', '1.000', '0.000', 1, '2016-10-25 00:57:30', 1, '2017-05-30 13:52:32', 1, '2016-11-13 22:39:28', 1),
(6, 6, NULL, 'PRO6', '123456798128', '123456798128', 'BRAVIA KLV-32R302D', 'BRAVIA KLV-32R302D', '<br />\r\nWe&#39;ll facilitate the installation and demo through authorized service engineer at your convenience. The installation will be done within 2 to 5 Business days of delivery of the TV. The service engineer will install your new TV, either on wall mount or on table top. Installation and demo are provided free of cost. The engineer will also help you understand your new TV&#39;s features. The process generally covers: Wall-mounted or table-top installation, as requested (Wall mounted mode is recommended for better sound experience), Physical check of all ports, including power and USB ports. Accessories also checked, Demonstration of features and settings, Quick run-through on how to operate the TV.', 1, 0, 1, 0, '0.000', '0.000', '0.000', '0.000', 1, '2016-10-25 01:06:32', 1, '2017-05-30 13:53:21', 1, NULL, 1),
(7, 7, NULL, 'PRO7', '123456798129', '123456798129', 'test3', 'test3', '', 0, 0, 1, 1, '0.000', '0.000', '0.000', '0.000', 1, '2016-10-25 06:28:29', 1, '2017-05-30 13:53:25', 1, '2016-11-13 22:37:34', 1),
(8, 8, NULL, 'PRO8', '123456798130', '123456798130', 'pro1', 'pro1', 'pro1', 1, 0, 1, 1, '0.000', '0.000', '0.000', '0.000', 1, '2017-02-21 01:11:09', 1, '2017-05-30 13:53:30', 2, NULL, 1),
(9, 9, NULL, 'PRO9', '123456798131', 'test3', 'test3', 'test3', 'test3', 1, 0, 1, 1, '0.000', '0.000', '0.000', '0.000', 1, '2016-11-13 23:32:42', 1, '2017-05-19 12:35:55', 1, '2016-11-14 01:47:55', 1),
(10, 11, NULL, 'PRO11', '', '6921811800259', 'OnePlus 3T', 'OnePlus 3T', 'Onepluse 3t', 0, 0, 1, 1, '7.500', '15.300', '0.700', '165.000', 1, '2017-05-22 23:01:56', 1, '2017-05-25 09:56:38', 1, '2017-05-24 22:26:18', 1),
(11, 16, NULL, 'PRO16', '6921815600017', '6921815600017', '', 'OnePlus 3T2', 'OnePlus 3T', 0, 0, 1, 1, '0.720', '0.020', '0.120', '0.030', 1, '2017-05-30 02:31:08', 1, '2017-05-30 16:24:13', 1, '2017-05-30 04:54:07', 1),
(12, 17, NULL, 'PRO17', '6921815600018', '6921815600018', '', 'OnePlus 3T3', 'OnePlus 3T', 0, 0, 1, 1, '0.720', '0.020', '0.120', '0.030', 1, '2017-05-30 02:38:42', 1, '2017-05-30 16:24:01', 1, '2017-05-30 04:53:54', 1),
(13, 18, NULL, 'PRO18', '6921815600019', '6921815600019', '', '', '', 0, 0, 1, 1, '0.000', '0.000', '0.000', '0.000', 1, '2017-05-30 02:39:26', 1, '2017-06-29 14:53:03', 3, '2017-05-30 04:53:36', 1),
(14, 19, NULL, 'PRO19', '723755011083', '723755011083', 'moto-g5-plus', 'moto-g5-plus', 'Your search for a flaunt-worthy, feature-rich smartphone ends with the Moto G5 Plus. With a captivating design and an exceptional camera, this smartphone will change how you look at mobile photography.<br />\r\nFlaunt-worthy Design<br />\r\n<img data-reactid="1428" src="https://rukminim1.flixcart.com/image/200/200/mobile/c/z/2/motorola-g5-plus-xt-1686-original-imaes2md7yeqemwh.jpeg?q=90" />\r\n<p>This elegantly designed smartphone comes with a high-grade aluminium exterior that has been diamond cut and polished to perfection for a premium look and feel.</p>\r\n<br />\r\nAdvanced Camera<br />\r\n<img data-reactid="1436" src="https://rukminim1.flixcart.com/image/200/200/mobile/c/z/2/motorola-g5-plus-xt-1686-original-imaes2mdx5bgjxfg.jpeg?q=90" />\r\n<p>The new Moto G Plus (5th Gen), with a 12MP Rear Camera and Dual Autofocus Pixels, utilizes 10 times more pixels on the sensor and locks onto the subject up to 60% faster than before to give you perfect photographs. Whether you are a foodie who loves taking pictures of his food or if you love taking group pics with your friends, with the Moto G5 Plus&#39; advanced camera, you can click beautiful pictures in all lighting conditions. Thanks to its 5 MP front camera and its wide-angle lens, you can click share-worthy group selfies to show off Instagram. The Moto G Plus (5th Gen) also comes with Best Shot, Professional Mode, and Beautification Mode so you can click and capture attractive photos one after another.</p>\r\n<br />\r\nMore Power to You<br />\r\n<img data-reactid="1444" src="https://rukminim1.flixcart.com/image/200/200/mobile/c/z/2/motorola-g5-plus-xt-1686-original-imaes2mdhyhnhjda.jpeg?q=90" />\r\n<p>If you are always on the move, then you are going to love the Moto G Plus (5th Gen) as it&#39;s powered by a 3000 mAh battery that keeps you going all day long. Whether you want to listen to songs while traveling, click pictures of everything around you or just stay connected through social media, with the Moto G Plus (5th Gen), you can relax without worrying about running out of charge. This phone also comes with a TurboPower charger that charges up to 6 hours of battery in just about 15 minutes so you can always be on the move.</p>\r\n<br />\r\nGlitch-free Performance<br />\r\n<img data-reactid="1452" src="https://rukminim1.flixcart.com/image/200/200/mobile/c/z/2/motorola-g5-plus-xt-1686-original-imaes2mdrwzyha7u.jpeg?q=90" />\r\n<p>With the Moto G Plus (5th Gen), you can use multiple apps simultaneously without worrying about lag as it&rsquo;s powered by a blazing-fast 2.0 GHz octa-core processor and has powerful graphics capabilities. It also comes with 3 GB RAM which ensures a glitch-free performance.</p>\r\n<br />\r\nFull HD Display<br />\r\n<img data-reactid="1460" src="https://rukminim1.flixcart.com/image/200/200/mobile/c/z/2/motorola-g5-plus-xt-1686-original-imaes2mdejgvsmzz.jpeg?q=90" />\r\n<p>Watch movie characters come to life, view photos in life-like clarity and get involved in an immersive game on this phone&#39;s 13.20 cm (5.2) Full HD display. This phone also offers rich colors, fine image details, and picture-perfect clarity, thanks to its 1080/424 ppi resolution. This smartphone also comes with Corning Gorilla Glass 3 which adds to its durability.</p>\r\n<br />\r\nWater-repellent Coating\r\n<p>Don&rsquo;t let rain, accidental spills of water or splashes of your drink get in your way. The Moto G Plus (5th Gen) has a water-repellent coating that protects it inside-out.</p>\r\n<br />\r\nExclusive Moto Experiences<br />\r\n<img data-reactid="1474" src="https://rukminim1.flixcart.com/image/200/200/mobile/c/z/2/motorola-moto-g5-plus-xt-1686-original-imaes3ynzghgb2kj.jpeg?q=90" />\r\n<p>Get done with complicated passwords and patterns as the Moto G5 Plus can be unlocked with just a tap of your finger on the fingerprint sensor. This 4G LTE phone also comes with Moto Display feature which gives you a quick preview of notifications and updates so you don&rsquo;t have to unlock your phone every time. The Moto G Plus (5th Gen) runs on the Android Nougat OS that makes using this phone a lot more safer, simpler and easier.</p>\r\n<br />\r\nMoto Actions<br />\r\n<img data-reactid="1482" src="https://rukminim1.flixcart.com/image/200/200/mobile/c/z/2/motorola-g5-plus-xt-1686-original-imaes2mdbhc33cxx.jpeg?q=90" />\r\n<p>With Moto Actions, all you need are simple gestures to make life easier. This feature makes everyday interactions more convenient as you can open your camera with just a twist of your wrist and just chop down twice to get a torch. Also, you can just place this phone face down to activate the Do Not Disturb mode or pick up the phone when it rings to immediately switch to vibrate mode. You can even minimize your screen with a simple gesture so you can use the Moto G Plus (5th Gen) comfortably with just one hand.</p>\r\n<br />\r\nStorage\r\n<p>Store all that you want from movies to songs, videos to pictures, without worrying about running out of storage space as this phone comes with 16 GB of internal memory which can be expanded by up to 128 GB.</p>\r\n', 1, 0, 1, 1, '6.000', '1.000', '12.000', '155.000', 1, '2017-08-07 03:04:19', 3, NULL, 3, '2017-08-07 04:07:53', 1),
(25, 30, NULL, 'PRO30', '121321', '5645465', '1000222', '1000222', 'description', 1, 0, 0, 1, '100.000', '100.000', '100.000', '0.100', 0, '2018-07-05 12:06:05', 4, NULL, 4, NULL, NULL),
(26, 31, NULL, 'PRO31', '5000555', '6000666', 'mobiles-mobiles', 'mobiles-mobiles', 'descripption', 1, 0, 0, 1, '50.000', '100.000', '10.000', '0.200', 0, '2018-07-06 11:30:34', 4, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `product_discounts`
--
CREATE TABLE IF NOT EXISTS `product_discounts` (
`supplier_product_id` bigint(15) unsigned
,`discount_id` mediumint(8) unsigned
,`discount` varchar(100)
,`description` text
,`discount_type_id` tinyint(2) unsigned
,`discount_type` varchar(200)
,`discount_by` tinyint(2) unsigned
,`priority` smallint(6) unsigned
,`discount_value_type` tinyint(1) unsigned
,`is_qty_based` tinyint(1) unsigned
,`discount_value` double unsigned
,`currency_id` tinyint(3) unsigned
,`min_qty` smallint(6) unsigned
,`max_qty` smallint(6) unsigned
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `product_info`
--
CREATE TABLE IF NOT EXISTS `product_info` (
`product_id` int(10) unsigned
,`product_cmb_id` int(11) unsigned
,`product_code` varchar(20)
,`eanbarcode` varchar(20)
,`upcbarcode` varchar(20)
,`category_id` smallint(6) unsigned
,`category_code` varchar(3)
,`category` varchar(100)
,`category_url_str` varchar(200)
,`replacement_service_policy_id` tinyint(3) unsigned
,`assoc_category_id` text
,`brand_id` smallint(6) unsigned
,`brand_name` varchar(100)
,`brand_url_str` varchar(200)
,`brand_sku` varchar(255)
,`product_name` text
,`product_cmb` varchar(255)
,`product_slug` varchar(255)
,`sku` varchar(255)
,`description` text
,`is_combinations` tinyint(1) unsigned
);
-- --------------------------------------------------------

--
-- Table structure for table `product_mrp_price`
--

CREATE TABLE IF NOT EXISTS `product_mrp_price` (
  `pp_id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `mrp_price` double unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pp_id`),
  UNIQUE KEY `product_id_2` (`product_id`,`currency_id`),
  KEY `product_id` (`product_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `product_mrp_price`
--

INSERT INTO `product_mrp_price` (`pp_id`, `product_id`, `currency_id`, `mrp_price`) VALUES
(1, 1, 1, 0),
(2, 2, 1, 5000),
(3, 3, 1, 12),
(4, 4, 1, 5000),
(5, 5, 1, 12),
(6, 6, 1, 5000),
(7, 7, 1, 12),
(8, 8, 1, 5000),
(9, 9, 1, 12),
(10, 11, 1, 5000),
(11, 18, 1, 1000),
(12, 19, 1, 500),
(13, 29, 2, 0),
(14, 30, 2, 100),
(15, 30, 1, 1000),
(16, 31, 2, 100),
(17, 31, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_payment_types`
--

CREATE TABLE IF NOT EXISTS `product_payment_types` (
  `ptype_id` bigint(30) NOT NULL AUTO_INCREMENT,
  `payment_type_id` tinyint(3) unsigned DEFAULT NULL,
  `paymode_id` mediumint(30) NOT NULL,
  `supplier_product_id` bigint(15) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`ptype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `product_payment_types`
--

INSERT INTO `product_payment_types` (`ptype_id`, `payment_type_id`, `paymode_id`, `supplier_product_id`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 1, 4, 15, '2018-07-06 10:31:18', '2018-07-06 10:31:18', 1),
(2, 1, 4, 15, '2018-07-06 10:34:05', '2018-07-06 10:34:05', 1),
(3, 1, 4, 15, '2018-07-06 10:55:05', '2018-07-06 10:55:05', 0),
(4, 1, 4, 16, '2018-07-06 12:12:01', '2018-07-06 12:12:01', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_properties`
--

CREATE TABLE IF NOT EXISTS `product_properties` (
  `pp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `property_id` int(10) unsigned NOT NULL,
  `choosable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  `filterable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  `key_feature` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  `key_value` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`pp_id`),
  UNIQUE KEY `product_id_2` (`product_id`,`property_id`),
  KEY `product_id` (`product_id`),
  KEY `property_id` (`property_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `product_properties`
--

INSERT INTO `product_properties` (`pp_id`, `product_id`, `property_id`, `choosable`, `filterable`, `key_feature`, `key_value`, `is_deleted`) VALUES
(1, 1, 6, 1, 1, 0, NULL, 1),
(2, 1, 5, 1, 0, 0, NULL, 1),
(3, 1, 1, 1, 0, 0, NULL, 1),
(4, 2, 6, 1, 1, 0, NULL, 0),
(5, 2, 1, 1, 0, 0, NULL, 0),
(7, 3, 5, 1, 0, 0, NULL, 0),
(8, 3, 6, 1, 0, 0, NULL, 0),
(9, 1, 11, 1, 0, 0, NULL, 0),
(10, 6, 11, 1, 0, 0, NULL, 0),
(11, 6, 1, 0, 0, 0, '', 0),
(12, 6, 6, 1, 1, 0, NULL, 0),
(13, 6, 67, 1, 0, 0, NULL, 0),
(14, 6, 13, 1, 0, 0, NULL, 0),
(15, 1, 35, 0, 0, 0, NULL, 0),
(16, 30, 35, 0, 0, 0, NULL, 1),
(17, 30, 13, 1, 0, 0, NULL, 1),
(18, 30, 77, 0, 0, 0, '', 0),
(19, 30, 11, 0, 0, 0, NULL, 0),
(23, 31, 11, 1, 0, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_property_keys`
--

CREATE TABLE IF NOT EXISTS `product_property_keys` (
  `property_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_property_id` int(10) unsigned DEFAULT NULL,
  `property` varchar(255) NOT NULL,
  `property_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-Predefined, 2-Custom',
  `value_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-Number, 2-Text, 3-Color',
  `values_options_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-Checkbox,2-Range, 3-Color',
  `unit_id` smallint(4) unsigned DEFAULT NULL,
  `is_general` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`property_id`),
  KEY `unit_id` (`unit_id`),
  KEY `parent_property_id` (`parent_property_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=84 ;

--
-- Dumping data for table `product_property_keys`
--

INSERT INTO `product_property_keys` (`property_id`, `parent_property_id`, `property`, `property_type`, `value_type`, `values_options_type`, `unit_id`, `is_general`, `is_deleted`) VALUES
(1, NULL, 'RAM', 1, 1, 1, NULL, 0, 0),
(2, NULL, 'NETWORK TYPE', 1, 2, 1, NULL, 0, 0),
(3, NULL, 'SCREEN SIZE', 1, 1, 1, NULL, 0, 0),
(4, NULL, 'TYPE', 1, 2, 1, NULL, 0, 0),
(5, NULL, 'BATTERY CAPACITY', 1, 1, 1, NULL, 0, 0),
(6, NULL, 'OPERATING SYSTEM', 1, 2, 1, NULL, 0, 0),
(7, NULL, 'SECONDARY CAMERA', 1, 1, 1, NULL, 0, 0),
(8, NULL, 'INTERNET FEATURES', 1, 2, 1, NULL, 0, 0),
(9, NULL, 'FEATURES', 1, 2, 1, NULL, 0, 0),
(10, NULL, 'RESOLUTION TYPE', 1, 2, 1, NULL, 0, 0),
(11, NULL, 'AVAILABILITY', 1, 2, 1, NULL, 0, 0),
(12, NULL, 'OPERATING SYSTEM VERSION NAME', 1, 2, 1, NULL, 0, 0),
(13, NULL, 'SIM TYPE', 1, 2, 1, NULL, 0, 0),
(14, NULL, 'CLOCK SPEED', 1, 1, 1, NULL, 0, 0),
(15, NULL, 'PRIMARY CAMERA', 1, 1, 1, NULL, 0, 0),
(16, NULL, 'NUMBER OF CORES', 1, 2, 1, NULL, 0, 0),
(17, NULL, 'PROCESSOR', 1, 2, 1, NULL, 0, 0),
(18, NULL, 'SYSTEM MEMORY', 1, 1, 1, NULL, 0, 0),
(19, NULL, 'PROCESSOR BRAND', 1, 2, 1, NULL, 0, 0),
(20, NULL, 'TOUCH SCREEN', 1, 2, 1, NULL, 0, 0),
(21, NULL, 'DEDICATED GRAPHICS MEMORY', 2, 1, 1, NULL, 0, 0),
(22, NULL, 'LIFESTYLE', 1, 2, 1, NULL, 0, 0),
(23, NULL, 'HARD DISK CAPACITY', 1, 1, 1, NULL, 0, 0),
(24, NULL, 'STORAGE TYPE', 1, 2, 1, NULL, 0, 0),
(25, NULL, 'SSD CAPACITY', 1, 1, 1, NULL, 0, 0),
(26, NULL, 'GRAPHICS MEMORY TYPE', 1, 2, 1, NULL, 0, 0),
(27, NULL, 'WATER RESISTANT', 1, 2, 1, NULL, 0, 0),
(28, NULL, 'CALL FUNCTION', 1, 2, 1, NULL, 0, 0),
(29, NULL, 'DIAL SHAPE', 1, 2, 1, NULL, 0, 0),
(30, NULL, 'USAGE', 1, 2, 1, NULL, 0, 0),
(31, NULL, 'TOUCHSCREEN', 1, 2, 1, NULL, 0, 0),
(32, NULL, 'STRAP COLOR', 2, 3, 1, NULL, 0, 0),
(33, NULL, 'COMPATIBLE OS', 2, 2, 1, NULL, 0, 0),
(34, NULL, 'COMPATIBLE MOBILES', 2, 2, 1, NULL, 0, 0),
(35, NULL, 'MATERIAL', 1, 2, 1, NULL, 0, 0),
(36, NULL, 'COLOR', 2, 3, 1, NULL, 0, 0),
(37, NULL, 'THEME', 1, 2, 1, NULL, 0, 0),
(38, NULL, 'HEADPHONE TYPE', 1, 2, 1, NULL, 0, 0),
(39, NULL, 'WIRED OR WIRELESS', 1, 2, 1, NULL, 0, 0),
(40, NULL, 'HEADPHONE DESIGN', 1, 2, 1, NULL, 0, 0),
(41, NULL, 'CAPACITY', 1, 1, 1, NULL, 0, 0),
(42, NULL, 'CLASS', 1, 2, 1, NULL, 0, 0),
(43, NULL, 'CABLE LENGTH', 1, 1, 1, NULL, 0, 0),
(44, NULL, 'IDEAL FOR', 1, 2, 1, NULL, 0, 0),
(45, NULL, 'CONNECTIVITY', 1, 2, 1, NULL, 0, 0),
(46, NULL, 'FORM FACTOR', 1, 2, 1, NULL, 0, 0),
(47, NULL, 'INTERFACE', 1, 2, 1, NULL, 0, 0),
(48, NULL, 'CASE MATERIAL', 1, 2, 1, NULL, 0, 0),
(49, NULL, 'LLUMINATED KEYS', 1, 2, 1, NULL, 0, 0),
(50, NULL, 'DISPLAY SIZE', 1, 1, 1, NULL, 0, 0),
(51, NULL, 'PRINTER TYPE', 1, 2, 1, NULL, 0, 0),
(52, NULL, 'PRINTER OUTPUT', 1, 2, 1, NULL, 0, 0),
(53, NULL, 'FUNCTIONS', 1, 2, 1, NULL, 0, 0),
(54, NULL, 'DISPLAY TYPE', 1, 2, 1, NULL, 0, 0),
(55, NULL, 'RESPONSE TIME', 1, 1, 1, NULL, 0, 0),
(56, NULL, 'BROADBAND COMPATIBILITY', 1, 2, 1, NULL, 0, 0),
(57, NULL, 'WIRELESS SPEED', 1, 1, 1, NULL, 0, 0),
(58, NULL, 'FREQUENCY', 1, 1, 1, NULL, 0, 0),
(59, NULL, 'NO. OF USB PORTS', 1, 1, 1, NULL, 0, 0),
(60, NULL, 'ANTENNAE', 1, 2, 1, NULL, 0, 0),
(61, NULL, 'TRANSFER SPEED', 1, 1, 1, NULL, 0, 0),
(62, NULL, 'VOICE SUPPORT', 1, 2, 1, NULL, 0, 0),
(63, NULL, 'MEMORY CAPACITY', 1, 1, 1, NULL, 0, 0),
(64, NULL, 'CONNECTIVITY FEATURES', 1, 2, 1, NULL, 0, 0),
(65, NULL, 'NUMBER OF HDMI PORTS', 1, 2, 1, NULL, 0, 0),
(66, NULL, 'NUMBER OF USB PORTS', 1, 2, 1, NULL, 0, 0),
(67, NULL, 'SCREEN TYPE', 1, 2, 1, NULL, 0, 0),
(68, NULL, 'MEGA PIXEL', 1, 1, 1, NULL, 0, 0),
(69, NULL, 'BATTERY TYPE', 1, 2, 1, NULL, 0, 0),
(70, NULL, 'SENSOR TYPE', 1, 2, 1, NULL, 0, 0),
(71, NULL, 'LENS TYPE', 1, 2, 1, NULL, 0, 0),
(72, NULL, 'FOCAL LENGTH', 1, 1, 1, NULL, 0, 0),
(73, NULL, 'COMPATIBLE CAMERAS', 2, 2, 1, NULL, 0, 0),
(74, NULL, 'SUITABLE FOR', 1, 2, 1, NULL, 0, 0),
(75, NULL, 'PRIME/ZOOM', 1, 2, 1, NULL, 0, 0),
(76, NULL, 'LENS MOUNT', 1, 2, 1, NULL, 0, 0),
(77, NULL, 'MAXIMUM APERTURE', 2, 1, 1, 1, 0, 0),
(78, NULL, 'WIRELESS', 1, 2, 1, NULL, 0, 0),
(79, NULL, 'MAXIMUM LOAD CAPACITY', 1, 1, 1, NULL, 0, 0),
(80, NULL, 'VOICE CALLING FACILITY', 1, 2, 1, NULL, 0, 0),
(81, NULL, 'INTERNAL STORAGE', 1, 1, 1, NULL, 0, 0),
(82, NULL, 'PROCESSOR CLOCK SPEED', 1, 1, 1, NULL, 0, 0),
(83, NULL, 'Avaliablity', 1, 2, 1, NULL, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_property_key_values`
--

CREATE TABLE IF NOT EXISTS `product_property_key_values` (
  `value_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `property_id` int(10) unsigned NOT NULL,
  `key_value` varchar(255) NOT NULL,
  `unit_id` smallint(4) unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `property_id_2` (`property_id`,`key_value`,`unit_id`),
  KEY `unit_id` (`unit_id`),
  KEY `property_id` (`property_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=827 ;

--
-- Dumping data for table `product_property_key_values`
--

INSERT INTO `product_property_key_values` (`value_id`, `property_id`, `key_value`, `unit_id`, `is_deleted`) VALUES
(1, 1, '1', 1, 0),
(2, 1, '2', 1, 0),
(3, 1, '3', 1, 0),
(4, 1, '4', 1, 0),
(5, 1, '256', 2, 0),
(6, 1, '512', 2, 0),
(7, 2, '2G', NULL, 0),
(8, 2, '3G', NULL, 0),
(9, 2, '4G', NULL, 0),
(10, 2, '4G LTE', NULL, 0),
(11, 2, '4G VOLTE', NULL, 0),
(12, 2, 'CDMA', NULL, 0),
(13, 2, 'NA', NULL, 0),
(14, 3, '3', 3, 0),
(15, 3, '3.5', 3, 0),
(16, 3, '4', 3, 0),
(17, 3, '4.5', 3, 0),
(18, 3, '5', 3, 0),
(19, 3, '5.5', 3, 0),
(20, 4, 'Smartphones', NULL, 0),
(21, 4, 'Feature Phones', NULL, 0),
(22, 5, '100', 4, 0),
(23, 5, '1000', 4, 0),
(24, 5, '1001', 4, 0),
(25, 5, '1999', 4, 0),
(26, 5, '2000', 4, 0),
(27, 5, '2999', 4, 0),
(28, 5, '2001', 4, 0),
(29, 5, '3000', 4, 0),
(30, 5, '3999', 4, 0),
(31, 5, '4000', 4, 0),
(32, 6, 'Android', NULL, 0),
(33, 6, 'Bada', NULL, 0),
(34, 6, 'Blackberry OS', NULL, 0),
(35, 6, 'Brew', NULL, 0),
(36, 6, 'Firefox', NULL, 0),
(37, 6, 'iOS', NULL, 0),
(38, 6, 'Linux', NULL, 0),
(39, 6, 'NA', NULL, 0),
(40, 6, 'Nokia Asha', NULL, 0),
(41, 6, 'Nokia OS', NULL, 0),
(42, 6, 'Nokia X Software', NULL, 0),
(43, 6, 'Propreitory', NULL, 0),
(44, 6, 'Sailfish', NULL, 0),
(45, 6, 'Symbian', NULL, 0),
(46, 6, 'Tizen', NULL, 0),
(47, 6, 'Windows', NULL, 0),
(48, 7, '0', 5, 0),
(49, 7, '1.9', 5, 0),
(50, 7, '2', 5, 0),
(51, 7, '2.9', 5, 0),
(52, 7, '3', 5, 0),
(53, 7, '4.9', 5, 0),
(54, 7, '5', 5, 0),
(55, 7, '7.9', 5, 0),
(56, 7, '8', 5, 0),
(57, 8, '3G', NULL, 0),
(58, 8, '4G', NULL, 0),
(59, 8, 'EDGE', NULL, 0),
(60, 8, 'GPRS', NULL, 0),
(61, 8, 'WAP', NULL, 0),
(62, 8, 'Wi-fi', NULL, 0),
(63, 9, 'FM Player', NULL, 0),
(64, 9, 'HD Recording', NULL, 0),
(65, 9, 'Music Player', NULL, 0),
(66, 9, 'NFC', NULL, 0),
(67, 9, 'Bluetooth', NULL, 0),
(68, 9, ' USB', NULL, 0),
(69, 10, 'DVGA', NULL, 0),
(70, 10, 'Full HD', NULL, 0),
(71, 10, 'FWVGA', NULL, 0),
(72, 10, 'HD', NULL, 0),
(73, 10, 'HQVGA', NULL, 0),
(74, 10, 'HVGA', NULL, 0),
(75, 10, 'nHD', NULL, 0),
(76, 10, 'Others', NULL, 0),
(77, 10, 'Quad HD', NULL, 0),
(78, 10, 'quarter HD', NULL, 0),
(79, 10, 'Quarter QVGA', NULL, 0),
(80, 10, 'QVGA', NULL, 0),
(81, 10, 'Retina Display', NULL, 0),
(82, 10, 'SVGA', NULL, 0),
(83, 10, 'UHD 4K', NULL, 0),
(84, 10, 'VGA', NULL, 0),
(85, 10, 'WQHD', NULL, 0),
(86, 10, 'WQVGA', NULL, 0),
(87, 10, 'WSVGA', NULL, 0),
(88, 10, 'WVGA', NULL, 0),
(89, 11, 'Exclude Out of Stock', NULL, 0),
(90, 12, 'Anna', NULL, 0),
(91, 12, 'Belle', NULL, 0),
(92, 12, 'Donut', NULL, 0),
(93, 12, 'Eclair', NULL, 0),
(94, 12, 'Froyo', NULL, 0),
(95, 12, 'Gingerbread', NULL, 0),
(96, 12, 'Ice Cream Sandwich', NULL, 0),
(97, 12, 'Jelly Bean', NULL, 0),
(98, 12, 'KitKat', NULL, 0),
(99, 12, 'Lollipop', NULL, 0),
(100, 12, 'Marshmallow', NULL, 0),
(101, 12, 'NA', NULL, 0),
(102, 12, 'Series 30', NULL, 0),
(103, 12, 'Series 40', NULL, 0),
(104, 12, 'Series 60', NULL, 0),
(105, 13, 'Dual Sim', NULL, 0),
(106, 13, 'Four Sim', NULL, 0),
(107, 13, 'Single Sim', NULL, 0),
(108, 13, 'Triple Sim', NULL, 0),
(109, 14, '1', 6, 0),
(110, 14, '1.5', 6, 0),
(111, 14, '1.9', 6, 0),
(112, 14, '2', 6, 0),
(113, 14, '2.5', 6, 0),
(114, 14, '900', 1, 0),
(115, 15, '2', 5, 0),
(116, 15, '2.9', 5, 0),
(117, 15, '3', 5, 0),
(118, 15, '4.9', 5, 0),
(119, 15, '5', 5, 0),
(120, 15, '7.9', 5, 0),
(121, 15, '8', 5, 0),
(122, 16, 'Dual Core', NULL, 0),
(123, 16, 'Hexa Core', NULL, 0),
(124, 16, 'Octa Core', NULL, 0),
(125, 16, 'Quad Core', NULL, 0),
(126, 16, 'Single Core', NULL, 0),
(127, 3, '12', 3, 0),
(128, 3, '12.9', 3, 0),
(129, 3, '13', 3, 0),
(130, 3, '13.9', 3, 0),
(131, 3, '14', 3, 0),
(132, 3, '14.9', 3, 0),
(133, 3, '15', 3, 0),
(134, 3, '15.9', 3, 0),
(135, 3, '16', 3, 0),
(136, 3, '17.9', 3, 0),
(137, 3, '18', 3, 0),
(138, 3, '20', 3, 0),
(139, 4, 'Notebook', NULL, 0),
(140, 4, 'Hybrid Laptop', NULL, 0),
(141, 4, 'Ultrabook', NULL, 0),
(142, 4, 'Netbook', NULL, 0),
(143, 4, 'Chromebook', NULL, 0),
(144, 6, 'Windows 8.1', NULL, 0),
(145, 6, 'Windows 8', NULL, 0),
(146, 6, 'Windows 10', NULL, 0),
(147, 6, 'Ubuntu', NULL, 0),
(148, 6, 'Mac OS', NULL, 0),
(149, 6, 'Linux/Ubuntu', NULL, 0),
(150, 6, 'DOS', NULL, 0),
(151, 6, 'Chrome', NULL, 0),
(152, 6, 'Windows 7', NULL, 0),
(153, 6, 'Windows 10 Home', NULL, 0),
(154, 6, 'Free DOS', NULL, 0),
(155, 11, 'Include Out of Stock', NULL, 0),
(156, 17, 'APU Dual Core E1', NULL, 0),
(157, 17, 'APU Quad Core A10', NULL, 0),
(158, 17, 'APU Quad Core A6', NULL, 0),
(159, 17, 'APU Quad Core A8', NULL, 0),
(160, 17, 'APU Quad Core E2', NULL, 0),
(161, 17, 'Atom (1st Gen)', NULL, 0),
(162, 17, 'Celeron Dual Core', NULL, 0),
(163, 17, 'Celeron Dual Core (3rd Gen)', NULL, 0),
(164, 17, 'Celeron Quad Core', NULL, 0),
(165, 17, 'Core i3 (5th Gen)', NULL, 0),
(166, 17, 'Core i5 (4th Gen)', NULL, 0),
(167, 17, 'Core i5 (5th Gen)', NULL, 0),
(168, 17, 'Core i7', NULL, 0),
(169, 17, 'Intel Atom', NULL, 0),
(170, 17, 'Intel Atom Baytrail Quad Core', NULL, 0),
(171, 17, 'Intel Atom Quad Core', NULL, 0),
(172, 17, 'Intel Core i3', NULL, 0),
(173, 17, 'Intel Core i3 (4th Gen)', NULL, 0),
(174, 17, 'Intel Core i3 (5th Gen)', NULL, 0),
(175, 17, 'Intel Core i3 (6th Gen)', NULL, 0),
(176, 17, 'Intel Core i5', NULL, 0),
(177, 17, 'Intel Core i5 (4th Gen)', NULL, 0),
(178, 17, 'Intel Core i5 (5th Gen)', NULL, 0),
(179, 17, 'Intel Core i5 (6th Gen)', NULL, 0),
(180, 17, 'Intel Core i5 (7th Gen)', NULL, 0),
(181, 17, 'Intel Core M', NULL, 0),
(182, 17, 'Intel Core M (5th Gen)', NULL, 0),
(183, 17, 'Intel Core M (6th Gen)', NULL, 0),
(184, 17, 'Intel Dual Core', NULL, 0),
(185, 17, 'Intel Dual Core (6th Gen)', NULL, 0),
(186, 17, 'Others', NULL, 0),
(187, 17, 'Pentium Dual Core', NULL, 0),
(188, 17, 'Pentium Quad Core', NULL, 0),
(189, 17, 'APU Dual Core', NULL, 0),
(190, 17, 'APU Dual Core A4', NULL, 0),
(191, 17, 'APU Quad Core A4', NULL, 0),
(192, 17, 'Core 2 Duo', NULL, 0),
(193, 17, 'Core i3 (1st Gen)', NULL, 0),
(194, 17, 'Core i3 (2nd Gen)', NULL, 0),
(195, 17, 'Core i3 (3rd Gen)', NULL, 0),
(196, 17, 'Core i5 (3rd Gen)', NULL, 0),
(197, 17, 'Intel Core i7 (6th Gen)', NULL, 0),
(198, 17, 'Intel Dual Core (5th Gen)', NULL, 0),
(199, 18, '1', 1, 0),
(200, 18, '2', 1, 0),
(201, 18, '4', 1, 0),
(202, 18, '6', 1, 0),
(203, 18, '8', 1, 0),
(204, 18, '12', 1, 0),
(205, 18, '16', 1, 0),
(206, 18, '24', 1, 0),
(207, 18, '32', 1, 0),
(208, 19, 'Intel', NULL, 0),
(209, 19, 'AMD', NULL, 0),
(210, 19, 'NVIDIA', NULL, 0),
(211, 19, 'ARM', NULL, 0),
(212, 20, 'Yes', NULL, 0),
(213, 20, 'No', NULL, 0),
(214, 22, 'Everyday Use', NULL, 0),
(215, 22, 'Entertainment', NULL, 0),
(216, 22, 'Processing & Multitasking', NULL, 0),
(217, 22, 'Travel & Business', NULL, 0),
(218, 22, 'Gaming', NULL, 0),
(219, 22, 'Performance', NULL, 0),
(220, 23, '1', 1, 0),
(221, 23, '2', 1, 0),
(222, 23, '1.5', 1, 0),
(223, 23, '750', 1, 0),
(224, 23, '512', 1, 0),
(225, 23, '500', 1, 0),
(226, 23, '256', 1, 0),
(227, 23, '160', 1, 0),
(228, 23, '32', 1, 0),
(229, 23, '640', 1, 0),
(230, 23, '320', 1, 0),
(231, 23, '128', 1, 0),
(232, 23, '16', 1, 0),
(233, 23, '120', 1, 0),
(234, 23, '60', 1, 0),
(235, 23, '64', 1, 0),
(236, 23, '4', 1, 0),
(237, 24, 'HDD', NULL, 0),
(238, 24, 'SSD', NULL, 0),
(239, 25, '8', 1, 0),
(240, 25, '16', 1, 0),
(241, 25, '20', 1, 0),
(242, 25, '24', 1, 0),
(243, 25, '32', 1, 0),
(244, 25, '64', 1, 0),
(245, 25, '120', 1, 0),
(246, 25, '128', 1, 0),
(247, 25, '256', 1, 0),
(248, 25, '512', 1, 0),
(249, 26, 'DDR3', NULL, 0),
(250, 26, 'DDR5', NULL, 0),
(251, 26, 'GDDR3', NULL, 0),
(252, 26, 'GDDR5', NULL, 0),
(253, 26, 'NA', NULL, 0),
(254, 27, 'Water Resistant', NULL, 0),
(255, 28, 'With Call Function', NULL, 0),
(256, 28, 'Without Call Function', NULL, 0),
(257, 29, 'Circle', NULL, 0),
(258, 29, 'Contemporary', NULL, 0),
(259, 29, 'Curved', NULL, 0),
(260, 29, 'Oval', NULL, 0),
(261, 29, 'Rectangle', NULL, 0),
(262, 29, 'Square', NULL, 0),
(263, 29, 'Triangular', NULL, 0),
(264, 30, 'Fitness & Outdoor', NULL, 0),
(265, 30, 'Health & Medical', NULL, 0),
(266, 30, 'Notifier', NULL, 0),
(267, 30, 'Safety & Security', NULL, 0),
(268, 30, 'Watchphone', NULL, 0),
(269, 31, 'Touchscreen', NULL, 0),
(270, 35, 'Artificial Leather', NULL, 0),
(271, 35, 'Cloth', NULL, 0),
(272, 35, 'Leather', NULL, 0),
(273, 35, 'Metal', NULL, 0),
(274, 35, 'Plastic', NULL, 0),
(275, 35, 'Polyurethane', NULL, 0),
(276, 35, 'Rubber', NULL, 0),
(277, 35, 'Silicon', NULL, 0),
(278, 35, 'Wood', NULL, 0),
(279, 37, 'Automobiles', NULL, 0),
(280, 37, 'Comics & Cartoons', NULL, 0),
(281, 37, 'Famous Personalities', NULL, 0),
(282, 37, 'Festivals & Occasions', NULL, 0),
(283, 37, 'Graffiti & Illustrations', NULL, 0),
(284, 37, 'Movies & TV Series', NULL, 0),
(285, 37, 'Music', NULL, 0),
(286, 37, 'Nature', NULL, 0),
(287, 37, 'No Theme', NULL, 0),
(288, 37, 'Patterns & Ethnic', NULL, 0),
(289, 37, 'Signs & Symbols', NULL, 0),
(290, 37, 'Spiritual', NULL, 0),
(291, 37, 'Sports', NULL, 0),
(292, 37, 'Superheroes', NULL, 0),
(293, 37, 'Typography', NULL, 0),
(294, 37, 'Vintage', NULL, 0),
(295, 4, 'Shoulder Bag', NULL, 0),
(296, 4, 'Back Protector', NULL, 0),
(297, 4, 'Diamond Screen Guard', NULL, 0),
(298, 4, 'Front & Back Protector', NULL, 0),
(299, 4, 'Impossible Glass', NULL, 0),
(300, 4, 'Liquid Screen Guard', NULL, 0),
(301, 4, 'Matte Screen Guard', NULL, 0),
(302, 4, 'Mirror Screen Guard', NULL, 0),
(303, 4, 'Nano Liquid Screen Protector', NULL, 0),
(304, 4, 'Privacy Screen Guard', NULL, 0),
(305, 4, 'Screen Guard', NULL, 0),
(306, 4, 'Smart Screen Guard', NULL, 0),
(307, 4, 'Tempered Glass', NULL, 0),
(308, 9, 'Scratch Resistant', NULL, 0),
(309, 9, 'Anti Glare', NULL, 0),
(310, 9, 'Air-bubble Proof', NULL, 0),
(311, 9, 'Anti Fingerprint', NULL, 0),
(312, 9, 'UV Protection', NULL, 0),
(313, 9, 'Anti Bacterial', NULL, 0),
(314, 9, 'Washable', NULL, 0),
(315, 9, 'Anti Reflection', NULL, 0),
(316, 38, 'In the Ear', NULL, 0),
(317, 38, 'Over the Ear', NULL, 0),
(318, 38, 'On the Ear', NULL, 0),
(319, 39, 'Wired', NULL, 0),
(320, 39, 'Wireless', NULL, 0),
(321, 39, 'Wired & Wireless', NULL, 0),
(322, 40, 'Earbud', NULL, 0),
(323, 40, 'Over the Head', NULL, 0),
(324, 40, 'Canalphone', NULL, 0),
(325, 40, 'Behind the Neck', NULL, 0),
(326, 40, 'Ear Clip', NULL, 0),
(327, 4, 'Waist Bag', NULL, 0),
(328, 4, 'Extreme SDHC', NULL, 0),
(329, 4, 'Book Cover', NULL, 0),
(330, 41, '256', 1, 0),
(331, 41, '200', 1, 0),
(332, 41, '128', 1, 0),
(333, 41, '64', 1, 0),
(334, 41, '32', 1, 0),
(337, 41, '8', 2, 0),
(339, 41, '2', 1, 0),
(340, 41, '64', 2, 0),
(341, 41, '1', 1, 0),
(342, 42, 'Class 10', NULL, 0),
(343, 42, 'Class 2', NULL, 0),
(344, 42, 'Class 4', NULL, 0),
(345, 42, 'Class 6', NULL, 0),
(346, 42, 'UHS Class 1', NULL, 0),
(347, 42, 'UHS Class 3', NULL, 0),
(348, 4, 'Compact Flash', NULL, 0),
(349, 4, 'Memory Stick', NULL, 0),
(350, 4, 'MicroSD Card', NULL, 0),
(351, 4, 'MicroSDHC', NULL, 0),
(352, 4, 'MicroSDXC', NULL, 0),
(353, 4, 'MiniSD Card', NULL, 0),
(354, 4, 'MMC', NULL, 0),
(355, 4, 'SD Card', NULL, 0),
(356, 4, 'SDHC', NULL, 0),
(357, 4, 'SDXC', NULL, 0),
(358, 4, 'Ultra SDHC', NULL, 0),
(359, 4, 'Universal Flash Storage', NULL, 0),
(360, 4, 'Memory Stick Pro Duo Card', NULL, 0),
(361, 4, 'MMC Micro Card', NULL, 0),
(362, 4, 'Extreme HD Video', NULL, 0),
(363, 4, 'Sync & Charge Cable', NULL, 0),
(364, 4, 'Lightning Cable', NULL, 0),
(365, 4, 'AUX Cable', NULL, 0),
(366, 4, 'OTG Cable', NULL, 0),
(367, 4, 'USB C Type Cable', NULL, 0),
(368, 4, 'Headphone Splitter', NULL, 0),
(369, 4, 'Power Sharing Cable', NULL, 0),
(370, 43, '2', 1, 0),
(371, 43, '3.9', 1, 0),
(372, 43, '4', 1, 0),
(373, 43, '6', 1, 0),
(374, 41, '2000', 4, 0),
(375, 41, '2001', 4, 0),
(376, 41, '5000', 4, 0),
(377, 41, '5001', 4, 0),
(378, 41, '10000', 4, 0),
(379, 41, '10001', 4, 0),
(380, 41, '16000', 4, 0),
(381, 41, '0', 4, 0),
(382, 44, 'Baby Girls', NULL, 0),
(383, 44, 'Boys', NULL, 0),
(384, 44, 'Girls', NULL, 0),
(385, 44, 'Men', NULL, 0),
(386, 44, 'Women', NULL, 0),
(387, 30, 'Gaming & Entertainment', NULL, 0),
(388, 35, 'Aluminum', NULL, 0),
(389, 35, 'Brass', NULL, 0),
(390, 35, 'Copper', NULL, 0),
(391, 35, 'Genuine Leather', NULL, 0),
(392, 35, 'Leatherette', NULL, 0),
(393, 35, 'Nylon', NULL, 0),
(394, 35, 'Polypropylene', NULL, 0),
(395, 35, 'Silicone', NULL, 0),
(396, 35, 'Stainless Steel', NULL, 0),
(397, 35, 'Glass', NULL, 0),
(398, 35, 'Polyresin', NULL, 0),
(399, 35, 'Sponge', NULL, 0),
(400, 35, 'Steel', NULL, 0),
(401, 45, ' USB 3.0', NULL, 0),
(402, 45, 'USB 2.0', NULL, 0),
(403, 45, 'Gigabit Ethernet', NULL, 0),
(404, 45, 'Firewire 800', NULL, 0),
(405, 45, 'eSATA', NULL, 0),
(406, 45, 'Thunderbolt', NULL, 0),
(407, 45, 'Wireless', NULL, 0),
(408, 46, 'Portable', NULL, 0),
(409, 46, 'Desktop', NULL, 0),
(410, 46, 'Network', NULL, 0),
(412, 41, '1.5', 1, 0),
(414, 41, '3', 1, 0),
(415, 41, '4', 1, 0),
(416, 41, '5', 1, 0),
(417, 41, '6', 1, 0),
(418, 41, '8', 1, 0),
(419, 41, '10', 1, 0),
(420, 41, '12', 1, 0),
(421, 41, '16', 1, 0),
(422, 41, '20', 1, 0),
(423, 41, '0', 1, 0),
(424, 41, '120', 1, 0),
(425, 41, '250', 1, 0),
(426, 41, '500', 1, 0),
(427, 41, '240', 1, 0),
(428, 41, '512', 1, 0),
(429, 41, '750', 1, 0),
(430, 47, 'USB 2.0', NULL, 0),
(431, 47, 'USB 3.0', NULL, 0),
(432, 47, 'USB 3.1', NULL, 0),
(433, 48, 'Metal', NULL, 0),
(434, 48, 'Plastic', NULL, 0),
(435, 48, 'Rubber', NULL, 0),
(436, 48, 'Wood', NULL, 0),
(437, 4, 'Backpack', NULL, 0),
(438, 4, 'Laptop Backpack', NULL, 0),
(439, 4, 'Laptop Carry Case', NULL, 0),
(440, 4, 'Laptop Case', NULL, 0),
(441, 4, 'Laptop Hand-held Bag', NULL, 0),
(442, 4, 'Laptop Messenger Bag', NULL, 0),
(443, 4, 'Laptop Strolley Bag', NULL, 0),
(444, 4, 'Laptop Tote Bag', NULL, 0),
(445, 4, 'Messenger Bag', NULL, 0),
(446, 4, 'Sleeve/Slip Case', NULL, 0),
(447, 4, 'Sling Bag', NULL, 0),
(448, 4, 'Strolley', NULL, 0),
(449, 4, 'Trolley', NULL, 0),
(450, 4, 'Laptop Sleeve', NULL, 0),
(451, 47, 'USB', NULL, 0),
(452, 47, 'Bluetooth', NULL, 0),
(453, 47, 'PS/2', NULL, 0),
(454, 47, 'USB Receiver', NULL, 0),
(455, 47, 'Wired USB', NULL, 0),
(456, 47, 'Wireless', NULL, 0),
(457, 47, 'Internal', NULL, 0),
(458, 47, 'Bluetooth, Wireless', NULL, 0),
(459, 47, 'Virtual Laser', NULL, 0),
(460, 47, 'Wired', NULL, 0),
(461, 47, 'Virtual Laser, Bluetooth', NULL, 0),
(462, 47, 'Wireless, Bluetooth', NULL, 0),
(463, 47, 'Wired USB, PS2', NULL, 0),
(464, 47, 'Bluetooth, Wired USB', NULL, 0),
(465, 4, 'Tablet Keyboard', NULL, 0),
(466, 4, 'Laptop Keyboard', NULL, 0),
(467, 49, 'Illuminated Keys', NULL, 0),
(468, 50, '19', 3, 0),
(469, 4, '', NULL, 0),
(470, 4, '3D', NULL, 0),
(471, 4, '3D Ready', NULL, 0),
(472, 4, '4 Port USB Hub', NULL, 0),
(473, 4, 'A', NULL, 0),
(474, 4, 'ADSL & PHONE LINE SPLITTER', NULL, 0),
(475, 4, 'ADSL LINE SPLITTER', NULL, 0),
(476, 4, 'ADSL Splitter', NULL, 0),
(477, 4, 'B', NULL, 0),
(478, 4, 'Back Cover', NULL, 0),
(479, 4, 'Bluetooth', NULL, 0),
(480, 4, 'C', NULL, 0),
(481, 4, 'Cable Lock', NULL, 0),
(482, 4, 'Card Reader', NULL, 0),
(483, 4, 'Cases with Holder', NULL, 0),
(484, 4, 'Charging Station', NULL, 0),
(485, 4, 'Cigarette Lighter', NULL, 0),
(486, 4, 'Cleaner', NULL, 0),
(487, 4, 'COMPUTER LOCK', NULL, 0),
(488, 4, 'Cup Warmer', NULL, 0),
(489, 4, 'D', NULL, 0),
(490, 4, 'Delta Conversion', NULL, 0),
(491, 4, 'Satchel', NULL, 0),
(492, 4, 'Dlp', NULL, 0),
(493, 4, 'Hand Bag', NULL, 0),
(494, 4, 'Docking Station', NULL, 0),
(495, 4, 'E', NULL, 0),
(496, 4, 'Expansion Card', NULL, 0),
(497, 4, 'F', NULL, 0),
(498, 4, 'Flip Cover', NULL, 0),
(499, 4, 'Front & Back Case', NULL, 0),
(500, 4, 'Full HD', NULL, 0),
(501, 4, 'G', NULL, 0),
(502, 4, 'Grip Back Cover', NULL, 0),
(503, 4, 'H', NULL, 0),
(504, 4, 'HD', NULL, 0),
(505, 4, 'HD Ready', NULL, 0),
(506, 4, 'HDD Docking Station', NULL, 0),
(507, 4, 'HDMI Connector', NULL, 0),
(508, 4, 'Hub', NULL, 0),
(509, 4, 'K', NULL, 0),
(510, 4, 'Key Security Lock', NULL, 0),
(511, 4, 'Keyboard', NULL, 0),
(512, 4, 'L', NULL, 0),
(513, 4, 'Laptop Accessory', NULL, 0),
(514, 4, 'LED', NULL, 0),
(515, 4, 'Led Light', NULL, 0),
(516, 4, 'LED Projector', NULL, 0),
(517, 4, 'Line-interactive', NULL, 0),
(518, 4, 'N', NULL, 0),
(519, 4, 'numeric lock', NULL, 0),
(520, 4, 'O', NULL, 0),
(521, 4, 'Offline/Standby', NULL, 0),
(522, 4, 'Online/double-conversion', NULL, 0),
(523, 4, 'P', NULL, 0),
(524, 4, 'Pad Lock', NULL, 0),
(525, 4, 'Port Replicator', NULL, 0),
(526, 4, 'Pouch', NULL, 0),
(527, 4, 'PS3 DUAL', NULL, 0),
(528, 4, 'PS4 DUAL', NULL, 0),
(529, 4, 'S', NULL, 0),
(530, 4, 'Screen Magnifier', NULL, 0),
(531, 4, 'Security Cable', NULL, 0),
(532, 4, 'Security Lock', NULL, 0),
(533, 4, 'Security Tag', NULL, 0),
(534, 4, 'Shock Proof Case', NULL, 0),
(535, 4, 'Single Adapter', NULL, 0),
(536, 4, 'Sleeve', NULL, 0),
(537, 4, 'Sound Card', NULL, 0),
(538, 4, 'Strip', NULL, 0),
(539, 4, 'Suction Cup', NULL, 0),
(540, 4, 'SVGA', NULL, 0),
(541, 4, 'T', NULL, 0),
(542, 4, 'touch pad', NULL, 0),
(543, 4, 'U', NULL, 0),
(544, 4, 'UHD', NULL, 0),
(545, 4, 'Update Lens Firmware', NULL, 0),
(546, 4, 'USB 2.0', NULL, 0),
(547, 4, 'USB 3.0 SATA Hard Drive Dock', NULL, 0),
(548, 4, 'USB 3.0 Super Speed, 5Gbps , SATA 2.5 inch / 3.5 inch Hard Drive Docking Station', NULL, 0),
(549, 4, 'USB Air Freshener', NULL, 0),
(550, 4, 'USB Biometric Fingerprint Scanner', NULL, 0),
(551, 4, 'USB Cable', NULL, 0),
(552, 4, 'USB Charger', NULL, 0),
(553, 4, 'USB Fan', NULL, 0),
(554, 4, 'USB Flash Drive', NULL, 0),
(555, 4, 'USB Fridge', NULL, 0),
(556, 4, 'USB Hub', NULL, 0),
(557, 4, 'USB LAN Card', NULL, 0),
(558, 4, 'USB Mouse', NULL, 0),
(559, 4, 'USB Mug', NULL, 0),
(560, 4, 'USB PORT REPLICATOR', NULL, 0),
(561, 4, 'V', NULL, 0),
(562, 4, 'VGA', NULL, 0),
(563, 4, 'W', NULL, 0),
(564, 4, 'Wall Mount', NULL, 0),
(565, 4, 'Wallet Case Cover', NULL, 0),
(566, 4, 'Water Proof Case', NULL, 0),
(567, 4, 'Wireless', NULL, 0),
(568, 4, 'WUXGA', NULL, 0),
(569, 4, 'WXGA', NULL, 0),
(570, 4, 'X', NULL, 0),
(571, 4, 'XB-1 DUAL', NULL, 0),
(572, 4, 'XGA', NULL, 0),
(573, 4, 'Not available', NULL, 0),
(574, 4, 'Arm Band Case', NULL, 0),
(575, 4, 'Case', NULL, 0),
(576, 4, 'Bumper Case', NULL, 0),
(577, 4, 'Holster', NULL, 0),
(578, 4, 'Clapper Board', NULL, 0),
(579, 4, 'Shoulder Carry Bag', NULL, 0),
(580, 4, 'Mini', NULL, 0),
(581, 51, 'Laser', NULL, 0),
(582, 51, 'Inkjet', NULL, 0),
(583, 47, 'Ethernet', NULL, 0),
(584, 47, 'Parallel Port', NULL, 0),
(585, 47, 'Card Slot', NULL, 0),
(586, 50, '20', 3, 0),
(587, 50, '25', 3, 0),
(588, 50, '26', 3, 0),
(589, 50, '30', 3, 0),
(590, 50, '31', 3, 0),
(591, 52, 'Color', 3, 0),
(592, 52, 'Color, Monochrome', 3, 0),
(593, 52, 'Monochrome, Color', 3, 0),
(594, 52, 'Monochrome', NULL, 0),
(595, 53, 'Print', NULL, 0),
(596, 53, 'Scan', NULL, 0),
(597, 53, 'Copy', NULL, 0),
(598, 53, 'Fax', NULL, 0),
(599, 54, 'LED Backlit LCD', NULL, 0),
(600, 54, 'LED', NULL, 0),
(601, 54, 'LCD', NULL, 0),
(602, 54, 'LED Backlit', NULL, 0),
(603, 54, 'IPS LED', NULL, 0),
(604, 54, 'TFT', NULL, 0),
(605, 54, 'LED Backlight LCD Monitor', NULL, 0),
(606, 54, 'Backlight LED', NULL, 0),
(607, 54, 'LCD With Backlit LED', NULL, 0),
(608, 54, 'LED Backlit LCD (Non Gsync)', NULL, 0),
(609, 54, 'TN (Twisted Nematic) LED Backlit LCD', NULL, 0),
(610, 54, 'TFT LED', NULL, 0),
(611, 54, 'COL', NULL, 0),
(612, 54, 'Backlit Led', NULL, 0),
(613, 54, 'MONITOR', NULL, 0),
(614, 54, 'Lcd With Led Backlit', NULL, 0),
(615, 54, 'Lcd With Led Backlight', NULL, 0),
(616, 54, 'HD LED', NULL, 0),
(617, 55, '5', 1, 0),
(618, 55, '10', 1, 0),
(619, 55, '11', 1, 0),
(620, 55, '15', 1, 0),
(621, 55, '16', 1, 0),
(622, 9, 'Yes', NULL, 0),
(623, 9, 'No', NULL, 0),
(624, 4, 'Wireless Without modem', NULL, 0),
(625, 4, 'Wireless With Modem', NULL, 0),
(626, 4, 'Range Extenders/Repeaters', NULL, 0),
(627, 4, '3G Routers', NULL, 0),
(628, 4, 'Wired With Modem', NULL, 0),
(629, 4, 'Wired Without Modem', NULL, 0),
(630, 4, '4G Routers', NULL, 0),
(631, 56, 'Access Point Mode', NULL, 0),
(632, 56, 'ADSL (Telephone)', NULL, 0),
(633, 56, 'Both (Ethernet and ADSL)', NULL, 0),
(634, 56, 'Ethernet (Cable Broadband)', NULL, 0),
(635, 56, 'MiFi (Sim Based)', NULL, 0),
(636, 57, '0', 1, 0),
(637, 57, '150', 1, 0),
(638, 57, '300', 1, 0),
(639, 57, '450', 1, 0),
(640, 57, '600', 1, 0),
(641, 57, '750', 1, 0),
(642, 57, '1000', 1, 0),
(643, 58, '2', 6, 0),
(644, 58, '2.1', 6, 0),
(645, 58, '2.4', 6, 0),
(646, 58, '2.48', 6, 0),
(647, 58, '5', 6, 0),
(648, 58, '2.472', 6, 0),
(649, 58, '2.483', 6, 0),
(650, 58, '2.4835', 6, 0),
(651, 58, '2.4836', 6, 0),
(652, 58, '2.484', 6, 0),
(653, 58, '3', 6, 0),
(654, 58, '5.0', 6, 0),
(655, 58, '5.2', 6, 0),
(656, 58, '2.5', 6, 0),
(657, 58, '2488', 1, 0),
(658, 58, '5.1', 6, 0),
(659, 58, '5.8', 6, 0),
(660, 59, '1', 1, 0),
(661, 59, '2', 1, 0),
(662, 59, '3', 1, 0),
(663, 59, '4', 1, 0),
(664, 59, '5', 1, 0),
(665, 60, 'External', NULL, 0),
(666, 60, 'Internal', NULL, 0),
(667, 60, 'Internal & External', NULL, 0),
(668, 61, '3.1', 1, 0),
(669, 61, '7.2', 1, 0),
(670, 61, '14.4', 1, 0),
(671, 61, '21', 1, 0),
(672, 61, '21.6', 1, 0),
(673, 61, '150', 1, 0),
(674, 61, '3.6', 1, 0),
(675, 62, 'Call Support', NULL, 0),
(676, 62, 'No Call Support', NULL, 0),
(677, 63, '4', 1, 0),
(678, 63, '8', 1, 0),
(679, 63, '16', 1, 0),
(680, 63, '32', 1, 0),
(681, 63, '64', 1, 0),
(682, 63, '2000', 1, 0),
(683, 3, '24', 3, 0),
(684, 3, '25', 3, 0),
(685, 3, '31', 3, 0),
(686, 3, '32', 3, 0),
(687, 3, '39', 3, 0),
(688, 3, '43', 3, 0),
(689, 3, '48', 3, 0),
(690, 3, '50', 3, 0),
(691, 3, '55', 3, 0),
(692, 10, 'HD Ready', NULL, 0),
(693, 10, 'Ultra HD (4K)', NULL, 0),
(694, 10, 'WXGA', NULL, 0),
(695, 9, '3D TV', NULL, 0),
(696, 9, 'Curved TV', NULL, 0),
(697, 9, 'Smart TV', NULL, 0),
(698, 64, 'Bluetooth', NULL, 0),
(699, 64, 'Built-in Wi-Fi', NULL, 0),
(700, 64, 'Ethernet', NULL, 0),
(701, 64, 'MHL', NULL, 0),
(702, 64, 'Yes', NULL, 0),
(703, 65, 'Four', NULL, 0),
(704, 65, 'Three', NULL, 0),
(705, 65, 'Two', NULL, 0),
(706, 65, 'One', NULL, 0),
(707, 66, 'Four', NULL, 0),
(708, 66, 'Three', NULL, 0),
(709, 66, 'Two', NULL, 0),
(710, 66, 'One', NULL, 0),
(711, 67, 'LCD', NULL, 0),
(712, 67, 'LED', NULL, 0),
(713, 67, 'OLED', NULL, 0),
(714, 68, '10', 5, 0),
(715, 68, '13.99', 5, 0),
(716, 68, '16', 5, 0),
(717, 68, '17.99', 5, 0),
(718, 68, '18', 5, 0),
(719, 68, '23.99', 5, 0),
(720, 68, '24', 5, 0),
(721, 68, '6', 5, 0),
(722, 68, '7.9', 5, 0),
(723, 68, '8', 5, 0),
(724, 68, '9.9', 5, 0),
(725, 68, '14', 5, 0),
(726, 68, '15.99', 5, 0),
(727, 4, 'DSLR', NULL, 0),
(728, 4, 'Advanced Point & Shoot', NULL, 0),
(729, 4, 'Instant Camera', NULL, 0),
(730, 4, 'Camcorder', NULL, 0),
(731, 4, 'Point & Shoot', NULL, 0),
(732, 4, 'Mirrorless', NULL, 0),
(733, 69, 'AA Alkaline Battery', NULL, 0),
(734, 69, 'AAA Alkaline Battery', NULL, 0),
(735, 69, 'Lithium Battery', NULL, 0),
(736, 69, 'AA Battery', NULL, 0),
(737, 69, 'AA Rechargeable Battery', NULL, 0),
(738, 69, 'AAA Rechargeable Battery', NULL, 0),
(739, 70, 'CCD', NULL, 0),
(740, 70, 'CMOS', NULL, 0),
(741, 70, 'BSI CMOS', NULL, 0),
(742, 70, 'MOS', NULL, 0),
(743, 70, 'NMOS', NULL, 0),
(744, 71, 'Fisheye', NULL, 0),
(745, 71, 'Macro', NULL, 0),
(746, 71, 'Standard', NULL, 0),
(747, 71, 'Telephoto', NULL, 0),
(748, 71, 'Wide-angle', NULL, 0),
(749, 72, '16', 1, 0),
(750, 72, '28', 1, 0),
(751, 72, '55', 1, 0),
(752, 72, '250', 1, 0),
(753, 74, 'Art', NULL, 0),
(754, 74, 'Journalism', NULL, 0),
(755, 74, 'Landscape', NULL, 0),
(756, 74, 'Portrait', NULL, 0),
(757, 74, 'Sports & Action', NULL, 0),
(758, 74, 'Travel & Adventure', NULL, 0),
(759, 74, 'Wedding & Events', NULL, 0),
(760, 74, 'Wildlife', NULL, 0),
(761, 75, 'Prime', NULL, 0),
(762, 75, 'Zoom', NULL, 0),
(763, 76, 'A-mount', NULL, 0),
(764, 76, 'AF', NULL, 0),
(765, 76, 'AF-S', NULL, 0),
(766, 76, 'Canon Mount', NULL, 0),
(767, 76, 'E-Mount', NULL, 0),
(768, 76, 'EF', NULL, 0),
(769, 76, 'EF-S', NULL, 0),
(770, 76, 'Mirrorless', NULL, 0),
(771, 76, 'Sony Mount', NULL, 0),
(772, 76, 'c-mount', NULL, 0),
(773, 76, 'E', NULL, 0),
(774, 78, 'No', NULL, 0),
(775, 79, '500', 1, 0),
(776, 79, '1000', 1, 0),
(777, 79, '2000', 1, 0),
(778, 4, 'Extreme Pro SDHC', NULL, 0),
(779, 4, 'CompactFlash II Card', NULL, 0),
(780, 4, 'XD Picture Card', NULL, 0),
(781, 74, 'Advance Point & Shoot Camera', NULL, 0),
(782, 74, 'CSC/MILC', NULL, 0),
(783, 74, 'DSLR/SLR Camera', NULL, 0),
(784, 74, 'Film Camera', NULL, 0),
(785, 74, 'Lens', NULL, 0),
(786, 74, 'Medium Format Camera', NULL, 0),
(787, 74, 'Point & Shoot Camera', NULL, 0),
(788, 74, 'Tripod', NULL, 0),
(789, 74, 'Video Camera', NULL, 0),
(790, 6, 'Others', NULL, 0),
(791, 45, 'Wi-Fi+3G', NULL, 0),
(792, 45, 'Wi-Fi+4G', NULL, 0),
(793, 45, '2G', NULL, 0),
(794, 45, '3G', NULL, 0),
(795, 45, '4G', NULL, 0),
(796, 45, 'EDGE', NULL, 0),
(797, 45, 'Three G via Dongle', NULL, 0),
(798, 45, 'Wi-Fi Only', NULL, 0),
(799, 45, 'Wi-Fi+2G', NULL, 0),
(800, 50, '7', 3, 0),
(801, 50, '8', 3, 0),
(802, 50, '9', 3, 0),
(803, 50, '10', 3, 0),
(804, 1, '1.5', 1, 0),
(805, 80, 'Yes', NULL, 0),
(806, 80, 'No', NULL, 0),
(807, 5, '6000', 4, 0),
(808, 5, '8000', 4, 0),
(809, 54, 'HD', NULL, 0),
(810, 54, 'Full HD', NULL, 0),
(811, 54, 'Quad HD', NULL, 0),
(812, 54, 'SD', NULL, 0),
(813, 81, '4', 1, 0),
(814, 81, '8', 1, 0),
(815, 81, '16', 1, 0),
(816, 81, '32', 1, 0),
(817, 81, '64', 1, 0),
(818, 81, '128', 1, 0),
(819, 82, '1.2', 6, 0),
(820, 82, '1.4', 6, 0),
(821, 82, '1.5', 6, 0),
(822, 82, '1.7', 6, 0),
(823, 82, '1', 6, 0),
(824, 82, '1.1', 6, 0),
(825, 82, '1.8', 6, 0),
(826, 83, 'Exclude of Stock', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_property_values`
--

CREATE TABLE IF NOT EXISTS `product_property_values` (
  `ppv_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pp_id` int(10) unsigned NOT NULL,
  `value_id` bigint(20) unsigned NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`ppv_id`),
  KEY `pp_id` (`pp_id`),
  KEY `value_id` (`value_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `product_property_values`
--

INSERT INTO `product_property_values` (`ppv_id`, `pp_id`, `value_id`, `is_deleted`) VALUES
(1, 3, 6, 0),
(2, 3, 2, 0),
(3, 3, 1, 0),
(4, 1, 5, 0),
(5, 1, 11, 0),
(6, 4, 5, 0),
(7, 5, 2, 0),
(8, 9, 5, 0),
(9, 10, 2, 0),
(10, 5, 1, 0),
(11, 5, 9, 0),
(12, 7, 22, 0),
(13, 8, 32, 0),
(14, 9, 89, 0),
(15, 9, 155, 0),
(16, 10, 89, 0),
(17, 12, 661, 0),
(18, 13, 712, 0),
(19, 14, 105, 0),
(20, 15, 273, 0),
(21, 15, 277, 0),
(22, 16, 396, 0),
(23, 17, 400, 0),
(24, 19, 155, 0),
(25, 23, 89, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_redirect_lookups`
--

CREATE TABLE IF NOT EXISTS `product_redirect_lookups` (
  `redirect_lookup_id` smallint(2) NOT NULL AUTO_INCREMENT,
  `redirect_desc` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`redirect_lookup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `product_redirect_lookups`
--

INSERT INTO `product_redirect_lookups` (`redirect_lookup_id`, `redirect_desc`, `status`) VALUES
(1, 'No redirect (404)', 1),
(2, 'Redirected permanently (301)', 1),
(3, 'Redirected temporarily (302)', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_stock_status_lookups`
--

CREATE TABLE IF NOT EXISTS `product_stock_status_lookups` (
  `stock_status_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `orderable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  `list_to_users` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  PRIMARY KEY (`stock_status_id`),
  UNIQUE KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `product_stock_status_lookups`
--

INSERT INTO `product_stock_status_lookups` (`stock_status_id`, `status`, `description`, `orderable`, `list_to_users`) VALUES
(1, 'Forthcoming', 'The product is not released yet. You can however ‘book’ an order for this. Shipping happens only after the product launch', 1, 1),
(2, 'In Stock', 'Products ‘In Stock’ are readily available with seller', 1, 1),
(3, 'Out of Stock', 'Currently, the item is not available for sale. Use the ''Notify Me'' feature to know once it is available for purchase', 0, 1),
(4, 'Back In Stock Soon', 'The item is popular and is sold out. You can however ''book'' an order for the product and it will be shipped according to the timelines mentioned by the Seller.', 1, 1),
(5, 'Permanently Discontinued', 'This product is no longer available because it is obsolete and/or its production has been discontinued.', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_tag`
--

CREATE TABLE IF NOT EXISTS `product_tag` (
  `tag_id` bigint(20) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  UNIQUE KEY `tag_id` (`tag_id`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_tag`
--

INSERT INTO `product_tag` (`tag_id`, `product_id`, `is_deleted`) VALUES
(1, 18, 0),
(1, 19, 1),
(3, 24, 0),
(3, 25, 0),
(3, 26, 0),
(3, 27, 0),
(3, 28, 0),
(3, 29, 0),
(3, 30, 0),
(6, 18, 0),
(7, 19, 0),
(7, 24, 0),
(7, 25, 0),
(7, 26, 0),
(7, 27, 0),
(7, 28, 0),
(7, 29, 0),
(7, 30, 0),
(7, 31, 0),
(8, 19, 1),
(9, 19, 1),
(9, 31, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_visiblity_lookups`
--

CREATE TABLE IF NOT EXISTS `product_visiblity_lookups` (
  `visiblity_id` smallint(2) NOT NULL AUTO_INCREMENT,
  `visiblity_desc` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`visiblity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `product_visiblity_lookups`
--

INSERT INTO `product_visiblity_lookups` (`visiblity_id`, `visiblity_desc`, `status`) VALUES
(1, 'Everywhere', 1),
(2, 'Catalog only', 1),
(3, 'Search only', 1),
(4, 'Nowhere', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_weight_slab_rates`
--

CREATE TABLE IF NOT EXISTS `product_weight_slab_rates` (
  `weight_slab_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `weight_slab_title` varchar(50) DEFAULT NULL,
  `for_each_grams` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT 'Kgs',
  `min_grams` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT 'Kgs',
  `max_grams` decimal(10,3) unsigned DEFAULT NULL COMMENT 'Kgs',
  `status` tinyint(1) unsigned NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`weight_slab_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `product_weight_slab_rates`
--

INSERT INTO `product_weight_slab_rates` (`weight_slab_id`, `weight_slab_title`, `for_each_grams`, `min_grams`, `max_grams`, `status`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, '0.0 Kg - 0.5 Kg', '0.500', '0.000', '0.500', 1, '2017-05-03 16:52:07', '2017-05-03 11:22:07', 0),
(2, '0.5 Kg - 5.0 Kg ', '0.500', '0.500', '5.000', 1, '2017-05-03 16:52:07', '2017-05-03 11:30:54', 0),
(3, '> 5.0 Kg', '1.000', '5.000', NULL, 1, '2017-05-03 16:52:07', '2017-05-03 11:31:42', 0);

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE IF NOT EXISTS `rating` (
  `rating_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_type_id` tinyint(2) unsigned NOT NULL,
  `relative_post_id` bigint(20) unsigned NOT NULL,
  `reviews_count` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rating_1` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_2` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_3` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_4` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_5` int(10) unsigned NOT NULL DEFAULT '0',
  `tot_rating` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rating_count` int(10) unsigned NOT NULL DEFAULT '0',
  `avg_rating` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rating_id`),
  KEY `relative_post_id` (`relative_post_id`),
  KEY `post_type_id` (`post_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `rating`
--

INSERT INTO `rating` (`rating_id`, `post_type_id`, `relative_post_id`, `reviews_count`, `rating_1`, `rating_2`, `rating_3`, `rating_4`, `rating_5`, `tot_rating`, `rating_count`, `avg_rating`) VALUES
(1, 3, 2, 0, 3, 4, 5, 4, 5, 275, 51, 4),
(2, 3, 6, 0, 0, 0, 1, 0, 0, 3, 1, 3),
(3, 3, 1, 0, 0, 0, 0, 1, 0, 4, 1, 4),
(4, 3, 0, 0, 0, 0, 0, 3, 0, 12, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `rating_status_lookup`
--

CREATE TABLE IF NOT EXISTS `rating_status_lookup` (
  `status_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `rating_status_lookup`
--

INSERT INTO `rating_status_lookup` (`status_id`, `status`) VALUES
(1, 'Published'),
(2, 'Unpublished');

-- --------------------------------------------------------

--
-- Table structure for table `referral_earnings`
--

CREATE TABLE IF NOT EXISTS `referral_earnings` (
  `ref_id` bigint(20) NOT NULL,
  `from_account_id` bigint(20) NOT NULL,
  `to_account_id` bigint(20) NOT NULL,
  `subscrib_id` bigint(20) NOT NULL,
  `subscrib_topup_id` bigint(20) NOT NULL DEFAULT '0',
  `package_id` int(10) unsigned NOT NULL DEFAULT '0',
  `transaction_id` varchar(50) NOT NULL,
  `amount` double DEFAULT NULL,
  `qv` double NOT NULL,
  `c_rate` double DEFAULT NULL,
  `fund_source_type` tinyint(1) unsigned DEFAULT NULL COMMENT '1-Add Fund,2-Payment Gateway, 3-Support Center, 4-Auto Level',
  `amount_without_tax` double DEFAULT NULL,
  `service_tax` double DEFAULT NULL,
  `service_tax_details` text,
  `service_tax_per` text,
  `service_tax_invoice_path` varchar(255) DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `nth_sale` int(11) NOT NULL,
  `from_user_level` int(11) NOT NULL,
  `passup` smallint(6) NOT NULL DEFAULT '0' COMMENT '1-Direct Referral,2-Upline',
  `payout_type` int(2) NOT NULL DEFAULT '0',
  `created_date` datetime NOT NULL,
  `confirmed_date` datetime NOT NULL,
  `earnings_timeout` datetime DEFAULT NULL,
  `processing_data` text,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` smallint(2) NOT NULL DEFAULT '0' COMMENT '0-Pending, 1-Confirmed,4-Lapsed,5-Waiting',
  `is_deleted` smallint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `report_abuse`
--

CREATE TABLE IF NOT EXISTS `report_abuse` (
  `abuse_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `account_rating_id` bigint(20) NOT NULL,
  `description` text,
  `status` tinyint(2) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`abuse_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `report_abuse`
--

INSERT INTO `report_abuse` (`abuse_id`, `account_id`, `account_rating_id`, `description`, `status`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 2, 5, NULL, 0, '2017-05-12 04:57:43', '2017-05-12 10:57:43', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `screen_sizes`
--

CREATE TABLE IF NOT EXISTS `screen_sizes` (
  `size_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `size_name` varchar(50) NOT NULL,
  `width_min` smallint(6) unsigned DEFAULT NULL COMMENT 'Pixels',
  `width_max` smallint(6) unsigned DEFAULT NULL COMMENT 'Pixels',
  PRIMARY KEY (`size_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `screen_sizes`
--

INSERT INTO `screen_sizes` (`size_id`, `size_name`, `width_min`, `width_max`) VALUES
(1, 'xs', 0, 767),
(2, 'sm', 768, 991),
(3, 'md', 992, 1199),
(4, 'lg', 1200, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `service_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `service_name` varchar(255) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0- Disabled, 1-Enabled',
  PRIMARY KEY (`service_id`),
  UNIQUE KEY `service_name` (`service_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `status`) VALUES
(1, 'Shopping Portal', 1),
(2, 'API Service', 1);

-- --------------------------------------------------------

--
-- Table structure for table `service_policies`
--

CREATE TABLE IF NOT EXISTS `service_policies` (
  `service_policy_id` int(10) NOT NULL AUTO_INCREMENT,
  `policy_type` smallint(2) NOT NULL COMMENT '1-replacement_policy,2-return_policy',
  `policy_period` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'days',
  `policy_title` varchar(200) NOT NULL,
  `policy_desc` text NOT NULL,
  `status` smallint(2) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  `created_on` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`service_policy_id`),
  KEY `policy_period` (`policy_period`,`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `service_policies`
--

INSERT INTO `service_policies` (`service_policy_id`, `policy_type`, `policy_period`, `policy_title`, `policy_desc`, `status`, `is_deleted`, `created_on`, `created_by`, `updated_on`, `updated_by`) VALUES
(1, 1, 30, 'Days Replacement Policy', '<strong>If you have received a damaged or defective product or if it is not as described, you can raise a replacement request within 30 days of receiving the product. Successful pick-up of the product is subject to the following conditions being met: Correct and complete product (with the original brand, article number, undetached MRP tag, product&#39;s original packaging, freebies and accessories) The product should be in unused, undamaged and original condition without any stains, scratches, tears or holes</strong>', 1, 0, '2016-09-27 00:00:00', 1, '2016-09-26 04:24:28', 1),
(2, 1, 12, 'Days Replacement Policy', 'The validity for products installed only by Flipkart authorized personnel shall be 10 days from installation If you have received a damaged or defective product or if it is not as described, you can raise a replacement request on the Website/App/Mobile site within 10 days of receiving the product. We shall help by verifying and trying to resolve your product issue as part of the return verification process. The seller will arrange for a replacement if the issue has not been resolved Successful pick-up of the product is subject to the following conditions being met: Correct and complete product (with the original brand/product Id/undetached MRP tag/product&#39;s original packaging/freebies and accessories) The product should be in unused, undamaged and original condition without any scratches or dents Before returning a Mobile/Laptop/Tablet, the device should be formatted and iCloud accounts should be unlocked for iOS devices', 0, 1, '2016-09-26 00:00:00', 0, '2016-09-26 04:24:28', 1),
(3, 1, 20, 'Days Replacement Policy', '20&nbsp;Days Replacement Policy', 1, 0, '2016-09-30 00:38:14', 1, '2016-09-30 01:08:14', 1),
(4, 1, 45, 'Replacement Policy', 'policy', 1, 0, '2016-09-30 00:58:58', 1, '2016-09-30 01:28:58', 1);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` int(10) NOT NULL AUTO_INCREMENT,
  `settings_name` varchar(200) NOT NULL,
  `setting_key` varchar(250) NOT NULL,
  `discryption` varchar(250) NOT NULL,
  `setting_value` text NOT NULL,
  `settings_type` tinyint(11) NOT NULL COMMENT '1-Bonus,2-Auto Assignment, 3-SMS Gateway',
  `mail_service` tinyint(2) NOT NULL DEFAULT '0',
  `time_flag` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`),
  KEY `setting_key` (`setting_key`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `settings_name`, `setting_key`, `discryption`, `setting_value`, `settings_type`, `mail_service`, `time_flag`) VALUES
(1, 'SupplierCode', 'supplier_code', 'supplier_code_length', '8', 0, 0, '2016-05-08 23:27:01'),
(2, 'Purchase Package Code Prefix', 'purchase_package_code_prefix', 'purchase_package_code_prefix', 'PRC', 0, 0, '2016-05-09 01:46:58'),
(3, 'Supplier Settlement', 'supplier_settlement_autocredit', 'Supplier Settlement Auto Credit(ENABLE/DISABLE)', '1', 0, 0, '2017-05-04 04:13:35'),
(4, 'Partner Package Expiry Notify', 'package_expnotify_days', 'Partner Package Expiry Notify', '5', 0, 1, '2017-05-04 04:13:38'),
(5, 'Collection Fee', 'collection_fee', 'Collection Fee json', '{"type":2,"value":2,"currency_id":null}', 0, 0, '2017-05-04 05:02:56'),
(6, 'Fixed Fee', 'fixed_fee', 'Fixed Fee json', '{"type":1,"value":30,"currency_id":2}', 0, 0, '2017-05-04 05:03:00'),
(7, 'CV to Currency Conversion Rate', 'cv_currency_rate', 'CV to Currency Conversion Rate, Currency_id:Rate', '{\\"1\\":1,\\"2\\":65,\\"4\\":4,\\"5\\":1.25,\\"6\\":50}', 1, 0, '2018-06-25 11:18:00'),
(8, 'Instore commission settings', 'seller_commissions_instore', 'Setting for instore minimum commissions and cashback setting', '{"minimum_commission":3,"minimum_cashback":1}', 0, 0, '2018-07-03 09:21:46');

-- --------------------------------------------------------

--
-- Table structure for table `site_menus`
--

CREATE TABLE IF NOT EXISTS `site_menus` (
  `menu_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(255) NOT NULL,
  `menu_postion_id` tinyint(2) NOT NULL DEFAULT '0',
  `selected` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `is_editable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `site_menus`
--

INSERT INTO `site_menus` (`menu_id`, `menu_name`, `menu_postion_id`, `selected`, `is_editable`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 'Header Catlogue Menu', 1, 1, 1, '2017-07-28 05:56:18', '2017-07-28 06:26:18', 0),
(2, 'Header Primary Menu', 2, 1, 1, '2017-07-28 06:33:50', '2017-07-28 07:03:50', 0),
(3, 'Footer Primary Menu', 3, 1, 0, NULL, '2017-07-29 01:37:12', 0),
(4, 'Footer Seconday Menu', 4, 1, 0, NULL, '2017-07-29 01:37:12', 0),
(5, 'Footer Catlog Menu', 5, 1, 0, NULL, '2017-07-29 01:37:39', 0),
(6, 'Footer Account Menu', 6, 1, 0, NULL, '2017-07-29 01:37:39', 0),
(7, 'Footer Support Menu', 7, 1, 0, NULL, '2017-07-29 01:37:53', 0);

-- --------------------------------------------------------

--
-- Table structure for table `site_menu_navigations`
--

CREATE TABLE IF NOT EXISTS `site_menu_navigations` (
  `navigation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` smallint(6) unsigned NOT NULL,
  `parent_navigation_id` int(10) unsigned DEFAULT NULL,
  `navigation_name` varchar(255) NOT NULL,
  `navigation_url` text NOT NULL,
  `nav_settings` text,
  `type` tinyint(2) NOT NULL COMMENT '1-Top-Menu, 2-Group, 3-Links',
  `link_type` tinyint(2) DEFAULT NULL COMMENT '1-Product,2-Category',
  `group_link` int(10) unsigned DEFAULT NULL,
  `no_of_columns` tinyint(2) unsigned DEFAULT NULL,
  `sort_order` tinyint(3) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_login_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`navigation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=56 ;

--
-- Dumping data for table `site_menu_navigations`
--

INSERT INTO `site_menu_navigations` (`navigation_id`, `menu_id`, `parent_navigation_id`, `navigation_name`, `navigation_url`, `nav_settings`, `type`, `link_type`, `group_link`, `no_of_columns`, `sort_order`, `created_on`, `updated_on`, `is_login_required`, `is_deleted`) VALUES
(1, 2, NULL, 'Home', 'http://localhost/5dg-shopping-portal/all/br?spath=xyz', NULL, 1, 2, NULL, NULL, 1, '2017-07-28 05:56:44', '2017-07-28 06:26:44', 0, 0),
(2, 2, NULL, 'Fashion', 'http://localhost/5dg-shopping-portal/all/br?spath=xyz', NULL, 1, 2, NULL, 4, 2, '2017-07-28 05:58:20', '2017-07-28 06:28:20', 0, 0),
(3, 2, 2, 'Men''s', '', NULL, 2, 2, 2, NULL, 1, NULL, '2017-07-29 01:01:54', 0, 0),
(4, 2, 2, 'Women''s', '', NULL, 2, 2, 2, NULL, 2, NULL, '2017-07-29 01:10:56', 0, 0),
(5, 2, 2, 'Kids', '', NULL, 2, 2, 2, NULL, 3, NULL, '2017-07-29 01:10:56', 0, 0),
(6, 2, 2, 'Trending', '', NULL, 2, 2, 2, NULL, 4, NULL, '2017-07-29 01:11:32', 0, 0),
(7, 2, 2, 'Skirts', '', NULL, 3, 2, 3, NULL, 1, NULL, '2017-07-29 01:15:11', 0, 0),
(8, 2, 2, 'Jackets', '', NULL, 3, 2, 3, NULL, 2, NULL, '2017-07-29 01:15:11', 0, 0),
(9, 2, 2, 'Tops', '', NULL, 3, 2, 3, NULL, 3, NULL, '2017-07-29 01:15:11', 0, 0),
(10, 2, 2, 'Scarves', '', NULL, 3, 2, 3, NULL, 4, NULL, '2017-07-29 01:15:11', 0, 0),
(11, 2, 2, 'Pants', '', NULL, 3, 2, 3, NULL, 5, NULL, '2017-07-29 01:15:11', 0, 0),
(12, 2, 2, 'Skirts', '', NULL, 3, 2, 4, NULL, 1, NULL, '2017-07-29 01:26:56', 0, 0),
(13, 2, 2, 'Jackets', '', NULL, 3, 2, 4, NULL, 2, NULL, '2017-07-29 01:26:56', 0, 0),
(14, 2, 2, 'Tops', '', NULL, 3, 2, 4, NULL, 3, NULL, '2017-07-29 01:26:56', 0, 0),
(15, 2, 2, 'Scarves', '', NULL, 3, 2, 4, NULL, 4, NULL, '2017-07-29 01:26:56', 0, 0),
(16, 2, 2, 'Pants', '', NULL, 3, 2, 4, NULL, 5, NULL, '2017-07-29 01:26:56', 0, 0),
(17, 2, 2, 'Shoes', '', NULL, 3, 2, 5, NULL, 1, NULL, '2017-07-29 01:27:56', 0, 0),
(18, 2, 2, 'Clothing', '', NULL, 3, 2, 5, NULL, 2, NULL, '2017-07-29 01:27:56', 0, 0),
(19, 2, 2, 'Tops', '', NULL, 3, 2, 5, NULL, 3, NULL, '2017-07-29 01:27:56', 0, 0),
(20, 2, 2, 'Scarves', '', NULL, 3, 2, 5, NULL, 4, NULL, '2017-07-29 01:27:56', 0, 0),
(21, 2, 2, 'Accessories', '', NULL, 3, 2, 5, NULL, 5, NULL, '2017-07-29 01:27:56', 0, 0),
(22, 2, 2, 'Men''s Clothing', '', NULL, 3, 2, 6, NULL, 1, NULL, '2017-07-29 01:30:54', 0, 0),
(23, 2, 2, 'Kid''s Clothing', '', NULL, 3, 2, 6, NULL, 2, NULL, '2017-07-29 01:30:54', 0, 0),
(24, 2, 2, 'Women''s Clothing', '', NULL, 3, 2, 6, NULL, 3, NULL, '2017-07-29 01:30:54', 0, 0),
(25, 2, 2, 'Accessories', '', NULL, 3, 2, 6, NULL, 4, NULL, '2017-07-29 01:30:54', 0, 0),
(26, 2, NULL, 'Sports', '', NULL, 1, 2, NULL, NULL, 3, NULL, '2017-07-29 01:58:20', 0, 0),
(27, 2, NULL, 'Foods', '', NULL, 1, 2, NULL, 4, 4, NULL, '2017-07-29 01:58:20', 0, 0),
(28, 2, NULL, 'Digital', '', NULL, 1, 2, NULL, NULL, 5, NULL, '2017-07-29 01:59:31', 0, 0),
(29, 2, NULL, 'Furnitures', '', NULL, 1, 2, NULL, NULL, 6, NULL, '2017-07-29 01:59:31', 0, 0),
(30, 2, NULL, 'Jewellery', '', NULL, 1, 2, NULL, NULL, 7, NULL, '2017-07-29 02:00:25', 0, 0),
(31, 2, NULL, 'Blog', '', NULL, 1, 2, NULL, NULL, 8, NULL, '2017-07-29 02:00:25', 0, 0),
(32, 2, 27, 'ASIAN', '', NULL, 2, 2, 27, NULL, 1, NULL, '2017-07-29 02:08:50', 0, 0),
(33, 2, 27, 'EUROPEAN', '', NULL, 2, 2, 27, NULL, 2, NULL, '2017-07-29 02:08:50', 0, 0),
(34, 2, 27, 'FAST', '', NULL, 2, 2, 27, NULL, 3, NULL, '2017-07-29 02:13:30', 0, 0),
(35, 2, 27, 'SAUSAGES', '', NULL, 2, 2, 27, NULL, 4, NULL, '2017-07-29 02:13:30', 0, 0),
(36, 2, 27, 'Vietnamese Pho', '', NULL, 3, 2, 32, NULL, 1, NULL, '2017-07-29 02:15:33', 0, 0),
(37, 2, 27, 'Noodles', '', NULL, 3, 2, 32, NULL, 2, NULL, '2017-07-29 02:15:33', 0, 0),
(38, 3, NULL, 'COMPANY', '', NULL, 1, NULL, NULL, NULL, 1, NULL, '2017-07-30 23:38:45', 0, 0),
(39, 3, 38, 'About Us', '', NULL, 3, NULL, 38, NULL, 1, NULL, '2017-07-30 23:41:39', 0, 0),
(40, 3, 38, 'Testimonials', '', NULL, 3, NULL, 38, NULL, 2, NULL, '2017-07-30 23:41:39', 0, 0),
(41, 3, 38, 'Affiliate Program', '', NULL, 3, NULL, 38, NULL, 3, NULL, '2017-07-30 23:41:39', 0, 0),
(42, 3, 38, 'Terms & Conditions', '', NULL, 3, NULL, 38, NULL, 4, NULL, '2017-07-30 23:41:39', 0, 0),
(43, 3, 38, 'Contact Us', '', NULL, 3, NULL, 38, NULL, 5, NULL, '2017-07-30 23:41:39', 0, 0),
(44, 6, NULL, 'MY ACCOUNT', '', NULL, 1, NULL, NULL, NULL, 1, NULL, '2017-07-30 23:54:03', 1, 0),
(45, 6, 44, 'My Order', '', NULL, 3, NULL, 44, NULL, 1, NULL, '2017-07-30 23:56:57', 1, 0),
(46, 6, 44, 'My Wishlist', '', NULL, 3, NULL, 44, NULL, 2, NULL, '2017-07-30 23:56:57', 1, 0),
(47, 6, 44, 'My Credit Slip', '', NULL, 3, NULL, 44, NULL, 3, NULL, '2017-07-30 23:56:57', 1, 0),
(48, 6, 44, 'My Addresses', '', NULL, 3, NULL, 44, NULL, 4, NULL, '2017-07-30 23:56:57', 1, 0),
(49, 6, 44, 'My Personal In', '', NULL, 3, NULL, 44, NULL, 5, NULL, '2017-07-30 23:56:57', 1, 0),
(50, 7, NULL, 'SUPPORT', '', NULL, 1, NULL, NULL, NULL, 1, NULL, '2017-07-30 23:57:35', 0, 0),
(51, 7, 50, 'About Us', '', NULL, 3, NULL, 50, NULL, 1, NULL, '2017-07-30 23:58:48', 0, 0),
(52, 7, 50, 'Testimonials', '', NULL, 3, NULL, 50, NULL, 2, NULL, '2017-07-30 23:58:48', 0, 0),
(53, 7, 50, 'Affiliate Program', '', NULL, 3, NULL, 50, NULL, 3, NULL, '2017-07-30 23:58:48', 0, 0),
(54, 7, 50, 'Terms & Conditions', '', NULL, 3, NULL, 50, NULL, 4, NULL, '2017-07-30 23:58:48', 0, 0),
(55, 7, 50, 'Contact Us', '', NULL, 3, NULL, 50, NULL, 5, NULL, '2017-07-30 23:58:48', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `site_menu_nav_lookups`
--

CREATE TABLE IF NOT EXISTS `site_menu_nav_lookups` (
  `nav_type_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `nav_type` varchar(50) NOT NULL,
  `nav_type_slug` varchar(50) NOT NULL,
  PRIMARY KEY (`nav_type_id`),
  UNIQUE KEY `nav_type` (`nav_type`),
  UNIQUE KEY `nav_type_slug` (`nav_type_slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `site_menu_nav_lookups`
--

INSERT INTO `site_menu_nav_lookups` (`nav_type_id`, `nav_type`, `nav_type_slug`) VALUES
(1, 'Top Menu', 'top_menu'),
(2, 'Group Title', 'group_title'),
(3, 'Group Link', 'group_link'),
(4, 'Parent Link', 'parent_link'),
(5, 'Child Link', 'child_link'),
(6, 'Link', 'link');

-- --------------------------------------------------------

--
-- Table structure for table `site_menu_positions_lookups`
--

CREATE TABLE IF NOT EXISTS `site_menu_positions_lookups` (
  `menu_postion_id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `position_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `position_slug` varchar(200) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`menu_postion_id`),
  UNIQUE KEY `position_slug` (`position_slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `site_menu_positions_lookups`
--

INSERT INTO `site_menu_positions_lookups` (`menu_postion_id`, `position_name`, `position_slug`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 'Header catalogue Menu', 'header_catalogue', '2016-09-28 17:32:02', '2016-09-28 12:02:02', 0),
(2, 'Header Primary Menu', 'header_primary', '2016-09-28 17:32:02', '2016-09-28 12:02:02', 0),
(3, 'Footer Primary Menu ', 'footer_primary', '2016-09-28 17:32:06', '2016-09-28 12:02:06', 0),
(4, 'Footer Seconday Menu ', 'footer_secondry', '2016-09-28 17:32:06', '2016-09-28 12:02:06', 0),
(5, 'Footer Catlog Menu ', 'footer_catalogue', '2016-09-28 18:30:30', '2016-09-28 13:00:30', 0),
(6, 'Footer Account Menu', 'footer_account', '2016-10-14 05:10:13', '2016-10-05 09:22:25', 0),
(7, 'Footer Support Menu', 'footer_support', '2016-10-05 14:52:25', '2016-10-05 09:22:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE IF NOT EXISTS `site_settings` (
  `sid` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL,
  `site_url` varchar(255) NOT NULL,
  `site_domain` varchar(100) NOT NULL,
  `site_logo` varchar(255) NOT NULL,
  `site_currency_id` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `site_language_id` smallint(2) unsigned NOT NULL,
  `site_logo_large` varchar(50) NOT NULL,
  `fav_icon` varchar(255) NOT NULL DEFAULT '0',
  `site_meta_title` varchar(255) NOT NULL,
  `site_meta_keyword` text NOT NULL,
  `site_meta_description` text NOT NULL,
  `analytical_code` text NOT NULL,
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `user_login` tinyint(4) NOT NULL COMMENT '0 - not allow, 1 - allow',
  `user_registration` tinyint(4) NOT NULL COMMENT '0 - not allow, 1 - allow ',
  `captcha_code` smallint(6) NOT NULL COMMENT '0-off, 1-on',
  `email_confirmation` tinyint(4) NOT NULL COMMENT '0 - No, 1 - Yes',
  `noreply_email` varchar(150) NOT NULL,
  `enquiry_receive_type` int(1) NOT NULL DEFAULT '0' COMMENT '1- save DB, 2-Email',
  `outbound_email_api` text,
  `transaction_pin` tinyint(4) NOT NULL COMMENT '0 - off, 1 - on',
  `site_status` tinyint(4) NOT NULL COMMENT '0-off, 1-on',
  `related_applications` text,
  `site_offline_message` text NOT NULL,
  `user_authentication_limit` tinyint(4) NOT NULL,
  `left_ads_count` int(3) NOT NULL COMMENT 'Left Side Ads Count',
  `right_ads_count` int(3) NOT NULL COMMENT 'Right Side Ads Count',
  `top_ads_count` int(3) NOT NULL COMMENT 'Top Side Ads Count',
  `bottom_ads_count` int(3) NOT NULL COMMENT 'Bottom Side Ads Count',
  `ads_type_value` text NOT NULL COMMENT 'Ads Credit Value by Ads Type',
  `footer_content` text NOT NULL,
  `dated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`),
  KEY `site_currency_id` (`site_currency_id`),
  KEY `site_language_id` (`site_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`sid`, `site_name`, `site_url`, `site_domain`, `site_logo`, `site_currency_id`, `site_language_id`, `site_logo_large`, `fav_icon`, `site_meta_title`, `site_meta_keyword`, `site_meta_description`, `analytical_code`, `address`, `phone`, `user_login`, `user_registration`, `captcha_code`, `email_confirmation`, `noreply_email`, `enquiry_receive_type`, `outbound_email_api`, `transaction_pin`, `site_status`, `related_applications`, `site_offline_message`, `user_authentication_limit`, `left_ads_count`, `right_ads_count`, `top_ads_count`, `bottom_ads_count`, `ads_type_value`, `footer_content`, `dated`) VALUES
(1, '5DG-Portal', 'http://5DG-Portal.com/', '5DG-Portal.com', 'assets/imgs/logo.png', 1, 1, 'assets/imgs/logo.png', 'assets/imgs/fav-icon.png', '5DG-Portal', '5DG-Portal', '5DG-Portal', '', 'Example Street 68, Mahattan, New York, USA.', '+00 123 456 789', 1, 1, 0, 0, 'support@business.com', 0, NULL, 1, 1, NULL, 'SITE IS  UNDER MAINTENANCE , WILL COME BACK SOON.', 10, 0, 0, 0, 0, '', '2015-16 © 5DG-Portal.com', '2017-05-04 05:24:36');

-- --------------------------------------------------------

--
-- Table structure for table `site_silder_blocks`
--

CREATE TABLE IF NOT EXISTS `site_silder_blocks` (
  `block_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slider_id` smallint(6) unsigned NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `block_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-Browse, 2-Product, 3-Page',
  `supplier_product_id` bigint(20) unsigned DEFAULT NULL,
  `url` text,
  `img_id` bigint(20) unsigned NOT NULL COMMENT 'porduct_id of the image',
  `title` varchar(100) NOT NULL,
  `subtitle` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `sub_description` varchar(100) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Inactive, 1-Active',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '1',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`block_id`),
  KEY `slider_id` (`slider_id`),
  KEY `supplier_product_id` (`supplier_product_id`),
  KEY `img_id` (`img_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `site_silder_blocks`
--

INSERT INTO `site_silder_blocks` (`block_id`, `slider_id`, `sort_order`, `block_type`, `supplier_product_id`, `url`, `img_id`, `title`, `subtitle`, `description`, `sub_description`, `status`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 1, 0, 1, NULL, 'admin/preference/featured-sliders/blocks/1', 1, 'Test-title', 'Test-subtitle', 'Test-desc', NULL, 1, '2016-10-16 18:31:17', '2016-10-17 04:32:00', 1, 0),
(2, 1, 0, 1, NULL, 'admin/preference/featured-sliders/blocks/1', 2, 'Test-title1', 'Test-subtitle-1', 'Test-desc-1', NULL, 1, '2016-10-16 18:43:40', '2016-10-17 04:32:04', 1, 0),
(3, 3, 2, 1, NULL, 'admin/preference/featured-sliders/blocks/2', 3, 'Slider 2 title', 'Slider 2 subtitle', 'Slider 2 desc', NULL, 1, '2016-10-16 22:22:10', '2016-10-18 23:24:16', 1, 0),
(4, 3, 1, 1, NULL, 'admin/preference/featured-sliders/blocks/1', 1, 'Slider 2 title-2', 'Slider 2 subtitle-2', 'Slider 2 desc-2', NULL, 1, '2016-10-16 22:22:39', '2016-10-18 23:24:12', 1, 0),
(5, 2, 3, 2, 1, NULL, 1, 'Apple 230 Dual SIM', 'Sample', 'USD $ 50,000.00', NULL, 1, '2016-10-17 17:44:28', '2016-10-19 04:15:03', 1, 0),
(6, 2, 2, 2, 2, 'product/mobiles/Nokia 130 DS?pid=PRO2', 2, 'Nokia 130 DS', 'Sample-2', 'USD $ 400.00', NULL, 1, '2016-10-17 17:50:14', '2016-10-19 04:15:03', 1, 0),
(7, 2, 1, 2, 1, NULL, 1, 'Apple 230 Dual SIM', 'Sample-3', 'USD $ 50,000.00', 'USD $ 500.00', 1, '2016-10-18 22:14:31', '2016-10-19 04:16:54', 1, 0),
(8, 2, 4, 2, 2, NULL, 1, 'Nokia 130 DS', 'Sample-4', 'USD $ 400.00', 'USD $ 5,000.00', 1, '2016-10-18 22:14:58', '2016-10-19 04:15:09', 1, 0),
(9, 2, 14, 2, 1, NULL, 2, 'Apple 230 Dual SIM', 'Sample-5', 'USD $ 50,000.00', 'USD $ 500.00', 1, '2016-10-18 22:15:33', '2016-10-19 04:16:56', 1, 0),
(10, 2, 5, 2, 1, NULL, 1, 'Pro 1', 'test', '$ 400.00 USD', '500', 1, '2017-05-02 22:38:54', '2017-05-17 05:03:36', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `statement_line`
--

CREATE TABLE IF NOT EXISTS `statement_line` (
  `statementline_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `statementline` varchar(100) NOT NULL,
  `description` varchar(300) NOT NULL,
  `transaction_type` tinyint(1) NOT NULL COMMENT '0 - Debit, 1 - Credit',
  PRIMARY KEY (`statementline_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `statement_line`
--

INSERT INTO `statement_line` (`statementline_id`, `statementline`, `description`, `transaction_type`) VALUES
(1, 'Currency Conversion - Debit', 'Exchange amount between the currencies of the logged user', 0),
(2, 'Currency Conversion - Credit', 'Exchange amount between the currencies of the logged user', 1),
(3, 'User to User Fund Transfer(Admin Commission) -Credit', 'User To User Transfer Commission For Admin ', 1),
(4, 'Cancel Purchase Item Refund-Credit', 'Cancel Purchase Item', 1),
(5, 'Funds Added from System - Credit', 'Adding fund to the user from or by company', 1),
(6, 'Funds Debited by System - Debit', 'Debiting fund to the user from or by company', 0),
(7, 'Supplier Order Cancelation', 'Supplier Order Cancelation - Refund', 1),
(8, 'Supplier Order Cancelation', 'Supplier Order Cancelation - Refund', 1),
(9, 'Admin Order Cancelation', 'Admin Order Cancelation - Refund', 1),
(10, 'Cancelled to In-progress', 'Cancelled to In-progress', 0),
(12, 'Commission Debited by System', 'Admin Commission From Supplier', 0),
(13, 'Commission Credit by System', 'Admin Commission From Supplier', 1),
(14, 'Cancel Purchase Item Refund-Dedit', 'Cancel Purchase Item Refund-Dedit', 0),
(15, 'Cancel Purchase Refund-Credit', 'ancel Purchase Refund-Credit', 1),
(16, 'Cancel Purchase Refund-Debit', 'Cancel Purchase Refund-Debit', 0),
(17, 'Order Payment by Partner to Order - Debit', 'Order Payment by Partner to Order - Debit', 0),
(18, 'Order Payment by Partner to Order - Credit', 'Order Payment by Partner to Order - Credit', 1),
(19, 'Order Payment by Partner to Sub Order - Debit', 'Order Payment by Partner to Sub Order - Debit', 0),
(20, 'Order Payment by Partner to Sub Order - Credit', 'Order Payment by Partner to Sub Order - Credit', 1),
(21, 'Order Payment by Partner to Order Item - Debit', 'Order Payment by Partner to Order Item - Debit', 0),
(22, 'Order Payment by Partner to Order Item - Credit', 'Order Payment by Partner to Order Item - Credit', 1),
(23, 'Withdraw - Debit', 'Withdraw - Debit', 0),
(24, 'Withdraw - Credit', 'Withdraw - Credit', 1),
(25, 'Withdrawal Charges - Debit', 'Withdrawal Charges - Debit', 0),
(26, 'Withdrawal Charges - Credit', 'Withdrawal Charges - Credit', 1),
(27, 'Withdraw Cancel - Debit', 'Withdraw Cancel - Debit', 0),
(28, 'Withdraw Cancel - Credit', 'Withdraw Cancel - Credit', 1),
(29, 'Withdrawal Payment- Debit', 'Withdrawal Payment- Debit', 0),
(30, 'Purchase - Debit', 'Purchase - Debit', 0),
(31, 'Purchase - Credit', 'Purchase - Credit', 0);

-- --------------------------------------------------------

--
-- Table structure for table `status_lookups`
--

CREATE TABLE IF NOT EXISTS `status_lookups` (
  `status_id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `status` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `status_lookups`
--

INSERT INTO `status_lookups` (`status_id`, `status`, `is_deleted`) VALUES
(1, 'Draft', 0),
(2, 'Published', 0),
(3, 'Unpublished', 0),
(4, 'Completed', 0),
(5, 'Cancelled', 0);

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE IF NOT EXISTS `stores` (
  `store_id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `primary_store` tinyint(1) unsigned NOT NULL,
  `store_name` varchar(50) NOT NULL,
  `store_code` varchar(10) NOT NULL,
  `store_logo` varchar(50) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`store_id`),
  UNIQUE KEY `supplier_id_2` (`supplier_id`,`primary_store`),
  KEY `supplier_id` (`supplier_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`store_id`, `supplier_id`, `primary_store`, `store_name`, `store_code`, `store_logo`, `status`, `is_approved`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 1, 1, '', '', '', 1, 0, '2017-03-01 03:33:18', '2018-03-07 16:58:29', 1, 0),
(2, 53, 1, 'Spicewebs', '6630770002', '', 1, 1, '2018-07-17 09:01:54', '2018-07-18 03:38:11', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `store_business_hours`
--

CREATE TABLE IF NOT EXISTS `store_business_hours` (
  `bus_hrs_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `store_id` bigint(12) unsigned DEFAULT NULL,
  `week_day` tinyint(1) unsigned NOT NULL,
  `session` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `from_time` time DEFAULT NULL,
  `to_time` time DEFAULT NULL,
  `is_closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bus_hrs_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `store_id` (`store_id`),
  KEY `week_day` (`week_day`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `store_business_hours`
--

INSERT INTO `store_business_hours` (`bus_hrs_id`, `supplier_id`, `store_id`, `week_day`, `session`, `from_time`, `to_time`, `is_closed`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 53, 2, 1, 1, '05:00:00', '10:00:00', 0, '0000-00-00 00:00:00', NULL, 0),
(2, 53, 2, 1, 2, '14:00:00', '18:00:00', 0, '0000-00-00 00:00:00', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `store_extras`
--

CREATE TABLE IF NOT EXISTS `store_extras` (
  `store_id` bigint(20) NOT NULL DEFAULT '0',
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `city_id` bigint(20) unsigned DEFAULT NULL,
  `state_id` int(10) unsigned NOT NULL,
  `country_id` tinyint(3) unsigned NOT NULL,
  `website` text,
  `postal_code` varchar(15) NOT NULL DEFAULT '0',
  `landline_no` varchar(15) NOT NULL DEFAULT '0',
  `mobile_no` varchar(15) NOT NULL DEFAULT '0',
  `phonecode` varchar(4) NOT NULL DEFAULT '0',
  `email` varchar(50) DEFAULT NULL,
  `working_days` varchar(15) DEFAULT NULL,
  `working_hours_from` time DEFAULT NULL,
  `working_hours_to` time DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`store_id`),
  KEY `city_id` (`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `store_extras`
--

INSERT INTO `store_extras` (`store_id`, `firstname`, `lastname`, `address1`, `address2`, `city_id`, `state_id`, `country_id`, `website`, `postal_code`, `landline_no`, `mobile_no`, `phonecode`, `email`, `working_days`, `working_hours_from`, `working_hours_to`, `is_deleted`) VALUES
(1, 'Parthiban', 'K', 'New Str', 'Vanjur', NULL, 0, 0, 'http://parthiban.com', '632006', '0', '9626128834', '', 'supplier1@gmail.com', '1,2,3,4', '09:00:00', '18:00:00', 0),
(2, NULL, NULL, NULL, NULL, NULL, 0, 77, 'http://www.spice.com', '0', '123456789', '8668081112', '0', 'jayaprakashs.in@gmail.com', NULL, '10:00:00', '17:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `store_settings`
--

CREATE TABLE IF NOT EXISTS `store_settings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `store_id` bigint(12) unsigned DEFAULT NULL,
  `specify_working_hours` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-Not Specified,2-Common,3-Own',
  `split_working_hours` tinyint(1) NOT NULL DEFAULT '0',
  `cb_tracking_days` int(2) unsigned DEFAULT NULL,
  `cb_waiting_days` int(2) unsigned DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `store_settings`
--

INSERT INTO `store_settings` (`id`, `store_id`, `specify_working_hours`, `split_working_hours`, `cb_tracking_days`, `cb_waiting_days`, `updated_by`, `updated_on`) VALUES
(1, 2, 3, 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sub_orders`
--

CREATE TABLE IF NOT EXISTS `sub_orders` (
  `sub_order_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sub_order_code` varchar(30) NOT NULL DEFAULT '0',
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `qty` smallint(6) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `sub_total` double unsigned NOT NULL DEFAULT '0',
  `shipping_charge` double unsigned NOT NULL DEFAULT '0',
  `net_pay` double unsigned NOT NULL DEFAULT '0',
  `sub_order_status_id` tinyint(2) unsigned DEFAULT '0',
  `approval_status_id` tinyint(1) unsigned DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`sub_order_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `order_id` (`order_id`),
  KEY `account_id` (`account_id`),
  KEY `currency_id` (`currency_id`),
  KEY `sub_order_status_id` (`sub_order_status_id`),
  KEY `approval_status_id` (`approval_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `sub_orders`
--

INSERT INTO `sub_orders` (`sub_order_id`, `sub_order_code`, `order_id`, `account_id`, `supplier_id`, `qty`, `currency_id`, `sub_total`, `shipping_charge`, `net_pay`, `sub_order_status_id`, `approval_status_id`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 'OD1S1', 1, 2, 1, 5, 1, 3960, 0, 3960, 3, 1, '2017-04-21 07:17:44', '2018-07-13 07:02:48', 0),
(2, 'OD2S2', 2, 2, 1, 4, 1, 1430, 0, 1430, 2, 1, '2017-04-22 00:35:57', '2017-06-19 13:24:28', 0),
(3, 'OD3S3', 3, 2, 1, 4, 1, 2030, 0, 2030, 2, 5, '2017-04-25 00:31:20', '2017-04-25 19:55:36', 0),
(4, 'OD4S4', 4, 2, 1, 2, 1, 875, 0, 875, 1, 5, '2017-04-26 05:32:20', '2017-04-25 20:07:53', 0),
(6, 'OD6S6', 6, 2, 1, 4, 1, 1600, 400, 2000, 1, 5, '2017-05-04 01:40:54', '2017-06-13 17:22:12', 0),
(17, 'OD17S17', 17, 2, 1, 1, 1, 1350, 100, 1450, 1, 5, '2017-05-04 04:36:28', '2017-06-13 17:22:04', 0),
(18, 'OD18S18', 18, 2, 1, 2, 2, 1285, 350, 1635, 2, 1, '2017-07-11 06:13:46', '2018-07-13 06:04:34', 0);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers_order_customers`
--

CREATE TABLE IF NOT EXISTS `suppliers_order_customers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

--
-- Dumping data for table `suppliers_order_customers`
--

INSERT INTO `suppliers_order_customers` (`id`, `supplier_id`, `account_id`, `updated_on`) VALUES
(1, 35, 7, '2016-05-10 07:26:27'),
(2, 35, 7, '2016-05-10 22:57:13'),
(3, 35, 7, '2016-05-10 22:57:14'),
(4, 35, 7, '2016-05-10 22:59:25'),
(5, 35, 7, '2016-05-10 22:59:25'),
(6, 35, 7, '2016-05-10 23:00:39'),
(7, 35, 7, '2016-05-10 23:00:39'),
(8, 35, 7, '2016-05-10 23:03:11'),
(9, 35, 7, '2016-05-10 23:03:11'),
(10, 35, 7, '2016-05-10 23:04:55'),
(11, 35, 7, '2016-05-10 23:04:55'),
(12, 35, 7, '2016-05-10 23:07:44'),
(13, 35, 7, '2016-05-10 23:07:44'),
(14, 35, 7, '2016-05-10 23:08:32'),
(15, 35, 7, '2016-05-10 23:10:17'),
(16, 35, 7, '2016-05-10 23:28:11'),
(17, 35, 7, '2016-05-10 23:28:11'),
(18, 35, 7, '2016-05-10 23:33:47'),
(19, 35, 7, '2016-05-10 23:33:47'),
(20, 35, 7, '2016-06-06 00:50:15'),
(21, 35, 7, '2016-06-06 01:01:21'),
(22, 35, 7, '2016-06-06 01:17:13'),
(23, 35, 7, '2016-06-06 01:18:21'),
(24, 35, 7, '2016-06-06 01:21:35'),
(25, 35, 7, '2016-06-06 01:23:43'),
(26, 35, 7, '2016-06-06 01:23:43'),
(27, 35, 7, '2016-06-06 01:27:44'),
(28, 35, 7, '2016-06-06 01:27:44'),
(29, 35, 7, '2016-06-06 01:33:36'),
(30, 35, 7, '2016-06-06 01:33:36'),
(31, 35, 7, '2016-06-06 01:34:06'),
(32, 35, 7, '2016-06-06 01:34:06'),
(33, 35, 7, '2016-06-06 02:00:36'),
(34, 35, 7, '2016-06-06 02:00:36'),
(35, 35, 7, '2016-06-06 03:07:50'),
(36, 35, 7, '2016-06-06 03:08:14'),
(37, 35, 7, '2016-06-06 03:08:14');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_brand_associate`
--

CREATE TABLE IF NOT EXISTS `supplier_brand_associate` (
  `supplier_brand_id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL DEFAULT '0',
  `brand_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`supplier_brand_id`),
  UNIQUE KEY `supplier_id` (`supplier_id`,`brand_id`),
  KEY `brand_id` (`brand_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `supplier_brand_associate`
--

INSERT INTO `supplier_brand_associate` (`supplier_brand_id`, `supplier_id`, `brand_id`, `status`, `is_verified`, `updated_by`, `updated_on`, `is_deleted`) VALUES
(1, 1, 1, 1, 0, 1, '2017-05-25 06:59:53', 0),
(2, 1, 2, 1, 0, 1, '2017-05-25 06:59:55', 0),
(3, 1, 3, 1, 0, 1, '2017-05-25 06:59:57', 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_cashback_settings`
--

CREATE TABLE IF NOT EXISTS `supplier_cashback_settings` (
  `scs_id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `store_id` bigint(12) unsigned DEFAULT NULL,
  `shop_and_earn` tinyint(10) unsigned NOT NULL DEFAULT '1',
  `redeem` tinyint(1) unsigned DEFAULT '1' COMMENT '0 - deactivate 1 - activate (Accept CashBack/Redeem)',
  `pay` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0 - deactivate 1 - activate(Accept Payments / Pay)',
  `offer_cashback` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0- Disabled 1-Enabled',
  `is_cashback_period` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0- Disabled 1-Enabled',
  `cashback_start` datetime DEFAULT NULL,
  `cashback_end` datetime DEFAULT NULL,
  `is_redeem_otp_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `member_redeem_wallets` varchar(20) NOT NULL DEFAULT '1,2,3,4',
  PRIMARY KEY (`scs_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `supplier_cashback_settings`
--

INSERT INTO `supplier_cashback_settings` (`scs_id`, `supplier_id`, `store_id`, `shop_and_earn`, `redeem`, `pay`, `offer_cashback`, `is_cashback_period`, `cashback_start`, `cashback_end`, `is_redeem_otp_required`, `member_redeem_wallets`) VALUES
(1, 1, NULL, 1, 1, 1, 0, 0, '2018-07-11 00:00:00', '2018-07-11 00:00:00', 0, '1,2,3,5'),
(2, 53, NULL, 1, 1, 1, 0, 0, '2018-07-17 00:00:00', '2018-07-17 00:00:00', 1, '1,2,3,4,5');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_category_associate`
--

CREATE TABLE IF NOT EXISTS `supplier_category_associate` (
  `supplier_category_id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL DEFAULT '0',
  `category_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`supplier_category_id`),
  UNIQUE KEY `supplier_id` (`supplier_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `supplier_category_associate`
--

INSERT INTO `supplier_category_associate` (`supplier_category_id`, `supplier_id`, `category_id`, `status`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 1, 1, 1, '2017-04-05 07:08:38', 34, 0),
(2, 1, 2, 1, '2017-04-05 07:08:40', 34, 0),
(3, 1, 3, 1, '2017-04-05 07:08:43', 18, 0),
(4, 1, 4, 1, '2017-04-05 07:08:51', 0, 0),
(5, 1, 5, 1, '2017-04-05 07:08:53', 0, 0),
(7, 1, 6, 1, '2017-05-14 13:59:08', 3, 0),
(8, 1, 7, 1, '2017-05-14 13:57:36', 3, 0),
(9, 1, 8, 1, '2017-06-21 18:35:56', 3, 0),
(10, 1, 9, 1, '2017-05-14 14:02:29', 3, 0),
(11, 53, 192, 1, '2018-07-18 06:34:27', 155, 0),
(12, 33, 12, 1, '2018-07-06 23:05:56', 155, 1),
(13, 33, 25, 1, '2018-07-05 11:09:39', 3, 0),
(14, 33, 571, 1, '2018-07-06 07:27:37', 155, 1),
(15, 33, 95, 1, '2018-07-06 23:47:50', 155, 0),
(16, 33, 29, 1, '2018-07-06 23:47:46', 155, 0),
(17, 53, 1404, 0, '2018-07-17 13:02:18', 4, 0),
(18, 53, 571, 0, '2018-07-17 13:03:31', 4, 0),
(19, 53, 67, 0, '2018-07-17 13:04:09', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_commissions_lookups`
--

CREATE TABLE IF NOT EXISTS `supplier_commissions_lookups` (
  `commission_type_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `commission_type` varchar(200) NOT NULL,
  PRIMARY KEY (`commission_type_id`),
  UNIQUE KEY `commission_type_id` (`commission_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `supplier_commissions_lookups`
--

INSERT INTO `supplier_commissions_lookups` (`commission_type_id`, `commission_type`) VALUES
(1, 'Fixed'),
(2, 'Flexible');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_commissions_settings`
--

CREATE TABLE IF NOT EXISTS `supplier_commissions_settings` (
  `commission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `commission_type` tinyint(1) unsigned NOT NULL COMMENT '1-fixed,2-flexible',
  `commission_unit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1-percentage,2-fixed_rate',
  `commission_value` double unsigned NOT NULL DEFAULT '0',
  `currency_id` tinyint(3) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`commission_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `updated_by` (`updated_by`),
  KEY `created_by` (`created_by`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `supplier_commissions_settings`
--

INSERT INTO `supplier_commissions_settings` (`commission_id`, `supplier_id`, `commission_type`, `commission_unit`, `commission_value`, `currency_id`, `created_by`, `created_on`, `updated_by`, `updated_on`, `status`, `is_deleted`) VALUES
(1, 1, 2, 0, 0, NULL, 1, '2016-08-04 01:54:09', 1, '2016-08-04 02:24:09', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_flexible_commissions`
--

CREATE TABLE IF NOT EXISTS `supplier_flexible_commissions` (
  `spc_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_type_id` tinyint(2) unsigned DEFAULT '3',
  `relation_id` bigint(20) unsigned DEFAULT NULL,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `commission_unit` tinyint(1) unsigned NOT NULL,
  `commission_value` double unsigned NOT NULL DEFAULT '0',
  `currency_id` tinyint(3) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '1',
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`spc_id`),
  KEY `post_type_id` (`post_type_id`),
  KEY `relation_id` (`relation_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `currency_id` (`currency_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_mst`
--

CREATE TABLE IF NOT EXISTS `supplier_mst` (
  `supplier_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `category_id` smallint(6) unsigned NOT NULL,
  `supplier_code` varchar(15) NOT NULL,
  `type_of_bussiness` tinyint(2) unsigned DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `reg_company_name` varchar(100) DEFAULT NULL,
  `file_path` varchar(200) NOT NULL,
  `office_fax` varchar(15) DEFAULT NULL,
  `office_phone` varchar(15) DEFAULT NULL,
  `website` tinytext,
  `completed_steps` varchar(100) DEFAULT NULL,
  `verified_steps` varchar(100) DEFAULT NULL,
  `next_step` tinyint(2) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-InActive, 1-Active',
  `is_verified` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `enable_multistore` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `has_specific_hrs` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  `is_closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`supplier_id`),
  UNIQUE KEY `unique_id` (`supplier_id`) USING BTREE,
  UNIQUE KEY `account_id` (`account_id`),
  UNIQUE KEY `supplier_code` (`supplier_code`),
  KEY `updated_by` (`updated_by`),
  KEY `type_of_bussiness` (`type_of_bussiness`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54 ;

--
-- Dumping data for table `supplier_mst`
--

INSERT INTO `supplier_mst` (`supplier_id`, `account_id`, `category_id`, `supplier_code`, `type_of_bussiness`, `company_name`, `reg_company_name`, `file_path`, `office_fax`, `office_phone`, `website`, `completed_steps`, `verified_steps`, `next_step`, `status`, `is_verified`, `enable_multistore`, `has_specific_hrs`, `is_deleted`, `is_closed`, `created_on`, `updated_by`, `updated_on`) VALUES
(1, 3, 0, 'SUP42428', NULL, 'Sathya', NULL, '', '9626128834', '9626128834', 'http://www.sathya.com', NULL, NULL, NULL, 1, 1, 0, 0, 0, 0, '2016-05-18 01:22:01', NULL, '2018-03-08 05:27:43'),
(53, 4, 192, 'S3340770004', 1, 'reg company', 'sws', 'S3340770004/', NULL, NULL, 'http://www.spice.com', '1,2,3,4', '1,2,3,4', 0, 1, 1, 0, 0, 0, 0, '2018-07-17 09:01:54', 1, '2018-07-17 11:44:24');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_order_particulars_discounts`
--

CREATE TABLE IF NOT EXISTS `supplier_order_particulars_discounts` (
  `opd_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint(20) unsigned NOT NULL,
  `discount_id` int(10) unsigned NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`opd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payment_settings`
--

CREATE TABLE IF NOT EXISTS `supplier_payment_settings` (
  `sps_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `payment_settings` text NOT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sps_id`),
  KEY `updated_by` (`updated_by`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `supplier_payment_settings`
--

INSERT INTO `supplier_payment_settings` (`sps_id`, `supplier_id`, `payment_settings`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 53, '{"bank_name":"KVB","account_holder_name":"prakash","account_no":"123415555555","account_type":"Savings","ifsc_code":"SBIC0001000","branch":"Guduvancherry","pan":"DJFLK1234D","address1":"Add1","address2":"Add2","postal_code":"600033","country_id":"77","state_id":"31","city_id":"839874"}', '2018-07-17 11:41:58', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_pick_address`
--

CREATE TABLE IF NOT EXISTS `supplier_pick_address` (
  `address_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `address_type_id` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `street1` varchar(255) DEFAULT NULL,
  `street2` varchar(255) DEFAULT NULL,
  `city_id` bigint(20) unsigned DEFAULT NULL,
  `state_id` int(11) unsigned DEFAULT NULL,
  `country_id` tinyint(3) unsigned DEFAULT NULL,
  `postal_code` varchar(15) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`address_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `city_id` (`city_id`),
  KEY `state_id` (`state_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_preference`
--

CREATE TABLE IF NOT EXISTS `supplier_preference` (
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `is_ownshipment` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `logistic_id` smallint(6) unsigned DEFAULT NULL,
  `is_default_logistic_if_not_avaliable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-No, 1-Yes',
  UNIQUE KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `supplier_preference`
--

INSERT INTO `supplier_preference` (`supplier_id`, `is_ownshipment`, `logistic_id`, `is_default_logistic_if_not_avaliable`) VALUES
(1, 0, 1, 1),
(33, 1, NULL, 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `supplier_products_list`
--
CREATE TABLE IF NOT EXISTS `supplier_products_list` (
`supplier_product_id` bigint(15) unsigned
,`product_id` int(10) unsigned
,`product_cmb_id` int(11) unsigned
,`product_code` varchar(20)
,`supplier_product_code` varchar(35)
,`eanbarcode` varchar(20)
,`upcbarcode` varchar(20)
,`category_id` smallint(6) unsigned
,`category_code` varchar(3)
,`category` varchar(100)
,`category_url_str` varchar(200)
,`replacement_service_policy_id` tinyint(3) unsigned
,`category_status` tinyint(1) unsigned
,`assoc_category_id` text
,`brand_id` smallint(6) unsigned
,`brand_name` varchar(100)
,`brand_url_str` varchar(200)
,`brand_sku` varchar(255)
,`brand_status` tinyint(1) unsigned
,`product_name` text
,`product_cmb` varchar(255)
,`product_slug` varchar(255)
,`sku` varchar(255)
,`description` text
,`currency_id` tinyint(3) unsigned
,`mrp_price` double unsigned
,`price` double
,`product_rating_1` int(10) unsigned
,`product_rating_2` int(10) unsigned
,`product_rating_3` int(10) unsigned
,`product_rating_4` int(10) unsigned
,`product_rating_5` int(10) unsigned
,`product_avg_rating` int(10) unsigned
,`product_rating_count` int(10) unsigned
,`supplier_rating_1` int(10) unsigned
,`supplier_rating_2` int(10) unsigned
,`supplier_rating_3` int(10) unsigned
,`supplier_rating_4` int(10) unsigned
,`supplier_rating_5` int(10) unsigned
,`supplier_avg_rating` int(10) unsigned
,`supplier_rating_count` int(10) unsigned
,`is_combinations` tinyint(1) unsigned
,`visiblity_id` tinyint(1) unsigned
,`redirect_id` tinyint(2) unsigned
,`is_verified` tinyint(1) unsigned
,`is_exclusive` tinyint(1) unsigned
,`width` decimal(10,3) unsigned
,`height` decimal(10,3) unsigned
,`length` decimal(10,3) unsigned
,`weight` decimal(10,3) unsigned
,`volumetric_weight` decimal(10,3)
,`status` tinyint(1) unsigned
,`created_on` datetime
,`created_by` bigint(20) unsigned
,`updated_on` datetime
,`updated_by` bigint(20) unsigned
,`verified_on` datetime
,`verified_by` bigint(20) unsigned
,`is_deleted` decimal(3,0)
,`supplier_id` int(10) unsigned
,`condition_id` tinyint(2) unsigned
,`is_existing` tinyint(1) unsigned
,`is_replaceable` tinyint(1) unsigned
,`is_assured` tinyint(1) unsigned
,`is_shipping_beared` tinyint(1) unsigned
,`is_featured` tinyint(2) unsigned
,`promote_to_homepage` tinyint(2) unsigned
,`spi_status` tinyint(1) unsigned
,`pre_order` tinyint(1) unsigned
,`stock_status_id` tinyint(3) unsigned
,`spi_created_on` datetime
,`spi_updated_on` timestamp
,`spi_updated_by` bigint(20) unsigned
,`spi_is_deleted` tinyint(1) unsigned
);
-- --------------------------------------------------------

--
-- Table structure for table `supplier_product_cmb_price`
--

CREATE TABLE IF NOT EXISTS `supplier_product_cmb_price` (
  `spcp_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `product_cmb_id` int(11) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `impact_on_price` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`spcp_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `product_cmb_id` (`product_cmb_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `supplier_product_cmb_price`
--

INSERT INTO `supplier_product_cmb_price` (`spcp_id`, `supplier_id`, `product_cmb_id`, `currency_id`, `impact_on_price`) VALUES
(1, 1, 1, 1, 10);

-- --------------------------------------------------------

--
-- Stand-in structure for view `supplier_product_info`
--
CREATE TABLE IF NOT EXISTS `supplier_product_info` (
`supplier_product_id` bigint(15) unsigned
,`product_id` int(10) unsigned
,`product_cmb_id` int(11) unsigned
,`product_code` varchar(20)
,`eanbarcode` varchar(20)
,`upcbarcode` varchar(20)
,`category_id` smallint(6) unsigned
,`category_code` varchar(3)
,`category` varchar(100)
,`category_url_str` varchar(200)
,`replacement_service_policy_id` tinyint(3) unsigned
,`assoc_category_id` text
,`brand_id` smallint(6) unsigned
,`brand_name` varchar(100)
,`brand_url_str` varchar(200)
,`brand_sku` varchar(255)
,`product_name` text
,`product_cmb` varchar(255)
,`product_slug` varchar(255)
,`sku` varchar(255)
,`description` text
,`is_combinations` tinyint(1) unsigned
,`supplier_id` int(10) unsigned
);
-- --------------------------------------------------------

--
-- Table structure for table `supplier_product_items`
--

CREATE TABLE IF NOT EXISTS `supplier_product_items` (
  `supplier_product_id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_product_code` varchar(35) DEFAULT NULL,
  `supplier_id` int(10) unsigned NOT NULL DEFAULT '0',
  `store_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL,
  `is_featured` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `promote_to_homepage` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `product_cmb_id` int(11) unsigned DEFAULT NULL,
  `condition_id` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `is_existing` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_replaceable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `is_assured` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `is_shipping_beared` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Shipping Charge by Customer, 1-Shipping Charge By Supplier',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pre_order` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `stock_status_id` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`supplier_product_id`),
  UNIQUE KEY `supplier_product_code` (`supplier_product_code`),
  KEY `supplier_id` (`supplier_id`),
  KEY `store_id` (`store_id`),
  KEY `product_id` (`product_id`),
  KEY `product_cmb_id` (`product_cmb_id`),
  KEY `stock_status_id` (`stock_status_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `supplier_product_items`
--

INSERT INTO `supplier_product_items` (`supplier_product_id`, `supplier_product_code`, `supplier_id`, `store_id`, `product_id`, `is_featured`, `promote_to_homepage`, `product_cmb_id`, `condition_id`, `is_existing`, `is_replaceable`, `is_assured`, `is_shipping_beared`, `status`, `pre_order`, `stock_status_id`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 'SPRO1', 1, 1, 1, 0, 0, NULL, 1, 0, 0, 0, 0, 1, 1, 1, '2016-05-10 00:00:00', '2017-06-29 06:18:05', 3, 0),
(2, 'SPRO2', 1, 2, 2, 0, 0, NULL, 1, 0, 0, 0, 0, 1, 1, 1, '2016-05-10 00:00:00', '2017-05-16 06:24:10', 1, 0),
(3, 'SPRO3', 1, 3, 3, 0, 0, NULL, 1, 0, 0, 0, 0, 1, 1, 1, '2016-06-06 00:00:00', '2017-05-16 06:24:10', 1, 0),
(4, 'SPRO4', 1, 4, 4, 0, 0, NULL, 1, 0, 0, 0, 0, 1, 1, 1, '2016-07-13 23:25:28', '2017-05-16 06:24:10', 1, 0),
(5, 'SPRO5', 1, 5, 5, 0, 0, NULL, 1, 0, 0, 0, 0, 1, 1, 1, '2016-08-23 01:49:02', '2017-05-16 06:24:10', 1, 0),
(6, 'SPRO6', 1, 0, 6, 0, 0, NULL, 1, 0, 0, 0, 0, 1, 1, 3, NULL, '2017-06-24 02:49:30', 3, 0),
(7, 'SPRO7', 1, 0, 7, 0, 0, NULL, 1, 0, 0, 0, 0, 1, 1, 3, '2016-10-25 01:06:32', '2017-06-24 02:52:41', 3, 0),
(8, 'SPRO8', 1, 0, 8, 0, 0, NULL, 1, 0, 0, 0, 0, 1, 1, 3, '2016-11-13 23:32:42', '2017-05-16 06:24:10', 1, 0),
(9, 'SPRO9', 1, 0, 2, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1, 3, '2017-02-21 01:11:09', '2017-05-16 05:40:36', 1, 0),
(10, 'SP10', 2, 0, 11, 0, 0, NULL, 1, 0, 0, 0, 0, 1, 0, 3, '2017-05-22 23:01:56', '2017-06-27 01:55:24', 1, 0),
(12, 'SP12', 1, 0, 18, 0, 0, NULL, 1, 0, 0, 0, 1, 1, 1, 3, '2017-05-30 02:39:26', '2017-07-24 05:03:04', 3, 0),
(13, 'SP13', 1, 0, 19, 1, 1, NULL, 1, 0, 0, 0, 0, 1, 1, 3, '2017-08-07 03:04:19', '2017-08-11 03:52:59', 3, 0),
(14, 'SP14', 53, 0, 29, 0, 1, NULL, 1, 0, 0, 0, 0, 0, 0, 3, '2018-07-05 12:03:18', '2018-07-18 06:50:38', 4, 1),
(15, 'SP15', 53, 0, 30, 0, 1, NULL, 1, 0, 0, 0, 1, 0, 1, 3, '2018-07-05 12:06:06', '2018-07-18 06:50:58', 4, 0),
(16, 'SP16', 53, 0, 31, 0, 1, NULL, 1, 0, 0, 0, 1, 1, 0, 3, '2018-07-06 11:30:35', '2018-07-18 06:51:44', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_product_price`
--

CREATE TABLE IF NOT EXISTS `supplier_product_price` (
  `spp_id` bigint(23) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `price` double unsigned NOT NULL DEFAULT '0',
  `off_perc` tinyint(3) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`spp_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `product_id` (`product_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `supplier_product_price`
--

INSERT INTO `supplier_product_price` (`spp_id`, `supplier_id`, `product_id`, `currency_id`, `price`, `off_perc`, `is_deleted`) VALUES
(1, 33, 29, 2, 0, 0, 0),
(2, 33, 30, 2, 80, 20, 0),
(3, 33, 30, 1, 800, 20, 0),
(4, 33, 31, 2, 80, 20, 0),
(5, 33, 31, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_product_shippment_charges`
--

CREATE TABLE IF NOT EXISTS `supplier_product_shippment_charges` (
  `psc_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pss_id` bigint(20) NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `geo_zone_id` smallint(6) unsigned NOT NULL,
  `mode_id` smallint(4) unsigned NOT NULL,
  `delivery_days` smallint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_charge` decimal(10,3) unsigned NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`psc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `supplier_product_shippment_charges`
--

INSERT INTO `supplier_product_shippment_charges` (`psc_id`, `pss_id`, `currency_id`, `geo_zone_id`, `mode_id`, `delivery_days`, `delivery_charge`) VALUES
(1, 3, 0, 1, 1, 10, '20.000'),
(2, 3, 0, 2, 1, 20, '10.000'),
(3, 3, 0, 3, 2, 10, '5.000'),
(4, 0, 0, 1, 2, 10, '10.000'),
(5, 0, 0, 1, 2, 10, '10.000'),
(6, 0, 0, 1, 2, 1, '10.000'),
(7, 4, 1, 1, 4, 5, '50.000'),
(8, 0, 0, 2, 1, 5, '50.000'),
(9, 0, 0, 2, 1, 5, '50.000'),
(10, 0, 0, 2, 7, 8, '40.000'),
(11, 5, 0, 2, 4, 8, '80.000'),
(12, 6, 0, 1, 1, 5, '50.000');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_product_shippment_settings`
--

CREATE TABLE IF NOT EXISTS `supplier_product_shippment_settings` (
  `pss_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `logistic_id` smallint(6) unsigned DEFAULT NULL,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `supplier_product_id` int(10) DEFAULT NULL,
  `currency_id` tinyint(3) unsigned DEFAULT NULL,
  `country_id` tinyint(3) unsigned DEFAULT NULL,
  `mode_id` smallint(4) unsigned DEFAULT '1',
  `weight_slab_id` tinyint(3) unsigned NOT NULL,
  `delivery_days` smallint(3) unsigned NOT NULL DEFAULT '0',
  `delivery_charge` decimal(10,3) unsigned NOT NULL DEFAULT '0.000',
  `zone_delivery_days` smallint(3) unsigned NOT NULL DEFAULT '0',
  `zone_delivery_charges` decimal(10,3) unsigned NOT NULL DEFAULT '0.000',
  `national_delivery_days` smallint(3) unsigned NOT NULL DEFAULT '0',
  `national_delivery_charges` decimal(10,3) unsigned NOT NULL DEFAULT '0.000',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`pss_id`),
  KEY `supplier_product_id` (`logistic_id`),
  KEY `carrier_id` (`logistic_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `currency_id` (`currency_id`),
  KEY `country_id` (`country_id`),
  KEY `mode_id` (`mode_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `supplier_product_shippment_settings`
--

INSERT INTO `supplier_product_shippment_settings` (`pss_id`, `logistic_id`, `supplier_id`, `supplier_product_id`, `currency_id`, `country_id`, `mode_id`, `weight_slab_id`, `delivery_days`, `delivery_charge`, `zone_delivery_days`, `zone_delivery_charges`, `national_delivery_days`, `national_delivery_charges`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 1, NULL, NULL, 1, 77, 1, 1, 3, '30.000', 0, '50.000', 0, '70.000', '2017-05-03 18:05:55', '2017-05-03 07:07:05', 0, 0),
(2, 1, NULL, NULL, 1, 77, 1, 2, 3, '30.000', 5, '35.000', 7, '40.000', '2017-05-03 18:06:12', '2017-05-03 07:07:10', 0, 0),
(3, 1, NULL, NULL, 1, 77, 1, 3, 3, '5.000', 5, '10.000', 7, '20.000', '2017-05-03 18:06:54', '2017-05-03 07:07:16', 0, 0),
(4, NULL, 33, 15, NULL, NULL, 7, 0, 8, '40.000', 0, '0.000', 0, '0.000', '2018-07-06 00:00:00', '2018-07-06 10:04:17', 155, 1),
(5, NULL, 33, 15, NULL, NULL, 4, 0, 8, '80.000', 0, '0.000', 0, '0.000', '2018-07-06 00:00:00', '2018-07-06 10:04:30', 155, 0),
(6, NULL, 33, 16, NULL, NULL, 1, 0, 5, '50.000', 0, '0.000', 0, '0.000', '2018-07-06 00:00:00', '2018-07-06 12:11:38', 155, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_product_stock_log`
--

CREATE TABLE IF NOT EXISTS `supplier_product_stock_log` (
  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_product_id` bigint(15) unsigned NOT NULL,
  `stock_value` smallint(6) unsigned NOT NULL,
  `current_stock_value` int(10) unsigned NOT NULL,
  `transaction_type` smallint(2) unsigned NOT NULL COMMENT '1-increment,0-decrement',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`log_id`),
  KEY `supplier_product_id` (`supplier_product_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `supplier_product_stock_log`
--

INSERT INTO `supplier_product_stock_log` (`log_id`, `supplier_product_id`, `stock_value`, `current_stock_value`, `transaction_type`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 15, 5, 105, 1, '2018-07-06 10:16:50', '2018-07-06 10:16:51', 4, 0),
(2, 15, 10, 115, 1, '2018-07-06 10:17:04', '2018-07-06 10:17:04', 4, 0),
(3, 16, 50, 50, 1, '2018-07-06 12:11:49', '2018-07-06 12:11:49', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_product_stock_management`
--

CREATE TABLE IF NOT EXISTS `supplier_product_stock_management` (
  `stock_id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint(15) unsigned NOT NULL,
  `supplier_product_id` bigint(15) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `product_cmb_id` int(11) unsigned DEFAULT NULL,
  `stock_on_hand` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'total_items',
  `commited_stock` int(10) unsigned NOT NULL DEFAULT '0',
  `sold_items` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'sold items',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stock_id`),
  KEY `supplier_product_id` (`supplier_product_id`),
  KEY `product_id` (`product_id`),
  KEY `product_cmb_id` (`product_cmb_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `supplier_product_stock_management`
--

INSERT INTO `supplier_product_stock_management` (`stock_id`, `store_id`, `supplier_product_id`, `product_id`, `product_cmb_id`, `stock_on_hand`, `commited_stock`, `sold_items`, `created_on`, `updated_on`) VALUES
(1, 0, 14, 29, NULL, 0, 0, 0, '2018-07-05 12:03:18', '2018-07-05 12:03:18'),
(5, 0, 15, 30, NULL, 115, 0, 0, '2018-07-06 10:07:12', '2018-07-06 10:17:04'),
(7, 0, 16, 31, NULL, 50, 0, 0, '2018-07-06 11:30:35', '2018-07-06 12:11:49');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_profit_sharing`
--

CREATE TABLE IF NOT EXISTS `supplier_profit_sharing` (
  `sps_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `commision_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - Instore',
  `bcategory_id` smallint(5) unsigned DEFAULT NULL,
  `profit_sharing` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cashback_on_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cashback_on_redeem` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cashback_on_shop_and_earn` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_cashback_period` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disable, 1-Enable',
  `cashback_start` datetime DEFAULT NULL,
  `cashback_end` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Pending,1-Accepted, 2-Rejected, 3-Closed, 4-Cancelled',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sps_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `bcategory_id` (`bcategory_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `supplier_profit_sharing`
--

INSERT INTO `supplier_profit_sharing` (`sps_id`, `supplier_id`, `commision_type`, `bcategory_id`, `profit_sharing`, `cashback_on_pay`, `cashback_on_redeem`, `cashback_on_shop_and_earn`, `is_cashback_period`, `cashback_start`, `cashback_end`, `status`, `created_on`, `updated_on`, `created_by`, `updated_by`, `is_deleted`) VALUES
(13, 33, 1, NULL, 20, 20, 20, 20, 0, NULL, NULL, 1, '2018-07-03 12:18:07', '2018-07-11 11:53:56', 155, 1, 0),
(14, 35, 1, NULL, 15, 13, 13, 15, 0, NULL, NULL, 1, '2018-07-13 05:31:42', '2018-07-13 06:29:15', 157, 1, 0),
(15, 53, 1, NULL, 4, 2, 2, 4, 0, NULL, NULL, 1, '2018-07-17 11:44:55', '2018-07-17 12:21:09', 4, 1, 0),
(16, 53, 1, NULL, 50, 48, 48, 50, 0, NULL, NULL, 1, '2018-07-17 12:38:06', '2018-07-17 12:38:43', 4, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_store_sales`
--

CREATE TABLE IF NOT EXISTS `supplier_store_sales` (
  `supplier_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  `order_count` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `supplier_store_sales`
--

INSERT INTO `supplier_store_sales` (`supplier_id`, `store_id`, `order_count`) VALUES
(33, 6, 34);

-- --------------------------------------------------------

--
-- Table structure for table `support_issues_category`
--

CREATE TABLE IF NOT EXISTS `support_issues_category` (
  `issues_category_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_issues_category_id` int(10) NOT NULL DEFAULT '0',
  `category` varchar(60) CHARACTER SET latin1 NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`issues_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `support_issues_category`
--

INSERT INTO `support_issues_category` (`issues_category_id`, `parent_issues_category_id`, `category`, `created_by`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 0, 'Need assistance with existing order.', 1, '2016-10-25 03:46:55', NULL, 0),
(2, 0, 'Any other assistance?', 1, '2016-10-11 10:17:32', '2016-10-25 01:59:14', 0),
(3, 1, 'wallet', 1, NULL, '2016-10-27 18:21:47', 0),
(4, 2, 'payment', 1, NULL, '2016-10-27 18:21:47', 0);

-- --------------------------------------------------------

--
-- Table structure for table `support_issues_query`
--

CREATE TABLE IF NOT EXISTS `support_issues_query` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `issues_category_id` bigint(20) NOT NULL DEFAULT '0',
  `issues_category_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1- placed order 2-need order',
  `related_faq_id` text CHARACTER SET latin1 NOT NULL,
  `title` varchar(100) CHARACTER SET latin1 NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `system_fees`
--

CREATE TABLE IF NOT EXISTS `system_fees` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `system_fee_type_id` smallint(5) DEFAULT NULL,
  `fee_unit` smallint(2) DEFAULT NULL,
  `fee` varchar(250) DEFAULT NULL,
  `status` smallint(1) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `system_fees_setting`
--

CREATE TABLE IF NOT EXISTS `system_fees_setting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `country_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fee_unit` tinyint(1) DEFAULT NULL COMMENT '1-perc 2-flat',
  `fee_title` varchar(50) NOT NULL,
  `fee_value` decimal(10,2) NOT NULL,
  `currency_id` tinyint(3) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `fee_unit` (`fee_unit`),
  KEY `country_id` (`country_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `system_fees_setting`
--

INSERT INTO `system_fees_setting` (`id`, `supplier_id`, `country_id`, `fee_unit`, `fee_title`, `fee_value`, `currency_id`, `status`, `created_on`, `updated_on`, `is_deleted`) VALUES
(1, 1, 77, 1, 'Collection Fee', '2.00', NULL, 1, '2017-03-06 09:22:23', '2017-03-06 09:53:03', 0),
(2, 1, 77, 2, 'Fixed Fee', '30.00', 1, 1, '2017-03-06 09:22:23', '2017-03-06 09:53:12', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `tag_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(30) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`tag_id`, `tag_name`, `is_deleted`) VALUES
(1, 'password', 0),
(2, 'location', 0),
(3, 'email', 0),
(4, 'username', 0),
(5, 'asdas', 0),
(6, 'test', 0),
(7, 'moto', 0),
(8, 'g5', 0),
(9, 'moto g5', 0),
(10, 'ac', 0);

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE IF NOT EXISTS `taxes` (
  `tax_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `tax` varchar(255) NOT NULL,
  `value_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-Percentage, 2-Value',
  `currency_id` tinyint(3) unsigned DEFAULT NULL,
  `geo_zone_id` tinyint(5) unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  `created_on` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`tax_id`),
  KEY `currency_id` (`currency_id`),
  KEY `geo_zone_id` (`geo_zone_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`tax_id`, `tax`, `value_type`, `currency_id`, `geo_zone_id`, `start_date`, `end_date`, `status`, `created_on`, `is_deleted`) VALUES
(1, 'GST', 1, NULL, 1, '2017-05-01', '2018-05-01', 1, '2017-07-17 01:26:23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tax_classes`
--

CREATE TABLE IF NOT EXISTS `tax_classes` (
  `tax_class_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `tax_class` varchar(255) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`tax_class_id`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tax_classes`
--

INSERT INTO `tax_classes` (`tax_class_id`, `tax_class`, `created_on`, `updated_on`, `updated_by`, `is_deleted`) VALUES
(1, 'Indian Sales Tax', '2017-05-17 12:48:07', '2017-07-17 03:31:33', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tax_class_taxes`
--

CREATE TABLE IF NOT EXISTS `tax_class_taxes` (
  `ct_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tax_class_id` smallint(6) unsigned NOT NULL,
  `tax_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `based_on` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-Shipping Address, 2-Payment Address',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`ct_id`),
  UNIQUE KEY `tax_class_id_2` (`tax_class_id`,`tax_id`),
  KEY `tax_class_id` (`tax_class_id`),
  KEY `tax_id` (`tax_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tax_class_taxes`
--

INSERT INTO `tax_class_taxes` (`ct_id`, `tax_class_id`, `tax_id`, `based_on`, `is_deleted`) VALUES
(1, 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tax_status_lookups`
--

CREATE TABLE IF NOT EXISTS `tax_status_lookups` (
  `status_id` tinyint(1) unsigned NOT NULL,
  `status` text NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_status_lookups`
--

INSERT INTO `tax_status_lookups` (`status_id`, `status`) VALUES
(0, 'Disabled'),
(1, 'Enabled');

-- --------------------------------------------------------

--
-- Table structure for table `tax_values`
--

CREATE TABLE IF NOT EXISTS `tax_values` (
  `tax_values_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tax_id` tinyint(3) unsigned NOT NULL,
  `post_type_id` tinyint(2) unsigned NOT NULL,
  `relative_id` text,
  `is_range` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `range_start_from` smallint(10) NOT NULL,
  `range_end_to` smallint(10) NOT NULL,
  `tax_value` smallint(6) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1- Deleted',
  PRIMARY KEY (`tax_values_id`),
  KEY `post_type_id` (`post_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tax_values`
--

INSERT INTO `tax_values` (`tax_values_id`, `tax_id`, `post_type_id`, `relative_id`, `is_range`, `range_start_from`, `range_end_to`, `tax_value`, `created_on`, `is_deleted`) VALUES
(1, 1, 2, '12', 0, 10, 1000, 18, '2017-07-17 01:38:11', 0),
(2, 1, 2, '299', 0, 10, 100, 14, '2017-07-17 02:49:56', 0);

-- --------------------------------------------------------

--
-- Table structure for table `time_zone_lookups`
--

CREATE TABLE IF NOT EXISTS `time_zone_lookups` (
  `time_zone_id` smallint(2) NOT NULL AUTO_INCREMENT,
  `time_zone_name` varchar(30) DEFAULT NULL,
  `time_zone_description` text NOT NULL,
  PRIMARY KEY (`time_zone_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `time_zone_lookups`
--

INSERT INTO `time_zone_lookups` (`time_zone_id`, `time_zone_name`, `time_zone_description`) VALUES
(1, 'UTC', 'Coordinated Universal Time'),
(2, 'ACDT', 'Australian Central Daylight Savings Time'),
(3, 'ACST', 'Australian Central Standard Time');

-- --------------------------------------------------------

--
-- Table structure for table `type_of_business`
--

CREATE TABLE IF NOT EXISTS `type_of_business` (
  `business_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `business` varchar(100) NOT NULL,
  PRIMARY KEY (`business_id`),
  UNIQUE KEY `business` (`business`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `type_of_business`
--

INSERT INTO `type_of_business` (`business_id`, `business`) VALUES
(4, 'Distributor'),
(2, 'Importer'),
(1, 'Manufacturer'),
(5, 'Shopkeeper'),
(3, 'Trader');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE IF NOT EXISTS `units` (
  `unit_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `unit` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Not Deleted, 1-Deleted',
  PRIMARY KEY (`unit_id`),
  UNIQUE KEY `unit` (`unit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `unit`, `description`, `status`, `is_deleted`) VALUES
(1, 'mm', 'MilliMeter', 1, 0),
(2, 'cm', 'CentiMeter', 1, 0),
(3, 'm', 'Meter', 1, 0),
(4, 'rpm', 'Revolution Per Minute', 1, 0),
(5, 'g', 'grams', 1, 0),
(6, 'kg', 'Kilograms', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `verification_status_lookups`
--

CREATE TABLE IF NOT EXISTS `verification_status_lookups` (
  `is_verified` tinyint(1) unsigned NOT NULL,
  `verification` varchar(50) NOT NULL,
  `verification_class` varchar(20) NOT NULL,
  PRIMARY KEY (`is_verified`),
  UNIQUE KEY `verification` (`verification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `verification_status_lookups`
--

INSERT INTO `verification_status_lookups` (`is_verified`, `verification`, `verification_class`) VALUES
(0, 'Not Verified', 'label label-danger'),
(1, 'Verified', 'label label-success');

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE IF NOT EXISTS `wallet` (
  `wallet_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `wallet_code` varchar(20) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-Disabled, 1-Enabled',
  `creditable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-Disabled, 1-Enabled',
  `accessable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-Disabled, 1-Enabled',
  `withdrawal_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-Not Allowed, 1-Allowed',
  `fundtransfer_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-NotAllowed, 1-Allowed',
  `fr_fund_transfer_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-NotAllowed, 1-Allowed',
  `internaltransfer_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-NotAllowed, 1-Allowed',
  `purchase_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0-NotAllowed, 1-Allowed',
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`wallet_id`),
  UNIQUE KEY `wallet_name` (`wallet_code`),
  KEY `status` (`status`),
  KEY `withdrawal_status` (`withdrawal_status`),
  KEY `fundtransfer_status` (`fundtransfer_status`),
  KEY `internaltransfer_status` (`internaltransfer_status`),
  KEY `purchase_status` (`purchase_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`wallet_id`, `wallet_code`, `status`, `creditable`, `accessable`, `withdrawal_status`, `fundtransfer_status`, `fr_fund_transfer_status`, `internaltransfer_status`, `purchase_status`, `created_on`, `updated_on`) VALUES
(1, 'vi-m', 1, 1, 1, 1, 0, 0, 0, 1, '2015-02-10 00:00:00', '2018-07-05 04:51:29'),
(2, 'vi-sp', 1, 1, 1, 1, 1, 0, 1, 1, '2015-03-09 04:17:24', '2018-07-26 11:57:29'),
(3, 'vi-b', 1, 1, 1, 1, 0, 1, 0, 0, '2016-03-23 13:17:10', '2018-07-26 11:58:18'),
(4, 'ngo', 1, 0, 0, 0, 0, 0, 0, 0, NULL, '2018-06-25 10:16:58'),
(5, 'pw', 1, 1, 1, 1, 0, 0, 1, 1, '2018-07-05 08:13:22', '2018-07-05 05:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_lang`
--

CREATE TABLE IF NOT EXISTS `wallet_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wallet_id` int(5) NOT NULL,
  `lang_id` enum('en','spanish','gm','french','bengali','arabic','russian') NOT NULL,
  `wallet` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `wallet_lang`
--

INSERT INTO `wallet_lang` (`id`, `wallet_id`, `lang_id`, `wallet`) VALUES
(1, 1, 'en', 'Vi-Money'),
(2, 2, 'en', 'Vi-Shop'),
(3, 3, 'en', 'Vi-Bonus'),
(4, 4, 'en', 'NGO Wallet'),
(5, 5, 'en', 'Purchase Wallet');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_mst`
--

CREATE TABLE IF NOT EXISTS `withdrawal_mst` (
  `withdrawal_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(150) NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `wallet_id` tinyint(2) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `payment_type_id` tinyint(3) unsigned DEFAULT NULL,
  `amount` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `handleamt` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `handle_perc` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '% Percentage',
  `paidamt` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `tds` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `relation_transaction_id` text,
  `conversion_details` text,
  `account_info` text NOT NULL,
  `paid_proof_info` text,
  `proof_file` varchar(60) DEFAULT NULL,
  `payment_status_id` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - Pending, 1 - Confirmed, 2 - Processing, 3 - Cancelled, 4 - Failed',
  `status_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `reason` text,
  `created_on` datetime DEFAULT NULL,
  `expected_on` date DEFAULT NULL,
  `cancelled_on` datetime DEFAULT NULL,
  `confirmed_on` datetime DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0- Not Deleted,1-Deleted',
  PRIMARY KEY (`withdrawal_id`),
  UNIQUE KEY `transaction_id` (`transaction_id`),
  KEY `account_id` (`account_id`),
  KEY `currency_id` (`currency_id`),
  KEY `wallet_id` (`wallet_id`),
  KEY `status` (`status_id`),
  KEY `payment_status_id` (`payment_status_id`),
  KEY `updated_by` (`updated_by`),
  KEY `payment_type_id` (`payment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_payment_type`
--

CREATE TABLE IF NOT EXISTS `withdrawal_payment_type` (
  `payment_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `description` text CHARACTER SET latin1,
  `charges` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT 'Fees Vary',
  `currency_allowed` text CHARACTER SET latin1,
  `is_country_based` tinyint(1) unsigned NOT NULL COMMENT '0 - Off, 1 - On',
  `is_user_country_based` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-Disabled, 1-Enabled',
  `countries_allowed` text CHARACTER SET latin1,
  `countries_not_allowed` tinytext CHARACTER SET latin1,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0 - InActive, 1 - Active',
  `timeflag` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `withdrawal_payment_type`
--

INSERT INTO `withdrawal_payment_type` (`payment_type_id`, `description`, `charges`, `currency_allowed`, `is_country_based`, `is_user_country_based`, `countries_allowed`, `countries_not_allowed`, `status`, `timeflag`) VALUES
(1, 'Withdraw funds to your PayPal account.', '7%', '1,5,6,9,10,11,12', 0, 0, NULL, NULL, 1, '2017-04-07 02:01:44'),
(4, 'Withdraw funds to your Solid Trust Pay account.', '10%', '1', 0, 0, NULL, NULL, 0, '2017-04-07 01:58:12'),
(5, 'Withdraw funds to your Bitcoin Wallet account.', '7%', '13', 0, 0, NULL, NULL, 1, '2017-04-07 02:03:04'),
(9, 'Coming Soon', 'Fees Vary', NULL, 0, 0, NULL, NULL, 0, '2017-04-07 02:03:32'),
(13, 'The fastest method to withdraw funds, directly to your local bank account! Available in selected countries only.', '10%', '1,2,4,5', 1, 0, '{"1":[183],"2":[77],"4":[104],"5":[152]}', NULL, 1, '2017-04-11 07:01:25'),
(14, 'Withdraw funds directly to your bank account. For countries where Express Withdrawal is unavailable', 'Fees Vary', '1,6,3,7,8,9,10,11,12', 1, 0, ' {"1":[183],"3":[78],"6":[135],"7":[13],"8":[36],"9":[],"10":[],"11":[9],"12":[170]}', '{"2":[77],"4":[104],"5":[152]}', 1, '2017-04-11 07:13:24'),
(16, 'Withdraw funds to your Ko-Kard Wallet account.', '5%', '1', 0, 0, NULL, NULL, 0, '2017-04-07 02:03:20'),
(19, 'Local Money Transfer', '10%', '6', 1, 1, '{"6":[135]}', NULL, 1, '2017-04-11 07:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_settings`
--

CREATE TABLE IF NOT EXISTS `withdrawal_settings` (
  `withdrawal_setting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `payment_type_id` tinyint(3) unsigned DEFAULT NULL,
  `country_id` tinyint(3) unsigned NOT NULL,
  `min_amount` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `max_amount` decimal(12,2) unsigned NOT NULL DEFAULT '1500000.00',
  `is_range` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - OFF,1 - ON (charge based on range or not) ',
  `charges` tinytext,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `charge_in_amount` decimal(12,2) unsigned NOT NULL DEFAULT '0.00',
  `timeflag` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`withdrawal_setting_id`),
  UNIQUE KEY `payment_type_id_2` (`payment_type_id`,`country_id`,`currency_id`),
  KEY `payment_type_id` (`payment_type_id`),
  KEY `country_id` (`country_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `withdrawal_settings`
--

INSERT INTO `withdrawal_settings` (`withdrawal_setting_id`, `payment_type_id`, `country_id`, `min_amount`, `max_amount`, `is_range`, `charges`, `currency_id`, `charge_in_amount`, `timeflag`) VALUES
(1, 13, 77, '1000.00', '1500000.00', 0, 's:41:"{"default":{"charge":10,"charge_type":0}}";', 2, '0.00', '2017-04-12 00:36:19'),
(2, 13, 182, '100.00', '20000.00', 0, 's:41:"{"default":{"charge":10,"charge_type":0}}";', 1, '0.00', '2017-04-12 00:36:37'),
(3, 13, 77, '100.00', '20000.00', 0, 's:41:"{"default":{"charge":10,"charge_type":0}}";', 4, '0.00', '2017-04-12 00:36:37'),
(4, 13, 77, '100.00', '20000.00', 0, 's:41:"{"default":{"charge":10,"charge_type":0}}";', 5, '0.00', '2017-04-12 00:36:37'),
(5, 1, 77, '2500.00', '1500000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 6, '0.00', '2017-04-12 00:37:07'),
(6, 1, 182, '50.00', '20000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 1, '0.00', '2017-04-12 00:37:07'),
(7, 1, 77, '100.00', '20000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 9, '0.00', '2017-04-12 00:37:07'),
(8, 5, 77, '50.00', '20000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 5, '0.00', '2017-04-12 00:39:39'),
(9, 4, 77, '1000.00', '1500000.00', 0, 's:41:"{"default":{"charge":10,"charge_type":0}}";', 2, '0.00', '2016-01-13 08:15:23'),
(10, 4, 182, '100.00', '20000.00', 0, 's:41:"{"default":{"charge":10,"charge_type":0}}";', 1, '0.00', '2016-01-13 08:15:23'),
(11, 4, 77, '100.00', '20000.00', 0, 's:41:"{"default":{"charge":10,"charge_type":0}}";', 4, '0.00', '2016-01-13 08:15:23'),
(12, 4, 77, '100.00', '20000.00', 0, 's:41:"{"default":{"charge":10,"charge_type":0}}";', 5, '0.00', '2016-01-13 08:15:23'),
(13, 14, 77, '1000.00', '1500000.00', 1, 's:94:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":1000,"charge":5,"charge_type":0}}";', 2, '0.00', '2017-04-12 00:38:23'),
(14, 14, 182, '100.00', '20000.00', 1, 's:94:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":1000,"charge":5,"charge_type":0}}";', 1, '0.00', '2017-04-12 00:38:23'),
(15, 14, 77, '100.00', '20000.00', 1, 's:94:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":1000,"charge":5,"charge_type":0}}";', 4, '0.00', '2017-04-12 00:38:23'),
(16, 14, 77, '100.00', '20000.00', 1, 's:94:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":1000,"charge":5,"charge_type":0}}";', 5, '0.00', '2017-04-12 00:38:23'),
(21, 14, 77, '5000.00', '10000000.00', 1, 's:94:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":5000,"charge":5,"charge_type":0}}";', 6, '0.00', '2017-04-12 00:38:23'),
(22, 14, 77, '1500000.00', '10000000.00', 1, 's:97:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":1500000,"charge":5,"charge_type":0}}";', 3, '0.00', '2017-04-12 00:38:23'),
(23, 14, 77, '10000.00', '10000000.00', 1, 's:95:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":10000,"charge":5,"charge_type":0}}";', 7, '0.00', '2017-04-12 00:38:23'),
(24, 14, 77, '750.00', '10000000.00', 1, 's:93:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":750,"charge":5,"charge_type":0}}";', 8, '0.00', '2017-04-12 00:38:23'),
(25, 14, 77, '100.00', '10000000.00', 1, 's:93:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":100,"charge":5,"charge_type":0}}";', 9, '0.00', '2017-04-12 00:38:23'),
(26, 14, 77, '100.00', '10000000.00', 1, 's:93:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":100,"charge":5,"charge_type":0}}";', 10, '0.00', '2017-04-12 00:38:23'),
(27, 14, 77, '150.00', '10000000.00', 1, 's:93:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":150,"charge":5,"charge_type":0}}";', 11, '0.00', '2017-04-12 00:38:23'),
(28, 14, 77, '5000.00', '10000000.00', 1, 's:94:"{"default":{"charge":10,"charge_type":0},"range":{"min_amnt":5000,"charge":5,"charge_type":0}}";', 12, '0.00', '2017-04-12 00:38:23'),
(29, 1, 77, '50.00', '20000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 10, '0.00', '2017-04-12 00:37:07'),
(30, 1, 77, '75.00', '20000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 11, '0.00', '2017-04-12 00:37:07'),
(31, 1, 77, '2500.00', '20000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 12, '0.00', '2017-04-12 00:37:07'),
(32, 16, 182, '50.00', '20000.00', 0, 's:40:"{"default":{"charge":5,"charge_type":0}}";', 1, '0.00', '2017-04-12 00:39:12'),
(33, 5, 182, '0.00', '1500000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 1, '0.00', '2017-04-12 00:40:48'),
(34, 5, 77, '0.00', '1500000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 2, '0.00', '2017-04-12 00:40:48'),
(35, 5, 152, '0.00', '1500000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 5, '0.00', '2017-04-12 00:40:48'),
(36, 5, 36, '0.00', '1500000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 8, '0.00', '2017-04-12 00:40:48'),
(37, 5, 77, '0.00', '1500000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 9, '0.00', '2017-04-12 00:40:48'),
(38, 5, 77, '0.00', '1500000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 10, '0.00', '2017-04-12 00:40:48'),
(39, 5, 9, '0.00', '1500000.00', 0, 's:40:\\"{\\"default\\":{\\"charge\\":7,\\"charge_type\\":0}}\\";', 11, '0.00', '2017-04-12 00:40:48'),
(41, 19, 135, '1000.00', '1500000.00', 0, 's:41:\\"{\\"default\\":{\\"charge\\":10,\\"charge_type\\":0}}\\";', 6, '0.00', '2017-04-12 00:41:25');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_status`
--

CREATE TABLE IF NOT EXISTS `withdrawal_status` (
  `status_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `withdrawal_status`
--

INSERT INTO `withdrawal_status` (`status_id`, `status`) VALUES
(3, 'Cancelled'),
(1, 'Confirmed'),
(4, 'Pending'),
(2, 'Processed');

-- --------------------------------------------------------

--
-- Table structure for table `working_days`
--

CREATE TABLE IF NOT EXISTS `working_days` (
  `working_day_id` tinyint(1) unsigned NOT NULL,
  `working_day_key` varchar(3) NOT NULL,
  PRIMARY KEY (`working_day_id`),
  UNIQUE KEY `working_day_key` (`working_day_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `working_days`
--

INSERT INTO `working_days` (`working_day_id`, `working_day_key`) VALUES
(5, 'fri'),
(1, 'mon'),
(6, 'sat'),
(7, 'sun'),
(4, 'thu'),
(2, 'tue'),
(3, 'wed');

-- --------------------------------------------------------

--
-- Table structure for table `working_days_lang`
--

CREATE TABLE IF NOT EXISTS `working_days_lang` (
  `working_day_id` tinyint(1) unsigned NOT NULL,
  `lang_id` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `days` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`working_day_id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `working_days_lang`
--

INSERT INTO `working_days_lang` (`working_day_id`, `lang_id`, `days`) VALUES
(1, 1, 'Mon'),
(2, 1, 'Tue'),
(3, 1, 'Wed'),
(4, 1, 'Thu'),
(5, 1, 'Fri'),
(6, 1, 'Sat'),
(7, 1, 'Sun');

-- --------------------------------------------------------

--
-- Table structure for table `working_lookups`
--

CREATE TABLE IF NOT EXISTS `working_lookups` (
  `day_id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `day` varchar(15) NOT NULL,
  PRIMARY KEY (`day_id`),
  UNIQUE KEY `day` (`day`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `working_lookups`
--

INSERT INTO `working_lookups` (`day_id`, `day`) VALUES
(6, 'Friday'),
(2, 'Monday'),
(7, 'Saturday'),
(1, 'Sunday'),
(5, 'Thursday'),
(3, 'Tuesday'),
(4, 'Wednesday');

-- --------------------------------------------------------

--
-- Structure for view `category_info`
--
DROP TABLE IF EXISTS `category_info`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `category_info` AS select `pc`.`category_id` AS `category_id`,`pcp`.`parent_category_id` AS `parent_category_id`,`pc`.`category` AS `category`,(select group_concat(`parent`.`url_str` order by `parent`.`category_id` ASC separator '/') from ((`product_categories_parents` `np` join `product_categories_parents` `pp` on(((`np`.`cat_lftnode` between `pp`.`cat_lftnode` and `pp`.`cat_rgtnode`) and (`pp`.`category_id` <> 1) and (`pp`.`is_deleted` = 0)))) join `product_categories` `parent` on(((`parent`.`category_id` = `pp`.`category_id`) and (`parent`.`status` = 1) and (`parent`.`is_deleted` = 0)))) where (`np`.`category_id` = `pc`.`category_id`) order by `pp`.`cat_lftnode`) AS `url`,`pc`.`category_code` AS `category_code` from (`product_categories` `pc` join `product_categories_parents` `pcp` on((`pcp`.`category_id` = `pc`.`category_id`))) where find_in_set(`pc`.`category_id`,(select group_concat(distinct concat(`pcp`.`category_id`,',',`pcp`.`parent_category_id`) separator ',') from (`product_categories_parents` `pcp` join `supplier_product_info` `spi` on((`spi`.`category_id` = `pcp`.`category_id`))))) order by `pc`.`category_id`;

-- --------------------------------------------------------

--
-- Structure for view `local_shipping_charges`
--
DROP TABLE IF EXISTS `local_shipping_charges`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `local_shipping_charges` AS select `spl`.`supplier_product_id` AS `supplier_product_id`,`spl`.`supplier_id` AS `supplier_id`,if((`sp`.`is_ownshipment` = 1),`sp`.`logistic_id`,1) AS `sup_logistic_id`,`spss`.`mode_id` AS `mode_id`,greatest(`pd`.`weight`,(((`pd`.`height` * `pd`.`width`) * `pd`.`length`) / 500)) AS `pro_weight`,`pw`.`weight_slab_title` AS `weight_slab_title`,`pw`.`for_each_grams` AS `for_each_grams`,`spss`.`delivery_charge` AS `for_each_grams_delivery_charge`,cast(((greatest(`pd`.`weight`,(((`pd`.`height` * `pd`.`width`) * `pd`.`length`) / 500)) / `pw`.`for_each_grams`) * `spss`.`delivery_charge`) as decimal(10,3)) AS `delivery_charge`,`spss`.`delivery_days` AS `delivery_days` from (((((`supplier_product_items` `spl` left join `products` `p` on((`p`.`product_id` = `spl`.`product_id`))) left join `product_details` `pd` on(((`pd`.`product_id` = `spl`.`product_id`) and ((`p`.`is_combinations` = 0) or ((`p`.`is_combinations` = 1) and (`pd`.`product_cmb_id` = `spl`.`product_cmb_id`)))))) left join `supplier_preference` `sp` on((`sp`.`supplier_id` = `spl`.`supplier_id`))) left join `product_weight_slab_rates` `pw` on(((`pw`.`min_grams` <= greatest(`pd`.`weight`,(((`pd`.`height` * `pd`.`width`) * `pd`.`length`) / 500))) and (isnull(`pw`.`max_grams`) or ((`pw`.`max_grams` is not null) and (`pw`.`max_grams` >= greatest(`pd`.`weight`,(((`pd`.`height` * `pd`.`width`) * `pd`.`length`) / 500)))))))) left join `supplier_product_shippment_settings` `spss` on(((`spss`.`weight_slab_id` = `pw`.`weight_slab_id`) and (`spss`.`logistic_id` = if((`sp`.`is_ownshipment` = 1),`sp`.`logistic_id`,1))))) order by `spl`.`supplier_product_id`;

-- --------------------------------------------------------

--
-- Structure for view `products_list`
--
DROP TABLE IF EXISTS `products_list`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `products_list` AS select `p`.`product_id` AS `product_id`,`pcmb`.`product_cmb_id` AS `product_cmb_id`,`pd`.`product_code` AS `product_code`,`pd`.`eanbarcode` AS `eanbarcode`,`pd`.`upcbarcode` AS `upcbarcode`,`p`.`category_id` AS `category_id`,`pc`.`category_code` AS `category_code`,`pc`.`category` AS `category`,`pc`.`url_str` AS `category_url_str`,`pc`.`replacement_service_policy_id` AS `replacement_service_policy_id`,`pc`.`status` AS `category_status`,`p`.`assoc_category_id` AS `assoc_category_id`,`p`.`brand_id` AS `brand_id`,`pb`.`brand_name` AS `brand_name`,`pb`.`url_str` AS `brand_url_str`,`pb`.`sku` AS `brand_sku`,`pb`.`status` AS `brand_status`,if((`p`.`is_combinations` = 0),`p`.`product_name`,concat(`p`.`product_name`,' (',`pcmb`.`product_cmb`,')')) AS `product_name`,`pcmb`.`product_cmb` AS `product_cmb`,`pd`.`product_slug` AS `product_slug`,`pd`.`sku` AS `sku`,`pd`.`description` AS `description`,`mrp`.`currency_id` AS `currency_id`,`mrp`.`mrp_price` AS `mrp_price`,`r`.`rating_1` AS `rating_1`,`r`.`rating_2` AS `rating_2`,`r`.`rating_3` AS `rating_3`,`r`.`rating_4` AS `rating_4`,`r`.`rating_5` AS `rating_5`,`r`.`avg_rating` AS `avg_rating`,`r`.`rating_count` AS `rating_count`,`p`.`is_combinations` AS `is_combinations`,`pd`.`visiblity_id` AS `visiblity_id`,`pd`.`redirect_id` AS `redirect_id`,`pd`.`is_verified` AS `is_verified`,`pd`.`is_exclusive` AS `is_exclusive`,`pd`.`width` AS `width`,`pd`.`height` AS `height`,`pd`.`length` AS `length`,`pd`.`weight` AS `weight`,cast((((`pd`.`width` * `pd`.`height`) * `pd`.`length`) / 5000) as decimal(10,3)) AS `volumetric_weight`,`pd`.`status` AS `status`,`pd`.`created_on` AS `created_on`,`pd`.`created_by` AS `created_by`,`pd`.`updated_on` AS `updated_on`,`pd`.`updated_by` AS `updated_by`,`pd`.`verified_on` AS `verified_on`,`pd`.`verified_by` AS `verified_by`,if((`p`.`is_deleted` = 1),`p`.`is_deleted`,if((`pcmb`.`is_deleted` is not null),`pcmb`.`is_deleted`,0)) AS `is_deleted` from ((((((`products` `p` left join `product_combinations` `pcmb` on((`pcmb`.`product_id` = `p`.`product_id`))) left join `product_details` `pd` on(((`pd`.`product_id` = `p`.`product_id`) and (((`p`.`is_combinations` = 0) and isnull(`pd`.`product_cmb_id`)) or ((`p`.`is_combinations` = 1) and (`pd`.`product_cmb_id` = `pcmb`.`product_cmb_id`)))))) left join `product_brands` `pb` on((`pb`.`brand_id` = `p`.`brand_id`))) left join `product_categories` `pc` on((`pc`.`category_id` = `p`.`category_id`))) left join `product_mrp_price` `mrp` on((`mrp`.`product_id` = `p`.`product_id`))) left join `rating` `r` on(((`r`.`relative_post_id` = `p`.`product_id`) and (`r`.`post_type_id` = 3))));

-- --------------------------------------------------------

--
-- Structure for view `product_brand_categories`
--
DROP TABLE IF EXISTS `product_brand_categories`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `product_brand_categories` AS select `p`.`category_id` AS `category_id`,`p`.`brand_id` AS `brand_id` from (`products` `p` join `product_details` `pd` on(((`pd`.`product_id` = `p`.`product_id`) and (`pd`.`is_verified` = 1)))) group by `p`.`category_id`,`p`.`brand_id` order by `p`.`category_id`,`p`.`brand_id`;

-- --------------------------------------------------------

--
-- Structure for view `product_browser_path_by_categories`
--
DROP TABLE IF EXISTS `product_browser_path_by_categories`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `product_browser_path_by_categories` AS select `pc`.`category_id` AS `category_id`,concat((select group_concat(`parent`.`url_str` order by `parent`.`category_id` ASC separator '/') from ((`product_categories_parents` `np` left join `product_categories_parents` `pp` on(((`np`.`cat_lftnode` between `pp`.`cat_lftnode` and `pp`.`cat_rgtnode`) and (`pp`.`category_id` <> 1)))) left join `product_categories` `parent` on((`parent`.`category_id` = `pp`.`category_id`))) where (`np`.`category_id` = `pc`.`category_id`) order by `pp`.`cat_lftnode`),'/br?spath=',`pc`.`category_code`) AS `url` from `product_categories` `pc` order by `pc`.`category_id`;

-- --------------------------------------------------------

--
-- Structure for view `product_discounts`
--
DROP TABLE IF EXISTS `product_discounts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `product_discounts` AS select `spi`.`supplier_product_id` AS `supplier_product_id`,`d`.`discount_id` AS `discount_id`,`d`.`discount` AS `discount`,`d`.`description` AS `description`,`d`.`discount_type_id` AS `discount_type_id`,`dt`.`discount_type` AS `discount_type`,`d`.`discount_by` AS `discount_by`,`d`.`priority` AS `priority`,`dp`.`discount_value_type` AS `discount_value_type`,`dp`.`is_qty_based` AS `is_qty_based`,`dv`.`discount_value` AS `discount_value`,`dv`.`currency_id` AS `currency_id`,`dv`.`min_qty` AS `min_qty`,`dv`.`max_qty` AS `max_qty` from (((((((`supplier_product_items` `spi` join `products` `p` on(((`p`.`product_id` = `spi`.`product_id`) and (`p`.`is_deleted` = 0)))) join `product_countries` `pc` on(((`pc`.`product_id` = `spi`.`product_id`) and (`pc`.`is_deleted` = 0)))) join `product_details` `pd` on(((`pd`.`product_id` = `spi`.`product_id`) and ((`p`.`is_combinations` = 0) or ((`p`.`is_combinations` = 1) and `pd`.`product_cmb_id`))))) join `discounts` `d` on((`d`.`country_id` = `pc`.`country_id`))) join `discount_type_lookups` `dt` on(((`dt`.`discount_type_id` = `d`.`discount_type_id`) and (`d`.`is_deleted` = 0) and (`d`.`status` = 1) and (`d`.`start_date` <= now()) and (`d`.`end_date` >= now())))) join `discount_posts` `dp` on(((`dp`.`discount_id` = `d`.`discount_id`) and (`dp`.`is_deleted` = 0)))) join `discount_value` `dv` on(((`dv`.`dp_id` = `dp`.`dp_id`) and (`dv`.`is_deleted` = 0)))) where ((isnull(`dp`.`product_cmb_ids`) or ((`dp`.`product_cmb_ids` is not null) and find_in_set(`spi`.`product_cmb_id`,`dp`.`product_cmb_ids`))) and (isnull(`dp`.`product_ids`) or ((`dp`.`product_ids` is not null) and find_in_set(`spi`.`product_id`,`dp`.`product_ids`))) and (isnull(`dp`.`supplier_ids`) or ((`dp`.`supplier_ids` is not null) and find_in_set(`spi`.`supplier_id`,`dp`.`supplier_ids`))) and (isnull(`dp`.`category_ids`) or ((`dp`.`category_ids` is not null) and find_in_set(`p`.`category_id`,`dp`.`category_ids`))) and (isnull(`dp`.`brand_ids`) or ((`dp`.`brand_ids` is not null) and find_in_set(`p`.`brand_id`,`dp`.`brand_ids`))));

-- --------------------------------------------------------

--
-- Structure for view `product_info`
--
DROP TABLE IF EXISTS `product_info`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `product_info` AS select `p`.`product_id` AS `product_id`,`pcmb`.`product_cmb_id` AS `product_cmb_id`,`pd`.`product_code` AS `product_code`,`pd`.`eanbarcode` AS `eanbarcode`,`pd`.`upcbarcode` AS `upcbarcode`,`p`.`category_id` AS `category_id`,`pc`.`category_code` AS `category_code`,`pc`.`category` AS `category`,`pc`.`url_str` AS `category_url_str`,`pc`.`replacement_service_policy_id` AS `replacement_service_policy_id`,`p`.`assoc_category_id` AS `assoc_category_id`,`p`.`brand_id` AS `brand_id`,`pb`.`brand_name` AS `brand_name`,`pb`.`url_str` AS `brand_url_str`,`pb`.`sku` AS `brand_sku`,if((`p`.`is_combinations` = 0),`p`.`product_name`,concat(`p`.`product_name`,' (',`pcmb`.`product_cmb`,')')) AS `product_name`,`pcmb`.`product_cmb` AS `product_cmb`,`pd`.`product_slug` AS `product_slug`,`pd`.`sku` AS `sku`,`pd`.`description` AS `description`,`p`.`is_combinations` AS `is_combinations` from ((((`products` `p` left join `product_combinations` `pcmb` on((`pcmb`.`product_id` = `p`.`product_id`))) left join `product_details` `pd` on(((`pd`.`product_id` = `p`.`product_id`) and (((`p`.`is_combinations` = 0) and isnull(`pd`.`product_cmb_id`)) or ((`p`.`is_combinations` = 1) and (`pd`.`product_cmb_id` = `pcmb`.`product_cmb_id`)))))) left join `product_brands` `pb` on((`pb`.`brand_id` = `p`.`brand_id`))) left join `product_categories` `pc` on((`pc`.`category_id` = `p`.`category_id`)));

-- --------------------------------------------------------

--
-- Structure for view `supplier_products_list`
--
DROP TABLE IF EXISTS `supplier_products_list`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `supplier_products_list` AS select `spi`.`supplier_product_id` AS `supplier_product_id`,`pl`.`product_id` AS `product_id`,`pl`.`product_cmb_id` AS `product_cmb_id`,`pl`.`product_code` AS `product_code`,`spi`.`supplier_product_code` AS `supplier_product_code`,`pl`.`eanbarcode` AS `eanbarcode`,`pl`.`upcbarcode` AS `upcbarcode`,`pl`.`category_id` AS `category_id`,`pl`.`category_code` AS `category_code`,`pl`.`category` AS `category`,`pl`.`category_url_str` AS `category_url_str`,`pl`.`replacement_service_policy_id` AS `replacement_service_policy_id`,`pl`.`category_status` AS `category_status`,`pl`.`assoc_category_id` AS `assoc_category_id`,`pl`.`brand_id` AS `brand_id`,`pl`.`brand_name` AS `brand_name`,`pl`.`brand_url_str` AS `brand_url_str`,`pl`.`brand_sku` AS `brand_sku`,`pl`.`brand_status` AS `brand_status`,`pl`.`product_name` AS `product_name`,`pl`.`product_cmb` AS `product_cmb`,`pl`.`product_slug` AS `product_slug`,`pl`.`sku` AS `sku`,`pl`.`description` AS `description`,`pl`.`currency_id` AS `currency_id`,`pl`.`mrp_price` AS `mrp_price`,if((`pl`.`is_combinations` = 1),(`spp`.`price` + `spcp`.`impact_on_price`),`spp`.`price`) AS `price`,`pl`.`rating_1` AS `product_rating_1`,`pl`.`rating_2` AS `product_rating_2`,`pl`.`rating_3` AS `product_rating_3`,`pl`.`rating_4` AS `product_rating_4`,`pl`.`rating_5` AS `product_rating_5`,`pl`.`avg_rating` AS `product_avg_rating`,`pl`.`rating_count` AS `product_rating_count`,`r`.`rating_1` AS `supplier_rating_1`,`r`.`rating_2` AS `supplier_rating_2`,`r`.`rating_3` AS `supplier_rating_3`,`r`.`rating_4` AS `supplier_rating_4`,`r`.`rating_5` AS `supplier_rating_5`,`r`.`avg_rating` AS `supplier_avg_rating`,`r`.`rating_count` AS `supplier_rating_count`,`pl`.`is_combinations` AS `is_combinations`,`pl`.`visiblity_id` AS `visiblity_id`,`pl`.`redirect_id` AS `redirect_id`,`pl`.`is_verified` AS `is_verified`,`pl`.`is_exclusive` AS `is_exclusive`,`pl`.`width` AS `width`,`pl`.`height` AS `height`,`pl`.`length` AS `length`,`pl`.`weight` AS `weight`,`pl`.`volumetric_weight` AS `volumetric_weight`,`pl`.`status` AS `status`,`pl`.`created_on` AS `created_on`,`pl`.`created_by` AS `created_by`,`pl`.`updated_on` AS `updated_on`,`pl`.`updated_by` AS `updated_by`,`pl`.`verified_on` AS `verified_on`,`pl`.`verified_by` AS `verified_by`,`pl`.`is_deleted` AS `is_deleted`,`spi`.`supplier_id` AS `supplier_id`,`spi`.`condition_id` AS `condition_id`,`spi`.`is_existing` AS `is_existing`,`spi`.`is_replaceable` AS `is_replaceable`,`spi`.`is_assured` AS `is_assured`,`spi`.`is_shipping_beared` AS `is_shipping_beared`,`spi`.`is_featured` AS `is_featured`,`spi`.`promote_to_homepage` AS `promote_to_homepage`,`spi`.`status` AS `spi_status`,`spi`.`pre_order` AS `pre_order`,`spi`.`stock_status_id` AS `stock_status_id`,`spi`.`created_on` AS `spi_created_on`,`spi`.`updated_on` AS `spi_updated_on`,`spi`.`updated_by` AS `spi_updated_by`,`spi`.`is_deleted` AS `spi_is_deleted` from ((((`supplier_product_items` `spi` left join `products_list` `pl` on(((`pl`.`product_id` = `spi`.`product_id`) and ((`pl`.`is_combinations` = 0) or ((`pl`.`is_combinations` = 1) and (`pl`.`product_cmb_id` = `spi`.`product_cmb_id`)))))) left join `supplier_product_price` `spp` on(((`spp`.`product_id` = `pl`.`product_id`) and (`spp`.`currency_id` = `pl`.`currency_id`)))) left join `supplier_product_cmb_price` `spcp` on(((`spcp`.`supplier_id` = `spp`.`supplier_id`) and (`spcp`.`product_cmb_id` = `spi`.`product_cmb_id`) and (`spcp`.`currency_id` = `spp`.`currency_id`)))) left join `rating` `r` on(((`r`.`relative_post_id` = `spi`.`supplier_id`) and (`r`.`post_type_id` = 5))));

-- --------------------------------------------------------

--
-- Structure for view `supplier_product_info`
--
DROP TABLE IF EXISTS `supplier_product_info`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `supplier_product_info` AS select `spi`.`supplier_product_id` AS `supplier_product_id`,`pi`.`product_id` AS `product_id`,`pi`.`product_cmb_id` AS `product_cmb_id`,`pi`.`product_code` AS `product_code`,`pi`.`eanbarcode` AS `eanbarcode`,`pi`.`upcbarcode` AS `upcbarcode`,`pi`.`category_id` AS `category_id`,`pi`.`category_code` AS `category_code`,`pi`.`category` AS `category`,`pi`.`category_url_str` AS `category_url_str`,`pi`.`replacement_service_policy_id` AS `replacement_service_policy_id`,`pi`.`assoc_category_id` AS `assoc_category_id`,`pi`.`brand_id` AS `brand_id`,`pi`.`brand_name` AS `brand_name`,`pi`.`brand_url_str` AS `brand_url_str`,`pi`.`brand_sku` AS `brand_sku`,`pi`.`product_name` AS `product_name`,`pi`.`product_cmb` AS `product_cmb`,`pi`.`product_slug` AS `product_slug`,`pi`.`sku` AS `sku`,`pi`.`description` AS `description`,`pi`.`is_combinations` AS `is_combinations`,`spi`.`supplier_id` AS `supplier_id` from (`supplier_product_items` `spi` left join `product_info` `pi` on(((`pi`.`product_id` = `spi`.`product_id`) and ((`pi`.`is_combinations` = 0) or ((`pi`.`is_combinations` = 1) and (`pi`.`product_cmb_id` = `spi`.`product_cmb_id`))))));

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_login_log`
--
ALTER TABLE `account_login_log`
  ADD CONSTRAINT `account_login_log_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `account_login_log_ibfk_2` FOREIGN KEY (`device_log_id`) REFERENCES `device_log` (`device_log_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `account_logistics`
--
ALTER TABLE `account_logistics`
  ADD CONSTRAINT `account_logistics_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `account_notifications_read`
--
ALTER TABLE `account_notifications_read`
  ADD CONSTRAINT `account_notifications_read_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `account_notifications` (`notification_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `account_notifications_read_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `account_ratings`
--
ALTER TABLE `account_ratings`
  ADD CONSTRAINT `account_ratings_ibfk_1` FOREIGN KEY (`post_type_id`) REFERENCES `post_type_lookups` (`post_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `account_ratings_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `account_sale_points`
--
ALTER TABLE `account_sale_points`
  ADD CONSTRAINT `account_sale_points_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account_mst` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `account_transaction`
--
ALTER TABLE `account_transaction`
  ADD CONSTRAINT `account_transaction_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `account_transaction_ibfk_3` FOREIGN KEY (`payment_type_id`) REFERENCES `payment_types` (`payment_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `account_transaction_ibfk_4` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `account_transaction_ibfk_5` FOREIGN KEY (`wallet_id`) REFERENCES `wallet` (`wallet_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `account_transaction_ibfk_6` FOREIGN KEY (`statementline_id`) REFERENCES `statement_line` (`statementline_id`) ON UPDATE CASCADE;

--
-- Constraints for table `account_verification`
--
ALTER TABLE `account_verification`
  ADD CONSTRAINT `account_verification_ibfk_2` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`document_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `account_wallet_balance`
--
ALTER TABLE `account_wallet_balance`
  ADD CONSTRAINT `account_wallet_balance_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `account_wallet_balance_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `account_wallet_balance_ibfk_3` FOREIGN KEY (`wallet_id`) REFERENCES `wallet` (`wallet_id`) ON UPDATE CASCADE;

--
-- Constraints for table `account_wish_list`
--
ALTER TABLE `account_wish_list`
  ADD CONSTRAINT `account_wish_list_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `add_fund_mst`
--
ALTER TABLE `add_fund_mst`
  ADD CONSTRAINT `add_fund_mst_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `add_fund_mst_ibfk_2` FOREIGN KEY (`payment_type_id`) REFERENCES `payment_types` (`payment_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `add_fund_mst_ibfk_3` FOREIGN KEY (`wallet_id`) REFERENCES `wallet` (`wallet_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `add_fund_mst_ibfk_4` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `add_fund_mst_ibfk_5` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `admin_mst`
--
ALTER TABLE `admin_mst`
  ADD CONSTRAINT `admin_mst_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`);

--
-- Constraints for table `cashfree_transaction`
--
ALTER TABLE `cashfree_transaction`
  ADD CONSTRAINT `cashfree_transaction_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `currency_exchange_settings`
--
ALTER TABLE `currency_exchange_settings`
  ADD CONSTRAINT `currency_exchange_settings_ibfk_1` FOREIGN KEY (`from_currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `currency_exchange_settings_ibfk_2` FOREIGN KEY (`to_currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `discount_posts`
--
ALTER TABLE `discount_posts`
  ADD CONSTRAINT `discount_posts_ibfk_1` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`discount_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `discount_value`
--
ALTER TABLE `discount_value`
  ADD CONSTRAINT `discount_value_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `discount_value_ibfk_3` FOREIGN KEY (`dp_id`) REFERENCES `discount_posts` (`dp_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `imgs`
--
ALTER TABLE `imgs`
  ADD CONSTRAINT `imgs_ibfk_1` FOREIGN KEY (`img_type`) REFERENCES `img_type_settings` (`img_type_id`),
  ADD CONSTRAINT `imgs_ibfk_2` FOREIGN KEY (`post_type_id`) REFERENCES `post_type_lookups` (`post_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imgs_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `img_filters`
--
ALTER TABLE `img_filters`
  ADD CONSTRAINT `img_filters_ibfk_1` FOREIGN KEY (`img_id`) REFERENCES `imgs` (`img_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `img_filters_ibfk_2` FOREIGN KEY (`post_type_id`) REFERENCES `post_type_lookups` (`post_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `img_settings`
--
ALTER TABLE `img_settings`
  ADD CONSTRAINT `img_settings_ibfk_1` FOREIGN KEY (`img_type_id`) REFERENCES `img_type_settings` (`img_type_id`),
  ADD CONSTRAINT `img_settings_ibfk_2` FOREIGN KEY (`post_type_id`) REFERENCES `post_type_lookups` (`post_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `kyc_documents`
--
ALTER TABLE `kyc_documents`
  ADD CONSTRAINT `kyc_documents_ibfk_1` FOREIGN KEY (`id_proof_document_type_id`) REFERENCES `document_types` (`document_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `location_countries`
--
ALTER TABLE `location_countries`
  ADD CONSTRAINT `location_countries_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `location_districts`
--
ALTER TABLE `location_districts`
  ADD CONSTRAINT `location_districts_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `location_states` (`state_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `location_regions`
--
ALTER TABLE `location_regions`
  ADD CONSTRAINT `location_regions_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `location_countries` (`country_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `location_states`
--
ALTER TABLE `location_states`
  ADD CONSTRAINT `location_states_ibfk_3` FOREIGN KEY (`region_id`) REFERENCES `location_regions` (`region_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `location_states_ibfk_4` FOREIGN KEY (`country_id`) REFERENCES `location_countries` (`country_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `logistics_handling_pincodes`
--
ALTER TABLE `logistics_handling_pincodes`
  ADD CONSTRAINT `logistics_handling_pincodes_ibfk_1` FOREIGN KEY (`pincode_id`) REFERENCES `location_pincodes` (`pincode_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `logistics_handling_pincodes_ibfk_2` FOREIGN KEY (`logistic_id`) REFERENCES `account_logistics` (`logistic_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `logistics_product_avaliablity`
--
ALTER TABLE `logistics_product_avaliablity`
  ADD CONSTRAINT `logistics_product_avaliablity_ibfk_1` FOREIGN KEY (`lhp_id`) REFERENCES `logistics_handling_pincodes` (`lhp_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `logistics_product_avaliablity_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `meta_info`
--
ALTER TABLE `meta_info`
  ADD CONSTRAINT `meta_info_ibfk_1` FOREIGN KEY (`post_type_id`) REFERENCES `post_type_lookups` (`post_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_approval_status_notification`
--
ALTER TABLE `order_approval_status_notification`
  ADD CONSTRAINT `order_approval_status_notification_ibfk_1` FOREIGN KEY (`oass_id`) REFERENCES `order_approval_status_settings` (`oass_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_approval_status_notification_ibfk_2` FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`approval_status_id`) REFERENCES `order_approval_status` (`approval_status_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`order_item_status_id`) REFERENCES `order_status_lookup` (`order_status_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_4` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_mst` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_5` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_6` FOREIGN KEY (`sub_order_id`) REFERENCES `sub_orders` (`sub_order_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_7` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_8` FOREIGN KEY (`supplier_product_id`) REFERENCES `supplier_product_items` (`supplier_product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_sales_commissioin_payments`
--
ALTER TABLE `order_sales_commissioin_payments`
  ADD CONSTRAINT `order_sales_commissioin_payments_ibfk_1` FOREIGN KEY (`osc_id`) REFERENCES `order_sales_commission` (`osc_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_sales_commissioin_payments_ibfk_2` FOREIGN KEY (`supplier_payment_status_id`) REFERENCES `payment_status_lookups` (`payment_status_id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_sales_commission`
--
ALTER TABLE `order_sales_commission`
  ADD CONSTRAINT `order_sales_commission_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_sales_commission_ibfk_2` FOREIGN KEY (`sub_order_id`) REFERENCES `sub_orders` (`sub_order_id`),
  ADD CONSTRAINT `order_sales_commission_ibfk_3` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`order_item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_sales_commission_ibfk_4` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_mst` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_sales_commission_ibfk_6` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_status_account_type_messages`
--
ALTER TABLE `order_status_account_type_messages`
  ADD CONSTRAINT `order_status_account_type_messages_ibfk_1` FOREIGN KEY (`order_status_id`) REFERENCES `order_status_lookup` (`order_status_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_status_account_type_messages_ibfk_2` FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_status_notification`
--
ALTER TABLE `order_status_notification`
  ADD CONSTRAINT `order_status_notification_ibfk_1` FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_status_notification_ibfk_2` FOREIGN KEY (`oss_id`) REFERENCES `order_status_settings` (`oss_id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_status_settings`
--
ALTER TABLE `order_status_settings`
  ADD CONSTRAINT `order_status_settings_ibfk_1` FOREIGN KEY (`from_order_status_id`) REFERENCES `order_status_lookup` (`order_status_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_status_settings_ibfk_2` FOREIGN KEY (`to_order_status_id`) REFERENCES `order_status_lookup` (`order_status_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_status_settings_ibfk_3` FOREIGN KEY (`account_type_id`) REFERENCES `account_types` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `product_brands` (`brand_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_brands`
--
ALTER TABLE `product_brands`
  ADD CONSTRAINT `product_brands_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`);

--
-- Constraints for table `product_category_assoc`
--
ALTER TABLE `product_category_assoc`
  ADD CONSTRAINT `product_category_assoc_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `product_category_assoc_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_category_properties`
--
ALTER TABLE `product_category_properties`
  ADD CONSTRAINT `product_category_properties_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`),
  ADD CONSTRAINT `product_category_properties_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `product_property_keys` (`property_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_category_property_values`
--
ALTER TABLE `product_category_property_values`
  ADD CONSTRAINT `product_category_property_values_ibfk_1` FOREIGN KEY (`value_id`) REFERENCES `product_property_key_values` (`value_id`),
  ADD CONSTRAINT `product_category_property_values_ibfk_2` FOREIGN KEY (`category_property_id`) REFERENCES `product_category_properties` (`category_property_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_cmb_properties`
--
ALTER TABLE `product_cmb_properties`
  ADD CONSTRAINT `product_cmb_properties_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `product_property_keys` (`property_id`),
  ADD CONSTRAINT `product_cmb_properties_ibfk_2` FOREIGN KEY (`value_id`) REFERENCES `product_property_key_values` (`value_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `product_cmb_properties_ibfk_3` FOREIGN KEY (`product_cmb_id`) REFERENCES `product_combinations` (`product_cmb_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_combinations`
--
ALTER TABLE `product_combinations`
  ADD CONSTRAINT `product_combinations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_details`
--
ALTER TABLE `product_details`
  ADD CONSTRAINT `product_details_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `product_details_ibfk_3` FOREIGN KEY (`product_cmb_id`) REFERENCES `product_combinations` (`product_cmb_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_mrp_price`
--
ALTER TABLE `product_mrp_price`
  ADD CONSTRAINT `product_mrp_price_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `product_mrp_price_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_properties`
--
ALTER TABLE `product_properties`
  ADD CONSTRAINT `product_properties_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `product_properties_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `product_property_keys` (`property_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_property_keys`
--
ALTER TABLE `product_property_keys`
  ADD CONSTRAINT `product_property_keys_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`),
  ADD CONSTRAINT `product_property_keys_ibfk_2` FOREIGN KEY (`parent_property_id`) REFERENCES `product_property_keys` (`property_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_property_key_values`
--
ALTER TABLE `product_property_key_values`
  ADD CONSTRAINT `product_property_key_values_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`),
  ADD CONSTRAINT `product_property_key_values_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `product_property_keys` (`property_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_property_values`
--
ALTER TABLE `product_property_values`
  ADD CONSTRAINT `product_property_values_ibfk_1` FOREIGN KEY (`pp_id`) REFERENCES `product_properties` (`pp_id`),
  ADD CONSTRAINT `product_property_values_ibfk_2` FOREIGN KEY (`value_id`) REFERENCES `product_property_key_values` (`value_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_tag`
--
ALTER TABLE `product_tag`
  ADD CONSTRAINT `product_tag_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `product_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON UPDATE CASCADE;

--
-- Constraints for table `rating`
--
ALTER TABLE `rating`
  ADD CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`post_type_id`) REFERENCES `post_type_lookups` (`post_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD CONSTRAINT `site_settings_ibfk_3` FOREIGN KEY (`site_currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `site_settings_ibfk_4` FOREIGN KEY (`site_language_id`) REFERENCES `language_lookups` (`language_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `site_silder_blocks`
--
ALTER TABLE `site_silder_blocks`
  ADD CONSTRAINT `site_silder_blocks_ibfk_1` FOREIGN KEY (`supplier_product_id`) REFERENCES `supplier_product_items` (`supplier_product_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `site_silder_blocks_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `site_silder_blocks_ibfk_4` FOREIGN KEY (`img_id`) REFERENCES `imgs` (`img_id`) ON UPDATE CASCADE;

--
-- Constraints for table `stores`
--
ALTER TABLE `stores`
  ADD CONSTRAINT `stores_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_mst` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stores_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `store_extras`
--
ALTER TABLE `store_extras`
  ADD CONSTRAINT `store_extras_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `location_city` (`city_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_orders`
--
ALTER TABLE `sub_orders`
  ADD CONSTRAINT `sub_orders_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `sub_orders_ibfk_2` FOREIGN KEY (`approval_status_id`) REFERENCES `order_approval_status` (`approval_status_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_orders_ibfk_3` FOREIGN KEY (`sub_order_status_id`) REFERENCES `order_status_lookup` (`order_status_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_orders_ibfk_4` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_mst` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_orders_ibfk_5` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_orders_ibfk_6` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplier_brand_associate`
--
ALTER TABLE `supplier_brand_associate`
  ADD CONSTRAINT `supplier_brand_associate_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `product_brands` (`brand_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_brand_associate_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_mst` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_brand_associate_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplier_category_associate`
--
ALTER TABLE `supplier_category_associate`
  ADD CONSTRAINT `supplier_category_associate_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplier_commissions_settings`
--
ALTER TABLE `supplier_commissions_settings`
  ADD CONSTRAINT `supplier_commissions_settings_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_mst` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_commissions_settings_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_commissions_settings_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_commissions_settings_ibfk_4` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplier_flexible_commissions`
--
ALTER TABLE `supplier_flexible_commissions`
  ADD CONSTRAINT `supplier_flexible_commissions_ibfk_1` FOREIGN KEY (`post_type_id`) REFERENCES `post_type_lookups` (`post_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_flexible_commissions_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_mst` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_flexible_commissions_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_flexible_commissions_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplier_mst`
--
ALTER TABLE `supplier_mst`
  ADD CONSTRAINT `supplier_mst_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_mst_ibfk_3` FOREIGN KEY (`type_of_bussiness`) REFERENCES `type_of_business` (`business_id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplier_payment_settings`
--
ALTER TABLE `supplier_payment_settings`
  ADD CONSTRAINT `supplier_payment_settings_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_mst` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_payment_settings_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplier_product_cmb_price`
--
ALTER TABLE `supplier_product_cmb_price`
  ADD CONSTRAINT `supplier_product_cmb_price_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_mst` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_product_cmb_price_ibfk_2` FOREIGN KEY (`product_cmb_id`) REFERENCES `product_combinations` (`product_cmb_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_product_cmb_price_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplier_product_items`
--
ALTER TABLE `supplier_product_items`
  ADD CONSTRAINT `supplier_product_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `supplier_product_price`
--
ALTER TABLE `supplier_product_price`
  ADD CONSTRAINT `supplier_product_price_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplier_product_stock_management`
--
ALTER TABLE `supplier_product_stock_management`
  ADD CONSTRAINT `supplier_product_stock_management_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `supplier_product_stock_management_ibfk_2` FOREIGN KEY (`product_cmb_id`) REFERENCES `product_combinations` (`product_cmb_id`) ON UPDATE CASCADE;

--
-- Constraints for table `taxes`
--
ALTER TABLE `taxes`
  ADD CONSTRAINT `taxes_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `taxes_ibfk_2` FOREIGN KEY (`status`) REFERENCES `tax_status_lookups` (`status_id`);

--
-- Constraints for table `tax_classes`
--
ALTER TABLE `tax_classes`
  ADD CONSTRAINT `tax_classes_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tax_class_taxes`
--
ALTER TABLE `tax_class_taxes`
  ADD CONSTRAINT `tax_class_taxes_ibfk_1` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`tax_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tax_class_taxes_ibfk_2` FOREIGN KEY (`tax_class_id`) REFERENCES `tax_classes` (`tax_class_id`) ON UPDATE CASCADE;

--
-- Constraints for table `withdrawal_mst`
--
ALTER TABLE `withdrawal_mst`
  ADD CONSTRAINT `withdrawal_mst_ibfk_1` FOREIGN KEY (`wallet_id`) REFERENCES `wallet` (`wallet_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `withdrawal_mst_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `withdrawal_mst_ibfk_3` FOREIGN KEY (`account_id`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `withdrawal_mst_ibfk_4` FOREIGN KEY (`payment_type_id`) REFERENCES `withdrawal_payment_type` (`payment_type_id`),
  ADD CONSTRAINT `withdrawal_mst_ibfk_5` FOREIGN KEY (`updated_by`) REFERENCES `account_details` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `withdrawal_mst_ibfk_6` FOREIGN KEY (`payment_status_id`) REFERENCES `payment_status_lookups` (`payment_status_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `withdrawal_mst_ibfk_7` FOREIGN KEY (`status_id`) REFERENCES `withdrawal_status` (`status_id`) ON UPDATE CASCADE;

--
-- Constraints for table `withdrawal_payment_type`
--
ALTER TABLE `withdrawal_payment_type`
  ADD CONSTRAINT `withdrawal_payment_type_ibfk_1` FOREIGN KEY (`payment_type_id`) REFERENCES `payment_types` (`payment_type_id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
