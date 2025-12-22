-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3307
-- 生成日期： 2025-12-21 14:13:01
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `pet_boarding_1`
--

DELIMITER $$
--
-- 存储过程
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_UpdateEmployeeID` (IN `old_id` INT, IN `new_id` INT)   BEGIN
  IF EXISTS (SELECT 1 FROM `Employee` WHERE `EmployeeID` = new_id) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '新员工ID已存在';
  END IF;

  UPDATE `Employee`
    SET `EmployeeID` = new_id
    WHERE `EmployeeID` = old_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `cat`
--

CREATE TABLE `cat` (
  `PetID` int(11) NOT NULL,
  `FurLength` enum('短毛','中毛','长毛') NOT NULL,
  `Personality` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `cat`
--

INSERT INTO `cat` (`PetID`, `FurLength`, `Personality`) VALUES
(300003, '短毛', '活泼'),
(300004, '中毛', '温顺');

-- --------------------------------------------------------

--
-- 表的结构 `customer`
--

CREATE TABLE `customer` (
  `CustomerID` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `Name` varchar(45) NOT NULL,
  `Gender` enum('男','女') NOT NULL,
  `Contact` varchar(45) NOT NULL,
  `Address` varchar(45) NOT NULL,
  `MemberLevel` enum('普通会员','银卡会员','金卡会员') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `customer`
--

INSERT INTO `customer` (`CustomerID`, `user_id`, `Name`, `Gender`, `Contact`, `Address`, `MemberLevel`) VALUES
(200001, NULL, '赵银', '男', '15000001111', '成都市锦江区春熙路街道红星路三段128号附3号', '银卡会员'),
(200002, NULL, '钱朴', '男', '15000002222', '成都市金牛区荷花池街道北站西一路100号附8号', '普通会员'),
(200003, NULL, '孙津', '男', '15000003333', '成都市望江路街道郭家桥北街5号附10号', '金卡会员'),
(200004, NULL, '李芜', '男', '15000004444', '成都市青羊区少城街道小南街88号附2号', NULL);

--
-- 触发器 `customer`
--
DELIMITER $$
CREATE TRIGGER `trg_before_customer_delete` BEFORE DELETE ON `customer` FOR EACH ROW BEGIN
  IF OLD.`MemberLevel` = '金卡会员' THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = '金卡会员不能被删除';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `dog`
--

CREATE TABLE `dog` (
  `PetID` int(11) NOT NULL,
  `DogBreedType` enum('大型犬','中型犬','小型犬') NOT NULL,
  `TrainingLevel` enum('未训练','基础训练','高级训练') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `dog`
--

INSERT INTO `dog` (`PetID`, `DogBreedType`, `TrainingLevel`) VALUES
(300001, '中型犬', '基础训练'),
(300002, '大型犬', '高级训练');

-- --------------------------------------------------------

--
-- 表的结构 `employee`
--

CREATE TABLE `employee` (
  `EmployeeID` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `Name` varchar(45) NOT NULL,
  `Gender` enum('男','女') NOT NULL,
  `Position` varchar(45) NOT NULL,
  `Contact` varchar(45) NOT NULL,
  `HireDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `employee`
--

INSERT INTO `employee` (`EmployeeID`, `user_id`, `Name`, `Gender`, `Position`, `Contact`, `HireDate`) VALUES
(100001, NULL, '户里一', '男', '护理员', '13800001111', '2023-01-10'),
(100002, NULL, '户里二', '女', '护理员', '13800002222', '2023-02-15'),
(100003, NULL, '寿依', '男', '兽医', '13800003333', '2023-03-20'),
(100004, NULL, '户里三', '女', '护理员', '13800004444', '2023-04-16'),
(100005, NULL, '荀恋', '女', '宠物行为训练师', '13800005555', '2023-05-06'),
(100006, NULL, '营扬诗', '男', '营养师', '13800006666', '2023-03-26');

--
-- 触发器 `employee`
--
DELIMITER $$
CREATE TRIGGER `trg_sync_empid` AFTER UPDATE ON `employee` FOR EACH ROW BEGIN
  UPDATE `Order_Employee`
    SET `EmployeeID` = NEW.`EmployeeID`
    WHERE `EmployeeID` = OLD.`EmployeeID`;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `fosterorder`
--

CREATE TABLE `fosterorder` (
  `OrderID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `PetID` int(11) NOT NULL,
  `ServiceID` int(11) NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL,
  `OrderStatus` enum('未支付','已支付') NOT NULL,
  `PaymentAmount` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `fosterorder`
--

INSERT INTO `fosterorder` (`OrderID`, `CustomerID`, `PetID`, `ServiceID`, `StartTime`, `EndTime`, `OrderStatus`, `PaymentAmount`) VALUES
(700001, 200001, 300001, 400002, '2025-03-20 10:00:00', '2025-03-21 10:00:00', '未支付', 60.00),
(700002, 200002, 300002, 500003, '2025-03-16 14:00:00', '2025-03-20 14:00:00', '已支付', 640.00),
(700003, 200003, 300003, 400004, '2025-03-02 09:00:00', '2025-03-05 09:00:00', '已支付', 90.00),
(700004, 200004, 300004, 400004, '2025-02-02 09:00:00', '2025-02-09 09:00:00', '已支付', 210.00);

--
-- 触发器 `fosterorder`
--
DELIMITER $$
CREATE TRIGGER `trg_calc_payment` BEFORE INSERT ON `fosterorder` FOR EACH ROW BEGIN
  DECLARE v_secs INT;
  DECLARE v_days INT;
  DECLARE v_price DECIMAL(8,2);

  IF NEW.`EndTime` <= NEW.`StartTime` THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '结束时间必须晚于开始时间';
  END IF;

  SET v_secs = TIMESTAMPDIFF(SECOND, NEW.`StartTime`, NEW.`EndTime`);
  SET v_days = CEIL(v_secs / 86400);

  SELECT `Price` INTO v_price
  FROM `FosterService`
  WHERE `ServiceID` = NEW.`ServiceID`;

  SET NEW.`PaymentAmount` = v_days * v_price;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `fosterservice`
--

CREATE TABLE `fosterservice` (
  `ServiceID` int(11) NOT NULL,
  `ServiceType` enum('普通寄养','豪华寄养') NOT NULL,
  `PetCategory` enum('猫','小型犬','中型犬','大型犬') NOT NULL,
  `Price` decimal(8,2) NOT NULL,
  `Duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `fosterservice`
--

INSERT INTO `fosterservice` (`ServiceID`, `ServiceType`, `PetCategory`, `Price`, `Duration`) VALUES
(400001, '普通寄养', '小型犬', 40.00, 1),
(400002, '普通寄养', '中型犬', 60.00, 1),
(400003, '普通寄养', '大型犬', 80.00, 1),
(400004, '普通寄养', '猫', 30.00, 1),
(500001, '豪华寄养', '小型犬', 80.00, 1),
(500002, '豪华寄养', '中型犬', 120.00, 1),
(500003, '豪华寄养', '大型犬', 160.00, 1),
(500004, '豪华寄养', '猫', 80.00, 1);

-- --------------------------------------------------------

--
-- 表的结构 `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1766246710),
('m130524_201442_init', 1766246746),
('m190124_110200_add_verification_token_column_to_user_table', 1766246746);

-- --------------------------------------------------------

--
-- 表的结构 `order_employee`
--

CREATE TABLE `order_employee` (
  `OrderID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `order_employee`
--

INSERT INTO `order_employee` (`OrderID`, `EmployeeID`) VALUES
(700001, 100001),
(700001, 100002),
(700001, 100003),
(700001, 100004),
(700002, 100001),
(700002, 100002),
(700002, 100003),
(700002, 100004),
(700002, 100005),
(700003, 100001),
(700003, 100002),
(700003, 100003),
(700003, 100004),
(700004, 100001),
(700004, 100002),
(700004, 100003),
(700004, 100004),
(700004, 100005);

-- --------------------------------------------------------

--
-- 表的结构 `pet`
--

CREATE TABLE `pet` (
  `PetID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `PetName` varchar(45) NOT NULL,
  `Gender` enum('公','母') NOT NULL,
  `AgeYears` int(11) NOT NULL DEFAULT 0,
  `AgeMonths` int(11) NOT NULL DEFAULT 0,
  `HealthStatus` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `pet`
--

INSERT INTO `pet` (`PetID`, `CustomerID`, `PetName`, `Gender`, `AgeYears`, `AgeMonths`, `HealthStatus`) VALUES
(300001, 200001, '小白', '母', 0, 9, '健康'),
(300002, 200002, '大黄', '公', 2, 0, '良好'),
(300003, 200003, '汤圆', '母', 5, 4, '健康'),
(300004, 200004, '饺子', '公', 5, 6, '生病：感冒');

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT 10,
  `role` varchar(20) NOT NULL DEFAULT 'customer' COMMENT 'admin|employee|customer',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `verification_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `role`, `created_at`, `updated_at`, `verification_token`) VALUES
(1, 'root', 'pc2y1enUCilB-THcSc1-gW9uVxbDlmzQ', '$2y$13$p0OKROuswL63ythhT3ph8.GDwOrT6BG6eAg.zKhQuRmZ9fsh0dmMa', NULL, '2313501@mail.nankai.edu.cn', 10, 'admin', 1766246808, 1766246808, 'rEUqxDQvOYobwjE-VIzRCRw1x-LRQo8-_1766246808');

-- --------------------------------------------------------

--
-- 替换视图以便查看 `v_orderinfo`
-- （参见下面的实际视图）
--
CREATE TABLE `v_orderinfo` (
`OrderID` int(11)
,`CustomerID` int(11)
,`PetID` int(11)
,`ServiceID` int(11)
,`EmployeeID` int(11)
,`StartTime` datetime
,`EndTime` datetime
,`OrderStatus` enum('未支付','已支付')
,`PaymentAmount` decimal(8,2)
);

-- --------------------------------------------------------

--
-- 视图结构 `v_orderinfo`
--
DROP TABLE IF EXISTS `v_orderinfo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_orderinfo`  AS SELECT `f`.`OrderID` AS `OrderID`, `f`.`CustomerID` AS `CustomerID`, `f`.`PetID` AS `PetID`, `f`.`ServiceID` AS `ServiceID`, `oe`.`EmployeeID` AS `EmployeeID`, `f`.`StartTime` AS `StartTime`, `f`.`EndTime` AS `EndTime`, `f`.`OrderStatus` AS `OrderStatus`, `f`.`PaymentAmount` AS `PaymentAmount` FROM (`fosterorder` `f` left join `order_employee` `oe` on(`f`.`OrderID` = `oe`.`OrderID`)) ;

--
-- 转储表的索引
--

--
-- 表的索引 `cat`
--
ALTER TABLE `cat`
  ADD PRIMARY KEY (`PetID`);

--
-- 表的索引 `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustomerID`),
  ADD UNIQUE KEY `uk_customer_user_id` (`user_id`);

--
-- 表的索引 `dog`
--
ALTER TABLE `dog`
  ADD PRIMARY KEY (`PetID`);

--
-- 表的索引 `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`EmployeeID`),
  ADD UNIQUE KEY `uk_employee_user_id` (`user_id`);

--
-- 表的索引 `fosterorder`
--
ALTER TABLE `fosterorder`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `idx_Order_Customer` (`CustomerID`),
  ADD KEY `idx_Order_Pet` (`PetID`),
  ADD KEY `idx_Order_Service` (`ServiceID`);

--
-- 表的索引 `fosterservice`
--
ALTER TABLE `fosterservice`
  ADD PRIMARY KEY (`ServiceID`);

--
-- 表的索引 `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- 表的索引 `order_employee`
--
ALTER TABLE `order_employee`
  ADD PRIMARY KEY (`OrderID`,`EmployeeID`),
  ADD KEY `idx_OE_Order` (`OrderID`),
  ADD KEY `idx_OE_Employee` (`EmployeeID`);

--
-- 表的索引 `pet`
--
ALTER TABLE `pet`
  ADD PRIMARY KEY (`PetID`),
  ADD KEY `idx_Pet_Customer` (`CustomerID`);

--
-- 表的索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 限制导出的表
--

--
-- 限制表 `cat`
--
ALTER TABLE `cat`
  ADD CONSTRAINT `fk_Cat_Pet` FOREIGN KEY (`PetID`) REFERENCES `pet` (`PetID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `fk_customer_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- 限制表 `dog`
--
ALTER TABLE `dog`
  ADD CONSTRAINT `fk_Dog_Pet` FOREIGN KEY (`PetID`) REFERENCES `pet` (`PetID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `fk_employee_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- 限制表 `fosterorder`
--
ALTER TABLE `fosterorder`
  ADD CONSTRAINT `fk_Order_Customer` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Order_Pet` FOREIGN KEY (`PetID`) REFERENCES `pet` (`PetID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Order_Service` FOREIGN KEY (`ServiceID`) REFERENCES `fosterservice` (`ServiceID`) ON UPDATE CASCADE;

--
-- 限制表 `order_employee`
--
ALTER TABLE `order_employee`
  ADD CONSTRAINT `fk_OE_Employee` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_OE_Order` FOREIGN KEY (`OrderID`) REFERENCES `fosterorder` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `pet`
--
ALTER TABLE `pet`
  ADD CONSTRAINT `fk_Pet_Customer` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
