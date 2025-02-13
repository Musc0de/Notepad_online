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
                console.log('Content saved successfully.');
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
    }, 10); // 100ms delay
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
