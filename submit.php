<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';
require 'email_config.php'; // Added this line

header('Content-Type: application/json'); // Set response type to JSON

// Database connection
$conn = new mysqli("localhost", "root", "", "food_donation");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Sanitize and validate input data
$name = $conn->real_escape_string($_POST['name']);
$email = $conn->real_escape_string($_POST['email']);
$phone = $conn->real_escape_string($_POST['phone']);
$address = $conn->real_escape_string($_POST['address']);
$food_type = $conn->real_escape_string($_POST['food_type']);
$quantity = $conn->real_escape_string($_POST['quantity']);
$best_before = $conn->real_escape_string($_POST['best_before']);
$instructions = $conn->real_escape_string($_POST['instructions']);

// Insert into database
$sql = "INSERT INTO donations (name, email, phone, address, food_type, quantity, best_before, instructions)
        VALUES ('$name', '$email', '$phone', '$address', '$food_type', '$quantity', '$best_before', '$instructions')";

if ($conn->query($sql)) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL;
        $mail->Password = PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom(EMAIL, 'FeedBridge');
        $mail->addAddress($email); // Donor's email
        $mail->addAddress(EMAIL); // Admin email (your email)
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Thank you for your donation!';
        $mail->Body = "
            <h2>Thank you for your donation, $name!</h2>
            <p>We've received your donation details:</p>
            <ul>
                <li><strong>Food Type:</strong> $food_type</li>
                <li><strong>Quantity:</strong> $quantity</li>
                <li><strong>Best Before:</strong> $best_before</li>
                <li><strong>Pickup Address:</strong> $address</li>
            </ul>
            <p>Our team will contact you shortly at $phone to arrange pickup.</p>
            <p>Special instructions: " . ($instructions ? $instructions : 'None') . "</p>
            <p>Thank you for helping fight hunger in our community!</p>
            <p>- The FeedBridge Team</p>
        ";
        
        $mail->AltBody = "Thank you for your donation, $name! We've received your details and will contact you shortly at $phone to arrange pickup.";

        $mail->send();
        
        // Success response
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for donating! A confirmation has been sent to your email.'
        ]);
        
    } catch (Exception $e) {
        // Email failed but database was saved
        error_log("Mailer Error: " . $mail->ErrorInfo);
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for donating! We could not send a confirmation email but your donation was recorded.'
        ]);
    }
} else {
    // Database error
    echo json_encode([
        'success' => false,
        'message' => 'Error submitting your donation. Please try again later.'
    ]);
}

$conn->close();
?>