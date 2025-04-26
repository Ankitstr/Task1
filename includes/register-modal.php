<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="registerModalLabel">Create Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="registerError" class="alert alert-danger d-none"></div>
                
                <form id="registerForm" action="process_register.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback">
                                Please enter your full name.
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="register_email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="register_email" name="email" required>
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="register_password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="register_password" name="password" required>
                            <div class="invalid-feedback">
                                Please enter a password.
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="invalid-feedback">
                                Please confirm your password.
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" required>
                        <label class="form-check-label" for="terms">I agree to the <a href="#" class="text-primary">Terms and Conditions</a></label>
                        <div class="invalid-feedback">
                            You must agree to the terms and conditions.
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? <a href="#" class="text-primary" id="switchToLogin">Login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }
    
    .modal-header {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    .modal-title {
        font-weight: 600;
    }
    
    .btn-close {
        color: white;
    }
    
    .form-label {
        font-weight: 500;
        color: #333;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }
    
    .form-control {
        border-left: none;
    }
    
    .form-control:focus {
        box-shadow: none;
        border-color: #ced4da;
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        padding: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }
    
    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .text-primary {
        color: #007bff !important;
        text-decoration: none;
        font-weight: 500;
    }
    
    .text-primary:hover {
        text-decoration: underline;
    }

    /* Modal backdrop styles */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5) !important;
        opacity: 1 !important;
    }
    
    .modal-open {
        overflow: auto !important;
        padding-right: 0 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const registerForm = document.getElementById('registerForm');
        const registerError = document.getElementById('registerError');
        
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset error message
                registerError.classList.add('d-none');
                registerError.textContent = '';
                
                // Check if passwords match
                const password = document.getElementById('register_password');
                const confirmPassword = document.getElementById('confirm_password');
                
                if (password.value !== confirmPassword.value) {
                    registerError.textContent = "Passwords don't match";
                    registerError.classList.remove('d-none');
                    return;
                }
                
                // Validate form
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    return;
                }
                
                // Submit form via AJAX
                const formData = new FormData(this);
                
                fetch('process_register.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    try {
                        const result = JSON.parse(data);
                        if (result.success) {
                            // Close modal and reload page
                            const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                            registerModal.hide();
                            window.location.reload();
                        } else {
                            // Show error message
                            registerError.textContent = result.message;
                            registerError.classList.remove('d-none');
                        }
                    } catch (e) {
                        // If response is not JSON, it's a redirect
                        window.location.href = data;
                    }
                })
                .catch(error => {
                    registerError.textContent = "An error occurred. Please try again.";
                    registerError.classList.remove('d-none');
                    console.error('Error:', error);
                });
            });
        }

        // Switch between login and register modals
        const switchToLogin = document.getElementById('switchToLogin');
        if (switchToLogin) {
            switchToLogin.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get the register modal instance
                const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                
                // Hide the register modal
                registerModal.hide();
                
                // Clean up any existing backdrops
                const existingBackdrops = document.querySelectorAll('.modal-backdrop');
                existingBackdrops.forEach(backdrop => backdrop.remove());
                
                // Reset body styles
                document.body.classList.remove('modal-open');
                document.body.style.overflow = 'auto';
                document.body.style.paddingRight = '0';
                
                // Show login modal after a short delay
                setTimeout(function() {
                    // Create new login modal instance
                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'), {
                        backdrop: true,
                        keyboard: true
                    });
                    
                    // Show the login modal
                    loginModal.show();
                }, 300);
            });
        }

        // Handle modal closing
        const registerModal = document.getElementById('registerModal');
        registerModal.addEventListener('hidden.bs.modal', function () {
            // Remove modal-open class and reset body styles
            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '0';
            
            // Remove any remaining backdrop
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Reset form
            registerForm.reset();
            registerForm.classList.remove('was-validated');
            registerError.classList.add('d-none');
        });
    });
</script> 