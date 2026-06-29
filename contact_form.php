<?php
// Set content type to JSON for AJAX responses
header('Content-Type: application/json');

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input data
    $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $phone = filter_var(trim($_POST["phone"]), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST["message"]), FILTER_SANITIZE_STRING);
    
    // Validation
    $errors = array();
    
    if (empty($name)) {
        $errors[] = "Jméno je povinné.";
    }
    
    if (empty($email) || !$email) {
        $errors[] = "Platný e-mail je povinný.";
    }
    
    if (empty($message)) {
        $errors[] = "Zpráva je povinná.";
    }
    
    // If no errors, send email
    if (empty($errors)) {
        
        // Email configuration
        $to = "milosmarikosobnitrener@email.cz";
        $subject = "Nová zpráva z webu - Miloš Mařík Osobní Trenér";
        
        // Create email content
        $email_content = "
        <!DOCTYPE html>
        <html lang='cs'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Nová zpráva z kontaktního formuláře</title>
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background-color: #f8f8f8;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 10px;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                    overflow: hidden;
                }
                .header {
                    background-color: #080b01;
                    color: #ffffff;
                    padding: 30px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 700;
                }
                .header p {
                    margin: 10px 0 0 0;
                    font-size: 14px;
                    opacity: 0.9;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                }
                .content {
                    padding: 30px;
                }
                .field {
                    margin-bottom: 20px;
                    border-bottom: 1px solid #e0e0e0;
                    padding-bottom: 15px;
                }
                .field:last-child {
                    border-bottom: none;
                    margin-bottom: 0;
                }
                .field-label {
                    font-weight: 600;
                    color: #080b01;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    margin-bottom: 8px;
                }
                .field-value {
                    font-size: 16px;
                    color: #333;
                    line-height: 1.5;
                }
                .footer {
                    background-color: #f8f8f8;
                    padding: 20px 30px;
                    border-top: 1px solid #e0e0e0;
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                }
                .highlight {
                    background-color: #f0f9ff;
                    padding: 15px;
                    border-radius: 5px;
                    border-left: 4px solid #080b01;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Miloš Mařík</h1>
                    <p>Osobní Trenér</p>
                </div>
                
                <div class='content'>
                    <h2 style='color: #080b01; margin-top: 0; margin-bottom: 25px;'>Nová zpráva z kontaktního formuláře</h2>
                    
                    <div class='field'>
                        <div class='field-label'>Jméno a příjmení:</div>
                        <div class='field-value'>" . htmlspecialchars($name) . "</div>
                    </div>
                    
                    <div class='field'>
                        <div class='field-label'>E-mailová adresa:</div>
                        <div class='field-value'>" . htmlspecialchars($email) . "</div>
                    </div>";
                    
                    if (!empty($phone)) {
                        $email_content .= "
                    <div class='field'>
                        <div class='field-label'>Telefon:</div>
                        <div class='field-value'>" . htmlspecialchars($phone) . "</div>
                    </div>";
                    }
                    
                    $email_content .= "
                    <div class='field'>
                        <div class='field-label'>Zpráva:</div>
                        <div class='field-value highlight'>" . nl2br(htmlspecialchars($message)) . "</div>
                    </div>
                </div>
                
                <div class='footer'>
                    
                    <p>Datum odeslání: " . date('d.m.Y H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Email configuration - IMPORTANT: Change this to your website's domain
        $from_email = "milosmarikosobnitrener@email.cz"; // Replace with your actual domain
        // Alternative if you have a contact email: $from_email = "contact@yourwebsite.com";
        
        // Email headers
        $headers = array(
            'MIME-Version' => '1.0',
            'Content-type' => 'text/html; charset=utf-8',
            'From' => $from_email,
            'Reply-To' => $email, // This allows jakub to reply directly to the user
            'X-Mailer' => 'PHP/' . phpversion()
        );
        
        // Convert headers array to string
        $headers_string = '';
        foreach($headers as $key => $value) {
            $headers_string .= $key . ': ' . $value . "\r\n";
        }
        
        // Send email
        if (mail($to, $subject, $email_content, $headers_string)) {
            echo json_encode([
                'success' => true,
                'message' => 'Vaše zpráva byla úspěšně odeslána. Brzy se vám ozvu!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Nepodařilo se odeslat zprávu. Zkuste to prosím znovu později.'
            ]);
        }
        
    } else {
        // Return validation errors
        echo json_encode([
            'success' => false,
            'message' => implode(' ', $errors)
        ]);
    }
    
} else {
    // Not a POST request
    echo json_encode([
        'success' => false,
        'message' => 'Neplatná metoda požadavku.'
    ]);
}
?>