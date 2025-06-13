<?php
$captchaQuestion = $_POST["captcha_question"];
$captchaAnswer = $_POST["captcha_answer"];

error_log("CAPTCHA Question: $captchaQuestion");
error_log("CAPTCHA Answer: $captchaAnswer");

// Basic validation
if (empty($captchaQuestion) || empty($captchaAnswer)) {
    echo json_encode(["status" => "error", "message" => "CAPTCHA question or answer is empty."]);
    exit;
}

list($num1, $operator, $num2) = explode(" ", $captchaQuestion);

// Basic validation
if (!in_array($operator, ['+', '-'])) {
    echo json_encode(["status" => "error", "message" => "Invalid operator in CAPTCHA question."]);
    exit;
}

$num1 = (int) $num1;
$num2 = (int) $num2;

$result = 0;
switch ($operator) {
    case "+":
        $result = $num1 + $num2;
        break;
    case "-":
        $result = $num1 - $num2;
        break;
}

error_log("Calculated Result: $result");

$log = [
    "captchaQuestion" => $captchaQuestion,
    "captchaAnswer" => $captchaAnswer,
    "result" => $result,
    "comparison" => (trim($captchaAnswer) == (string) $result) ? "TRUE" : "FALSE",
];

// Check if CAPTCHA answer is correct (using loose comparison for testing)
if (trim($captchaAnswer) == (string) $result) {
    echo json_encode(["status" => "success", "log" => $log]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid CAPTCHA answer.", "log" => $log]);
}
?>