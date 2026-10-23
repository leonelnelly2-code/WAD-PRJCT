document.addEventListener('DOMContentLoaded', function() {
    
    // 1. registration validation
    const regForm = document.getElementById('registrationForm');
    if (regForm) {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');
        const messageBox = document.getElementById('registrationMessage');

        regForm.addEventListener('submit', function(event) {
            // check if passwords match
            if (password.value !== confirmPassword.value) {
                event.preventDefault(); // prevent from submitting to php
                
                // show error message
                messageBox.style.display = 'block';
                messageBox.style.backgroundColor = '#ffebee';
                messageBox.style.color = '#c62828';
                messageBox.style.border = '1px solid #ef9a9a';
                messageBox.style.padding = '10px';
                messageBox.style.marginTop = '15px';
                messageBox.style.borderRadius = '5px';
                messageBox.textContent = "Error: Passwords do not match!";
                
                confirmPassword.focus();
            }
        });
    }

  
    const contactMessage = document.getElementById('message');
    if (contactMessage) {
        const charCountDisplay = document.createElement('small');
        charCountDisplay.style.display = 'block';
        charCountDisplay.style.textAlign = 'right';
        charCountDisplay.style.color = '#666';
        charCountDisplay.style.marginTop = '5px';
        charCountDisplay.textContent = '0 / 500 characters';

        contactMessage.parentNode.appendChild(charCountDisplay);

        contactMessage.addEventListener('input', function() {
            const currentLength = contactMessage.value.length;
            charCountDisplay.textContent = currentLength + ' / 500 characters';
            
            if (currentLength >= 500) {
                charCountDisplay.style.color = 'red';
                charCountDisplay.textContent = 'Limit reached: ' + currentLength + ' / 500 characters';
            } else {
                charCountDisplay.style.color = '#666';
            }
        });
    }

    const routeForm = document.querySelector('.route-form-card form');
    if (routeForm) {
        routeForm.addEventListener('submit', function(event) {
            const startPoint = document.getElementById('start_point').value;
            const destination = document.getElementById('destination').value;

            if (startPoint === destination) {
                event.preventDefault();
                alert('Start point and destination cannot be the same!');
            }
        });
    }
});