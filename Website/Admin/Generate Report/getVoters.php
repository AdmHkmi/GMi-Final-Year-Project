<?php
include '../../../Database/DatabaseConnection.php';

if (isset($_GET['studentId'])) {
    $candidateId = $_GET['studentId'];

    // Sanitize the input to prevent SQL injection
    $candidateId = $conn->real_escape_string($candidateId);

    // Query to fetch voters with VoteType "src" only
    $votersQuery = "
        SELECT VSStudents.StudentName, VSStudents.StudentID, VSVoteHistory.VoteType
        FROM VSStudents
        JOIN VSVoteHistory ON VSStudents.StudentID = VSVoteHistory.VoterID
        WHERE VSVoteHistory.CandidateID = '$candidateId' AND VSVoteHistory.VoteType = 'src'
    ";

    $result = $conn->query($votersQuery);

    $voters = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $voters[] = [
                'name' => $row['StudentName'],
                'studentId' => $row['StudentID'],
                'voteType' => $row['VoteType']
            ];
        }
        echo json_encode($voters);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(['error' => 'Candidate ID not provided.']);
}
?>
