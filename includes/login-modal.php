<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="loginModalLabel">Login to Your Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loginError" class="alert alert-danger d-none"></div>
                
                <form id="loginForm" action="process_login.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="login_email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="login_email" name="email" required>
                            <div class="invalid-feedback">
                                Please enter your email address.
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="login_password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="login_password" name="password" required>
                            <div class="invalid-feedback">
                                Please enter your password.
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                    
                    <div class="text-center">
                        <p class="mb-0">Don't have an account? <a href="#" class="text-primary" id="switchToRegister">Register</a></p>
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
        const loginForm = document.getElementById('loginForm');
        const loginError = document.getElementById('loginError');
        
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset error message
                loginError.classList.add('d-none');
                loginError.textContent = '';
                
                // Validate form
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    return;
                }
                
                // Submit form via AJAX
                const formData = new FormData(this);
                
                fetch('process_login.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    try {
                        const result = JSON.parse(data);
                        if (result.success) {
                            // Close modal and reload page
                            const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                            loginModal.hide();
                            window.location.reload();
                        } else {
                            // Show error message
                            loginError.textContent = result.message;
                            loginError.classList.remove('d-none');
                        }
                    } catch (e) {
                        // If response is not JSON, it's a redirect
                        window.location.href = data;
                    }
                })
                .catch(error => {
                    loginError.textContent = "An error occurred. Please try again.";
                    loginError.classList.remove('d-none');
                    console.error('Error:', error);
                });
            });
        }

        // Switch between login and register modals
        const switchToRegister = document.getElementById('switchToRegister');
        if (switchToRegister) {
            switchToRegister.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get the login modal instance
                const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                
                // Hide the login modal
                loginModal.hide();
                
                // Clean up any existing backdrops
                const existingBackdrops = document.querySelectorAll('.modal-backdrop');
                existingBackdrops.forEach(backdrop => backdrop.remove());
                
                // Reset body styles
                document.body.classList.remove('modal-open');
                document.body.style.overflow = 'auto';
                document.body.style.paddingRight = '0';
                
                // Show register modal after a short delay
                setTimeout(function() {
                    // Create new register modal instance
                    const registerModal = new bootstrap.Modal(document.getElementById('registerModal'), {
                        backdrop: true,
                        keyboard: true
                    });
                    
                    // Show the register modal
                    registerModal.show();
                }, 300);
            });
        }

        // Handle modal closing
        const loginModal = document.getElementById('loginModal');
        loginModal.addEventListener('hidden.bs.modal', function () {
            // Remove modal-open class and reset body styles
            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '0';
            
            // Remove any remaining backdrop
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Reset form
            loginForm.reset();
            loginForm.classList.remove('was-validated');
            loginError.classList.add('d-none');
        });
    });
</script> 