<?php
include '../../../Database/DatabaseConnection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report</title>
    <link rel="stylesheet" href="GenerateReport.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Additional styles for the table */
        table {
            width: 100%;
            border-collapse: collapse; /* Collapse borders */
            margin-top: 20px; /* Spacing above the table */
        }
        th, td {
            border: 1px solid #ddd; /* Border for each cell */
            padding: 12px; /* Increased padding for spacing */
            text-align: left;
        }
        th {
            background-color: #f2f2f2; /* Light background for header */
        }
        tr:nth-child(even) {
            background-color: #f9f9f9; /* Light grey for even rows */
        }
        tr:hover {
            background-color: #f1f1f1; /* Highlight row on hover */
        }
        /* Style for SRC image */
        .src-image {
            width: 50px;
            height: 50px;
            object-fit: cover; /* Ensure images fit nicely */
        }
        /* Modal Styles */
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Adjust width as needed */
            max-width: 600px; /* Max width for modal */
            position: relative; /* To position close button */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="background-image"></div>
    <header>
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <div class="top-right-buttons">
            <a href="../Home Page/AdminHomepage.php">
                <button class="back-button">
                    <i class='fas fa-arrow-left'></i> 
                </button>
            </a>
        </div>
    </header>
    <main>
        <div class="report-container">
            <center>
                <h1>Event Report</h1>

                <!-- Placeholder for the bar chart -->
                <canvas id="reportChart" width="400" height="200"></canvas>

                <!-- New custom legend for the bar chart -->
                <div id="customLegend" style="margin-top: 20px; text-align: left;">
                    <p><span style="background-color: rgba(75, 192, 192, 0.2); padding: 5px; border: 1px solid rgba(75, 192, 192, 1); margin-right: 10px;"></span> Total Users</p>
                    <p><span style="background-color: rgba(54, 162, 235, 0.2); padding: 5px; border: 1px solid rgba(54, 162, 235, 1); margin-right: 10px;"></span> Approved Users</p>
                    <p><span style="background-color: rgba(255, 99, 132, 0.2); padding: 5px; border: 1px solid rgba(255, 99, 132, 1); margin-right: 10px;"></span> Unapproved Users</p>
                    <p><span style="background-color: rgba(255, 206, 86, 0.2); padding: 5px; border: 1px solid rgba(255, 206, 86, 1); margin-right: 10px;"></span> Candidates</p>
                    <p><span style="background-color: rgba(153, 102, 255, 0.2); padding: 5px; border: 1px solid rgba(153, 102, 255, 1); margin-right: 10px;"></span> SRC</p>
                </div>

                <!-- Updated table for the main report -->
                <div class="report-details" style="margin-top: 20px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch report data from the database
                            $reportQuery = "
                                SELECT
                                    (SELECT COUNT(DISTINCT StudentID) FROM VSStudents) AS totalUsers,
                                    (SELECT COUNT(DISTINCT StudentID) FROM VSStudents WHERE UserApproval = 1) AS totalApprovedUsers,
                                    (SELECT COUNT(DISTINCT StudentID) FROM VSStudents WHERE UserApproval = 0) AS totalUnapprovedUsers,
                                    (SELECT COUNT(DISTINCT StudentID) FROM VSVote WHERE CandidateApproval = 1) AS totalCandidates,
                                    (SELECT COUNT(DISTINCT StudentID) FROM VSVote WHERE SRCApproval = 1) AS totalSRC,
                                    (SELECT COUNT(DISTINCT StudentID) FROM VSVote WHERE NominationVoteLimit >= 1) AS totalParticipationNomination,
                                    (SELECT COUNT(DISTINCT StudentID) FROM VSVote WHERE SRCVoteLimit >= 1) AS totalParticipationSRC
                                FROM dual
                            ";

                            $result = $conn->query($reportQuery);

                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $totalUsers = $row['totalUsers'];
                                $totalApprovedUsers = $row['totalApprovedUsers'];
                                $totalUnapprovedUsers = $row['totalUnapprovedUsers'];
                                $totalCandidates = $row['totalCandidates'];
                                $totalSRC = $row['totalSRC'];
                                $totalParticipationNomination = $row['totalParticipationNomination'];
                                $totalParticipationSRC = $row['totalParticipationSRC'];

                                // Display the data in table rows
                                echo "<tr><td>Total Users</td><td>" . $totalUsers . "</td></tr>";
                                echo "<tr><td>Total Approved Users</td><td>" . $totalApprovedUsers . "</td></tr>";
                                echo "<tr><td>Total Unapproved Users</td><td>" . $totalUnapprovedUsers . "</td></tr>";
                                echo "<tr><td>Total Candidates</td><td>" . $totalCandidates . "</td></tr>";
                                echo "<tr><td>Total SRC</td><td>" . $totalSRC . "</td></tr>";
                            } else {
                                echo "<tr><td colspan='2'>No data available.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- New pie chart for participation -->
                <canvas id="participationChart" width="400" height="200"></canvas>

                <!-- Table for the participation data (below pie chart) -->
                <div class="participation-details" style="margin-top: 20px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Participation Type</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nomination Vote Participation</td>
                                <td><?php echo $totalParticipationNomination; ?></td>
                            </tr>
                            <tr>
                                <td>SRC Vote Participation</td>
                                <td><?php echo $totalParticipationSRC; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- New section for displaying SRC-approved students sorted by votes -->
                <div class="src-details" style="margin-top: 20px;">
                    <h2>Active SRC List</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Student ID</th>
                                <th>Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query to fetch SRC-approved students
                            $srcQuery = "
                                SELECT VSStudents.StudentProfilePicture, 
                                       VSStudents.StudentName, 
                                       VSStudents.StudentID, 
                                       VSVote.TotalSRCVote
                                FROM VSStudents
                                JOIN VSVote ON VSStudents.StudentID = VSVote.StudentID
                                WHERE VSVote.SRCApproval = 1
                                ORDER BY VSVote.TotalSRCVote DESC
                            ";
                            $result = $conn->query($srcQuery);

                            // Display each SRC-approved student in the table
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td><img src='../../../ProfilePicture/" . htmlspecialchars($row["StudentProfilePicture"]) . "' class='src-image' alt='Profile Picture'></td>";
                                    echo "<td>" . htmlspecialchars($row["StudentName"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["StudentID"]) . "</td>";
                                    echo "<td><button class='view-voters-button' data-student-id='" . htmlspecialchars($row["StudentID"]) . "'>" . htmlspecialchars($row["TotalSRCVote"]) . " View Voters</button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No SRC-approved students found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Modal for displaying voters -->
                <div id="votersModal" style="display:none;">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Voters List</h2>
                        <div id="votersList"></div>
                    </div>
                </div>

            </center>
        </div>
    </main>
    <script>
        // JavaScript for charts
        const ctxReport = document.getElementById('reportChart').getContext('2d');
        const reportChart = new Chart(ctxReport, {
            type: 'bar',
            data: {
                labels: ['Total Users', 'Approved Users', 'Unapproved Users', 'Candidates', 'SRC'],
                datasets: [{
                    label: 'Count',
                    data: [
                        <?php echo $totalUsers; ?>,
                        <?php echo $totalApprovedUsers; ?>,
                        <?php echo $totalUnapprovedUsers; ?>,
                        <?php echo $totalCandidates; ?>,
                        <?php echo $totalSRC; ?>
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Pie chart for participation
        const ctxParticipation = document.getElementById('participationChart').getContext('2d');
        const participationChart = new Chart(ctxParticipation, {
            type: 'pie',
            data: {
                labels: ['Nomination Vote Participation', 'SRC Vote Participation'],
                datasets: [{
                    data: [
                        <?php echo $totalParticipationNomination; ?>,
                        <?php echo $totalParticipationSRC; ?>
                    ],
                    backgroundColor: ['rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)'],
                    borderColor: ['rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Participation Overview'
                    }
                }
            }
        });

        // JavaScript to handle button click and modal display
        document.querySelectorAll('.view-voters-button').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.getAttribute('data-student-id');
                fetchVoters(studentId);
            });
        });

function fetchVoters(studentId) {
    // Fetch the voters based on the selected SRC student
    fetch(`getVoters.php?studentId=${studentId}`)
        .then(response => response.json())
        .then(data => {
            const votersList = document.getElementById('votersList');
            votersList.innerHTML = ''; // Clear previous content
            
            if (data.length > 0) {
                const list = document.createElement('ul');
                data.forEach(voter => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `Voter Name: ${voter.name}, Voter ID: ${voter.studentId}, Vote Type: ${voter.voteType}`;
                    list.appendChild(listItem);
                });
                votersList.appendChild(list);
            } else {
                votersList.innerHTML = 'No voters found for this SRC.';
            }

            // Show the modal
            document.getElementById('votersModal').style.display = 'block';
        })
        .catch(error => console.error('Error fetching voters:', error));
}


        // Close modal when the close button is clicked
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('votersModal').style.display = 'none';
        });

        // Close modal when clicking outside of the modal
        window.onclick = function(event) {
            if (event.target == document.getElementById('votersModal')) {
                document.getElementById('votersModal').style.display = 'none';
            }
        };
    </script>
</body>
</html>
