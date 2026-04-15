<?php
class Usuario
{
    private $db;
    private $tabela = 'usuarios';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function criar($nome, $email, $senha)
    {
        $query = "INSERT INTO {$this->tabela} (nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt = $this->db->prepare($query);
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);
        return $stmt->execute();
    }

    public function lerPorEmail($email)
    {
        $query = "SELECT * FROM {$this->tabela} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function lerPorId($id)
    {
        $query = "SELECT * FROM {$this->tabela} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listar()
    {
        $query = "SELECT id, nome, email, tipo FROM {$this->tabela} ORDER BY nome";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function atualizar($id, $nome, $email, $senha = null)
    {
        if ($senha) {
            $query = "UPDATE {$this->tabela} SET nome = :nome, email = :email, senha = :senha WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt->bindParam(':senha', $senhaHash);
        } else {
            $query = "UPDATE {$this->tabela} SET nome = :nome, email = :email WHERE id = :id";
            $stmt = $this->db->prepare($query);
        }
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function excluir($id)
    {
        $query = "DELETE FROM {$this->tabela} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function emailExiste($email, $ignorarId = null)
    {
        if ($ignorarId) {
            $query = "SELECT id FROM {$this->tabela} WHERE email = :email AND id != :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $ignorarId);
        } else {
            $query = "SELECT id FROM {$this->tabela} WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($query);
        }
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
