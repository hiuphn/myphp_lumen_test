<!-- src/components/Login.vue -->
<template>
    <div class="container">
      <div class="left-section">
        <div class="logo">AMU</div>
        <a href="#" class="back-btn">Back to website →</a>
        <div class="text">Capturing Moments,<br>Creating Memories</div>
      </div>
      <div class="right-section">
        <h2>Register</h2>
        
        <form @submit.prevent="handleLogin">
          <!-- <div class="form-group">
            <input type="text" placeholder="Fletcher" v-model="form.firstName" />
          </div> -->
          <div class="form-group">
            <input type="text" placeholder="Full name" v-model="form.name" />
          </div>
          <div class="form-group">
            <input type="email" placeholder="Email" v-model="form.email" required />
          </div>
          <div class="form-group">
            <input type="password" placeholder="Enter your password" v-model="form.password" required />
          </div>
          <div class="checkbox">
            <input type="checkbox" id="terms" v-model="form.terms" required />
            <label for="terms">I agree to the Terms & Conditions</label>
          </div>
          <button type="submit" class="create-btn">Create account</button>
          <div class="login-link">
          <router-link to="/register">Already have not an account? register</router-link>
        </div>
        </form>
        <div class="divider">Or register with</div>
        <div class="social-login">
          <button @click="socialLogin('google')">
            <img src="https://www.google.com/favicon.ico" alt="Google" /> Google
          </button>
          <button @click="socialLogin('apple')">
            <img src="https://www.apple.com/favicon.ico" alt="Apple" /> Apple
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'LoginPage',
    data() {
      return {
        form: {
          name: '',
          email: '',
          password: '',
          terms: false,
        },
      };
    },
    methods: {
      async handleLogin() {
        // Basic client-side validation
        if (!this.form.email || !this.form.password || !this.form.terms) {
          alert('Please fill in all required fields and agree to the terms.');
          return;
        }
  
        try {
          const response = await fetch('http://localhost:8000/auth/register', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              name: this.form.name,
              email: this.form.email,
              password: this.form.password,
            }),
          });
  
          const data = await response.json();
  
          if (response.ok) {
            alert(data.message); 
            // Redirect to a dashboard or homepage
            this.$router.push('/vat-invoices');
          } else {
            alert(data.error || 'Đăng kí thất bại');
          }
        } catch (error) {
          console.error('Error:', error);
          alert('An error occurred during login');
        }
      },
      socialLogin(provider) {
        // Placeholder for social login (Google, Apple)
        alert(`Login with ${provider} is not implemented yet.`);
      },
    },
  };
  </script>
  
  <style scoped>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Arial', sans-serif;
  }
  
  .container {
    display: flex;
    width: 900px;
    height: 600px;
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin: 0 auto;
  }
  
  .left-section {
    width: 50%;
    background: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c') no-repeat center center;
    background-size: cover;
    position: relative;
  }
  
  .left-section .logo {
    position: absolute;
    top: 20px;
    left: 20px;
    color: #fff;
    font-size: 24px;
    font-weight: bold;
  }
  
  .left-section .back-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    color: #fff;
    text-decoration: none;
    background-color: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
  }
  
  .left-section .text {
    position: absolute;
    bottom: 40px;
    left: 20px;
    color: #fff;
    font-size: 24px;
    font-weight: 300;
  }
  
  .right-section {
    width: 50%;
    padding: 40px;
    background-color: #1a1a2e;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  
  .right-section h2 {
    font-size: 28px;
    margin-bottom: 10px;
  }
  
  .right-section .login-link {
    text-align: center;
    margin-bottom: 20px;
  }
  
  .right-section .login-link a {
    color: #a0a0ff;
    text-decoration: none;
    font-size: 14px;
  }
  
  .right-section .form-group {
    margin-bottom: 15px;
  }
  
  .right-section .form-group input {
    width: 100%;
    padding: 12px;
    border: 1px solid #444;
    border-radius: 5px;
    background-color: #2a2a3e;
    color: #fff;
    font-size: 14px;
  }
  
  .right-section .form-group input::placeholder {
    color: #888;
  }
  
  .right-section .checkbox {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    font-size: 14px;
  }
  
  .right-section .checkbox input {
    margin-right: 10px;
  }
  
  .right-section .create-btn {
    width: 100%;
    padding: 12px;
    background-color: #6a5af9;
    border: none;
    border-radius: 5px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    margin-bottom: 20px;
  }
  
  .right-section .create-btn:hover {
    background-color: #5a4af9;
  }
  
  .right-section .divider {
    text-align: center;
    margin: 20px 0;
    color: #888;
    font-size: 14px;
  }
  
  .right-section .social-login {
    display: flex;
    justify-content: space-between;
  }
  
  .right-section .social-login button {
    width: 48%;
    padding: 10px;
    border: 1px solid #444;
    border-radius: 5px;
    background-color: transparent;
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .right-section .social-login button img {
    width: 20px;
    margin-right: 10px;
  }
  </style>