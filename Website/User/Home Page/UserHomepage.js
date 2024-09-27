document.getElementById('barsButton').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').style.width = document.getElementById('overlay').style.width === '100%' ? '0' : '100%';
});

document.getElementById('overlay').addEventListener('click', function() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').style.width = '0';
});

const LOGOUTButton = document.getElementById('LOGOUTButton');

LOGOUTButton.addEventListener('click', function() {
    // Confirm logout
    if (confirm("Are you sure you want to logout?")) {
        // Perform logout by clearing session and redirecting to login page
        fetch('LogOutProcess.php', {
            method: 'POST',
            credentials: 'same-origin'
        })
        .then(response => {
            if (response.ok) {
                alert('Logged out successfully!');
                window.location.href = '../../../index.html'; // Updated URL
            } else {
                throw new Error('Logout failed.');
            }
        })
        .catch(error => {
            console.error('Logout error:', error);
            alert('Logout failed. Please try again.');
        });
    }
});