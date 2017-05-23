-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2016 at 03:58 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `truck_shipment`
--

CREATE TABLE IF NOT EXISTS `truck_shipment` (
  `shipment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `truck_id` int(11) NOT NULL,
  `pickup_location` text NOT NULL,
  `platitude` varchar(50) NOT NULL,
  `plongitude` varchar(50) NOT NULL,
  `dropup_location` text NOT NULL,
  `pickup_date` varchar(50) NOT NULL,
  `load_type` varchar(50) NOT NULL,
  `capacity` varchar(50) NOT NULL,
  `tracking` int(11) NOT NULL COMMENT '1=>active,2=>inactive',
  `sharing` int(11) NOT NULL COMMENT '1=>active,2=>inactive',
  `shipment_type` int(11) NOT NULL COMMENT '1=>bid,2=>offer',
  `offered_price` int(11) NOT NULL,
  `dispatch_status` int(11) NOT NULL COMMENT '1=>bid,2=>offer,3=>readytodispatch,4=>dispatched,5=>cancel,6=>delivered',
  `status` int(11) NOT NULL COMMENT '1=>active,2=>inactive',
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(1, 'Truck (6 Wheel)', '2016-01-04 00:00:00'),
(2, 'Truck (10 Wheel)', '2016-01-04 00:00:00'),
(3, 'Truck (12 Wheel)', '2016-01-04 00:00:00');

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
  `rating` int(11) NOT NULL,
  `user_status` int(11) NOT NULL COMMENT '0=>inactive,1=>active',
  `created_date` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `mobile_number`, `location`, `password`, `image`, `device_type`, `device_token`, `user_type`, `otp_code`, `mobile_verification`, `email_verification`, `rating`, `user_status`, `created_date`) VALUES
(1, 'nitin', 'n@gmail.com', '8699244059', 'loc', 'qwerty', '', 'android', 'APA91bEJ4fdK55C5MHnJTpQ8rjYa-Z8GhC7TjycT03op9IpeJC95wSNQULMV072y-0lDsDe3kxHH3-lpWkJgswBX9NSGnMGM4OHEIq4iucHbc9HKtb9WfU_mrz4EBREsN1Ro5iE6sSJX', 2, 'g9O0', 1, 1, 0, 1, '2015-12-29 11:53:36'),
(2, 'gourav', 'g@gmail.com', '7837234329', 'loc', 'qwerty', '', 'android', 'APA91bFg-enKDZLnBp7-dv_wWr2XoWG943VSXDtIG1kV4NKUCYYpyHGBTYSKfuvbZOH5kKH9RO66ZYUAIY0zvmQn2yoxox7DrtBkGxnq9cEEmZ0k19vxbYe6k8USPIbno6eA4fMUH9kj', 1, 'z6U7', 1, 1, 0, 1, '2015-12-29 11:56:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `load_type`
--
ALTER TABLE `load_type`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `load_type`
--
ALTER TABLE `load_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `truck_chat`
--
ALTER TABLE `truck_chat`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `truck_list`
--
ALTER TABLE `truck_list`
  MODIFY `truck_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `truck_location`
--
ALTER TABLE `truck_location`
  MODIFY `t_loc_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `truck_notification`
--
ALTER TABLE `truck_notification`
  MODIFY `noti_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `truck_request`
--
ALTER TABLE `truck_request`
  MODIFY `tkreq_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `truck_shipment`
--
ALTER TABLE `truck_shipment`
  MODIFY `shipment_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `truck_type`
--
ALTER TABLE `truck_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
