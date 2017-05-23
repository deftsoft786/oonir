-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2016 at 01:53 PM
-- Server version: 5.6.24
-- PHP Version: 5.5.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `c254888_transport`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`, `created_date`) VALUES
(1, 'admin@admin.com', 'admin123', '2016-01-09 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `load_type`
--

CREATE TABLE IF NOT EXISTS `load_type` (
  `id` int(11) NOT NULL,
  `load_type` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `load_type`
--

INSERT INTO `load_type` (`id`, `load_type`, `created_date`) VALUES
(1, 'Commercial', '2016-01-04 00:00:00'),
(2, 'Industry', '2016-01-04 00:00:00'),
(3, 'Private', '2016-01-04 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE IF NOT EXISTS `rating` (
  `rat_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `transporter_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `truck_chat`
--

CREATE TABLE IF NOT EXISTS `truck_chat` (
  `chat_id` int(11) NOT NULL,
  `send_by` int(11) NOT NULL,
  `send_to` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `truck_list`
--

CREATE TABLE IF NOT EXISTS `truck_list` (
  `truck_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `truck_type` varchar(50) NOT NULL,
  `capacity` bigint(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `pickup_location` text NOT NULL,
  `dropup_location` text NOT NULL,
  `driver_name` varchar(50) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `registration_no` varchar(50) NOT NULL,
  `inbetween_location` text NOT NULL,
  `truck_code` varchar(50) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0=>not_active,1=>active',
  `created_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `truck_list`
--

INSERT INTO `truck_list` (`truck_id`, `user_id`, `truck_type`, `capacity`, `model`, `image`, `pickup_location`, `dropup_location`, `driver_name`, `contact_number`, `registration_no`, `inbetween_location`, `truck_code`, `status`, `created_date`) VALUES
(1, 2, 'Canters', 50, '2016', '', 'Chandigarh, India', 'Lucknow, Uttar Pradesh, India', 'D', '963258561423', 'Qw', 'Saharanpur, Uttar Pradesh, India', 'TK-9Wc6', 1, '2016-01-13 05:22:40'),
(2, 2, 'Jumbo Canter', 45, '2015', '5695e162616f1_img.image', 'Chandigarh, India', 'Lucknow, Uttar Pradesh, India', 'Dr', '25836952244', 'sr', 'Panipat, Haryana, India', 'TK-21Rn', 1, '2016-01-13 05:28:16'),
(3, 2, 'Canters', 50, '2014', '', 'Mohali, Punjab, India', 'Karnal, Haryana, India', 'Drr', '96325878993446', 'wdh', '', 'TK-CC13', 1, '2016-01-13 06:07:13'),
(4, 3, 'Truck (6 Wheel)', 50, '2016', '', 'Mohali, Punjab, India', 'Karnal, Haryana, India', 'Abc', '9876543210', 'tdgd637373', '', 'TK-s5Z8', 1, '2016-01-13 07:25:50'),
(5, 3, 'Truck (6 Wheel)', 50, '2016', '', 'Chandigarh, India', 'Lucknow, Uttar Pradesh, India', 'Trf', '36974874524', 'sjg', 'karnsl', 'TK-6s4Y', 1, '2016-01-13 07:49:19'),
(6, 3, 'Jumbo Canter', 50, '2014', '', 'Chandigarh, India', 'Delhi, India', 'Deepu', '1234568990', 'reg-123', '', 'TK-g6p5', 1, '2016-01-13 11:38:39'),
(7, 3, 'Jumbo Canter', 50, '2014', '', 'Chandigarh, India', 'Delhi, India', 'Deepu', '1234568990', 'reg-123', '', 'TK-9nV6', 1, '2016-01-13 11:39:40'),
(8, 5, 'Jumbo Canter', 45, '2016', '', 'Chandigarh, India', 'Delhi, India', 'Deep Singh', '54631278966', 'ewg688', '', 'TK-e37d', 1, '2016-01-13 11:52:29');

-- --------------------------------------------------------

--
-- Table structure for table `truck_location`
--

CREATE TABLE IF NOT EXISTS `truck_location` (
  `t_loc_id` int(11) NOT NULL,
  `truck_id` int(11) NOT NULL,
  `between_location` text NOT NULL,
  `status` int(11) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `truck_notification`
--

CREATE TABLE IF NOT EXISTS `truck_notification` (
  `noti_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `created_date` varchar(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `truck_notification`
--

INSERT INTO `truck_notification` (`noti_id`, `user_id`, `type`, `message`, `created_date`) VALUES
(1, 1, 'customer', 'Your shipment has been accepted by transporter', '2016-01-13 11:45:42 am'),
(2, 3, 'transporter', 'Your shipment has been booked by customer', '2016-01-13 11:52:43 am');

-- --------------------------------------------------------

--
-- Table structure for table `truck_request`
--

CREATE TABLE IF NOT EXISTS `truck_request` (
  `tkreq_id` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `transporter_id` int(11) NOT NULL,
  `truck_id` int(11) NOT NULL,
  `bid_value` bigint(20) NOT NULL,
  `shipment_type` int(11) NOT NULL COMMENT '1=>bid,2=>offer',
  `request_status` int(11) NOT NULL COMMENT '1=>pending,2=>accepted,3=>ready_to_dispatched,4=>dispatched,5=>cancelled,6=>delivered',
  `status` int(11) NOT NULL COMMENT '1=>active,2=>inactive',
  `created_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `truck_request`
--

INSERT INTO `truck_request` (`tkreq_id`, `shipment_id`, `customer_id`, `transporter_id`, `truck_id`, `bid_value`, `shipment_type`, `request_status`, `status`, `created_date`) VALUES
(1, 1, 1, 3, 5, 205, 1, 5, 1, '2016-01-13 11:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `truck_shipment`
--

CREATE TABLE IF NOT EXISTS `truck_shipment` (
  `shipment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `truck_id` int(11) NOT NULL,
  `pickup_location` text NOT NULL,
  `pickup_city` varchar(50) NOT NULL,
  `platitude` varchar(50) NOT NULL,
  `plongitude` varchar(50) NOT NULL,
  `dropup_location` text NOT NULL,
  `dropup_city` varchar(50) NOT NULL,
  `dlatitude` varchar(50) NOT NULL,
  `dlongitude` varchar(50) NOT NULL,
  `pickup_date` date NOT NULL,
  `load_type` varchar(50) NOT NULL,
  `capacity` varchar(50) NOT NULL,
  `tracking` int(11) NOT NULL COMMENT '1=>active,2=>inactive',
  `sharing` int(11) NOT NULL COMMENT '1=>active,2=>inactive',
  `shipment_type` int(11) NOT NULL COMMENT '1=>bid,2=>offer',
  `offered_price` int(11) NOT NULL,
  `dispatch_status` int(11) NOT NULL COMMENT '1=>bid,2=>offer,3=>readytodispatch,4=>dispatched,5=>cancel,6=>delivered',
  `status` int(11) NOT NULL COMMENT '1=>active,2=>inactive',
  `created_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `truck_shipment`
--

INSERT INTO `truck_shipment` (`shipment_id`, `user_id`, `truck_id`, `pickup_location`, `pickup_city`, `platitude`, `plongitude`, `dropup_location`, `dropup_city`, `dlatitude`, `dlongitude`, `pickup_date`, `load_type`, `capacity`, `tracking`, `sharing`, `shipment_type`, `offered_price`, `dispatch_status`, `status`, `created_date`) VALUES
(1, 1, 0, 'Chandigarh, India', 'IVY Hospital, Sahibzada Ajit Singh Nagar, Punjab, ', '30.7333148', '76.7794179', 'Delhi, India', 'Rohini, New Delhi, Delhi, India', '28.6139391', '77.2090212', '2016-01-14', 'Industry', '50', 1, 1, 1, 0, 1, 1, '2016-01-13 11:26:31'),
(4, 1, 0, 'Chandigarh, India', 'Chandigarh, India', '30.7333148', '76.7794179', 'Delhi, India', 'Delhi, India', '28.6139391', '77.2090212', '2016-01-14', 'Industry', '45', 1, 1, 1, 0, 1, 1, '2016-01-13 11:57:21'),
(5, 1, 0, 'Chandigarh, India', 'Chandigarh, India', '30.7333148', '76.7794179', 'Delhi, India', 'Delhi, India', '28.6139391', '77.2090212', '2016-01-15', 'Industry', '45', 1, 1, 1, 0, 1, 1, '2016-01-13 11:59:27');

-- --------------------------------------------------------

--
-- Table structure for table `truck_type`
--

CREATE TABLE IF NOT EXISTS `truck_type` (
  `id` int(11) NOT NULL,
  `truck_type` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `truck_type`
--

INSERT INTO `truck_type` (`id`, `truck_type`, `created_date`) VALUES
(1, 'Canters', '2016-01-07 00:00:00'),
(2, 'Jumbo Canter', '2016-01-07 00:00:00'),
(3, 'Truck (6 Wheel)', '2016-01-06 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mobile_number` varchar(50) NOT NULL,
  `location` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `device_type` varchar(50) NOT NULL,
  `device_token` text NOT NULL,
  `user_type` int(11) NOT NULL COMMENT '1=>transporter,2=>customer',
  `otp_code` varchar(50) NOT NULL,
  `mobile_verification` int(11) NOT NULL COMMENT '0=>no_verify,1=>verify',
  `email_verification` int(11) NOT NULL COMMENT '0=>no_verify,1=>verify',
  `rating` float NOT NULL,
  `user_status` int(11) NOT NULL COMMENT '0=>inactive,1=>active',
  `created_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `mobile_number`, `location`, `password`, `image`, `device_type`, `device_token`, `user_type`, `otp_code`, `mobile_verification`, `email_verification`, `rating`, `user_status`, `created_date`) VALUES
(1, 'Nitin C', 'n@gmail.com', '8699244059', 'loc', 'qwerty', '', 'android', 'gcmId', 2, '61mu', 1, 1, 0, 1, '2016-01-08 03:45:34'),
(2, 'Gourav Tr', 'g@gmail.com', '7696269055', 'loc', 'qwerty', '', 'android', 'gcmId', 1, '4r8O', 1, 1, 0, 1, '2016-01-08 03:47:17'),
(3, 'Ajay T', 'a@gmail.com', '7307013991', 'loc', 'qwerty', '', 'android', 'gcmId', 1, 'hI65', 1, 1, 0, 1, '2016-01-08 03:50:21'),
(4, 'sumit', 'sumit@gmail.com', '8699244058', 'loc', 'qwerty', '', 'android', 'gcmId', 2, '71id', 1, 1, 0, 1, '2016-01-11 11:32:48'),
(5, 'Gg', 'gk@gmail.com', '7837234329', 'loc', 'qwerty', '', 'android', 'gcmId', 1, 'sF91', 1, 1, 0, 1, '2016-01-13 10:07:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `load_type`
--
ALTER TABLE `load_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`rat_id`);

--
-- Indexes for table `truck_chat`
--
ALTER TABLE `truck_chat`
  ADD PRIMARY KEY (`chat_id`);

--
-- Indexes for table `truck_list`
--
ALTER TABLE `truck_list`
  ADD PRIMARY KEY (`truck_id`);

--
-- Indexes for table `truck_location`
--
ALTER TABLE `truck_location`
  ADD PRIMARY KEY (`t_loc_id`);

--
-- Indexes for table `truck_notification`
--
ALTER TABLE `truck_notification`
  ADD PRIMARY KEY (`noti_id`);

--
-- Indexes for table `truck_request`
--
ALTER TABLE `truck_request`
  ADD PRIMARY KEY (`tkreq_id`);

--
-- Indexes for table `truck_shipment`
--
ALTER TABLE `truck_shipment`
  ADD PRIMARY KEY (`shipment_id`);

--
-- Indexes for table `truck_type`
--
ALTER TABLE `truck_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `load_type`
--
ALTER TABLE `load_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
  MODIFY `rat_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `truck_chat`
--
ALTER TABLE `truck_chat`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `truck_list`
--
ALTER TABLE `truck_list`
  MODIFY `truck_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `truck_location`
--
ALTER TABLE `truck_location`
  MODIFY `t_loc_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `truck_notification`
--
ALTER TABLE `truck_notification`
  MODIFY `noti_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `truck_request`
--
ALTER TABLE `truck_request`
  MODIFY `tkreq_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `truck_shipment`
--
ALTER TABLE `truck_shipment`
  MODIFY `shipment_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `truck_type`
--
ALTER TABLE `truck_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
