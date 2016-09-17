-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 17, 2016 at 01:02 PM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`` PROCEDURE `AddGeometryColumn` (`catalog` VARCHAR(64), `t_schema` VARCHAR(64), `t_name` VARCHAR(64), `geometry_column` VARCHAR(64), `t_srid` INT)  begin
  set @qwe= concat('ALTER TABLE ', t_schema, '.', t_name, ' ADD ', geometry_column,' GEOMETRY REF_SYSTEM_ID=', t_srid); PREPARE ls from @qwe; execute ls; deallocate prepare ls; end$$

CREATE DEFINER=`` PROCEDURE `DropGeometryColumn` (`catalog` VARCHAR(64), `t_schema` VARCHAR(64), `t_name` VARCHAR(64), `geometry_column` VARCHAR(64))  begin
  set @qwe= concat('ALTER TABLE ', t_schema, '.', t_name, ' DROP ', geometry_column); PREPARE ls from @qwe; execute ls; deallocate prepare ls; end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ask`
--

CREATE TABLE `ask` (
  `id` int(11) NOT NULL,
  `student` int(11) DEFAULT NULL,
  `content` longtext,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ask`
--

INSERT INTO `ask` (`id`, `student`, `content`, `date`) VALUES
(15, 1, 'Em bi om', '2016-09-05'),
(17, 1, 'dsasdas', '2016-09-13'),
(18, 1, 'Em b om', '2016-09-14'),
(19, 1, 'om', '2016-09-24'),
(20, 1, 'em bi om', '2016-09-17');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `teacher` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` int(11) NOT NULL,
  `name` longtext,
  `school` int(11) DEFAULT NULL,
  `owner` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `name`, `school`, `owner`) VALUES
(1, '6A', 1, 1),
(2, '7A', 1, 2),
(3, '8A', 1, 3),
(4, '9A', 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `keys`
--

CREATE TABLE `keys` (
  `id` int(11) NOT NULL,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mark`
--

CREATE TABLE `mark` (
  `id` int(11) NOT NULL,
  `teacher` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `mark` float DEFAULT NULL,
  `student` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `test` int(11) DEFAULT NULL,
  `term` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mark`
--

INSERT INTO `mark` (`id`, `teacher`, `type`, `mark`, `student`, `date`, `test`, `term`) VALUES
(1, 1, 1, 9, 1, '2016-08-15', NULL, 1),
(2, 2, 1, 7, 1, '2016-08-17', NULL, 1),
(9, 1, 2, 10, 1, '2016-08-26', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `content` longtext,
  `status` int(11) DEFAULT '0',
  `teacher` int(11) DEFAULT NULL,
  `student` int(11) DEFAULT NULL,
  `term` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `date`, `content`, `status`, `teacher`, `student`, `term`) VALUES
(1, '2016-08-23 07:30:29', 'Yến còn nói chuyện riêng trong lớp', 1, 2, 1, 1),
(56, '2016-08-26 12:14:39', 'Có tiến bộ trong học tập', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `parent`
--

CREATE TABLE `parent` (
  `id` int(11) NOT NULL,
  `name` longtext,
  `phone` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`id`, `name`, `phone`) VALUES
(1, 'Phạm Văn Cường', '01234567899'),
(2, 'Nguyễn Thị Lan', '01234567898');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `class` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `period` int(11) DEFAULT NULL,
  `teacher` int(11) DEFAULT NULL,
  `term` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `class`, `day`, `period`, `teacher`, `term`) VALUES
(1, 1, 2, 1, 1, 1),
(2, 1, 2, 2, 1, 1),
(3, 1, 4, 1, 2, 1),
(4, 1, 4, 2, 2, 1),
(5, 1, 3, 1, 5, 1),
(6, 1, 3, 2, 3, 1),
(7, 1, 5, 2, 6, 1),
(8, 1, 5, 1, 4, 1),
(9, 1, 6, 1, 2, 1),
(10, 1, 6, 2, 5, 1),
(11, 1, 7, 1, 4, 1),
(12, 1, 7, 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE `school` (
  `id` int(11) NOT NULL,
  `name` longtext,
  `address` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `school`
--

INSERT INTO `school` (`id`, `name`, `address`) VALUES
(1, 'THCS Cầu Giấy', 'Trần Thái Tông, Dịch Vọng Hậu, Cầu Giấy, Hà Nội');

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `token` longtext,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `user` int(11) DEFAULT NULL,
  `FCMToken` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`id`, `token`, `date`, `user`, `FCMToken`) VALUES
(72, 'YqgidzHpti0hB6UDNphCrK7ssTd7eyyc', '2016-09-17 17:37:40', 2, 'f4cnuDjOix4:APA91bGapvDAsuIAfgIeuPj8-CmHJvV0dM3PIAIL--xJmqnKRpUE96hTSFILW_fyBMsMk6oWZPvjCss3wSIfMVVnOjQFgxTyBwOyUokNlDfcFwXdr9PeoSLrDFjQrNyN9Mn6THuRLNJS');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `name` longtext,
  `fname` longtext NOT NULL,
  `address` longtext,
  `class` int(11) DEFAULT NULL,
  `dad` int(11) DEFAULT NULL,
  `mom` int(11) DEFAULT NULL,
  `image` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `name`, `fname`, `address`, `class`, `dad`, `mom`, `image`) VALUES
(1, 'Phạm Thị Hải Yến', 'Yến', '144 Xuân Thủy, Cầu Giấy, Hà Nội', 1, 1, 2, 'yen.jpg'),
(2, 'Vũ Văn Tuấn', 'Tuấn', '144 Xuân Thủy, Cầu Giấy, Hà Nội', 1, 3, 4, NULL),
(3, 'Nguyễn Văn An', 'An', '144 Xuân Thủy, Cầu Giấy, Hà Nội', 1, 5, 6, NULL),
(4, 'Đặng Tiểu Bình', 'Bình', '144 Xuân Thủy, Cầu Giấy, Hà Nội', 1, 7, 8, NULL),
(5, 'Vũ Thị Hoa', 'Hoa', '144 Xuân Thủy, Cầu Giấy, Hà Nội', 1, 9, 10, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `id` int(11) NOT NULL,
  `name` longtext,
  `sort_name` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`id`, `name`, `sort_name`) VALUES
(1, 'Toán học', 'Toán'),
(2, 'Ngữ văn', 'Văn'),
(3, 'Vật lý', 'Lý'),
(4, 'Hóa học', 'Hóa'),
(5, 'Sinh học', 'Sinh'),
(6, 'Lịch sử', 'Sử'),
(7, 'Địa lý', 'Địa'),
(8, 'Tiếng Anh', 'Anh'),
(9, 'Thể dục', 'TD'),
(10, 'Âm nhạc', 'ÂN'),
(11, 'Mỹ thuật', 'Vẽ'),
(12, 'Công nghệ', 'CN'),
(13, 'Tin học', 'Tin'),
(14, 'Chào cờ', 'Chào cờ'),
(15, 'Giáo dục công dân', 'GDCD');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `id` int(11) NOT NULL,
  `name` longtext,
  `address` longtext,
  `phone` longtext,
  `type` int(11) DEFAULT NULL,
  `subject` int(11) DEFAULT NULL,
  `school` int(11) DEFAULT NULL,
  `image` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`id`, `name`, `address`, `phone`, `type`, `subject`, `school`, `image`) VALUES
(1, 'Đỗ Như Nghĩa', '144 Xuân Thủy, Cầu Giấy, Hà Nội', '0987654321', 2, 1, 1, 'nghiadn.jpg'),
(2, 'Nguyễn Thị Hiền', '144 Xuân Thủy, Cầu Giấy, Hà Nội', '01234567899', 1, 2, 1, ''),
(3, 'Hoàng Thị Thúy', '144 Xuân Thủy, Cầu Giấy, Hà Nội', '01234567899', 1, 3, 1, ''),
(4, 'Đặng Bảo Sơn', '144 Xuân Thủy, Cầu Giấy, Hà Nội', '01234567899', 1, 4, 1, ''),
(5, 'Vũ Văn Tuấn', '144 Xuân Thủy, Cầu Giấy, Hà Nội', '01234567899', 1, 5, 1, ''),
(6, 'Phạm Cao Cường', '144 Xuân Thủy, Cầu Giấy, Hà Nội', '01234567899', 1, 6, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `term`
--

CREATE TABLE `term` (
  `id` int(11) NOT NULL,
  `name` longtext,
  `year` longtext,
  `school` int(11) DEFAULT NULL,
  `current` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `term`
--

INSERT INTO `term` (`id`, `name`, `year`, `school`, `current`) VALUES
(1, 'Há»c kÃ¬ I', '2016', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `class` int(11) DEFAULT NULL,
  `teacher` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `term` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `date`, `class`, `teacher`, `type`, `term`) VALUES
(1, '2016-08-24', 1, 2, 1, 1),
(2, '2016-09-19', 1, 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` longtext,
  `password` longtext,
  `type` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `type`, `user`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 3, 0),
(2, '01234567899', 'e10adc3949ba59abbe56e057f20f883e', 2, 1),
(3, '01234567898', 'e10adc3949ba59abbe56e057f20f883e', 2, 1),
(4, '0987654321', 'e10adc3949ba59abbe56e057f20f883e', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ask`
--
ALTER TABLE `ask`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_idx` (`student`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_idx` (`student`),
  ADD KEY `teacher_idx` (`teacher`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school_idx` (`school`),
  ADD KEY `owner_idx` (`owner`);

--
-- Indexes for table `keys`
--
ALTER TABLE `keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mark`
--
ALTER TABLE `mark`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_idx` (`test`),
  ADD KEY `student_idx` (`student`),
  ADD KEY `fk_mark_teacher_idx` (`teacher`),
  ADD KEY `fk_mark_term_idx` (`term`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_idx` (`teacher`),
  ADD KEY `fk_notification_student_idx` (`student`);

--
-- Indexes for table `parent`
--
ALTER TABLE `parent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_idx` (`teacher`),
  ADD KEY `class_idx` (`class`),
  ADD KEY `fk_schedule_term_idx` (`term`);

--
-- Indexes for table `school`
--
ALTER TABLE `school`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_idx` (`user`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_idx` (`class`),
  ADD KEY `mom_idx` (`mom`),
  ADD KEY `dad_idx` (`dad`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_teacher_school_idx` (`school`),
  ADD KEY `fk_teacher_subject_idx` (`subject`);

--
-- Indexes for table `term`
--
ALTER TABLE `term`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school_idx` (`school`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_idx` (`teacher`),
  ADD KEY `class_idx1` (`class`),
  ADD KEY `fk_test_term_idx` (`term`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_idx` (`user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ask`
--
ALTER TABLE `ask`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `keys`
--
ALTER TABLE `keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mark`
--
ALTER TABLE `mark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT for table `parent`
--
ALTER TABLE `parent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `school`
--
ALTER TABLE `school`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;
--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `term`
--
ALTER TABLE `term`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
