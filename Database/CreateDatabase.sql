CREATE DATABASE VotingSystem;

-- Switch to the VotingSystem database
USE VotingSystem;

-- Create the VSAdmin table
CREATE TABLE VSAdmin (
    AdminUsername VARCHAR(255) NOT NULL,
    AdminPassword VARCHAR(255) NOT NULL,
    PRIMARY KEY (AdminUsername)
);

-- Create the VSStudents table
CREATE TABLE VSStudents (
    StudentID VARCHAR(255) NOT NULL,
    -- Removed AUTO_INCREMENT from VoteID
    VoteID INT,
    StudentEmail VARCHAR(255) NOT NULL,
    StudentPassword VARCHAR(255) NOT NULL,
    StudentName VARCHAR(255) NOT NULL,
    StudentProfilePicture VARCHAR(255),
    UserApproval BOOLEAN NOT NULL DEFAULT FALSE,
    VerificationToken VARCHAR(255) DEFAULT NULL,
    reset_token_hash VARCHAR(64) NULL DEFAULT NULL,
    reset_token_expires_at DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (StudentID, StudentEmail),
    UNIQUE (reset_token_hash)
);

-- Create the VSEvents table
CREATE TABLE VSEvents (
    EventID INT AUTO_INCREMENT PRIMARY KEY,
    EventName VARCHAR(255) NOT NULL,
    IsActive BOOLEAN DEFAULT FALSE,
    StartDate DATETIME,
    EndDate DATETIME
);

-- Create the VSNews table
CREATE TABLE VSNews (
    NewsID INT AUTO_INCREMENT PRIMARY KEY,
    NewsTitle VARCHAR(255) NOT NULL,
    NewsContent LONGTEXT NOT NULL,
    NewsImage VARCHAR(255),
    IsActive BOOLEAN DEFAULT FALSE
);

-- Create the VSVote table 
CREATE TABLE VSVote (
    VoteID INT AUTO_INCREMENT PRIMARY KEY,
    StudentID VARCHAR(255) NOT NULL,
    StudentEmail VARCHAR(255) NOT NULL,
    StudentName VARCHAR(255) NOT NULL,
    StudentProfilePicture VARCHAR(255),
    Manifesto LONGTEXT,
    CandidateType ENUM('Candidate', 'SRC') NOT NULL,
    TotalCandidateVote INT DEFAULT 0,
    TotalSRCVote INT DEFAULT 0,
    NominationVoteStatus INT DEFAULT 0,
    SRCVoteStatus INT DEFAULT 0,
    NominationApproval BOOLEAN NOT NULL DEFAULT FALSE,
    VotedDateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StudentID) REFERENCES VSStudents(StudentID) ON UPDATE CASCADE
);

-- Create the VSVoteHistory table 
CREATE TABLE VSVoteHistory (
    VoteHistoryID INT AUTO_INCREMENT PRIMARY KEY,
    StudentID VARCHAR(255) NOT NULL,
    StudentName VARCHAR(255) NOT NULL,
    CandidateID VARCHAR(255) NOT NULL,
    CandidateName VARCHAR(255) NOT NULL,
    VoteType ENUM('Candidate', 'SRC') NOT NULL,
    VotedDateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StudentID) REFERENCES VSStudents(StudentID) ON UPDATE CASCADE
);

-- Trigger to update StudentID, StudentEmail, StudentName, and StudentProfilePicture in VSVote
DELIMITER //
CREATE TRIGGER update_related_studentinfo_in_vsvote
AFTER UPDATE ON VSStudents
FOR EACH ROW
BEGIN
    UPDATE VSVote
    SET StudentID = NEW.StudentID,
        StudentEmail = NEW.StudentEmail,
        StudentName = NEW.StudentName,
        StudentProfilePicture = NEW.StudentProfilePicture
    WHERE StudentID = OLD.StudentID;

    UPDATE VSVoteHistory
    SET StudentID = NEW.StudentID,
        StudentName = NEW.StudentName
    WHERE StudentID = OLD.StudentID;
END;
//
DELIMITER ;

-- Insert data into the VSAdmin table
INSERT INTO VSAdmin (AdminUsername, AdminPassword)
VALUES ('GMiAdmin1991', 'SmartVotingGMi1991');

-- Insert data into the VSEvents table
INSERT INTO VSEvents (EventName)
VALUES ('Nomination Vote'), ('SRC Vote'), ('Nomination Result'), ('SRC Result');
