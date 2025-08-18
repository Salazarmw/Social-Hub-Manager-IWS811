/**
 * Profile page functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Handle profile information form
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            // Prevent default submission
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(profileForm);
            
            // Send AJAX request
            fetch(profileForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                // Show success message
                const successMessage = document.createElement('p');
                successMessage.textContent = 'Profile updated successfully!';
                successMessage.className = 'text-sm text-green-600 mt-2';
                
                // Find the submit button container
                const buttonContainer = profileForm.querySelector('.flex.items-center.gap-4');
                
                // Remove any existing success message
                const existingMessage = buttonContainer.querySelector('.text-green-600');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Add the new message
                buttonContainer.appendChild(successMessage);
                
                // Hide the message after 2 seconds
                setTimeout(() => {
                    successMessage.remove();
                }, 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error message
                const errorMessage = document.createElement('p');
                errorMessage.textContent = 'Error updating profile. Please try again.';
                errorMessage.className = 'text-sm text-red-600 mt-2';
                
                // Find the submit button container
                const buttonContainer = profileForm.querySelector('.flex.items-center.gap-4');
                
                // Remove any existing error message
                const existingMessage = buttonContainer.querySelector('.text-red-600');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Add the new message
                buttonContainer.appendChild(errorMessage);
                
                // Hide the message after 3 seconds
                setTimeout(() => {
                    errorMessage.remove();
                }, 3000);
            });
        });
    }
    
    // Handle password update form
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            // Prevent default submission
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(passwordForm);
            
            // Send AJAX request
            fetch(passwordForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error updating password');
                    });
                }
                return response.json();
            })
            .then(data => {
                // Show success message
                const successMessage = document.createElement('p');
                successMessage.textContent = 'Password updated successfully!';
                successMessage.className = 'text-sm text-green-600 mt-2';
                
                // Find the submit button container
                const buttonContainer = passwordForm.querySelector('.flex.items-center.gap-4');
                
                // Remove any existing success message
                const existingMessage = buttonContainer.querySelector('.text-green-600');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Add the new message
                buttonContainer.appendChild(successMessage);
                
                // Clear password fields
                passwordForm.querySelector('#update_password_current_password').value = '';
                passwordForm.querySelector('#update_password_password').value = '';
                passwordForm.querySelector('#update_password_password_confirmation').value = '';
                
                // Hide the message after 2 seconds
                setTimeout(() => {
                    successMessage.remove();
                }, 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error message
                const errorMessage = document.createElement('p');
                errorMessage.textContent = error.message || 'Error updating password. Please try again.';
                errorMessage.className = 'text-sm text-red-600 mt-2';
                
                // Find the submit button container
                const buttonContainer = passwordForm.querySelector('.flex.items-center.gap-4');
                
                // Remove any existing error message
                const existingMessage = buttonContainer.querySelector('.text-red-600');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Add the new message
                buttonContainer.appendChild(errorMessage);
                
                // Hide the message after 3 seconds
                setTimeout(() => {
                    errorMessage.remove();
                }, 3000);
            });
        });
    }
});