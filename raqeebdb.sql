-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 26, 2024 at 09:21 PM
-- Server version: 5.7.24
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `raqeebdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `camera`
--

CREATE TABLE `camera` (
  `CameraID` int(11) NOT NULL,
  `CameraIPAddress` varchar(255) DEFAULT NULL,
  `PortNo` int(11) DEFAULT NULL,
  `StreamingChannel` varchar(255) DEFAULT NULL,
  `CameraUsername` varchar(255) DEFAULT NULL,
  `CameraPassword` varchar(255) DEFAULT NULL,
  `CompanyID` int(11) DEFAULT NULL,
  `CameraName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `camera`
--

INSERT INTO `camera` (`CameraID`, `CameraIPAddress`, `PortNo`, `StreamingChannel`, `CameraUsername`, `CameraPassword`, `CompanyID`, `CameraName`) VALUES
(64, '192.168.8.46', 554, 'stream1', 'Raqeeb1', 'raqeebCCTV2025', 84, 'camera1'),
(65, '192.168.8.45', 554, 'stream1', 'Raqeeb2', 'raqeebCCTV2025', 84, 'camera2');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `CompanyID` int(11) NOT NULL,
  `Logo` varchar(255) DEFAULT NULL,
  `CompanyName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`CompanyID`, `Logo`, `CompanyName`, `Email`, `Password`) VALUES
(84, '84.jpeg', 'Tahaluf', 'ranaAlsayyari@gmail.com', '$2y$10$ds0Y4aDR0ZtnegJWrEi5U.em.iyEvGPVaLiXhOnuDu/UdfKOZquFC');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `EventID` int(11) NOT NULL,
  `EventName` varchar(255) NOT NULL,
  `EventLocation` varchar(255) DEFAULT NULL,
  `EventStartDate` date DEFAULT NULL,
  `EventEndDate` date DEFAULT NULL,
  `EventStartTime` time DEFAULT NULL,
  `EventEndTime` time DEFAULT NULL,
  `CompanyID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`EventID`, `EventName`, `EventLocation`, `EventStartDate`, `EventEndDate`, `EventStartTime`, `EventEndTime`, `CompanyID`) VALUES
(100, 'LEAP 2025', 'Riyadh', '2024-11-11', '2024-11-12', '19:58:00', '20:59:00', 84),
(101, 'Ai summit', 'Ruh', '2024-11-10', '2024-11-30', '18:05:00', '18:09:00', 84);

-- --------------------------------------------------------

--
-- Table structure for table `hall`
--

CREATE TABLE `hall` (
  `HallID` int(11) NOT NULL,
  `HallName` varchar(255) DEFAULT NULL,
  `HallThreshold` int(11) DEFAULT NULL,
  `EventID` int(11) DEFAULT NULL,
  `CameraID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `hall`
--

INSERT INTO `hall` (`HallID`, `HallName`, `HallThreshold`, `EventID`, `CameraID`) VALUES
(112, 'Main Hall', 100, 101, 64),
(113, 'VIP', 100, 101, 65);

-- --------------------------------------------------------

--
-- Table structure for table `monitoredsession`
--

CREATE TABLE `monitoredsession` (
  `SessionID` int(11) NOT NULL,
  `HallID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `peoplecount`
--

CREATE TABLE `peoplecount` (
  `PeopleCountID` int(11) NOT NULL,
  `Count` int(11) DEFAULT NULL,
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `SessionID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `persontrack`
--

CREATE TABLE `persontrack` (
  `PersonTrackID` int(11) NOT NULL,
  `ID` int(11) DEFAULT NULL,
  `EntranceTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ExitTime` timestamp NULL DEFAULT NULL,
  `SessionID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `camera`
--
ALTER TABLE `camera`
  ADD PRIMARY KEY (`CameraID`),
  ADD KEY `CompanyID` (`CompanyID`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`CompanyID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`EventID`),
  ADD KEY `CompanyID` (`CompanyID`);

--
-- Indexes for table `hall`
--
ALTER TABLE `hall`
  ADD PRIMARY KEY (`HallID`),
  ADD KEY `EventID` (`EventID`),
  ADD KEY `CameraID` (`CameraID`);

--
-- Indexes for table `monitoredsession`
--
ALTER TABLE `monitoredsession`
  ADD PRIMARY KEY (`SessionID`),
  ADD KEY `HallID` (`HallID`);

--
-- Indexes for table `peoplecount`
--
ALTER TABLE `peoplecount`
  ADD PRIMARY KEY (`PeopleCountID`),
  ADD KEY `SessionID` (`SessionID`);

--
-- Indexes for table `persontrack`
--
ALTER TABLE `persontrack`
  ADD PRIMARY KEY (`PersonTrackID`),
  ADD KEY `SessionID` (`SessionID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `camera`
--
ALTER TABLE `camera`
  MODIFY `CameraID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `CompanyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `EventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `hall`
--
ALTER TABLE `hall`
  MODIFY `HallID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `monitoredsession`
--
ALTER TABLE `monitoredsession`
  MODIFY `SessionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peoplecount`
--
ALTER TABLE `peoplecount`
  MODIFY `PeopleCountID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `camera`
--
ALTER TABLE `camera`
  ADD CONSTRAINT `camera_ibfk_1` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`) ON DELETE CASCADE;

--
-- Constraints for table `hall`
--
ALTER TABLE `hall`
  ADD CONSTRAINT `hall_ibfk_1` FOREIGN KEY (`EventID`) REFERENCES `events` (`EventID`) ON DELETE CASCADE,
  ADD CONSTRAINT `hall_ibfk_2` FOREIGN KEY (`CameraID`) REFERENCES `camera` (`CameraID`) ON DELETE SET NULL;

--
-- Constraints for table `monitoredsession`
--
ALTER TABLE `monitoredsession`
  ADD CONSTRAINT `monitoredsession_ibfk_1` FOREIGN KEY (`HallID`) REFERENCES `hall` (`HallID`);

--
-- Constraints for table `peoplecount`
--
ALTER TABLE `peoplecount`
  ADD CONSTRAINT `peoplecount_ibfk_1` FOREIGN KEY (`SessionID`) REFERENCES `monitoredsession` (`SessionID`);

--
-- Constraints for table `persontrack`
--
ALTER TABLE `persontrack`
  ADD CONSTRAINT `persontrack_ibfk_1` FOREIGN KEY (`SessionID`) REFERENCES `monitoredsession` (`SessionID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
