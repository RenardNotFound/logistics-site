<?php
// Remplace par ton vrai lien de webhook
$webhook_url = "https://discord.com/api/webhooks/1353329278087069739/zUz5A81UGb7iF2WRHbWdnPlQ-Vnh6yQMyrufullIQgUof2yL-3lBaV1gdAEDMJyhAjge";

header('Content-Type: application/json');

// Récupère et décode les données JSON envoyées par le site
$data = json_decode(file_get_contents("php://input"), true);

// Sécurité de base : vérifier que les champs existent
$name = trim($data['name'] ?? '');
$cart = $data['cart'] ?? [];
$total = $data['total'] ?? 0;

// Vérifications supplémentaires
if (empty($name) || empty($cart) || $total <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Commande invalide."]);
    exit;
}

// Prépare le message pour Discord
$formatted = "";
foreach ($cart as $item) {
    $itemName = htmlspecialchars($item['name']);
    $qty = intval($item['quantity']);
    $formatted .= "- {$qty}x {$itemName}\n";
}

$message = [
    "content" => "📦 Nouvelle commande de **" . htmlspecialchars($name) . "** :\n" . $formatted . "💰 Total : {$total}$"
];

// Envoie la commande à Discord
$options = [
    "http" => [
        "method"  => "POST",
        "header"  => "Content-type: application/json",
        "content" => json_encode($message)
    ]
];

$context  = stream_context_create($options);
file_get_contents($webhook_url, false, $context);

echo json_encode(["success" => true]);
?>
