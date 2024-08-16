INSERT INTO VSAdmin (AdminUsername, AdminPassword)
VALUES ('a', 'a');

INSERT INTO VSStudents (StudentID, StudentEmail, StudentPassword, StudentName, StudentProfilePicture, TandC, NominationVoteStatus, SRCVoteStatus, UserApproval, NominationApproval)
VALUES 
    ('b', 'b@b.com', 'b',  'Adam Hakimi Bin Razak', 'Default.jpg', 1, 0, 0, 1, 0),
    ('ABC123', 'Syed@gmail.com', 'abc123', 'Syed Haidar Bin Alattas', 'Default.jpg', 1, 0, 0, 1, 0),
    ('DEF123', 'Mukram@gmail.com', 'abc123', 'Mukram Hafiz Bin Irwan', 'Default.jpg', 1, 0, 0, 1, 0),
    ('GHI123', 'Harith@gmail.com', 'abc123', 'Harith Hilman Bin Shahril', 'Default.jpg', 1, 0, 0, 1, 0),
    ('JKL123', 'Afif@gmail.com', 'abc123', 'Ahmad Afif Bin Sazimi', 'Default.jpg', 1, 0, 0, 1, 0),
    ('OPQ123', 'Raziq@gmail.com', 'abc123', 'Ahmad Raziq Bin Shah', 'Default.jpg', 1, 0, 0, 1, 0),
    ('RST123', 'Fauzan@gmail.com', 'abc123', 'Ahmad Fauzan Bin Sadiran', 'Default.jpg', 1, 0, 0, 1, 0),
    ('UVW123', 'Farid@gmail.com', 'abc123', 'Muhammad Farid Bin Zakaria', 'Default.jpg', 1, 0, 0, 1, 0);

INSERT INTO VSNews (NewsTitle, NewsContent, NewsImage, IsActive)
VALUES ('Voting Event', 'Kesemua pelajar wajib mengambil tempat dalam acara ini.', 'VotingQueue.jpg', 1);