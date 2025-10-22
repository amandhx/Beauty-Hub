<?php
// Inicia a sessão 
session_start(); // garante que a sessão atual está ativa.

// Remove todas as variáveis de sessão  
session_unset(); // apaga todas as variáveis da sessão (por exemplo, $_SESSION["usuario_nome"]).

// Destroi a sessão
session_destroy(); //finaliza completamente a sessão no servidor.

// Redireciona para a página principal 
header("Location: principal.php"); 
exit;
?>
