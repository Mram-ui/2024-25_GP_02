-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2024 at 05:09 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
  `CameraIPAddress` varchar(255) NOT NULL,
  `PortNo` int(11) NOT NULL,
  `StreamingChannel` varchar(255) NOT NULL,
  `CameraUsername` varchar(255) NOT NULL,
  `CameraPassword` varchar(255) NOT NULL,
  `CompanyID` int(11) NOT NULL,
  `CameraName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `EventID` int(11) NOT NULL,
  `EventName` varchar(255) NOT NULL,
  `EventLocation` varchar(255) NOT NULL,
  `EventStartDate` date NOT NULL,
  `EventEndDate` date NOT NULL,
  `EventStartTime` time NOT NULL,
  `EventEndTime` time NOT NULL,
  `CompanyID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hall`
--

CREATE TABLE `hall` (
  `HallID` int(11) NOT NULL,
  `HallName` varchar(255) NOT NULL,
  `HallThreshold` int(11) NOT NULL,
  `EventID` int(11) NOT NULL,
  `CameraID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monitoredsession`
--

CREATE TABLE `monitoredsession` (
  `SessionID` int(11) NOT NULL,
  `HallID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peoplecount`
--

CREATE TABLE `peoplecount` (
  `PeopleCountID` int(11) NOT NULL,
  `Count` int(11) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `SessionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persontrack`
--

CREATE TABLE `persontrack` (
  `PersonTrackID` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `EntranceTime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ExitTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `SessionID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `CameraID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `CompanyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `EventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `hall`
--
ALTER TABLE `hall`
  MODIFY `HallID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
