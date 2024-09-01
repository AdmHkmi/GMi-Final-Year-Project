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
    StudentEmail VARCHAR(255) NOT NULL,
    StudentPassword VARCHAR(255) NOT NULL,
    StudentName VARCHAR(255) NOT NULL,
    StudentProfilePicture VARCHAR(255),
    Manifesto LONGTEXT,
    TotalCandidateVote INT DEFAULT 0,
    TotalSRCVote INT DEFAULT 0,
    TandC BOOLEAN NOT NULL DEFAULT FALSE,
    NominationVoteStatus INT DEFAULT 0,
    SRCVoteStatus INT DEFAULT 0,
    UserApproval BOOLEAN NOT NULL DEFAULT FALSE,
    NominationApproval BOOLEAN NOT NULL DEFAULT FALSE,
    VerificationToken VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (StudentID, StudentEmail)
);

-- Create the VSEvents table
CREATE TABLE VSEvents (
    EventID INT AUTO_INCREMENT PRIMARY KEY,
    EventName VARCHAR(255) NOT NULL,
    IsActive BOOLEAN DEFAULT FALSE,
    StartDate DATETIME,
    EndDate DATETIME
);

-- Create the VSApprovedCandidates table
CREATE TABLE VSCurrentCandidate (
    CandidateID INT NOT NULL AUTO_INCREMENT,
    StudentID VARCHAR(255) NOT NULL,
    StudentEmail VARCHAR(255) NOT NULL,
    StudentName VARCHAR(255) NOT NULL,
    StudentProfilePicture VARCHAR(255),
    PRIMARY KEY (CandidateID),
    FOREIGN KEY (StudentID, StudentEmail) REFERENCES VSStudents(StudentID, StudentEmail) ON UPDATE CASCADE
);

-- Create the VSApprovedSRC table
CREATE TABLE VSCurrentSRC (
    SRCID INT NOT NULL AUTO_INCREMENT,
    StudentID VARCHAR(255) NOT NULL,
    StudentEmail VARCHAR(255) NOT NULL,
    StudentName VARCHAR(255) NOT NULL,
    StudentProfilePicture VARCHAR(255),
    Manifesto LONGTEXT,
    PRIMARY KEY (SRCID),
    FOREIGN KEY (StudentID, StudentEmail) REFERENCES VSStudents(StudentID, StudentEmail) ON UPDATE CASCADE
);

-- Create the VSNews table
CREATE TABLE VSNews (
    NewsID INT AUTO_INCREMENT PRIMARY KEY,
    NewsTitle VARCHAR(255) NOT NULL,
    NewsContent LONGTEXT NOT NULL,
    NewsImage VARCHAR(255),
    IsActive BOOLEAN DEFAULT FALSE
);

-- Create the VSCandidateVote table
CREATE TABLE VSCandidateVote (
    VoteID INT AUTO_INCREMENT PRIMARY KEY,
    StudentID VARCHAR(255) NOT NULL,
    StudentName VARCHAR(255) NOT NULL,
    CandidateID VARCHAR(255) NOT NULL,
    CandidateName VARCHAR(255) NOT NULL,
    VotedDateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StudentID) REFERENCES VSStudents(StudentID) ON UPDATE CASCADE
);

-- Create the VSSRCVote table
CREATE TABLE VSSRCVote (
    VoteID INT AUTO_INCREMENT PRIMARY KEY,
    StudentID VARCHAR(255) NOT NULL,
    StudentName VARCHAR(255) NOT NULL,
    CandidateID VARCHAR(255) NOT NULL,
    CandidateName VARCHAR(255) NOT NULL,
    VotedDateTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StudentID) REFERENCES VSStudents(StudentID) ON UPDATE CASCADE
);

-- Trigger to update StudentID in related tables
DELIMITER //
CREATE TRIGGER update_related_studentid
AFTER UPDATE ON VSStudents
FOR EACH ROW
BEGIN
    UPDATE VSCurrentCandidate SET StudentID = NEW.StudentID WHERE StudentEmail = NEW.StudentEmail;
    UPDATE VSCurrentSRC SET StudentID = NEW.StudentID WHERE StudentEmail = NEW.StudentEmail;
    UPDATE VSCandidateVote SET StudentID = NEW.StudentID WHERE StudentID = OLD.StudentID;
    UPDATE VSSRCVote SET StudentID = NEW.StudentID WHERE StudentID = OLD.StudentID;
END;
//

-- Trigger to update StudentEmail in related tables
CREATE TRIGGER update_related_studentemail
AFTER UPDATE ON VSStudents
FOR EACH ROW
BEGIN
    UPDATE VSCurrentCandidate SET StudentEmail = NEW.StudentEmail WHERE StudentID = NEW.StudentID;
    UPDATE VSCurrentSRC SET StudentEmail = NEW.StudentEmail WHERE StudentID = NEW.StudentID;
END;
//

-- Trigger to update StudentName in related tables
CREATE TRIGGER update_related_studentname
AFTER UPDATE ON VSStudents
FOR EACH ROW
BEGIN
    UPDATE VSCurrentCandidate SET StudentName = NEW.StudentName WHERE StudentID = NEW.StudentID;
    UPDATE VSCurrentSRC SET StudentName = NEW.StudentName WHERE StudentID = NEW.StudentID;
    UPDATE VSCandidateVote SET StudentName = NEW.StudentName WHERE StudentID = NEW.StudentID;
    UPDATE VSSRCVote SET StudentName = NEW.StudentName WHERE StudentID = NEW.StudentID;
END;
//

-- Trigger to update StudentProfilePicture in related tables
CREATE TRIGGER update_related_studentprofilepicture
AFTER UPDATE ON VSStudents
FOR EACH ROW
BEGIN
    UPDATE VSCurrentCandidate SET StudentProfilePicture = NEW.StudentProfilePicture WHERE StudentID = NEW.StudentID;
    UPDATE VSCurrentSRC SET StudentProfilePicture = NEW.StudentProfilePicture WHERE StudentID = NEW.StudentID;
END;
//

-- Trigger to update StudentProfilePicture in related tables
CREATE TRIGGER update_related_manifesto
AFTER UPDATE ON VSStudents
FOR EACH ROW
BEGIN
    UPDATE VSCurrentSRC SET Manifesto = NEW.Manifesto WHERE StudentID = NEW.StudentID;
END;
//

DELIMITER ;

INSERT INTO VSAdmin (AdminUsername, AdminPassword)
VALUES ('GMiAdmin1991', 'SmartVotingGMi1991');

INSERT INTO VSEvents (EventName)
VALUES ('Nomination Vote'), ('SRC Vote'), ('Nomination Result'), ('SRC Result');
