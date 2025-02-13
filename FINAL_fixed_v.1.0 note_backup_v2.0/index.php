<?php
// index.php
include 'fetch.php'; // Memanggil dan mengeksekusi fetch.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($_GET['note']) ? htmlspecialchars($_GET['note'], ENT_QUOTES, 'UTF-8') : "Online Notepad - Dream Note - Your Online Notepad"; ?></title>
    <meta name="description" content="<?php
        if (isset($_GET['note'])) {
            $noteContent = file_get_contents($_GET['note']);
            $trimmedDescription = substr(strip_tags($noteContent), 0, 150); 
            echo htmlspecialchars($trimmedDescription . "...", ENT_QUOTES, 'UTF-8');
        } else {
            echo "Dream Note is a practical online notepad for organizing ideas, projects, and important information easily and securely.";
        }
    ?>">
    <meta name="keywords" content="online notepad, digital notepad, free online notepad, secure note taking, encrypted notes, private notes, collaborative note taking, note organization, productivity tools, idea organization, best online notepad, how to take notes online">
    <meta name="author" content="Dream Note">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?php echo isset($_GET['note']) ? htmlspecialchars($_GET['note'], ENT_QUOTES, 'UTF-8') : "Online Notepad - Dream Note - Your Online Notepad"; ?>">
    <meta property="og:description" content="<?php echo isset($trimmedDescription) ? htmlspecialchars($trimmedDescription, ENT_QUOTES, 'UTF-8') : "Dream Note is a practical online notepad for organizing ideas, projects, and important information easily and securely."; ?>">
    <meta property="og:type" content="website">
  	<meta name="description" content="Dream Note : A simple, secure online notepad for quickly jotting down your ideas, notes, and to-do lists. Your notes are private and accessible from anywhere.">
    <meta property="og:url" content="<?php echo $base_url; ?>">
    <meta property="og:image" content="<?php echo $base_url; ?>/favicon.svg">
    <meta name="twitter:card" content="summary_small_image">
  	<meta name="twitter:title" content="Home - Dream Note">
	<meta name="twitter:description" content="Explore Dream Note Privacy Policy to understand how we handle your data, protect your privacy, and ensure secure usage of our services.">
	<meta name="twitter:image" content="<?php echo $base_url; ?>/favicon.svg">
  	<meta name="yandex-verification" content="48e209397e0e93cb" />
    <link rel="icon" href="<?php echo $base_url; ?>/favicon.ico" sizes="any">
    <link rel="icon" href="<?php echo $base_url; ?>/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/styles.css">
</head>
<body>
    <header>
        <h1><?php echo isset($_GET['note']) ? htmlspecialchars($_GET['note'], ENT_QUOTES, 'UTF-8') : "Online Notepad - Secure Note Taking"; ?></h1>
        <img src="<?php echo $base_url; ?>/favicon.ico" alt="Online Notepad">
        <p><?php 
            if (isset($_GET['note'])) {
                echo nl2br(htmlspecialchars($noteContent, ENT_QUOTES, 'UTF-8'));
            } else {
                echo "Welcome to Dream Note, your reliable online notepad for secure and easy note taking. Organize your ideas, projects, and important information with ease.";
            }
        ?></p>
    </header>
    <div class="container">
        <label for="content"></label>
          <div class="bottom-right">
        <div class="button-container">
            <div class="note-options">
                <button class="new-note-button" id="newNoteButton">+</button>
                <?php if (!noteHasPassword($note_name)): ?>
                    <button class="set-password-button" id="setPasswordButton">Set Password</button>
                <?php endif; ?>
                <?php if (noteHasPassword($note_name)): ?>
                    <button class="change-password-button" id="changePasswordButton">Change Password</button>
                <?php endif; ?>
            </div>
        <button class="full-screen-button" id="fullScreenButton">Full Screen</button>
		<button class="light-switch-button" id="lightSwitchButton">Light Switch</button>
    </div>
        <textarea id="content" name="content"><?php
            if (is_file($path)) {
                echo htmlspecialchars(file_get_contents($path), ENT_QUOTES, 'UTF-8');
            }
        ?></textarea>
            <div class="links">
                <a href="<?php echo $base_url; ?>/Privacy.php">Privacy</a>
                <a href="<?php echo $base_url; ?>/Terms.php">Terms</a>
                <a href="<?php echo $base_url; ?>/ContactUs.php">Contact Us</a>
                <a href="<?php echo $base_url; ?>/AboutUs.php">About Us</a>
            </div>
        </div>


    <pre id="printable"></pre>
      <!-- Google tag (gtag.js) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

// Menggunakan tombol "Full Screen"
const fullScreenButton = document.getElementById('fullScreenButton');

fullScreenButton.addEventListener('click', () => {
    if (document.fullscreenElement) {
        // Keluar dari mode full screen jika sedang dalam full screen
        document.exitFullscreen();
    } else {
        // Memasuki mode full screen jika tidak dalam full screen
        document.documentElement.requestFullscreen();
    }
});


// Ambil tombol "Light Switch"
const lightSwitchButton = document.getElementById('lightSwitchButton');

// Tambahkan event listener untuk saat tombol ditekan
lightSwitchButton.addEventListener('click', function() {
    // Toggle kelas 'dark-mode' pada elemen body
    document.body.classList.toggle('dark-mode');
    
    // Simpan preferensi tema di local storage (opsional)
    const isDarkMode = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDarkMode);
});

// Terapkan tema yang disimpan saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
if (document.body.classList.contains('dark-mode')) {
  lightSwitchButton.textContent = 'Light Mode'; 
} else {
  lightSwitchButton.textContent = 'Dark Mode';
}
});

window.dataLayer = window.dataLayer || [];

   	document.addEventListener('DOMContentLoaded', function() {
        var newNoteButton = document.getElementById('newNoteButton');
        var setPasswordButton = document.getElementById('setPasswordButton');
        var changePasswordButton = document.getElementById('changePasswordButton');
        var contentTextarea = document.getElementById('content');
        var noteName = '<?php echo $note_name; ?>';
        var csrfToken = '<?php echo $csrf_token; ?>';
        var printable = document.getElementById('printable');
        var content = contentTextarea.value;

        newNoteButton.addEventListener('click', function() {
            window.location.href = '<?php echo $base_url; ?>';
        });

        <?php if (noteHasPassword($note_name)): ?>
var authenticated = localStorage.getItem(noteName + '_authenticated');

if (!authenticated) {
    contentTextarea.style.display = 'none';

    Swal.fire({
        title: 'Unlock Note',
        html: '<input type="password" id="password" class="swal2-input" placeholder="Enter password note">',
        showCancelButton: true,
        confirmButtonText: 'Unlock Note',
        preConfirm: () => {
            const password = Swal.getPopup().querySelector('#password').value;
            if (!password) {
                Swal.showValidationMessage('Password cannot be empty');
            } else {
                return { password: password };
            }
        }
    }).then((result) => {
        if (result.value) {
            authenticateNote(noteName, result.value.password, csrfToken, function(status) {
                if (status === 200) {
                    // Simpan status otentikasi di session server
                    var sessionXhr = new XMLHttpRequest();
                    sessionXhr.open('POST', '<?php echo $base_url; ?>/api/set_session.php', true);
                    sessionXhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    sessionXhr.send('note_path=' + encodeURIComponent(noteName) + '&status=valid');

                    // Simpan di localStorage juga
                    localStorage.setItem(noteName + '_authenticated', 'true');
                    localStorage.setItem(noteName + '_password', result.value.password); 

                    Swal.fire('Success', 'Note unlocked successfully.', 'success').then(function() {
                        contentTextarea.style.display = 'block';
                        contentTextarea.value = content; // Isi dengan konten dari note
                        window.location.reload(); // Reload halaman setelah berhasil otentikasi
                    });
                } else {
                    Swal.fire('Error', 'Incorrect password. Access denied.', 'error');
                }
            });
        }
    });
}
<?php endif; ?>
        if (setPasswordButton) {
            setPasswordButton.addEventListener('click', function() {
                Swal.fire({
                    title: 'Set Password',
                    input: 'password',
                    inputPlaceholder: 'Enter password for this note',
                    showCancelButton: true,
                    confirmButtonText: 'Set',
                    cancelButtonText: 'Cancel',
                    preConfirm: (value) => {
                        if (!value) {
                            Swal.showValidationMessage('Password cannot be empty');
                        } else {
                            return new Promise((resolve) => {
                                setNotePassword(noteName, value, csrfToken, function(success) {
                                    if (success) {
                                        Swal.fire('Success', 'Password set successfully for this note.', 'success');
                                        resolve();
                                    } else {
                                        Swal.showValidationMessage('Failed to set password.');
                                        resolve();
                                    }
                                });
                            });
                        }
                    }
                });
            });
        }

        if (changePasswordButton) {
            changePasswordButton.addEventListener('click', function() {
                Swal.fire({
                    title: 'Change Password',
                    html: '<input type="password" id="currentPassword" class="swal2-input" placeholder="Enter current password">' +
                          '<input type="password" id="newPassword" class="swal2-input" placeholder="Enter new password">',
                    showCancelButton: true,
                    confirmButtonText: 'Change',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const currentPassword = Swal.getPopup().querySelector('#currentPassword').value;
                        const newPassword = Swal.getPopup().querySelector('#newPassword').value;
                        if (!currentPassword || !newPassword) {
                            Swal.showValidationMessage('All fields are required');
                        } else {
                            return { currentPassword: currentPassword, newPassword: newPassword };
                        }
                    }
                }).then((result) => {
                    if (result.value) {
                        authenticateNote(noteName, result.value.currentPassword, csrfToken, function(status) {
                            if (status === 200) {
                                setNotePassword(noteName, result.value.newPassword, csrfToken, function(success) {
                                    if (success) {
                                        Swal.fire('Success', 'Password changed successfully for this note.', 'success');
                                    } else {
                                        Swal.fire('Error', 'Failed to change password.', 'error');
                                    }
                                });
                            } else if (status === 401) {
                                Swal.fire('Error', 'Incorrect current password. Access denied.', 'error');
                            } else {
                                Swal.fire('Error', 'An unexpected error occurred.', 'error');
                            }
                        });
                    }
                });
            });
        }

  function authenticateNote(notePath, password, csrfToken, callback) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '<?php echo $base_url; ?>/api/check_password.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) { // Authentication successful
        var sessionXhr = new XMLHttpRequest();
        sessionXhr.open('POST', '<?php echo $base_url; ?>/api/set_session.php', true);
        sessionXhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        sessionXhr.send('note_path=' + encodeURIComponent(notePath) + '&status=valid');

        localStorage.setItem(notePath + '_authenticated', 'true');
        localStorage.setItem(notePath + '_password', password);

        Swal.fire('Success', 'Note unlocked.', 'success').then(function() {
          contentTextarea.style.display = 'block';
          contentTextarea.value = content; 

          // Start auto-syncing after successful authentication and initial content loading
          startAutoSync(notePath, csrfToken);
          startAutoLockTimer(notePath, csrfToken); // You'll need to add this function

        });
      } else { // Authentication failed or other errors
        Swal.fire('Error', (xhr.status === 401) ? 'Incorrect password. Access denied.' : 'An unexpected error occurred.', 'error');
      }
      callback(xhr.status);
    }
  };

  xhr.send('note_path=' + encodeURIComponent(notePath) + '&password=' + encodeURIComponent(password) + '&csrf_token=' + encodeURIComponent(csrfToken));
}

// Function to re-ask for the password (moved outside the if block)
function askForPassword(noteName, csrfToken) {
  Swal.fire({
    title: 'Password Required',
    input: 'password',
    inputPlaceholder: 'Enter password for this note',
    showCancelButton: true,
    confirmButtonText: 'Unlock',
    cancelButtonText: 'Cancel',
    preConfirm: (password) => {
      return new Promise((resolve) => {
        authenticateNote(noteName, password, csrfToken, function(status) {
          if (status === 200) {
            localStorage.setItem(noteName + '_authenticated', 'true');
            localStorage.setItem(noteName + '_password', password);
            Swal.fire('Success', 'Note unlocked.', 'success').then(function() {
              saveContent(noteName, contentTextarea.value, csrfToken);
            });
            resolve();
          } else if (status === 401) {
            Swal.showValidationMessage('Incorrect password. Access denied.');
            resolve(); 
          } else {
            Swal.showValidationMessage('An unexpected error occurred.');
            resolve();
          }
        });
      });
    }
  });
}


function setNotePassword(notePath, password, csrfToken, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo $base_url; ?>/api/set_password.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) { // Success
                // Update session in the browser
                localStorage.setItem(notePath + '_authenticated', 'true');
                localStorage.setItem(notePath + '_password', password); 
                callback(true); 
            } else {
                callback(false); // Failure
                Swal.fire('Error', 'Failed to set password: ' + xhr.responseText, 'error');
            }
        }
    };

    xhr.send('note_path=' + encodeURIComponent(notePath) + '&password=' + encodeURIComponent(password) + '&csrf_token=' + encodeURIComponent(csrfToken));
}

function saveContent(noteName, content, csrfToken) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo $base_url; ?>/' + noteName, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
            } else if (xhr.status === 401) { // Unauthorized access
                // Tangani kasus di mana sesi habis atau tidak valid
                localStorage.removeItem(noteName + '_authenticated');
                localStorage.removeItem(noteName + '_password');
                askForPassword(noteName, csrfToken); // Minta password lagi
            } else {
                Swal.fire('Error', 'Try Again!!' + xhr.responseText, 'error');
            }
        }
    };

    var password = localStorage.getItem(noteName + '_password') || ''; // Get stored password
    // Kirim data tanpa password jika sesi sudah terotentikasi
    var data = 'text=' + encodeURIComponent(content) + '&csrf_token=' + encodeURIComponent(csrfToken);

    xhr.send(data);
}

contentTextarea.addEventListener('input', function() {
    saveContent(noteName, contentTextarea.value, csrfToken);
});


function startAutoSync(noteName, csrfToken) {
  setInterval(function() {
    fetch('<?php echo $base_url; ?>/api/auth_sync.php?note=' + encodeURIComponent(noteName) + '&csrf_token=' + encodeURIComponent(csrfToken), {
      headers: {
        'X-API-KEY': 'a4feaaa3-1686-4e42-919d-17198c355939'
      }
    })
    .then(response => {
      if (!response.ok) {
        if (response.status === 401) {
          clearInterval(this); 
          askForPassword(noteName, csrfToken);
        } else if (response.status === 403 && response.text() === 'Access Denied.') {
          // Stop syncing if the API key is invalid (prevents unnecessary requests)
          clearInterval(this);
          console.error('Error syncing note: Invalid API Key.');
        } else {
          throw new Error('Network response was not ok.');
        }
      }
      return response.text(); 
    })
    .then(newContent => {
      if (newContent !== contentTextarea.value) {
        contentTextarea.value = newContent;
      }
    })
    .catch(error => {
      console.error('Error syncing note:', error);
    });
  }, 5000); 
}


function startAutoLockTimer(notePath, csrfToken) {
  let inactivityTimer;

  function resetTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(lockNote, 10 * 10 * 100); // 10 minutes (adjust as needed)
  }

  function lockNote() {
    contentTextarea.style.display = 'none'; // Hide content
    localStorage.removeItem(notePath + '_authenticated'); // Clear authentication
    localStorage.removeItem(notePath + '_password'); // Remove plain text password

    // Introduce a delay using setTimeout
    setTimeout(function() {
      askForPassword(notePath, csrfToken); 
    }, 5); // 100ms delay
  }

  // Event listeners to reset the timer on user activity
  document.addEventListener('mousemove', resetTimer);
  document.addEventListener('keypress', resetTimer);  

  contentTextarea.addEventListener('input', resetTimer);

  // Start the timer initially
  resetTimer();
}
// Auto-sync for notes without passwords (start immediately)
<?php if (!noteHasPassword($note_name)): ?>
    startAutoSync('<?php echo $note_name; ?>', csrfToken); 
  <?php endif; ?>



    });
</script>

</body>
</html>
