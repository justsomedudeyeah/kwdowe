<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Você precisa estar logado para atualizar o carrinho.'); window.location='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'] ?? null;
    $quantidade = $_POST['quantidade'] ?? null;

    if (!$item_id || !$quantidade || $quantidade < 1) {
        echo "<script>alert('Quantidade inválida.'); window.location='carrinho.php';</script>";
        exit();
    }

    // Verifica se o item pertence ao carrinho do usuário
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "SELECT ic.id 
            FROM itens_carrinho ic
            JOIN carrinhos c ON ic.carrinho_id = c.id
            WHERE ic.id = ? AND c.usuario_id = ? AND c.finalizado = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$item_id, $usuario_id]);

    if ($stmt->rowCount() === 0) {
        echo "<script>alert('Item não encontrado no seu carrinho.'); window.location='carrinho.php';</script>";
        exit();
    }

    // Atualiza a quantidade
    $sql = "UPDATE itens_carrinho SET quantidade = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$quantidade, $item_id]);

    echo "<script>window.location='carrinho.php';</script>";
} else {
    echo "<script>alert('Requisição inválida.'); window.location='carrinho.php';</script>";
}
?>
