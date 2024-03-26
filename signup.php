<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
h1{
    color:blue;
    text-align:center;

    font-family: 'Arial', sans-serif;
    margin-top: 0;
}

    body {
    font-family: 'Arial', sans-serif;
    background-color:black;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

form {
    max-width: 400px;
    margin: 0 auto;
}

label {
    display: block;
    margin-bottom: 8px;
}

input,
textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

button {
    background-color: #4caf50;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #45a049;
}

.form-control {
    position: relative;
}

.form-control input,
.form-control textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.form-control small {
    position: absolute;
    bottom: 0;
    color: #d9534f;
}

.form-control.error input,
.form-control.error textarea {
    border-color: #d9534f;
}

.form-control.success input,
.form-control.success textarea {
    border-color: #5cb85c;
}

@media (max-width: 600px) {
    body {
        padding: 10px;
    }

    .container {
        width: 100%;
    }
}


</style>
<body>
    
    <div class="container">

    <h1>Sign UP</h1>
        <form id="signupForm" action="../model/Uuserinsert.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="address">Address:</label>
            <textarea id="address" name="address" required></textarea>

            <button type="submit">Sign Up</button>
            <p>Already have an account? <a href="login.php">Log in</a></p>

        </form>
    </div>

    <script>
      
      
        function validateForm() {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            var phone = document.getElementById('phone').value;
            var address = document.getElementById('address').value;

            if (username.trim() === '' || password.trim() === '' || phone.trim() === '' || address.trim() === '') {
                alert('All fields are required');
                return false;
            }

            // Additional validation logic can be added here
            //email validation
            var email = document.getElementById('email').value;
            var atposition = email.indexOf("@");
            var dotposition = email.lastIndexOf(".");
            if (atposition < 1 || dotposition < atposition + 2 || dotposition + 2 >= email.length) {
                alert("Please enter a valid e-mail address");
                return false;
            }
            //password validation
            var password = document.getElementById('password').value;
            var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
            if (!password.match(passw)) {
                alert("Password must be between 6 to 20 characters which contain at least one numeric digit, one uppercase and one lowercase letter");
                return false;
            }
            //phone validation
            var phone = document.getElementById('phone').value;
            var phoneno = /^\d{11}$/;

            if (!phone.match(phoneno)) {
                alert("Please enter a valid phone number");
                return false;
            }
            //address validation
            var address = document.getElementById('address').value;
            var address = /^[a-zA-Z0-9\s,.'-]{3,}$/;
            if (!address.match(address)) {
                alert("Please enter a valid address");
                return false;
            }


            return true;
        }
    </script>
</body>
</html>
