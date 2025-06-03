<?php
$botToken = getenv("TELEGRAM_BOT_TOKEN");
$website = "https://api.telegram.org/bot$botToken";

$content = file_get_contents("php://input");
$update = json_decode($content, true);

$chatId = $update["message"]["chat"]["id"];
$message = strtolower(trim($update["message"]["text"]));

$pasillos = [
    "pasillo 1" => ["carne", "queso", "jamón"],
    "pasillo 2" => ["leche", "yogurth", "cereal"],
    "pasillo 3" => ["bebidas", "jugos"],
    "pasillo 4" => ["pan", "pasteles", "tortas"],
    "pasillo 5" => ["detergente", "lavaloza"]
];

function detectarPasillo($mensaje, $pasillos) {
    foreach ($pasillos as $pasillo => $productos) {
        foreach ($productos as $producto) {
            if (strpos($mensaje, $producto) !== false) {
                return "Encontrarás *$producto* en el *$pasillo*.";
            }
        }
    }
    return "Lo siento, no entiendo tu pregunta.";
}

if ($message === "/start") {
    $keyboard = [
        "keyboard" => [
            [["text" => "Carne"], ["text" => "Queso"]],
            [["text" => "Leche"], ["text" => "Yogurth"]],
            [["text" => "Jugos"], ["text" => "Pan"]],
            [["text" => "Detergente"], ["text" => "Lavaloza"]]
        ],
        "resize_keyboard" => true,
        "one_time_keyboard" => false
    ];

    $reply = [
        "chat_id" => $chatId,
        "text" => "Hola 👋\nSelecciona un producto para saber en qué pasillo está:",
        "reply_markup" => json_encode($keyboard)
    ];
} else {
    $respuesta = detectarPasillo($message, $pasillos);
    $reply = [
        "chat_id" => $chatId,
        "text" => $respuesta,
        "parse_mode" => "Markdown"
    ];
}

$options = [
    "http" => [
        "method" => "POST",
        "header" => "Content-Type: application/json",
        "content" => json_encode($reply)
    ]
];
$context = stream_context_create($options);
file_get_contents("$website/sendMessage", false, $context);
