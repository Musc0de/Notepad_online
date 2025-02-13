const fullScreenButton = document.getElementById('fullScreenButton');

fullScreenButton.addEventListener('click', () => {
    if (document.fullscreenElement) {
        document.exitFullscreen();
    } else {
        document.documentElement.requestFullscreen();
    }
});
const lightSwitchButton = document.getElementById('lightSwitchButton');

lightSwitchButton.addEventListener('click', function() {
    document.body.classList.toggle('dark-mode');
    const isDarkMode = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDarkMode);
});

document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
        lightSwitchButton.textContent = 'Light Mode'; 
    } else {
        lightSwitchButton.textContent = 'Dark Mode';
    }
});
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-PV22MGC066');
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
                    var sessionXhr = new XMLHttpRequest();
                    sessionXhr.open('POST', '<?php echo $base_url; ?>/api/set_session.php', true);
                    sessionXhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    sessionXhr.send('note_path=' + encodeURIComponent(noteName) + '&status=valid');

                    localStorage.setItem(noteName + '_authenticated', 'true');
                    localStorage.setItem(noteName + '_password', result.value.password);

                    Swal.fire('Success', 'Note unlocked successfully.', 'success').then(function() {
                        contentTextarea.style.display = 'block';
                        contentTextarea.value = content;
                        window.location.reload();
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
function saveContent(noteName, content, csrfToken) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo $base_url; ?>/' + noteName, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                console.log('Content saved successfully.');
            } else if (xhr.status === 401) {
                localStorage.removeItem(noteName + '_authenticated');
                localStorage.removeItem(noteName + '_password');
                askForPassword(noteName, csrfToken);
            } else {
                Swal.fire('Error', 'Failed to save the note: ' + xhr.responseText, 'error');
            }
        }
    };

    var data = 'text=' + encodeURIComponent(content) + '&csrf_token=' + encodeURIComponent(csrfToken);
    xhr.send(data);
}

contentTextarea.addEventListener('input', function() {
    saveContent(noteName, contentTextarea.value, csrfToken);
});
